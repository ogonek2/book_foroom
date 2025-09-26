@props(['user', 'timestamp' => null, 'showGuest' => false])

<div class="user-mini-header">
    <div class="user-avatar">
        @if($showGuest || !$user)
            <!-- Guest avatar -->
            <div class="avatar-guest">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
            </div>
        @else
            <!-- User avatar -->
            @if($user->avatar)
                <img src="{{ Storage::url($user->avatar) }}" 
                     alt="{{ $user->name }}" 
                     class="avatar-image"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="avatar-fallback" style="display: none;">
                    {{ substr($user->name, 0, 1) }}
                </div>
            @else
                <div class="avatar-fallback">
                    {{ substr($user->name, 0, 1) }}
                </div>
            @endif
        @endif
    </div>
    
    <div class="user-info">
        <div class="user-name">
            @if($showGuest || !$user)
                {{ $user->name ?? 'Гість' }}
                @if($showGuest)
                    <span class="guest-badge">Гість</span>
                @endif
            @else
                {{ $user->name }}
            @endif
        </div>
        @if($timestamp)
            <div class="user-timestamp">{{ $timestamp }}</div>
        @endif
    </div>
</div>

