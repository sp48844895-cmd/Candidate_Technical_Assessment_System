<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Assessment - Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-8 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <div id="loadingContainer" class="text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-gray-600">Loading results...</p>
            </div>

            <div id="resultContainer" class="hidden space-y-6">
                <!-- Score Display -->
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Assessment Results</h1>
                    <div class="mt-6">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full border-4 border-blue-600">
                            <span id="scoreDisplay" class="text-3xl font-bold text-blue-600">0</span>
                        </div>
                        <p class="mt-2 text-gray-600">out of <span id="totalDisplay">0</span> questions</p>
                        <p class="mt-1 text-sm font-medium text-gray-700"><span id="percentageDisplay">0</span>%</p>
                    </div>
                </div>

                <!-- Pass/Fail Message -->
                <div id="passMessage" class="hidden p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-800 font-medium">Congratulations! You passed the assessment.</p>
                    </div>
                    <p class="mt-2 text-sm text-green-700">You can now upload your resume to complete the application process.</p>
                </div>

                <div id="failMessage" class="hidden p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-800 font-medium">Score threshold not met.</p>
                    </div>
                    <p class="mt-2 text-sm text-red-700">You need to score at least <span id="thresholdDisplay">0</span>% to proceed. Please try again later.</p>
                </div>

                <!-- Resume Upload Section -->
                <div id="resumeUploadSection" class="hidden mt-6 p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload Your Resume</h2>
                    <form id="resumeForm" enctype="multipart/form-data">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Resume File (PDF, DOC, DOCX - Max 5MB)
                                </label>
                                <input
                                    type="file"
                                    id="resumeFile"
                                    name="resume"
                                    accept=".pdf,.doc,.docx"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    required
                                >
                                <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, DOC, DOCX (Maximum size: 5MB)</p>
                            </div>
                            <button
                                type="submit"
                                id="uploadBtn"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Upload Resume
                            </button>
                        </div>
                    </form>
                    <div id="uploadSuccess" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-800 font-medium">Resume uploaded successfully!</p>
                        <p class="mt-1 text-sm text-green-700">Your application has been submitted.</p>
                    </div>
                    <div id="uploadError" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-800 font-medium" id="uploadErrorMessage"></p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-center">
                    <a
                        href="/"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                    >
                        Start New Assessment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sessionId = '{{ $sessionId }}';

        // Load results
        async function loadResults() {
            try {
                const response = await fetch(`/api/session/${sessionId}`);
                const data = await response.json();
                
                if (data.is_completed) {
                    displayResults(data);
                } else {
                    // If test not completed, redirect to test page
                    window.location.href = `/test/${sessionId}`;
                }
            } catch (error) {
                document.getElementById('loadingContainer').innerHTML = `
                    <p class="text-red-600">Failed to load results. Please try again.</p>
                `;
            }
        }

        // Display results
        function displayResults(data) {
            document.getElementById('loadingContainer').classList.add('hidden');
            document.getElementById('resultContainer').classList.remove('hidden');

            // Display score
            document.getElementById('scoreDisplay').textContent = data.score || 0;
            document.getElementById('totalDisplay').textContent = data.total_questions || 0;
            
            const percentage = data.total_questions > 0 
                ? Math.round((data.score / data.total_questions) * 100) 
                : 0;
            document.getElementById('percentageDisplay').textContent = percentage;

            // Calculate threshold (60%)
            const threshold = Math.ceil(data.total_questions * 0.6);
            document.getElementById('thresholdDisplay').textContent = threshold;

            // Show pass/fail message
            if (data.score >= threshold) {
                document.getElementById('passMessage').classList.remove('hidden');
                document.getElementById('failMessage').classList.add('hidden');
                document.getElementById('resumeUploadSection').classList.remove('hidden');
                
                // Check if resume already uploaded
                if (data.has_resume) {
                    document.getElementById('resumeForm').classList.add('hidden');
                    document.getElementById('uploadSuccess').classList.remove('hidden');
                }
            } else {
                document.getElementById('passMessage').classList.add('hidden');
                document.getElementById('failMessage').classList.remove('hidden');
                document.getElementById('resumeUploadSection').classList.add('hidden');
            }
        }

        // Handle resume upload
        document.getElementById('resumeForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const fileInput = document.getElementById('resumeFile');
            const file = fileInput.files[0];
            
            if (!file) {
                showUploadError('Please select a file.');
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showUploadError('File size must be less than 5MB.');
                return;
            }

            // Validate file type
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                showUploadError('Invalid file type. Please upload PDF, DOC, or DOCX files only.');
                return;
            }

            try {
                document.getElementById('uploadBtn').disabled = true;
                document.getElementById('uploadBtn').textContent = 'Uploading...';
                document.getElementById('uploadError').classList.add('hidden');

                const formData = new FormData();
                formData.append('resume', file);
                formData.append('_token', '{{ csrf_token() }}');

                const response = await fetch(`/api/session/${sessionId}/resume`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                if (response.ok) {
                    document.getElementById('resumeForm').classList.add('hidden');
                    document.getElementById('uploadSuccess').classList.remove('hidden');
                } else {
                    showUploadError(data.error || 'Failed to upload resume. Please try again.');
                    document.getElementById('uploadBtn').disabled = false;
                    document.getElementById('uploadBtn').textContent = 'Upload Resume';
                }
            } catch (error) {
                showUploadError('An error occurred. Please try again.');
                document.getElementById('uploadBtn').disabled = false;
                document.getElementById('uploadBtn').textContent = 'Upload Resume';
            }
        });

        // Show upload error
        function showUploadError(message) {
            const errorDiv = document.getElementById('uploadError');
            document.getElementById('uploadErrorMessage').textContent = message;
            errorDiv.classList.remove('hidden');
            setTimeout(() => {
                errorDiv.classList.add('hidden');
            }, 5000);
        }

        // Load results on page load
        loadResults();
    </script>
</body>
</html>

