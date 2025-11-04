<?php

namespace App\Http\Controllers;

use App\Models\AssessmentSession;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    /**
     * Get all available languages
     */
    public function getLanguages()
    {
        $languages = Question::distinct()->pluck('language')->toArray();
        return response()->json(['languages' => $languages]);
    }

    /**
     * Start a new assessment session
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'languages' => 'required|array|min:1',
            'languages.*' => 'string',
        ]);

        $languages = $request->input('languages');
        
        // Get questions for selected languages
        $questions = Question::whereIn('language', $languages)->get();
        
        if ($questions->isEmpty()) {
            return response()->json(['error' => 'No questions found for selected languages'], 400);
        }

        // Shuffle and select questions (max 10 per language)
        $selectedQuestions = [];
        foreach ($languages as $language) {
            $langQuestions = $questions->where('language', $language)->shuffle()->take(5);
            $selectedQuestions = array_merge($selectedQuestions, $langQuestions->all());
        }
        
        // Shuffle all selected questions
        shuffle($selectedQuestions);
        
        // Limit to 20 questions total
        $selectedQuestions = array_slice($selectedQuestions, 0, 20);
        
        $sessionId = Str::uuid()->toString();
        
        $session = AssessmentSession::create([
            'session_id' => $sessionId,
            'selected_languages' => $languages,
            'question_ids' => array_map(fn($q) => $q->id, $selectedQuestions),
            'total_questions' => count($selectedQuestions),
            'is_completed' => false,
        ]);

        // Return questions without correct answers
        $questionsData = array_map(function($question) {
            return [
                'id' => $question->id,
                'question' => $question->question,
                'options' => $question->options,
                'language' => $question->language,
            ];
        }, $selectedQuestions);

        return response()->json([
            'session_id' => $sessionId,
            'questions' => $questionsData,
        ]);
    }

    /**
     * Submit an answer for a question
     */
    public function submitAnswer(Request $request, $sessionId)
    {
        $request->validate([
            'question_id' => 'required|integer',
            'answer' => 'required|string',
        ]);

        $session = AssessmentSession::where('session_id', $sessionId)->firstOrFail();
        
        $answers = $session->answers ?? [];
        $answers[$request->question_id] = $request->answer;
        
        $session->update(['answers' => $answers]);

        return response()->json(['success' => true]);
    }

    /**
     * Submit the complete test
     */
    public function submitTest(Request $request, $sessionId)
    {
        $session = AssessmentSession::where('session_id', $sessionId)->firstOrFail();
        
        if ($session->is_completed) {
            return response()->json(['error' => 'Test already submitted'], 400);
        }

        $answers = $session->answers ?? [];
        $questionIds = $session->question_ids;
        
        // Calculate score
        $score = 0;
        $questions = Question::whereIn('id', $questionIds)->get();
        
        foreach ($questions as $question) {
            if (isset($answers[$question->id]) && $answers[$question->id] === $question->correct_answer) {
                $score++;
            }
        }

        $session->update([
            'score' => $score,
            'is_completed' => true,
        ]);

        $threshold = (int)($session->total_questions * 0.6); // 60% threshold

        return response()->json([
            'score' => $score,
            'total' => $session->total_questions,
            'percentage' => round(($score / $session->total_questions) * 100, 2),
            'passed' => $score >= $threshold,
            'threshold' => $threshold,
        ]);
    }

    /**
     * Get session details
     */
    public function getSession($sessionId)
    {
        $session = AssessmentSession::where('session_id', $sessionId)->firstOrFail();
        
        return response()->json([
            'session_id' => $session->session_id,
            'score' => $session->score,
            'total_questions' => $session->total_questions,
            'is_completed' => $session->is_completed,
            'has_resume' => !empty($session->resume_path),
        ]);
    }

    /**
     * Get questions for an existing session
     */
    public function getSessionQuestions($sessionId)
    {
        $session = AssessmentSession::where('session_id', $sessionId)->firstOrFail();
        
        if ($session->is_completed) {
            return response()->json(['error' => 'Test already completed'], 400);
        }

        // Get questions in the same order as stored in question_ids
        $questions = Question::whereIn('id', $session->question_ids)->get()->keyBy('id');
        
        // Return questions without correct answers in the same order as question_ids
        $questionsData = collect($session->question_ids)->map(function($questionId) use ($questions) {
            $question = $questions->get($questionId);
            if (!$question) return null;
            
            return [
                'id' => $question->id,
                'question' => $question->question,
                'options' => $question->options,
                'language' => $question->language,
            ];
        })->filter()->values()->toArray();

        // Convert timer keys from string to integer for consistency
        $timers = [];
        if ($session->timers) {
            foreach ($session->timers as $key => $value) {
                $timers[(int)$key] = (int)$value;
            }
        }

        return response()->json([
            'questions' => $questionsData,
            'answers' => $session->answers ?? [],
            'timers' => $timers,
        ]);
    }

    /**
     * Save timer state for a question
     */
    public function saveTimer(Request $request, $sessionId)
    {
        $request->validate([
            'question_id' => 'required|integer',
            'time_remaining' => 'required|integer|min:0',
            'timers' => 'sometimes|array',
        ]);

        $session = AssessmentSession::where('session_id', $sessionId)->firstOrFail();
        
        if ($session->is_completed) {
            return response()->json(['error' => 'Test already completed'], 400);
        }

        // Store all timers if provided
        if ($request->has('timers')) {
            $session->update(['timers' => $request->timers]);
        } else {
            // Store single timer
            $timers = $session->timers ?? [];
            $timers[$request->question_id] = $request->time_remaining;
            $session->update(['timers' => $timers]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Upload resume
     */
    public function uploadResume(Request $request, $sessionId)
    {
        $request->validate([
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
        ]);

        $session = AssessmentSession::where('session_id', $sessionId)->firstOrFail();
        
        if (!$session->is_completed) {
            return response()->json(['error' => 'Test must be completed first'], 400);
        }

        $threshold = (int)($session->total_questions * 0.6);
        if ($session->score < $threshold) {
            return response()->json(['error' => 'Score threshold not met'], 400);
        }

        if ($session->resume_path) {
            Storage::delete($session->resume_path);
        }

        $file = $request->file('resume');
        $path = $file->store('resumes', 'public');
        
        $session->update(['resume_path' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Resume uploaded successfully',
        ]);
    }
}
