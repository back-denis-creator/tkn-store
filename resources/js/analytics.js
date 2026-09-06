// Loaded only after cookie consent (see CookieConsent.vue) — gtag.js sets
// non-essential analytics cookies, so it must never run before the visitor
// has actually accepted.
const GA_MEASUREMENT_ID = import.meta.env.VITE_GA_MEASUREMENT_ID;

let loaded = false;

export function loadGoogleAnalytics() {
    if (loaded || !GA_MEASUREMENT_ID || typeof window === 'undefined') return;
    loaded = true;

    window.dataLayer = window.dataLayer || [];
    window.gtag = function gtag() {
        window.dataLayer.push(arguments);
    };
    window.gtag('js', new Date());
    window.gtag('config', GA_MEASUREMENT_ID);

    const script = document.createElement('script');
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${GA_MEASUREMENT_ID}`;
    document.head.appendChild(script);
}
