<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Assessment - Select Languages</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Technical Assessment Platform
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Select your preferred programming languages to begin the assessment
            </p>
        </div>
        
        <form id="languageForm" class="mt-8 space-y-6">
            <div id="errorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded"></div>
            <div id="loadingMessage" class="hidden text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-gray-600">Loading languages...</p>
            </div>
            
            <div id="languagesContainer" class="space-y-3">
                <!-- Languages will be loaded here -->
            </div>
            
            <div>
                <button
                    type="submit"
                    id="startTestBtn"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled
                >
                    Start Test
                </button>
            </div>
        </form>
    </div>

    <script>
        let languages = [];
        let selectedLanguages = [];

        // Load available languages
        async function loadLanguages() {
            try {
                document.getElementById('loadingMessage').classList.remove('hidden');
                const response = await fetch('/api/languages');
                const data = await response.json();
                languages = data.languages;
                renderLanguages();
                document.getElementById('loadingMessage').classList.add('hidden');
            } catch (error) {
                document.getElementById('loadingMessage').classList.add('hidden');
                showError('Failed to load languages. Please refresh the page.');
            }
        }

        // Render language checkboxes
        function renderLanguages() {
            const container = document.getElementById('languagesContainer');
            container.innerHTML = languages.map(lang => `
                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                    <input
                        type="checkbox"
                        name="languages"
                        value="${lang}"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        onchange="toggleLanguage('${lang}')"
                    >
                    <span class="ml-3 text-gray-700 font-medium">${lang}</span>
                </label>
            `).join('');
        }

        // Toggle language selection
        function toggleLanguage(lang) {
            const checkbox = document.querySelector(`input[value="${lang}"]`);
            if (checkbox.checked) {
                if (!selectedLanguages.includes(lang)) {
                    selectedLanguages.push(lang);
                }
            } else {
                selectedLanguages = selectedLanguages.filter(l => l !== lang);
            }
            document.getElementById('startTestBtn').disabled = selectedLanguages.length === 0;
        }

        // Show error message
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            setTimeout(() => {
                errorDiv.classList.add('hidden');
            }, 5000);
        }

        // Handle form submission
        document.getElementById('languageForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (selectedLanguages.length === 0) {
                showError('Please select at least one programming language.');
                return;
            }

            try {
                document.getElementById('startTestBtn').disabled = true;
                document.getElementById('startTestBtn').textContent = 'Starting...';
                
                const response = await fetch('/api/session/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ languages: selectedLanguages })
                });

                const data = await response.json();
                
                if (response.ok) {
                    window.location.href = `/test/${data.session_id}`;
                } else {
                    showError(data.error || 'Failed to start assessment. Please try again.');
                    document.getElementById('startTestBtn').disabled = false;
                    document.getElementById('startTestBtn').textContent = 'Start Test';
                }
            } catch (error) {
                showError('An error occurred. Please try again.');
                document.getElementById('startTestBtn').disabled = false;
                document.getElementById('startTestBtn').textContent = 'Start Test';
            }
        });

        // Load languages on page load
        loadLanguages();
    </script>
</body>
</html>

