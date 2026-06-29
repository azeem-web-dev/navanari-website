import Alpine from 'alpinejs';

/* ------------------------------------------------------------------ *
 *  Navanari — front-end interactions (animations + wishlist)
 * ------------------------------------------------------------------ */

const WISHLIST_KEY = 'navanari_wishlist';

const readWishlist = () => {
    try {
        return JSON.parse(localStorage.getItem(WISHLIST_KEY)) || [];
    } catch (e) {
        return [];
    }
};

/* Global wishlist store — persists in localStorage, no login required. */
Alpine.store('wishlist', {
    items: readWishlist(),

    init() {
        this.sync();
    },
    sync() {
        localStorage.setItem(WISHLIST_KEY, JSON.stringify(this.items));
    },
    has(id) {
        return this.items.some((p) => String(p.id) === String(id));
    },
    toggle(product) {
        if (this.has(product.id)) {
            this.items = this.items.filter((p) => String(p.id) !== String(product.id));
        } else {
            this.items.push(product);
        }
        this.sync();
    },
    remove(id) {
        this.items = this.items.filter((p) => String(p.id) !== String(id));
        this.sync();
    },
    get count() {
        return this.items.length;
    },
});

window.Alpine = Alpine;
Alpine.start();

/* ---- Scroll reveal ---- */
const initReveal = () => {
    const els = document.querySelectorAll('[data-reveal]');
    if (!('IntersectionObserver' in window) || !els.length) {
        els.forEach((el) => el.classList.add('is-visible'));
        return;
    }
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const delay = entry.target.dataset.revealDelay || 0;
                    setTimeout(() => entry.target.classList.add('is-visible'), delay);
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );
    els.forEach((el) => observer.observe(el));
};

/* ---- Animated number counters ---- */
const initCounters = () => {
    const counters = document.querySelectorAll('[data-counter]');
    if (!counters.length) return;
    const animate = (el) => {
        const target = parseFloat(el.dataset.counter) || 0;
        const dur = 1600;
        const start = performance.now();
        const step = (now) => {
            const p = Math.min((now - start) / dur, 1);
            const eased = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.floor(eased * target).toLocaleString();
            if (p < 1) requestAnimationFrame(step);
            else el.textContent = target.toLocaleString() + (el.dataset.suffix || '');
        };
        requestAnimationFrame(step);
    };
    const obs = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) {
                animate(e.target);
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach((c) => obs.observe(c));
};

/* ---- Shrinking / blurred header on scroll ---- */
const initHeader = () => {
    const header = document.querySelector('[data-site-header]');
    if (!header) return;
    const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 24);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
};

function boot() {
    // The inline <head> failsafe may have scheduled a reveal-all timer; cancel it
    // because we're about to drive the reveals properly.
    if (window.__revealFallback) {
        clearTimeout(window.__revealFallback);
        window.__revealFallback = null;
    }
    initReveal();
    initCounters();
    initHeader();
}

// This is a deferred module, so DOMContentLoaded has usually already fired by the
// time it runs — run immediately when the DOM is ready, otherwise wait for it.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
