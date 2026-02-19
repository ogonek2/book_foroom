@extends('layouts.app')

@section('title', 'Контакти - Книжковий форум')

@section('description', 'Як зв\'язатися з FOXY - електронна пошта, соціальні мережі та контактна інформація')
@section('keywords', 'контакти, FOXY, зв\'язок, підтримка, електронна пошта')
@section('canonical', route('contacts'))
@section('og_type', 'website')
@section('og_title', 'Контакти - FOXY')
@section('og_description', 'Як зв\'язатися з FOXY - електронна пошта, соціальні мережі та контактна інформація')
@section('og_url', route('contacts'))
@section('og_image', asset('favicon.svg'))
@section('twitter_title', 'Контакти - FOXY')
@section('twitter_description', 'Як зв\'язатися з FOXY - електронна пошта, соціальні мережі та контактна інформація')
@section('twitter_image', asset('favicon.svg'))

@section('main')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Контакти</h1>
    
    <div class="space-y-8">
        <section>
            <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Як зв'язатися з FOXY</h2>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-white">Електронна пошта FOXY</h3>
                <p class="text-lg mb-4 text-gray-700 dark:text-gray-300">
                    📧 <a href="mailto:foxybooksclub@gmail.com" class="text-brand-600 dark:text-brand-400 hover:underline">foxybooksclub@gmail.com</a>
                </p>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Ми відповідаємо на всі звернення і намагаємося робити це якнайшвидше.
                </p>
                <p class="text-gray-600 dark:text-gray-400 mb-2">
                    Для зручності обробки листів рекомендуємо вказувати тему повідомлення:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400 ml-4">
                    <li><strong class="text-gray-800 dark:text-gray-300">Підтримка</strong> — технічні помилки, проблеми з аккаунтом, роботою сайту</li>
                    <li><strong class="text-gray-800 dark:text-gray-300">Книги</strong> — пропозиції книг для додавання в каталог</li>
                    <li><strong class="text-gray-800 dark:text-gray-300">Співпраця</strong> — видавництва, автори, партнери, проекти</li>
                    <li><strong class="text-gray-800 dark:text-gray-300">Ідея</strong> — пропозиції по функціоналу і розвитку платформи</li>
                    <li><strong class="text-gray-800 dark:text-gray-300">Правила</strong> — питання по модерації і правилам спільноти</li>
                    <li><strong class="text-gray-800 dark:text-gray-300">Інше</strong> — якщо не знайшли відповідної категорії</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Соціальні мережі FOXY</h2>
            
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-white">Telegram</h3>
                    <div class="space-y-3">
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Новинний канал</strong><br>
                            📢 <a href="https://t.me/foxy_books_club" target="_blank" rel="noopener noreferrer" class="text-brand-600 dark:text-brand-400 hover:underline">https://t.me/foxy_books_club</a><br>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Анонси оновлень, новини сайту та події книжкового співтовариства.</span>
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Чат спільноти</strong><br>
                            💬 <a href="https://t.me/foxy_club_chat" target="_blank" rel="noopener noreferrer" class="text-brand-600 dark:text-brand-400 hover:underline">https://t.me/foxy_club_chat</a><br>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Обговорення книг, пропозиції по наповненню сайту, допомога та зворотний зв'язок від спільноти.</span>
                        </p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-white">Instagram</h3>
                    <p class="text-gray-700 dark:text-gray-300">
                        📸 <a href="https://www.instagram.com/foxybooksclub" target="_blank" rel="noopener noreferrer" class="text-brand-600 dark:text-brand-400 hover:underline">@foxybooksclub</a><br>
                        <a href="https://www.instagram.com/foxybooksclub" target="_blank" rel="noopener noreferrer" class="text-brand-600 dark:text-brand-400 hover:underline">https://www.instagram.com/foxybooksclub</a><br>
                        <span class="text-sm text-gray-600 dark:text-gray-400">В Instagram ми ділимося книжковими рекомендаціями, цитатами, рецензіями та новинами спільноти.</span>
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-white">Facebook</h3>
                    <p class="text-gray-700 dark:text-gray-300">
                        📘 <a href="https://www.facebook.com/FoxyBooksClub/" target="_blank" rel="noopener noreferrer" class="text-brand-600 dark:text-brand-400 hover:underline">Foxy — книжкове співтовариство</a><br>
                        <a href="https://www.facebook.com/FoxyBooksClub/" target="_blank" rel="noopener noreferrer" class="text-brand-600 dark:text-brand-400 hover:underline">https://www.facebook.com/FoxyBooksClub/</a><br>
                        <span class="text-sm text-gray-600 dark:text-gray-400">На Facebook-сторінці FOXY публікуються оновлення платформи, анонси та важливі новини для читачів.</span>
                    </p>
                </div>
            </div>
        </section>

        <section class="bg-brand-50 dark:bg-brand-900/20 rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-200">🦊 Важливо знати</h2>
            <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                <li>• Ми читаємо кожне повідомлення</li>
                <li>• Середній час відповіді: 1–3 робочих дні</li>
                <li>• FOXY розвивається разом зі спільнотою</li>
                <li>• Відправляючи повідомлення, ви погоджуєтеся на обробку персональних даних виключно в цілях зворотного зв'язку.</li>
            </ul>
        </section>
    </div>
</div>
@endsection
