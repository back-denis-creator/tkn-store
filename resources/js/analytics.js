import { router } from '@inertiajs/vue3';

// Loaded only after cookie consent (see CookieConsent.vue) — gtag.js sets
// non-essential analytics cookies, so it must never run before the visitor
// has actually accepted.
const GA_MEASUREMENT_ID = import.meta.env.VITE_GA_MEASUREMENT_ID;

let loaded = false;

export function loadGoogleAnalytics() {
    if (loaded || !GA_MEASUREMENT_ID || typeof window === 'undefined') return;
    loaded = true;

    // Must run before gtag.js's own script executes — see the big comment on
    // guardAgainstWebVitalsBug() below for why this exists at all.
    guardAgainstWebVitalsBug();

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

// gtag.js bundles Google's own web-vitals library to auto-report Core Web
// Vitals (LCP/CLS/INP). That library assumes a real full-page navigation and
// gets confused by Inertia's pushState-only transitions — Performance API
// entries from the previous "page" are still around, so its continuous
// LCP/CLS reporting callback (reportAllChanges) sometimes reads .startTime
// off an entry that no longer applies and throws.
//
// Tried and confirmed NOT to work: window.addEventListener('error'/
// 'unhandledrejection', ...) with event.preventDefault(). Verified live on
// production with temporary debug logging — neither listener fires at all
// for this error, meaning it never reaches window's normal uncaught-error
// pipeline in the first place (most likely reported by Chromium directly
// from its native scheduling/observer callback invocation, bypassing
// window.onerror entirely — a known quirk for some browser callback APIs).
// So the only place left to intercept it is the callback itself, before the
// browser ever gets to invoke it and report the throw.
//
// setTimeout is wrapped because the real stack trace's outermost frame is
// "n.timeout" (some internal scheduler that bottoms out in a real timer).
// PerformanceObserver is wrapped too since reportAllChanges is Web Vitals'
// own observer-driven reporting callback — whichever of the two actually
// invokes the throwing code, both are covered.
let guardInstalled = false;
function guardAgainstWebVitalsBug() {
    if (guardInstalled || typeof window === 'undefined') return;
    guardInstalled = true;

    const isKnownBug = (error) => error?.message?.includes("reading 'startTime'");

    const nativeSetTimeout = window.setTimeout;
    window.setTimeout = function guardedSetTimeout(handler, timeout, ...args) {
        if (typeof handler !== 'function') return nativeSetTimeout(handler, timeout, ...args);

        return nativeSetTimeout(function guardedTimeoutCallback(...callbackArgs) {
            try {
                return handler.apply(this, callbackArgs);
            } catch (error) {
                if (!isKnownBug(error)) throw error;
            }
        }, timeout, ...args);
    };

    if (typeof window.PerformanceObserver === 'function') {
        const NativePerformanceObserver = window.PerformanceObserver;
        function GuardedPerformanceObserver(callback) {
            return new NativePerformanceObserver(function guardedObserverCallback(...callbackArgs) {
                try {
                    return callback.apply(this, callbackArgs);
                } catch (error) {
                    if (!isKnownBug(error)) throw error;
                }
            });
        }
        GuardedPerformanceObserver.prototype = NativePerformanceObserver.prototype;
        GuardedPerformanceObserver.supportedEntryTypes = NativePerformanceObserver.supportedEntryTypes;
        window.PerformanceObserver = GuardedPerformanceObserver;
    }
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
