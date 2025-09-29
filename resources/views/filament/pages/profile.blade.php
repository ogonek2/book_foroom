<x-filament-panels::page>
    <div class="space-y-8">
        <!-- YouTube-style Header -->
        <div class="relative">
            <!-- Cover Image with Blur Effect -->
            <div class="relative h-48 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 overflow-hidden rounded-t-2xl">
                @if(auth()->user()->avatar)
                    <div class="absolute inset-0">
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" 
                             alt="{{ auth()->user()->name }}" 
                             class="w-full h-full object-cover filter blur-sm scale-110">
                        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                    </div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500"></div>
                @endif
                
                <!-- Overlay Pattern -->
                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                
                <!-- Profile Avatar - YouTube Style -->
                <div class="absolute bottom-0 left-0 transform translate-y-1/2 ml-6">
                    <div class="relative">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-2xl">
                        @else
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center border-4 border-white shadow-2xl">
                                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Online Status Indicator -->
                        <div class="absolute bottom-1 right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white"></div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="absolute bottom-4 right-6 flex space-x-3">
                    <a href="{{ route('profile.edit') }}" 
                       class="bg-white hover:bg-gray-100 text-gray-900 px-4 py-2 rounded-full font-medium shadow-lg transition-all duration-200 hover:shadow-xl">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Редактировать
                    </a>
                </div>
            </div>
            
            <!-- Profile Info Section -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-b-2xl -mt-4 relative z-10">
                <div class="px-6 pt-12 pb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                {{ auth()->user()->name }}
                            </h1>
                            <p class="text-lg text-gray-600 dark:text-gray-400 mb-1">@{{ auth()->user()->username }}</p>
                            <p class="text-gray-500 dark:text-gray-500 mb-4">{{ auth()->user()->email }}</p>
                            
                            @if(auth()->user()->rating)
                                <div class="flex items-center mb-4">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= floor(auth()->user()->rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-gray-700 dark:text-gray-300 font-medium">{{ number_format(auth()->user()->rating, 1) }}</span>
                                </div>
                            @endif
                            
                            @if(auth()->user()->bio)
                                <div class="mt-4">
                                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ auth()->user()->bio }}</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Stats Cards -->
                        <div class="mt-6 md:mt-0 md:ml-8">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                                    <div class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ auth()->user()->topics->count() }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Темы</div>
                                </div>
                                <div class="text-center bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                                    <div class="text-xl font-bold text-green-600 dark:text-green-400">{{ auth()->user()->posts->count() }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Посты</div>
                                </div>
                                <div class="text-center bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                                    <div class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ auth()->user()->reviews->count() }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Рецензии</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
