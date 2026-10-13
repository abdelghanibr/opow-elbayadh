/* ============================================================
   OPOW El Bayadh — PWA Service Worker
   Stratégie : réseau d'abord (la vérification QR exige le réseau),
   avec cache de secours pour la navigation hors-ligne.
   ============================================================ */
const VERSION = 'opow-verify-qr-v1';
const APP_SHELL = [
  '/manifest.json',
  '/pwa/icons/icon-192.png',
  '/pwa/icons/icon-512.png',
  '/pwa/icons/icon-maskable-512.png'
];
const OFFLINE_URL = '/tickets/verify-qr';

/* --- Installation : mise en cache du shell --- */
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(VERSION).then((cache) => cache.addAll(APP_SHELL))
  );
  self.skipWaiting();
});

/* --- Activation : nettoyage des anciens caches --- */
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== VERSION).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

/* --- Récupération : réseau d'abord --- */
self.addEventListener('fetch', (event) => {
  const request = event.request;

  // Ne pas intercepter les requêtes non-GET (POST check-qr passe toujours par le réseau)
  if (request.method !== 'GET') {
    return;
  }

  // Ne pas intercepter les appels cross-origin (cdns, caméra, etc.)
  const url = new URL(request.url);
  if (url.origin !== self.location.origin) {
    return;
  }

  // Stratégie : réseau d'abord, sinon cache, sinon fallback hors-ligne
  event.respondWith(
    fetch(request)
      .then((response) => {
        // Mise en cache des réponses réussies
        if (response && response.status === 200 && response.type === 'basic') {
          const copy = response.clone();
          caches.open(VERSION).then((cache) => cache.put(request, copy));
        }
        return response;
      })
      .catch(() =>
        caches.match(request).then((cached) => {
          if (cached) {
            return cached;
          }
          // Fallback hors-ligne : page de vérification QR (pour la navigation)
          if (request.mode === 'navigate') {
            return caches.match(OFFLINE_URL).then((offline) => offline || Response.error());
          }
          return Response.error();
        })
      )
  );
});
