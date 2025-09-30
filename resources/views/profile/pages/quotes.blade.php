@extends('profile.layout')

@section('profile-content')
<div>
    <h2 class="text-2xl font-bold text-white mb-6">Мої цитати</h2>
    
    @php
        $quotes = $user->quotes()->with('book')->orderBy('created_at', 'desc')->paginate(12);
    @endphp
    
    @if($quotes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($quotes as $quote)
                <div class="bg-gray-800 rounded-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition-shadow">
                    <blockquote class="text-lg text-gray-200 italic mb-4">
                        "{{ $quote->text }}"
                    </blockquote>
                    <div class="flex justify-between items-center">
                        <div>
                            <a href="{{ route('books.show', $quote->book->slug) }}" 
                               class="text-white font-medium hover:text-orange-400 transition-colors">
                                {{ $quote->book->title }}
                            </a>
                            <p class="text-gray-400 text-sm">{{ $quote->book->author }}</p>
                        </div>
                        <span class="text-gray-500 text-sm">{{ $quote->created_at->format('d.m.Y') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $quotes->links() }}
        </div>
    @else
        <div class="text-center py-12 bg-gray-800 rounded-lg">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-300 mb-2">Ще немає цитат</h3>
            <p class="text-gray-500">Зберігайте улюблені цитати з книг</p>
        </div>
    @endif
</div>
@endsection
