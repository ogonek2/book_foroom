@extends('layouts.app')

@section('title', 'Сповіщення')

@section('main')
<div id="notifications-page-app">
    <notifications-page @notification="showNotification"></notifications-page>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Vue({
        el: '#notifications-page-app',
        methods: {
            showNotification(data) {
                // Простая система уведомлений
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full ${
                    data.type === 'success' ? 'bg-green-500 text-white' : 
                    data.type === 'error' ? 'bg-red-500 text-white' : 
                    'bg-blue-500 text-white'
                }`;
                notification.textContent = data.message;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 100);
                
                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 3000);
            }
        }
    });
});
</script>
@endpush
@endsection