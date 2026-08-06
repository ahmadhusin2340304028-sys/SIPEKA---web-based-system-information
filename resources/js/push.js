/**
 * Mendaftarkan service worker + meminta izin notifikasi + subscribe ke Web Push API,
 * lalu mengirim subscription ke server (POST /push/subscribe).
 * Dipanggil otomatis oleh layouts/app.blade.php untuk user yang sudah login.
 */
(function () {
    const VAPID_PUBLIC_KEY = document.querySelector('meta[name="vapid-public-key"]')?.content;

    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !VAPID_PUBLIC_KEY) {
        return;
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        return Uint8Array.from([...rawData].map((c) => c.charCodeAt(0)));
    }

    async function subscribe() {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js');

            const permission = await Notification.requestPermission();
            if (permission !== 'granted') return;

            let subscription = await registration.pushManager.getSubscription();

            if (!subscription) {
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
                });
            }

            await fetch('/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    Accept: 'application/json',
                },
                body: JSON.stringify(subscription),
            });
        } catch (err) {
            console.warn('Web push subscription gagal:', err);
        }
    }

    subscribe();
})();
