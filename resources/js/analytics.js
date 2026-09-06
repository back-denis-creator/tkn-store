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
    // Without this, the browser reports errors thrown inside this
    // cross-origin script to our own 'error' listener as a sanitized
    // "Script error." with no message/stack — exactly the detail
    // suppressGtagWebVitalsBug() needs to identify it. googletagmanager.com
    // serves gtag.js with permissive CORS headers, so this doesn't block it.
    script.crossOrigin = 'anonymous';
    script.src = `https://www.googletagmanager.com/gtag/js?id=${GA_MEASUREMENT_ID}`;
    document.head.appendChild(script);

    // Not tracked here: Inertia's own 'navigate' event (below) always fires
    // once for the current page shortly after this runs, whether this is the
    // very first page load or a returning visitor with consent already
    // recorded — calling trackPageView() here too would double-count it.

    suppressGtagWebVitalsBug();
}

// gtag.js bundles Google's own web-vitals library to auto-report Core Web
// Vitals (LCP/CLS/INP). That library assumes a real full-page navigation and
// gets confused by Inertia's pushState-only transitions — Performance API
// entries from the previous "page" are still around, so its continuous
// LCP/CLS reporting callback (reportAllChanges) sometimes reads .startTime
// off an entry that no longer applies and throws. It's caught inside gtag.js's
// own setTimeout, so it never touches our app's execution — this exists
// purely to keep that specific, known-harmless noise out of the console
// (and any error-monitoring tool that's watching it).
//
// Matched on message text alone, not the stack: the throw happens inside a
// dynamically-evaluated sub-script gtag.js creates internally (shows in
// DevTools as "VM<n>", no real URL of its own) — our <script> tag's
// crossorigin attribute only governs errors from that tag's own top-level
// code, not this inner blob, so event.error/stack stay unavailable here
// regardless. The message text is the only reliably-present detail; it's
// specific enough (confirmed nowhere else in this codebase) that matching on
// it alone is safe.
let suppressorInstalled = false;
function suppressGtagWebVitalsBug() {
    if (suppressorInstalled || typeof window === 'undefined') return;
    suppressorInstalled = true;

    window.addEventListener('error', (event) => {
        if (event.message?.includes("reading 'startTime'")) {
            event.preventDefault();
        }
    });
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
