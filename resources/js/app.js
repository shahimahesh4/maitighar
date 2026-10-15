const showToast = (input = {}) => {
    const payload = Array.isArray(input) ? (input[0] ?? {}) : input;
    const message = payload.message ?? payload.text;
    if (!message) return;

    let region = document.querySelector('[data-kba-toast-region]');
    if (!region) {
        region = document.createElement('div');
        region.className = 'kba-toast-region';
        region.dataset.kbaToastRegion = '';
        region.setAttribute('aria-label', 'Notifications');
        document.body.appendChild(region);
    }

    const type = ['success', 'error', 'warning', 'info'].includes(payload.type) ? payload.type : 'info';
    const toast = document.createElement('div');
    toast.className = `kba-toast is-${type}`;
    toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
    toast.innerHTML = `<span class="kba-toast-icon" aria-hidden="true"></span><span class="kba-toast-copy"></span><button type="button" aria-label="Close notification">&times;</button>`;

    const copy = toast.querySelector('.kba-toast-copy');
    if (payload.title) {
        const title = document.createElement('strong');
        title.textContent = payload.title;
        copy.appendChild(title);
    }
    const text = document.createElement('span');
    text.textContent = message;
    copy.appendChild(text);

    let timer;
    const dismiss = () => {
        window.clearTimeout(timer);
        toast.classList.add('is-leaving');
        window.setTimeout(() => toast.remove(), 180);
    };
    toast.querySelector('button').addEventListener('click', dismiss);
    region.appendChild(toast);
    window.requestAnimationFrame(() => toast.classList.add('is-visible'));
    timer = window.setTimeout(dismiss, Math.max(1800, Number(payload.timer ?? payload.options?.timer) || 3500));
};

window.addEventListener('alert', (event) => showToast(event.detail));
window.addEventListener('kba-toast', (event) => showToast(event.detail));

let lastScrollY = window.scrollY;
let scrollTicking = false;

const mobileShellElements = () => ({
    header: document.querySelector('[data-kba-mobile-header]'),
    bottom: document.querySelector('[data-kba-mobile-bottom]'),
    sidebar: document.querySelector('[data-kba-mobile-sidebar]'),
    backdrop: document.querySelector('[data-kba-menu-backdrop]'),
    opener: document.querySelector('[data-kba-menu-open]'),
});

const setMobileMenu = (open, restoreFocus = true) => {
    const { header, bottom, sidebar, backdrop, opener } = mobileShellElements();

    if (!sidebar || !backdrop) return;

    const wasOpen = sidebar.classList.contains('is-open');

    sidebar.classList.toggle('is-open', open);
    backdrop.classList.toggle('is-open', open);
    sidebar.setAttribute('aria-hidden', String(!open));
    sidebar.inert = !open;
    backdrop.setAttribute('aria-hidden', String(!open));
    opener?.setAttribute('aria-expanded', String(open));
    document.body.classList.toggle('kba-mobile-menu-open', open);

    if (open) {
        header?.classList.remove('is-scroll-hidden');
        bottom?.classList.remove('is-scroll-hidden');
        window.setTimeout(() => sidebar.querySelector('[data-kba-menu-close]')?.focus(), 320);
    } else if (wasOpen && restoreFocus) {
        opener?.focus({ preventScroll: true });
    }
};

const resetMobileChrome = () => {
    const { header, bottom } = mobileShellElements();
    header?.classList.remove('is-scroll-hidden');
    bottom?.classList.remove('is-scroll-hidden');
    lastScrollY = window.scrollY;
};

const initializeGalleries = () => {
    document.querySelectorAll('[data-kba-gallery]').forEach((gallery) => {
        if (gallery.dataset.lightboxBound === 'true') return;
        gallery.dataset.lightboxBound = 'true';

        const lightbox = gallery.querySelector('[data-kba-lightbox]');
        const data = gallery.querySelector('[data-kba-gallery-images]');
        if (!lightbox || !data) return;

        let images = [];
        try { images = JSON.parse(data.textContent); } catch { return; }
        if (!images.length) return;

        const image = lightbox.querySelector('[data-kba-lightbox-image]');
        const caption = lightbox.querySelector('[data-kba-lightbox-caption]');
        const counter = lightbox.querySelector('[data-kba-lightbox-counter]');
        const closeButton = lightbox.querySelector('[data-kba-lightbox-close]:not(.kba-lightbox-backdrop)');
        let current = 0;
        let returnFocus = null;
        let touchStartX = null;

        const render = () => {
            const item = images[current];
            image.src = item.url;
            image.alt = item.alt;
            caption.textContent = item.alt;
            counter.textContent = `${current + 1} / ${images.length}`;
        };
        const open = (index, trigger) => {
            current = index;
            returnFocus = trigger;
            render();
            lightbox.classList.add('is-open');
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.classList.add('kba-lightbox-open');
            window.setTimeout(() => closeButton?.focus(), 30);
        };
        const close = () => {
            lightbox.classList.remove('is-open');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('kba-lightbox-open');
            image.removeAttribute('src');
            returnFocus?.focus({ preventScroll: true });
        };
        const move = (amount) => { current = (current + amount + images.length) % images.length; render(); };

        gallery.querySelectorAll('[data-kba-lightbox-open]').forEach((trigger) => trigger.addEventListener('click', () => open(Number(trigger.dataset.index), trigger)));
        lightbox.querySelectorAll('[data-kba-lightbox-close]').forEach((button) => button.addEventListener('click', close));
        lightbox.querySelector('[data-kba-lightbox-prev]')?.addEventListener('click', () => move(-1));
        lightbox.querySelector('[data-kba-lightbox-next]')?.addEventListener('click', () => move(1));
        lightbox.addEventListener('touchstart', (event) => { touchStartX = event.changedTouches[0]?.clientX ?? null; }, { passive: true });
        lightbox.addEventListener('touchend', (event) => {
            if (touchStartX === null) return;
            const distance = (event.changedTouches[0]?.clientX ?? touchStartX) - touchStartX;
            if (Math.abs(distance) > 55) move(distance > 0 ? -1 : 1);
            touchStartX = null;
        }, { passive: true });
        lightbox.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') close();
            if (event.key === 'ArrowLeft') move(-1);
            if (event.key === 'ArrowRight') move(1);
            if (event.key === 'Tab') {
                const controls = [...lightbox.querySelectorAll('button')];
                const first = controls[0];
                const last = controls[controls.length - 1];
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
                if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
            }
        });
    });
};

const initializeHomeSliders = () => {
    document.querySelectorAll('[data-kba-slider]').forEach((slider) => {
        if (slider.dataset.sliderBound === 'true') return;
        slider.dataset.sliderBound = 'true';

        const slides = [...slider.querySelectorAll('[data-kba-slide]')];
        const dots = [...slider.querySelectorAll('[data-kba-slider-dot]')];
        if (slides.length < 2) return;

        let current = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
        let timer = null;
        let paused = false;
        let touchStartX = null;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        const duration = () => Math.max(3000, Number(slides[current]?.dataset.duration) || 6000);
        const stop = () => { if (timer) window.clearTimeout(timer); timer = null; };
        const schedule = () => {
            stop();
            if (paused || reduceMotion || document.hidden || !slider.isConnected) return;
            timer = window.setTimeout(() => show(current + 1), duration());
        };
        const show = (index) => {
            current = (index + slides.length) % slides.length;
            slides.forEach((slide, slideIndex) => {
                const active = slideIndex === current;
                slide.classList.toggle('is-active', active);
                slide.classList.toggle('is-animating', active);
                slide.setAttribute('aria-hidden', String(!active));
            });
            dots.forEach((dot, dotIndex) => {
                const active = dotIndex === current;
                dot.classList.toggle('is-active', active);
                dot.setAttribute('aria-current', String(active));
            });
            schedule();
        };

        slider.querySelector('[data-kba-slider-prev]')?.addEventListener('click', () => show(current - 1));
        slider.querySelector('[data-kba-slider-next]')?.addEventListener('click', () => show(current + 1));
        dots.forEach((dot) => dot.addEventListener('click', () => show(Number(dot.dataset.kbaSliderDot))));
        slider.addEventListener('mouseenter', () => { paused = true; stop(); });
        slider.addEventListener('mouseleave', () => { paused = false; schedule(); });
        slider.addEventListener('focusin', () => { paused = true; stop(); });
        slider.addEventListener('focusout', () => { paused = false; schedule(); });
        slider.addEventListener('touchstart', (event) => { touchStartX = event.changedTouches[0]?.clientX ?? null; }, { passive: true });
        slider.addEventListener('touchend', (event) => {
            if (touchStartX === null) return;
            const distance = (event.changedTouches[0]?.clientX ?? touchStartX) - touchStartX;
            if (Math.abs(distance) > 45) show(current + (distance < 0 ? 1 : -1));
            touchStartX = null;
        }, { passive: true });
        document.addEventListener('visibilitychange', schedule);
        schedule();
    });
};

const initializeVideoGalleries = () => {
    document.querySelectorAll('[data-kba-video-gallery]').forEach((gallery) => {
        if (gallery.dataset.videoGalleryBound === 'true') return;
        gallery.dataset.videoGalleryBound = 'true';
        const lightbox = gallery.querySelector('[data-kba-video-lightbox]');
        const data = gallery.querySelector('[data-kba-video-items]');
        if (!lightbox || !data) return;
        let videos = [];
        try { videos = JSON.parse(data.textContent); } catch { return; }
        if (!videos.length) return;
        const player = lightbox.querySelector('[data-kba-video-player]');
        const caption = lightbox.querySelector('[data-kba-video-caption]');
        const counter = lightbox.querySelector('[data-kba-video-counter]');
        const closeButton = lightbox.querySelector('[data-kba-video-close]:not(.kba-lightbox-backdrop)');
        let current = 0;
        let returnFocus = null;
        let touchStartX = null;
        const render = () => {
            const item = videos[current];
            player.replaceChildren();
            const media = document.createElement(item.type === 'youtube' ? 'iframe' : 'video');
            media.src = item.source_url;
            media.title = item.title;
            if (item.type === 'youtube') {
                media.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
                media.allowFullscreen = true;
            } else {
                media.controls = true;
                media.autoplay = true;
                media.playsInline = true;
            }
            player.appendChild(media);
            caption.textContent = item.title;
            counter.textContent = `${current + 1} / ${videos.length}`;
        };
        const open = (index, trigger) => { current = index; returnFocus = trigger; render(); lightbox.classList.add('is-open'); lightbox.setAttribute('aria-hidden', 'false'); document.body.classList.add('kba-lightbox-open'); window.setTimeout(() => closeButton?.focus(), 30); };
        const close = () => { lightbox.classList.remove('is-open'); lightbox.setAttribute('aria-hidden', 'true'); document.body.classList.remove('kba-lightbox-open'); player.replaceChildren(); returnFocus?.focus({ preventScroll: true }); };
        const move = (amount) => { current = (current + amount + videos.length) % videos.length; render(); };
        gallery.querySelectorAll('[data-kba-video-open]').forEach((trigger) => trigger.addEventListener('click', () => open(Number(trigger.dataset.index), trigger)));
        lightbox.querySelectorAll('[data-kba-video-close]').forEach((button) => button.addEventListener('click', close));
        lightbox.querySelector('[data-kba-video-prev]')?.addEventListener('click', () => move(-1));
        lightbox.querySelector('[data-kba-video-next]')?.addEventListener('click', () => move(1));
        lightbox.addEventListener('touchstart', (event) => { touchStartX = event.changedTouches[0]?.clientX ?? null; }, { passive: true });
        lightbox.addEventListener('touchend', (event) => { if (touchStartX === null) return; const distance = (event.changedTouches[0]?.clientX ?? touchStartX) - touchStartX; if (Math.abs(distance) > 55) move(distance > 0 ? -1 : 1); touchStartX = null; }, { passive: true });
        lightbox.addEventListener('keydown', (event) => { if (event.key === 'Escape') close(); if (event.key === 'ArrowLeft') move(-1); if (event.key === 'ArrowRight') move(1); });
    });
};

const updateMobileChromeOnScroll = () => {
    scrollTicking = false;

    if (window.innerWidth > 820 || document.body.classList.contains('kba-mobile-menu-open')) {
        resetMobileChrome();
        return;
    }

    const currentY = Math.max(window.scrollY, 0);
    const delta = currentY - lastScrollY;
    const { header, bottom } = mobileShellElements();

    if (currentY < 72) {
        header?.classList.remove('is-scroll-hidden');
        bottom?.classList.remove('is-scroll-hidden');
    } else if (delta > 7) {
        header?.classList.add('is-scroll-hidden');
        bottom?.classList.add('is-scroll-hidden');
    } else if (delta < -7) {
        header?.classList.remove('is-scroll-hidden');
        bottom?.classList.remove('is-scroll-hidden');
    }

    lastScrollY = currentY;
};

if (!window.__kbaMobileShellBound) {
    window.__kbaMobileShellBound = true;

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-kba-menu-open]')) {
            setMobileMenu(true);
            return;
        }

        if (event.target.closest('[data-kba-menu-close], [data-kba-menu-backdrop]')) {
            setMobileMenu(false);
            return;
        }

        if (event.target.closest('[data-kba-menu-link]')) {
            // Let the native/Livewire link activation finish before applying
            // `inert` to the sidebar. Applying it during this click can cancel
            // navigation on touch browsers.
            window.setTimeout(() => setMobileMenu(false, false), 0);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && document.body.classList.contains('kba-mobile-menu-open')) {
            setMobileMenu(false);
        }
    });

    document.addEventListener('livewire:navigating', () => setMobileMenu(false, false));

    window.addEventListener('scroll', () => {
        if (!scrollTicking) {
            window.requestAnimationFrame(updateMobileChromeOnScroll);
            scrollTicking = true;
        }
    }, { passive: true });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 820) setMobileMenu(false);
        resetMobileChrome();
    }, { passive: true });
}

const initializePageUi = () => {
    setMobileMenu(false);
    resetMobileChrome();
    initializeGalleries();
    initializeHomeSliders();
    initializeVideoGalleries();
};

document.addEventListener('DOMContentLoaded', initializePageUi);
document.addEventListener('livewire:navigating', () => setMobileMenu(false));
document.addEventListener('livewire:navigated', initializePageUi);
