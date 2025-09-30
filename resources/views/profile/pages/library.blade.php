@extends('profile.layout')

@section('profile-content')
<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Моя бібліотека</h2>
        <div class="flex space-x-2">
            <a href="{{ route('profile.library', $user->username) }}?filter=all" 
               class="px-4 py-2 rounded-lg {{ (!request('filter') || request('filter') === 'all') ? 'bg-orange-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }} transition-colors">
                Всі
            </a>
            <a href="{{ route('profile.library', $user->username) }}?filter=reading" 
               class="px-4 py-2 rounded-lg {{ request('filter') === 'reading' ? 'bg-orange-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }} transition-colors">
                Читаю
            </a>
            <a href="{{ route('profile.library', $user->username) }}?filter=read" 
               class="px-4 py-2 rounded-lg {{ request('filter') === 'read' ? 'bg-orange-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }} transition-colors">
                Прочитано
            </a>
            <a href="{{ route('profile.library', $user->username) }}?filter=want_to_read" 
               class="px-4 py-2 rounded-lg {{ request('filter') === 'want_to_read' ? 'bg-orange-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }} transition-colors">
                Буду читати
            </a>
        </div>
    </div>

    @php
        $filter = request('filter', 'all');
        $query = $user->readingStatuses()->with('book');
        
        if ($filter !== 'all') {
            $query->where('status', $filter);
        }
        
        $libraryBooks = $query->orderBy('updated_at', 'desc')->paginate(12);
    @endphp

    @if($libraryBooks->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-2">
            @foreach($libraryBooks as $readingStatus)
                <div class="bg-gray-800 rounded-lg p-2 hover:shadow-xl transition-shadow flex align-center space-x-2">
                    <div>
                        @if($readingStatus->book->cover_image)
                            <img src="{{ $readingStatus->book->cover_image }}" 
                                alt="{{ $readingStatus->book->title }}" 
                                class="w-full h-full object-cover rounded-lg" style="aspect-ratio: 3/4; max-width: 100px;">
                        @else
                            <div class="w-full h-48 bg-gray-700 rounded-lg flex items-center justify-center">
                                <span class="text-gray-500">Без обкладинки</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col justify-between">
                        <div>
                            <h3 class="text-white font-semibold mb-2">{{ Str::limit($readingStatus->book->title, 40) }}</h3>
                            <p class="text-gray-400 text-sm mb-3">{{ $readingStatus->book->author }}</p>
                        </div>
                        <div class="mb-2">
                            <span class="text-xs px-2 py-1 rounded-full 
                                {{ $readingStatus->status === 'reading' ? 'bg-blue-500 text-white' : ($readingStatus->status === 'read' ? 'bg-green-500 text-white' : 'bg-yellow-500 text-white') }}">
                                @if($readingStatus->status === 'reading')
                                    Читаю
                                @elseif($readingStatus->status === 'read')
                                    Прочитано
                                @else
                                    Буду читати
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $libraryBooks->links() }}
        </div>
    @else
        <div class="text-center py-12 bg-gray-800 rounded-lg">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-300">Бібліотека порожня</h3>
            <p class="text-gray-500 mt-2">Додайте книги до своєї бібліотеки</p>
        </div>
    @endif
</div>
@endsection
