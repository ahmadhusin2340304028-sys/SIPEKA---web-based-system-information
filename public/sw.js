// Service worker untuk web push notification.
// Didaftarkan oleh resources/js/push.js. Wajib berada di /public (root domain)
// supaya scope-nya mencakup seluruh aplikasi.

self.addEventListener('push', function (event) {
    if (!event.data) return;

    const payload = event.data.json();

    const title = payload.title || 'SIPEKA';
    const options = {
        body: payload.body,
        icon: payload.icon || '/assets/image/dinsos_logo.png',
        badge: '/assets/image/dinsos_logo.png',
        data: payload.data || {},
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = (event.notification.data && event.notification.data.url) || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(function (windowClients) {
            for (const client of windowClients) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});
