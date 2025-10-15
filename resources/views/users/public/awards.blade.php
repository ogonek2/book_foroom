@extends('users.public.main')

@section('profile-content')
    <div class="flex-1">
        <!-- Awards Header -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Нагороди</h2>
                <div class="flex items-center space-x-4">
                    @if($userAwards->count() > 0)
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-500">{{ $userAwards->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Всього нагород</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-500">{{ $userAwards->sum('points') }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Очок зароблено</div>
                        </div>
                    @endif
                </div>
            </div>
            
            @if($userAwards->count() > 0)
                <p class="text-gray-600 dark:text-gray-400">
                    Користувач отримав {{ $userAwards->count() }} нагород за свою активність на сайті.
                    @if($userAwards->sum('points') > 0)
                        Загалом зароблено {{ $userAwards->sum('points') }} очок.
                    @endif
                </p>
            @else
                <p class="text-gray-600 dark:text-gray-400">
                    Поки що немає нагород. Нагороди присуджуються за активність на сайті.
                </p>
            @endif
        </div>

        @if($userAwards->count() > 0)
            <!-- Awards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($userAwards as $award)
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-2xl p-6 border border-yellow-200 dark:border-yellow-700/30 hover:shadow-lg transition-all duration-300 group">
                        <div class="flex items-start space-x-4">
                            @if($award->image)
                                <img src="{{ $award->image }}" alt="{{ $award->name }}" class="w-16 h-16 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-xl flex-shrink-0" style="background-color: {{ $award->color }}">
                                    {{ substr($award->name, 0, 1) }}
                                </div>
                            @endif
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-colors">
                                    {{ $award->name }}
                                </h3>
                                
                                @if($award->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 leading-relaxed">
                                        {{ $award->description }}
                                    </p>
                                @endif
                                
                                <div class="flex items-center justify-between">
                                    @if($award->points > 0)
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">
                                                +{{ $award->points }} очок
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                        Отримано
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="text-gray-400 mb-6">
                    <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Поки що немає нагород</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                    Нагороди присуджуються за активність на сайті: написання рецензій, додавання цитат, створення обговорень та інші корисні дії.
                </p>
            </div>
        @endif
    </div>
@endsection
