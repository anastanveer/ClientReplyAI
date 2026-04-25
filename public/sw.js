const CACHE_NAME = 'clientreplyai-v1';

// Only cache static shell assets — never API or Livewire requests
const PRECACHE_URLS = [
    '/',
    '/offline',
];

// Install: skip waiting to activate immediately
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

// Activate: clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

// Fetch: network-first for all requests
// Only serve offline fallback for navigation requests when network fails
self.addEventListener('fetch', (event) => {
    // Skip non-GET, Livewire wire requests, and cross-origin requests
    if (
        event.request.method !== 'GET' ||
        event.request.url.includes('livewire') ||
        event.request.url.includes('__laravel') ||
        !event.request.url.startsWith(self.location.origin)
    ) {
        return;
    }

    // Navigation requests: network-first, offline fallback
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() =>
                caches.match('/offline').then((cached) => cached || new Response(
                    '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Offline — ClientReplyAI</title><style>body{font-family:system-ui,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#fafaf9;color:#0f172a;text-align:center;padding:2rem}.box{max-width:360px}.title{font-size:1.5rem;font-weight:700;margin-bottom:.75rem}.sub{color:#57534e;font-size:.9rem;line-height:1.6}.btn{display:inline-block;margin-top:1.5rem;padding:.75rem 1.75rem;background:#0f172a;color:#fff;border-radius:9999px;text-decoration:none;font-size:.875rem;font-weight:600}</style></head><body><div class="box"><div class="title">You\'re offline</div><p class="sub">ClientReplyAI needs an internet connection to generate replies. Please check your connection and try again.</p><a href="/" class="btn">Retry</a></div></body></html>',
                    { headers: { 'Content-Type': 'text/html' } }
                ))
            )
        );
        return;
    }

    // Static assets (CSS, JS, fonts, images): cache-first
    if (
        event.request.destination === 'style' ||
        event.request.destination === 'script' ||
        event.request.destination === 'image' ||
        event.request.destination === 'font'
    ) {
        event.respondWith(
            caches.match(event.request).then((cached) => {
                if (cached) return cached;
                return fetch(event.request).then((response) => {
                    if (!response || response.status !== 200) return response;
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                    return response;
                });
            })
        );
    }
});
