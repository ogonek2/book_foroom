@extends('profile.layout')

@section('profile-content')
<div>
    <h2 class="text-2xl font-bold text-white mb-6">Мої обговорення</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Topics -->
        <div>
            <h3 class="text-xl font-semibold text-white mb-4">Теми ({{ $user->topics->count() }})</h3>
            
            @php
                $topics = $user->topics()->orderBy('created_at', 'desc')->paginate(10);
            @endphp
            
            @if($topics->count() > 0)
                <div class="space-y-3">
                    @foreach($topics as $topic)
                        <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition-colors">
                            <a href="{{ route('forum.topics.show', $topic) }}" 
                               class="text-white font-medium hover:text-orange-400 transition-colors">
                                {{ $topic->title }}
                            </a>
                            <p class="text-gray-400 text-sm mt-2">
                                💬 {{ $topic->posts_count ?? 0 }} відповідей • 
                                {{ $topic->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $topics->links() }}
                </div>
            @else
                <div class="bg-gray-800 rounded-lg p-8 text-center">
                    <p class="text-gray-400">Немає створених тем</p>
                </div>
            @endif
        </div>

        <!-- Posts -->
        <div>
            <h3 class="text-xl font-semibold text-white mb-4">Пости ({{ $user->posts->count() }})</h3>
            
            @php
                $posts = $user->posts()->with('topic')->orderBy('created_at', 'desc')->limit(10)->get();
            @endphp
            
            @if($posts->count() > 0)
                <div class="space-y-3">
                    @foreach($posts as $post)
                        <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition-colors">
                            <p class="text-gray-300 mb-2">{{ Str::limit($post->content, 100) }}</p>
                            <a href="{{ route('forum.topics.show', $post->topic_id) }}#post-{{ $post->id }}" 
                               class="text-orange-400 text-sm hover:text-orange-300 transition-colors">
                                Читати далі →
                            </a>
                            <p class="text-gray-500 text-xs mt-2">{{ $post->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-800 rounded-lg p-8 text-center">
                    <p class="text-gray-400">Немає постів</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
