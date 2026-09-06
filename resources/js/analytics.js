import { router } from '@inertiajs/vue3';

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
    // send_page_view is off because Inertia navigates via pushState, not a
    // real page load — gtag's own automatic pageview/history listener doesn't
    // know about those transitions (and its Web Vitals reporting errors on
    // them), so page views are sent by hand from initSpaPageViews() below.
    window.gtag('config', GA_MEASUREMENT_ID, { send_page_view: false });

    const script = document.createElement('script');
    script.async = true;
    script.crossOrigin = 'anonymous';
    script.src = `https://www.googletagmanager.com/gtag/js?id=${GA_MEASUREMENT_ID}`;
    document.head.appendChild(script);

    // Not tracked here: Inertia's own 'navigate' event (below) always fires
    // once for the current page shortly after this runs, whether this is the
    // very first page load or a returning visitor with consent already
    // recorded — calling trackPageView() here too would double-count it.
}

export function trackPageView() {
    if (typeof window.gtag !== 'function') return;

    window.gtag('event', 'page_view', {
        page_path: window.location.pathname + window.location.search,
        page_location: window.location.href,
        page_title: document.title,
    });
}

// Registered once from app.js regardless of consent state — harmless before
// analytics loads, since trackPageView() no-ops until gtag exists.
export function initSpaPageViews() {
    router.on('navigate', () => {
        // Inertia's <Head> title update is debounced by its own head manager
        // and lands a beat after this event fires, so reading document.title
        // immediately here would send the *previous* page's title.
        setTimeout(trackPageView, 50);
    });
}
