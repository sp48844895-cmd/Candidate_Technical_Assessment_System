<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * Display the language selection page
     */
    public function index()
    {
        return view('assessment.index');
    }

    /**
     * Display the assessment test page
     */
    public function test($sessionId)
    {
        return view('assessment.test', compact('sessionId'));
    }

    /**
     * Display the result page
     */
    public function result($sessionId)
    {
        return view('assessment.result', compact('sessionId'));
    }
}
