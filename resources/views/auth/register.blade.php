@extends('layouts.app')

@section('title', 'Реєстрація - Книжковий форум')

@section('main')
<div class="flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900">
                <svg class="h-8 w-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Створити акаунт
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Або
                <a href="{{ route('login') }}" class="font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors">
                    увійти в існуючий
                </a>
            </p>
        </div>

        <!-- Progress Steps -->
        <div class="flex justify-center mb-6">
            <div class="flex items-center space-x-3">
                <div class="flex items-center">
                    <div id="step-1-indicator" class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center text-sm font-bold">1</div>
                    <span id="step-1-text" class="ml-2 text-sm font-medium text-primary-600 dark:text-primary-400">Особисті дані</span>
                </div>
                <div class="w-6 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                <div class="flex items-center">
                    <div id="step-2-indicator" class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 flex items-center justify-center text-sm font-bold">2</div>
                    <span id="step-2-text" class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Додаткова інформація</span>
                </div>
                <div class="w-6 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                <div class="flex items-center">
                    <div id="step-3-indicator" class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 flex items-center justify-center text-sm font-bold">3</div>
                    <span id="step-3-text" class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Підтвердження</span>
                </div>
            </div>
        </div>

        <!-- Multi-step Form -->
        <form id="registration-form" class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf
            
            <!-- Step 1: Basic Information -->
            <div id="step-1" class="step-content">
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Ім'я <span class="text-red-500">*</span>
                        </label>
                        <input id="name" name="name" type="text" required 
                               class="input-field mt-1 block w-full"
                               placeholder="Ваше ім'я"
                               value="{{ old('name') }}">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input id="email" name="email" type="email" required 
                               class="input-field mt-1 block w-full"
                               placeholder="your@email.com"
                               value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Пароль <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required 
                                   class="input-field mt-1 block w-full pr-10"
                                   placeholder="••••••••">
                            <button type="button" onclick="togglePassword('password')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Підтвердіть пароль <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" required 
                                   class="input-field mt-1 block w-full pr-10"
                                   placeholder="••••••••">
                            <button type="button" onclick="togglePassword('password_confirmation')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Additional Information -->
            <div id="step-2" class="step-content hidden">
                <div class="space-y-4">
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Дата народження
                        </label>
                        <input id="birth_date" name="birth_date" type="date" 
                               class="input-field mt-1 block w-full"
                               value="{{ old('birth_date') }}">
                        @error('birth_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Місто
                        </label>
                        <input id="city" name="city" type="text" 
                               class="input-field mt-1 block w-full"
                               placeholder="Київ"
                               value="{{ old('city') }}">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Про себе
                        </label>
                        <textarea id="bio" name="bio" rows="3" 
                                  class="input-field mt-1 block w-full"
                                  placeholder="Розкажіть трохи про себе...">{{ old('bio') }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Улюблені жанри
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="favorite_genres[]" value="fiction" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Художня література</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="favorite_genres[]" value="non-fiction" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Наукова література</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="favorite_genres[]" value="mystery" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Детективи</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="favorite_genres[]" value="romance" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Романтика</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="favorite_genres[]" value="fantasy" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Фентезі</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="favorite_genres[]" value="sci-fi" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Наукова фантастика</span>
                            </label>
                        </div>
                        @error('favorite_genres')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="newsletter" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Підписатися на розсилку новин</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Step 3: Confirmation -->
            <div id="step-3" class="step-content hidden">
                <div class="space-y-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                    Підтвердження email
                                </h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    Після реєстрації на вашу електронну пошту буде надіслано лист з посиланням для підтвердження акаунту.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 rounded-md p-4">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Перевірте введені дані:</h4>
                        <div id="summary-data" class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <!-- Summary will be populated by JavaScript -->
                        </div>
                    </div>

                    <div>
                        <label class="flex items-start">
                            <input type="checkbox" name="terms" value="1" required class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Я погоджуюся з 
                                <a href="#" class="text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300">умовами використання</a> 
                                та 
                                <a href="#" class="text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300">політикою конфіденційності</a>
                            </span>
                        </label>
                        @error('terms')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between">
                <button type="button" id="prev-btn" class="btn-secondary hidden" onclick="previousStep()">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Назад
                </button>
                
                <div class="flex space-x-4">
                    <button type="button" id="next-btn" class="btn-primary" onclick="nextStep()">
                        Далі
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    
                    <button type="submit" id="submit-btn" class="btn-primary hidden">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Зареєструватися
                    </button>
                </div>
            </div>
        </form>

        <!-- OAuth Login Options -->
        <div class="mt-8">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400">Або увійдіть через</span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3">
                <button type="button" onclick="loginWithGoogle()" 
                        class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-2xl shadow-sm bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="ml-2">Google</span>
                </button>

                <button type="button" onclick="loginWithFacebook()" 
                        class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-2xl shadow-sm bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="#1877F2" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="ml-2">Facebook</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 3;

function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            document.getElementById(`step-${currentStep}`).classList.add('hidden');
            currentStep++;
            document.getElementById(`step-${currentStep}`).classList.remove('hidden');
            updateStepIndicator();
            updateNavigationButtons();
            
            if (currentStep === 3) {
                updateSummary();
            }
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        currentStep--;
        document.getElementById(`step-${currentStep}`).classList.remove('hidden');
        updateStepIndicator();
        updateNavigationButtons();
    }
}

function updateStepIndicator() {
    for (let i = 1; i <= totalSteps; i++) {
        const indicator = document.getElementById(`step-${i}-indicator`);
        const text = document.getElementById(`step-${i}-text`);
        
        if (i <= currentStep) {
            indicator.className = 'w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center text-sm font-bold';
            text.className = 'ml-2 text-sm font-medium text-primary-600 dark:text-primary-400';
        } else {
            indicator.className = 'w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 flex items-center justify-center text-sm font-bold';
            text.className = 'ml-2 text-sm font-medium text-gray-500 dark:text-gray-400';
        }
    }
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    
    if (currentStep === 1) {
        prevBtn.classList.add('hidden');
    } else {
        prevBtn.classList.remove('hidden');
    }
    
    if (currentStep === totalSteps) {
        nextBtn.classList.add('hidden');
        submitBtn.classList.remove('hidden');
    } else {
        nextBtn.classList.remove('hidden');
        submitBtn.classList.add('hidden');
    }
}

function validateCurrentStep() {
    const currentStepElement = document.getElementById(`step-${currentStep}`);
    const requiredFields = currentStepElement.querySelectorAll('input[required], textarea[required]');
    
    for (let field of requiredFields) {
        if (!field.value.trim()) {
            field.focus();
            return false;
        }
    }
    
    // Additional validation for step 1
    if (currentStep === 1) {
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        
        if (password !== passwordConfirmation) {
            alert('Паролі не співпадають');
            return false;
        }
        
        if (password.length < 8) {
            alert('Пароль повинен містити мінімум 8 символів');
            return false;
        }
    }
    
    return true;
}

function updateSummary() {
    const summary = document.getElementById('summary-data');
    const formData = new FormData(document.getElementById('registration-form'));
    
    let summaryHTML = `
        <p><strong>Ім'я:</strong> ${formData.get('name')}</p>
        <p><strong>Email:</strong> ${formData.get('email')}</p>
        <p><strong>Місто:</strong> ${formData.get('city') || 'Не вказано'}</p>
        <p><strong>Дата народження:</strong> ${formData.get('birth_date') || 'Не вказано'}</p>
    `;
    
    const selectedGenres = formData.getAll('favorite_genres[]');
    if (selectedGenres.length > 0) {
        summaryHTML += `<p><strong>Улюблені жанри:</strong> ${selectedGenres.join(', ')}</p>`;
    }
    
    summary.innerHTML = summaryHTML;
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
}

function loginWithGoogle() {
    window.location.href = '{{ route("auth.google") }}';
}

function loginWithFacebook() {
    window.location.href = '{{ route("auth.facebook") }}';
}

// Initialize
updateStepIndicator();
updateNavigationButtons();
</script>
@endsection
