<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Assessment - Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <!-- Header -->
            <div class="mb-6 pb-4 border-b">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Technical Assessment</h1>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">Question <span id="currentQuestion">1</span> of <span id="totalQuestions">0</span></p>
                        </div>
                    </div>
                    <!-- Timer Display -->
                    <div class="text-right">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500">Time Remaining</p>
                                <p id="timerDisplay" class="text-xl font-bold text-gray-900">60s</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>

            <!-- Question -->
            <div id="questionContainer" class="space-y-6">
                <div class="animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                    <div class="space-y-3">
                        <div class="h-4 bg-gray-200 rounded"></div>
                        <div class="h-4 bg-gray-200 rounded"></div>
                        <div class="h-4 bg-gray-200 rounded"></div>
                        <div class="h-4 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="mt-8 flex justify-between">
                <button
                    id="prevBtn"
                    onclick="previousQuestion()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled
                >
                    Previous
                </button>
                <button
                    id="nextBtn"
                    onclick="nextQuestion()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Next
                </button>
                <button
                    id="submitBtn"
                    onclick="submitTest()"
                    class="hidden px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                >
                    Submit Test
                </button>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="hidden mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded"></div>
            
            <!-- Timer Warning -->
            <div id="timerWarning" class="hidden mt-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Time is running out! Please answer quickly.</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sessionId = '{{ $sessionId }}';
        const TIME_LIMIT_PER_QUESTION = 60; // 60 seconds per question
        let questions = [];
        let currentQuestionIndex = 0;
        let answers = {};
        let questionTimers = {}; // Store remaining time per question ID
        let timerInterval = null;
        let timeRemaining = TIME_LIMIT_PER_QUESTION;
        let currentQuestionId = null;

        // Load questions
        async function loadQuestions() {
            try {
                const response = await fetch(`/api/session/${sessionId}/questions`);
                const data = await response.json();
                
                if (response.ok) {
                    questions = data.questions;
                    answers = data.answers || {};
                    questionTimers = data.timers || {}; // Load saved timer states
                    document.getElementById('totalQuestions').textContent = questions.length;
                    displayQuestion(); // This will start/resume the timer
                } else {
                    showError(data.error || 'Failed to load questions. Please try again.');
                }
            } catch (error) {
                showError('Failed to load assessment. Please try again.');
            }
        }

        // Display current question
        function displayQuestion() {
            if (questions.length === 0) {
                loadQuestions();
                return;
            }

            // Save current timer state before switching questions
            if (currentQuestionId !== null) {
                saveTimerState();
            }

            // Stop previous timer
            stopTimer();

            const question = questions[currentQuestionIndex];
            currentQuestionId = question.id;

            // Restore timer state for this question or initialize if first time
            // Check both string and number keys (JSON keys can be strings)
            const savedTime = questionTimers[question.id] || questionTimers[String(question.id)];
            if (savedTime !== undefined && savedTime !== null && savedTime > 0) {
                // Resume from saved time
                timeRemaining = parseInt(savedTime);
                questionTimers[question.id] = timeRemaining;
            } else {
                // First time seeing this question - start fresh
                timeRemaining = TIME_LIMIT_PER_QUESTION;
                questionTimers[question.id] = TIME_LIMIT_PER_QUESTION;
            }

            // Start timer
            startTimer();

            const container = document.getElementById('questionContainer');
            
            container.innerHTML = `
                <div>
                    <p class="text-sm text-gray-500 mb-2">${question.language}</p>
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">${question.question}</h2>
                    <div class="space-y-3">
                        ${question.options.map((option, index) => `
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors ${
                                answers[question.id] === option 
                                    ? 'border-blue-500 bg-blue-50' 
                                    : 'border-gray-200 hover:bg-gray-50'
                            }">
                                <input
                                    type="radio"
                                    name="answer"
                                    value="${option}"
                                    ${answers[question.id] === option ? 'checked' : ''}
                                    onchange="selectAnswer(${question.id}, '${option.replace(/'/g, "\\'")}')"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                >
                                <span class="ml-3 text-gray-700">${option}</span>
                            </label>
                        `).join('')}
                    </div>
                </div>
            `;

            // Update UI
            document.getElementById('currentQuestion').textContent = currentQuestionIndex + 1;
            updateNavigation();
            updateProgress();
        }

        // Start timer
        function startTimer() {
            updateTimerDisplay();
            timerInterval = setInterval(() => {
                timeRemaining--;
                
                // Save timer state continuously
                if (currentQuestionId !== null) {
                    questionTimers[currentQuestionId] = timeRemaining;
                    saveTimerStateToServer();
                }
                
                updateTimerDisplay();

                if (timeRemaining <= 0) {
                    stopTimer();
                    handleTimeExpired();
                } else if (timeRemaining <= 10) {
                    // Warning when 10 seconds or less
                    document.getElementById('timerDisplay').classList.add('text-red-600', 'animate-pulse');
                    document.getElementById('timerWarning').classList.remove('hidden');
                } else if (timeRemaining <= 30) {
                    // Warning when 30 seconds or less
                    document.getElementById('timerDisplay').classList.remove('text-gray-900');
                    document.getElementById('timerDisplay').classList.add('text-orange-600');
                    document.getElementById('timerWarning').classList.remove('hidden');
                } else {
                    document.getElementById('timerWarning').classList.add('hidden');
                }
            }, 1000);
        }

        // Save timer state to local variable
        function saveTimerState() {
            if (currentQuestionId !== null) {
                questionTimers[currentQuestionId] = timeRemaining;
            }
        }

        // Save timer state to server
        async function saveTimerStateToServer() {
            // Debounce: Only save every 5 seconds to reduce server load
            if (!saveTimerStateToServer.lastSave || Date.now() - saveTimerStateToServer.lastSave > 5000) {
                saveTimerStateToServer.lastSave = Date.now();
                
                try {
                    await fetch(`/api/session/${sessionId}/timer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            question_id: currentQuestionId,
                            time_remaining: timeRemaining,
                            timers: questionTimers
                        })
                    });
                } catch (error) {
                    console.error('Failed to save timer state:', error);
                }
            }
        }

        // Stop timer
        function stopTimer() {
            // Save current timer state before stopping
            saveTimerState();
            
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
            // Reset timer display styling
            const timerDisplay = document.getElementById('timerDisplay');
            timerDisplay.classList.remove('text-red-600', 'text-orange-600', 'animate-pulse');
            timerDisplay.classList.add('text-gray-900');
            // Hide warning
            document.getElementById('timerWarning').classList.add('hidden');
            
            // Save to server when stopping
            if (currentQuestionId !== null) {
                saveTimerStateToServer();
            }
        }

        // Update timer display
        function updateTimerDisplay() {
            const timerDisplay = document.getElementById('timerDisplay');
            timerDisplay.textContent = `${timeRemaining}s`;
        }

        // Handle time expired
        function handleTimeExpired() {
            // Auto-select first option if no answer selected (or leave blank)
            const question = questions[currentQuestionIndex];
            if (!answers[question.id]) {
                // Optionally auto-select first option
                // selectAnswer(question.id, question.options[0]);
            }

            // Auto-advance to next question or submit if last question
            if (currentQuestionIndex < questions.length - 1) {
                // Show notification
                showTimerNotification('Time expired! Moving to next question...');
                setTimeout(() => {
                    nextQuestion();
                }, 1500);
            } else {
                // Last question - auto submit
                showTimerNotification('Time expired! Submitting test...');
                setTimeout(() => {
                    submitTest();
                }, 1500);
            }
        }

        // Show timer notification
        function showTimerNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg shadow-lg z-50';
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Select answer
        function selectAnswer(questionId, answer) {
            answers[questionId] = answer;
            
            // Save answer to server
            fetch(`/api/session/${sessionId}/answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    question_id: questionId,
                    answer: answer,
                    time_remaining: questionTimers[questionId] || timeRemaining
                })
            }).catch(error => {
                console.error('Failed to save answer:', error);
            });

            // Update UI - timer continues running, don't reset question
            const question = questions[currentQuestionIndex];
            const container = document.getElementById('questionContainer');
            
            // Just update the selected answer visually without reloading the question
            container.querySelectorAll('input[type="radio"]').forEach(radio => {
                const label = radio.closest('label');
                if (radio.value === answer) {
                    label.classList.remove('border-gray-200');
                    label.classList.add('border-blue-500', 'bg-blue-50');
                } else {
                    label.classList.remove('border-blue-500', 'bg-blue-50');
                    label.classList.add('border-gray-200');
                }
            });
        }

        // Previous question
        function previousQuestion() {
            if (currentQuestionIndex > 0) {
                // Save current timer state before moving
                saveTimerState();
                stopTimer();
                currentQuestionIndex--;
                displayQuestion(); // Will resume timer from saved state
            }
        }

        // Next question
        function nextQuestion() {
            if (currentQuestionIndex < questions.length - 1) {
                // Save current timer state before moving
                saveTimerState();
                stopTimer();
                currentQuestionIndex++;
                displayQuestion(); // Will start new timer or resume if previously visited
            }
        }

        // Update navigation buttons
        function updateNavigation() {
            document.getElementById('prevBtn').disabled = currentQuestionIndex === 0;
            
            if (currentQuestionIndex === questions.length - 1) {
                document.getElementById('nextBtn').classList.add('hidden');
                document.getElementById('submitBtn').classList.remove('hidden');
            } else {
                document.getElementById('nextBtn').classList.remove('hidden');
                document.getElementById('submitBtn').classList.add('hidden');
            }
        }

        // Update progress bar
        function updateProgress() {
            const progress = ((currentQuestionIndex + 1) / questions.length) * 100;
            document.getElementById('progressBar').style.width = `${progress}%`;
        }

        // Submit test
        async function submitTest() {
            // Save all timer states before submitting
            saveTimerState();
            stopTimer();

            // Check if auto-submit (time expired) or manual submit
            const isAutoSubmit = timeRemaining <= 0;
            
            if (!isAutoSubmit && !confirm('Are you sure you want to submit the test? You cannot change your answers after submission.')) {
                // Restart timer if user cancels
                startTimer();
                return;
            }

            try {
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').textContent = 'Submitting...';

                const response = await fetch(`/api/session/${sessionId}/submit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        timers: questionTimers
                    })
                });

                const data = await response.json();
                
                if (response.ok) {
                    window.location.href = `/result/${sessionId}`;
                } else {
                    showError(data.error || 'Failed to submit test. Please try again.');
                    document.getElementById('submitBtn').disabled = false;
                    document.getElementById('submitBtn').textContent = 'Submit Test';
                    // Restart timer if submission failed
                    startTimer();
                }
            } catch (error) {
                showError('An error occurred. Please try again.');
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('submitBtn').textContent = 'Submit Test';
                // Restart timer if submission failed
                startTimer();
            }
        }

        // Show error
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            setTimeout(() => {
                errorDiv.classList.add('hidden');
            }, 5000);
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            stopTimer();
        });

        // Load questions on page load
        loadQuestions();
    </script>
</body>
</html>

