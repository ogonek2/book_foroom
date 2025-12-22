/**
 * Universal share helper function
 * Uses Web Share API if available, otherwise copies link to clipboard and shows notification
 */
export async function shareContent(options = {}) {
    const {
        title = '',
        text = '',
        url = window.location.href
    } = options;

    // Check if Web Share API is available
    if (navigator.share) {
        try {
            await navigator.share({
                title: title,
                text: text,
                url: url
            });
            return { success: true, method: 'native' };
        } catch (error) {
            // User cancelled or error occurred
            if (error.name === 'AbortError') {
                return { success: false, method: 'native', cancelled: true };
            }
            // Fall through to clipboard fallback
        }
    }

    // Fallback: Copy to clipboard
    try {
        const shareText = text ? `${text}\n\n${url}` : url;
        await navigator.clipboard.writeText(shareText);
        
        // Show custom notification
        showShareNotification('Посилання скопійовано в буфер обміну!');
        
        return { success: true, method: 'clipboard' };
    } catch (error) {
        console.error('Failed to copy to clipboard:', error);
        // Final fallback - show URL in alert
        if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
            window.alertModalInstance.$refs.modal.alert(
                `Посилання: ${url}`,
                'Посилання',
                'info'
            );
        } else {
            alert(`Посилання: ${url}`);
        }
        return { success: false, method: 'alert' };
    }
}

/**
 * Show share notification using AlertModal
 */
function showShareNotification(message) {
    // Try to use AlertModal if available
    if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
        window.alertModalInstance.$refs.modal.alert(message, 'Успіх', 'success');
        return;
    }
    
    // Fallback: wait a bit for AlertModal to initialize
    setTimeout(() => {
        if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
            window.alertModalInstance.$refs.modal.alert(message, 'Успіх', 'success');
        } else {
            // Final fallback: create custom notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full bg-green-500 text-white';
            notification.textContent = message;
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
    }, 100);
}

