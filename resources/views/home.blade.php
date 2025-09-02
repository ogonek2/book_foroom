@extends('layouts.app')

@section('title', 'Головна - Книжковий форум')

@section('main')
        <!-- 1. Книги якими зацікавились -->
        <section class="mb-12">
            <h2 class="text-3xl font-bold text-white mb-6">Книги якими зацікавились</h2>
            <div class="relative">
                <div class="flex space-x-6 overflow-x-auto pb-4 scrollbar-hide">
                    <!-- Book Card 1 -->
                    <div class="flex-shrink-0 w-48">
                        <div class="bg-gray-800 rounded-lg overflow-hidden hover:bg-gray-750 transition-colors duration-200">
                            <div class="w-full h-64 bg-gradient-to-br from-red-900 to-red-700 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="text-4xl font-bold mb-2">1984</div>
                                    <div class="text-sm opacity-80">Джордж Орвелл</div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold text-sm mb-1 line-clamp-2">1984</h3>
                                <p class="text-gray-400 text-xs mb-2">Джордж Орвелл</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>1949</span>
                                    <span>Антиутопія</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Book Card 2 -->
                    <div class="flex-shrink-0 w-48">
                        <div class="bg-gray-800 rounded-lg overflow-hidden hover:bg-gray-750 transition-colors duration-200">
                            <div class="w-full h-64 bg-gradient-to-br from-purple-900 to-blue-700 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="text-2xl font-bold mb-2">⚡</div>
                                    <div class="text-lg font-bold mb-1">Harry Potter</div>
                                    <div class="text-sm opacity-80">J.K. Rowling</div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold text-sm mb-1 line-clamp-2">Гаррі Поттер і філософський камінь</h3>
                                <p class="text-gray-400 text-xs mb-2">Дж. К. Роулінг</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>1997</span>
                                    <span>Фентезі</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Book Card 3 -->
                    <div class="flex-shrink-0 w-48">
                        <div class="bg-gray-800 rounded-lg overflow-hidden hover:bg-gray-750 transition-colors duration-200">
                            <div class="w-full h-64 bg-gradient-to-br from-amber-900 to-yellow-700 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="text-2xl font-bold mb-2">⚔️</div>
                                    <div class="text-lg font-bold mb-1">Війна і мир</div>
                                    <div class="text-sm opacity-80">Лев Толстой</div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold text-sm mb-1 line-clamp-2">Війна і мир</h3>
                                <p class="text-gray-400 text-xs mb-2">Лев Толстой</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>1869</span>
                                    <span>Класика</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Book Card 4 -->
                    <div class="flex-shrink-0 w-48">
                        <div class="bg-gray-800 rounded-lg overflow-hidden hover:bg-gray-750 transition-colors duration-200">
                            <div class="w-full h-64 bg-gradient-to-br from-blue-900 to-indigo-700 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="text-2xl font-bold mb-2">👑</div>
                                    <div class="text-lg font-bold mb-1">Le Petit Prince</div>
                                    <div class="text-sm opacity-80">Saint-Exupéry</div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold text-sm mb-1 line-clamp-2">Маленький принц</h3>
                                <p class="text-gray-400 text-xs mb-2">Антуан де Сент-Екзюпері</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>1943</span>
                                    <span>Філософія</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Book Card 5 -->
                    <div class="flex-shrink-0 w-48">
                        <div class="bg-gray-800 rounded-lg overflow-hidden hover:bg-gray-750 transition-colors duration-200">
                            <div class="w-full h-64 bg-gradient-to-br from-orange-900 to-yellow-700 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="text-2xl font-bold mb-2">⚗️</div>
                                    <div class="text-lg font-bold mb-1">O Alquimista</div>
                                    <div class="text-sm opacity-80">Paulo Coelho</div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold text-sm mb-1 line-clamp-2">Алхімік</h3>
                                <p class="text-gray-400 text-xs mb-2">Пауло Коельйо</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>1988</span>
                                    <span>Роман</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Scroll Arrow -->
                <button class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-800 hover:bg-gray-700 text-white p-2 rounded-full transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </section>

        <!-- 2. Рекомендовані книги -->
        <section class="mb-12">
            <h2 class="text-3xl font-bold text-white mb-6">Рекомендовані книги</h2>
            <div class="relative">
                <div class="flex space-x-6 overflow-x-auto pb-4 scrollbar-hide">
                    <!-- Book Card 1 -->
                    <div class="flex-shrink-0 w-48">
                        <div class="bg-gray-800 rounded-lg overflow-hidden hover:bg-gray-750 transition-colors duration-200">
                            <div class="w-full h-64 bg-gradient-to-br from-green-900 to-emerald-700 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="text-2xl font-bold mb-2">🐕</div>
                                    <div class="text-lg font-bold mb-1">Собаче серце</div>
                                    <div class="text-sm opacity-80">Булгаков</div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold text-sm mb-1 line-clamp-2">Собаче серце</h3>
                                <p class="text-gray-400 text-xs mb-2">Михайло Булгаков</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>1925</span>
                                    <span>Класика</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Book Card 2 -->
                    <div class="flex-shrink-0 w-48">
                        <div class="bg-gray-800 rounded-lg overflow-hidden hover:bg-gray-750 transition-colors duration-200">
                            <div class="w-full h-64 bg-gradient-to-br from-gray-900 to-slate-700 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="text-2xl font-bold mb-2">👑</div>
                                    <div class="text-lg font-bold mb-1">Game of Thrones</div>
                                    <div class="text-sm opacity-80">G.R.R. Martin</div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold text-sm mb-1 line-clamp-2">Гра престолів</h3>
                                <p class="text-gray-400 text-xs mb-2">Джордж Р. Р. Мартін</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>1996</span>
                                    <span>Фентезі</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Book Card 3 -->
                    <div class="flex-shrink-0 w-48">
                        <div class="bg-gray-800 rounded-lg overflow-hidden hover:bg-gray-750 transition-colors duration-200">
                            <img src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=200&h=300&fit=crop&crop=center" 
                                 alt="Дюна" 
                                 class="w-full h-64 object-cover">
                            <div class="p-4">
                                <h3 class="text-white font-semibold text-sm mb-1 line-clamp-2">Дюна</h3>
                                <p class="text-gray-400 text-xs mb-2">Френк Герберт</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>2024</span>
                                    <span>Наукова фантастика</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Book Card 4 -->
                    <div class="flex-shrink-0 w-48">
                        <div class="bg-gray-800 rounded-lg overflow-hidden hover:bg-gray-750 transition-colors duration-200">
                            <img src="https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=200&h=300&fit=crop&crop=center" 
                                 alt="Володар перснів" 
                                 class="w-full h-64 object-cover">
                            <div class="p-4">
                                <h3 class="text-white font-semibold text-sm mb-1 line-clamp-2">Володар перснів</h3>
                                <p class="text-gray-400 text-xs mb-2">Дж. Р. Р. Толкін</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>2024</span>
                                    <span>Фентезі</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Scroll Arrow -->
                <button class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-800 hover:bg-gray-700 text-white p-2 rounded-full transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </section>

        <!-- 3. Рецензії -->
        <section class="mb-12">
            <h2 class="text-3xl font-bold text-white mb-6">Рецензії</h2>
            <div class="relative">
                <div class="flex space-x-6 overflow-x-auto pb-4 scrollbar-hide">
                    <!-- Review Card 1 -->
                    <div class="flex-shrink-0 w-80">
                        <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-750 transition-colors duration-200">
                            <div class="flex items-start space-x-3">
                                <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=40&h=40&fit=crop&crop=face" 
                                     alt="User" 
                                     class="w-10 h-10 rounded-full">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="text-white font-medium text-sm">Анна Коваленко</span>
                                        <span class="text-gray-400 text-xs">близько 2 годин тому</span>
                                    </div>
                                    <p class="text-gray-300 text-sm leading-relaxed mb-3">
                                        "1984" - це справжній шедевр антиутопії. Орвелл створив світ, який змушує задуматися про сучасне суспільство...
                                    </p>
                                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                                        <span class="bg-orange-500/20 text-orange-400 px-2 py-1 rounded">Аніме</span>
                                        <span class="bg-orange-500/20 text-orange-400 px-2 py-1 rounded">1984</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Review Card 2 -->
                    <div class="flex-shrink-0 w-80">
                        <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-750 transition-colors duration-200">
                            <div class="flex items-start space-x-3">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face" 
                                     alt="User" 
                                     class="w-10 h-10 rounded-full">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="text-white font-medium text-sm">Максим Петренко</span>
                                        <span class="text-gray-400 text-xs">близько 5 годин тому</span>
                                    </div>
                                    <p class="text-gray-300 text-sm leading-relaxed mb-3">
                                        "Гаррі Поттер" - класика дитячої літератури! Роулінг створила цілий світ, який захоплює як дітей, так і дорослих...
                                    </p>
                                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                                        <span class="bg-orange-500/20 text-orange-400 px-2 py-1 rounded">Фентезі</span>
                                        <span class="bg-orange-500/20 text-orange-400 px-2 py-1 rounded">Гаррі Поттер</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Review Card 3 -->
                    <div class="flex-shrink-0 w-80">
                        <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-750 transition-colors duration-200">
                            <div class="flex items-start space-x-3">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" 
                                     alt="User" 
                                     class="w-10 h-10 rounded-full">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="text-white font-medium text-sm">Олексій Іваненко</span>
                                        <span class="text-gray-400 text-xs">близько 1 дня тому</span>
                                    </div>
                                    <p class="text-gray-300 text-sm leading-relaxed mb-3">
                                        "Війна і мир" - епічний роман, який показує всю складність людської природи та історичних подій...
                                    </p>
                                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                                        <span class="bg-orange-500/20 text-orange-400 px-2 py-1 rounded">Класика</span>
                                        <span class="bg-orange-500/20 text-orange-400 px-2 py-1 rounded">Війна і мир</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Scroll Arrow -->
                <button class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-800 hover:bg-gray-700 text-white p-2 rounded-full transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </section>

        <!-- 4. Цитати -->
        <section class="mb-12">
            <h2 class="text-3xl font-bold text-white mb-6">Цитати</h2>
            <div class="relative">
                <div class="flex space-x-6 overflow-x-auto pb-4 scrollbar-hide">
                    <!-- Quote Card 1 -->
                    <div class="flex-shrink-0 w-80">
                        <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-750 transition-colors duration-200">
                            <div class="text-4xl text-orange-500/30 mb-4">"</div>
                            <p class="text-gray-300 text-lg italic leading-relaxed mb-4">
                                Книги - це кораблі думок, що плавають по хвилях часу і бережно несуть свій дорогоцінний вантаж від покоління до покоління.
                            </p>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-white font-semibold">Френсіс Бекон</p>
                                    <p class="text-gray-400 text-sm">Філософські есеї</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button class="text-orange-500 hover:text-orange-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                    <span class="text-gray-400 text-sm">67</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quote Card 2 -->
                    <div class="flex-shrink-0 w-80">
                        <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-750 transition-colors duration-200">
                            <div class="text-4xl text-orange-500/30 mb-4">"</div>
                            <p class="text-gray-300 text-lg italic leading-relaxed mb-4">
                                Читання - це розмова з наймудрішими людьми минулих століть.
                            </p>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-white font-semibold">Рене Декарт</p>
                                    <p class="text-gray-400 text-sm">Роздуми про першу філософію</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button class="text-orange-500 hover:text-orange-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                    <span class="text-gray-400 text-sm">89</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quote Card 3 -->
                    <div class="flex-shrink-0 w-80">
                        <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-750 transition-colors duration-200">
                            <div class="text-4xl text-orange-500/30 mb-4">"</div>
                            <p class="text-gray-300 text-lg italic leading-relaxed mb-4">
                                Книга - це мрія, яку ви тримаєте в руках.
                            </p>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-white font-semibold">Ніл Гейман</p>
                                    <p class="text-gray-400 text-sm">Американські боги</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button class="text-orange-500 hover:text-orange-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                    <span class="text-gray-400 text-sm">124</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Scroll Arrow -->
                <button class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-800 hover:bg-gray-700 text-white p-2 rounded-full transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </section>


@push('styles')
<style>
/* Hide scrollbar for horizontal scroll */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom hover state */
.hover\:bg-gray-750:hover {
    background-color: #374151;
}
</style>
@endpush
@endsection