import Swiper from 'swiper';
import { Autoplay, Navigation, Pagination, EffectFade, Keyboard, A11y } from 'swiper/modules';
import gsap from 'gsap';

// Import core and module Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

/**
 * Animate individual layers of the active slide using GSAP
 */
export function animateSlideLayers(slideEl) {
    if (!slideEl) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const layers = slideEl.querySelectorAll('.rev-layer');

    if (!layers.length) return;

    if (prefersReducedMotion) {
        layers.forEach((layer) => {
            gsap.set(layer, { opacity: 1, x: 0, y: 0, scale: 1, rotation: 0 });
        });
        return;
    }

    layers.forEach((layer) => {
        const effect = layer.dataset.effect || 'fade_up';
        const delay = parseFloat(layer.dataset.delay) || 0;
        const duration = parseFloat(layer.dataset.duration) || 0.8;

        // Reset layer to initial state before animating
        gsap.killTweensOf(layer);

        let fromVars = { opacity: 0 };
        let toVars = { opacity: 1, duration, delay, ease: 'power3.out' };

        switch (effect) {
            case 'text_reveal':
                fromVars = { opacity: 0, x: -32, clipPath: 'inset(0 100% 0 0)' };
                toVars = { ...toVars, x: 0, clipPath: 'inset(0 0% 0 0)', ease: 'power4.out' };
                break;
            case 'fade_up':
                fromVars = { opacity: 0, y: 45 };
                toVars = { ...toVars, y: 0, ease: 'power3.out' };
                break;
            case 'fade_down':
                fromVars = { opacity: 0, y: -45 };
                toVars = { ...toVars, y: 0, ease: 'power3.out' };
                break;
            case 'slide_left':
                fromVars = { opacity: 0, x: 70 };
                toVars = { ...toVars, x: 0, ease: 'power3.out' };
                break;
            case 'slide_right':
                fromVars = { opacity: 0, x: -70 };
                toVars = { ...toVars, x: 0, ease: 'power3.out' };
                break;
            case 'zoom_in':
                fromVars = { opacity: 0, scale: 0.72 };
                toVars = { ...toVars, scale: 1, ease: 'back.out(1.5)' };
                break;
            case 'bounce_in':
                fromVars = { opacity: 0, scale: 0.3 };
                toVars = { ...toVars, scale: 1, ease: 'bounce.out' };
                break;
            case 'rotate_in':
                fromVars = { opacity: 0, rotation: -20, scale: 0.8 };
                toVars = { ...toVars, rotation: 0, scale: 1, ease: 'power3.out' };
                break;
            case 'float':
                fromVars = { opacity: 0, y: 30 };
                toVars = { ...toVars, y: 0, ease: 'power2.out' };
                break;
            case 'fade_in':
            default:
                fromVars = { opacity: 0 };
                toVars = { ...toVars, ease: 'power2.out' };
                break;
        }

        gsap.fromTo(layer, fromVars, toVars);
    });
}

/**
 * Initialize all Revolution Sliders on the page
 */
export function initRevSliders() {
    const sliderContainers = document.querySelectorAll('.rev-slider-container:not(.rev-initialized)');

    sliderContainers.forEach((container) => {
        container.classList.add('rev-initialized');

        const isAutoplay = container.dataset.autoplay === 'true';
        const autoplayDelay = parseInt(container.dataset.autoplaySpeed, 10) || 5000;
        const transitionSpeed = parseInt(container.dataset.transitionSpeed, 10) || 800;
        const effect = container.dataset.effect || 'fade';
        const pauseOnHover = container.dataset.pauseOnHover === 'true';

        const slideCount = container.querySelectorAll('.swiper-slide').length;
        const shouldLoop = slideCount > 1;

        const swiper = new Swiper(container, {
            modules: [Autoplay, Navigation, Pagination, EffectFade, Keyboard, A11y],
            effect: effect === 'slide' ? 'slide' : 'fade',
            fadeEffect: {
                crossFade: true,
            },
            speed: transitionSpeed,
            loop: shouldLoop,
            grabCursor: shouldLoop,
            touchEventsTarget: 'wrapper',
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            autoplay: isAutoplay && shouldLoop ? {
                delay: autoplayDelay,
                disableOnInteraction: false,
                pauseOnMouseEnter: pauseOnHover,
            } : false,
            navigation: {
                nextEl: container.querySelector('.rev-nav-next'),
                prevEl: container.querySelector('.rev-nav-prev'),
            },
            pagination: {
                el: container.querySelector('.rev-pagination'),
                clickable: true,
                bulletClass: 'rev-bullet',
                bulletActiveClass: 'rev-bullet-active',
            },
            on: {
                init: function () {
                    window.setTimeout(() => {
                        const activeSlide = this.slides[this.activeIndex];
                        if (activeSlide) animateSlideLayers(activeSlide);
                    }, 80);
                },
                slideChangeTransitionEnd: function () {
                    const activeSlide = this.slides[this.activeIndex];
                    if (activeSlide) animateSlideLayers(activeSlide);
                },
            },
        });

        // Covers cases where Swiper initializes before its init hook is observed.
        window.setTimeout(() => {
            const activeSlide = swiper.slides[swiper.activeIndex];
            if (activeSlide) animateSlideLayers(activeSlide);
        }, 120);

        // Store Swiper instance on element for external controls (like Preview Modal)
        container.__revSwiper = swiper;
    });
}

/**
 * Use the same Swiper slide engine as the homepage hero for Cultural Clothing cards.
 */
export function initCulturalCategoryCarousels() {
    document.querySelectorAll('.kba-cultural-swiper:not(.cultural-swiper-initialized)').forEach((container) => {
        const slideCount = container.querySelectorAll('.swiper-slide').length;

        if (slideCount < 2) return;

        container.classList.add('cultural-swiper-initialized');

        const region = container.closest('.kba-cultural-collection__products');
        const shouldLoop = slideCount > 3;
        const autoplayDelay = parseInt(container.dataset.autoplaySpeed, 10) || 3200;
        const transitionSpeed = parseInt(container.dataset.transitionSpeed, 10) || 800;

        container.__culturalSwiper = new Swiper(container, {
            modules: [Autoplay, Navigation, Keyboard, A11y],
            slidesPerView: 3,
            spaceBetween: 22,
            speed: transitionSpeed,
            loop: shouldLoop,
            grabCursor: shouldLoop,
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            autoplay: shouldLoop && !window.matchMedia('(prefers-reduced-motion: reduce)').matches ? {
                delay: autoplayDelay,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            } : false,
            navigation: {
                nextEl: region?.querySelector('.kba-cultural-carousel-nav--next'),
                prevEl: region?.querySelector('.kba-cultural-carousel-nav--previous'),
            },
            breakpoints: {
                0: { slidesPerView: 1.35, spaceBetween: 12 },
                721: { slidesPerView: 3, spaceBetween: 14 },
                1101: { slidesPerView: 3, spaceBetween: 22 },
            },
        });
    });
}

/**
 * New Arrivals shares the same Swiper-based slide experience as category cards.
 */
export function initProductCarousels() {
    document.querySelectorAll('.kba-product-swiper:not(.product-swiper-initialized), .kba-sale-product-swiper:not(.product-swiper-initialized)').forEach((container) => {
        const slideCount = container.querySelectorAll('.swiper-slide').length;

        if (slideCount < 2) return;

        container.classList.add('product-swiper-initialized');

        const region = container.closest('.kba-product-carousel');
        const shouldAutoSlide = slideCount > 4;
        const autoplayDelay = parseInt(container.dataset.autoplaySpeed, 10) || 4000;
        const transitionSpeed = parseInt(container.dataset.transitionSpeed, 10) || 800;

        container.__productSwiper = new Swiper(container, {
            modules: [Autoplay, Navigation, Keyboard, A11y],
            slidesPerView: 4,
            spaceBetween: 24,
            speed: transitionSpeed,
            // Product cards contain Livewire controls. Rewind keeps the continuous
            // carousel experience without cloning or moving those interactive nodes.
            loop: false,
            rewind: shouldAutoSlide,
            grabCursor: shouldAutoSlide,
            preventClicks: false,
            preventClicksPropagation: false,
            touchStartPreventDefault: false,
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            autoplay: shouldAutoSlide && !window.matchMedia('(prefers-reduced-motion: reduce)').matches ? {
                delay: autoplayDelay,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            } : false,
            navigation: {
                nextEl: region?.querySelector('.kba-product-carousel-nav--next'),
                prevEl: region?.querySelector('.kba-product-carousel-nav--previous'),
            },
            breakpoints: {
                0: { slidesPerView: 1.15, spaceBetween: 13 },
                821: { slidesPerView: 3, spaceBetween: 24 },
                1025: { slidesPerView: 4, spaceBetween: 24 },
            },
        });
    });
}

// Global helper for preview modal and external controls
window.replaySliderAnimation = function (containerEl) {
    const container = typeof containerEl === 'string' ? document.querySelector(containerEl) : containerEl;
    if (!container) return;

    const swiper = container.__revSwiper;
    if (swiper) {
        const activeSlide = swiper.slides[swiper.activeIndex];
        if (activeSlide) {
            animateSlideLayers(activeSlide);
        }
    } else {
        const activeSlide = container.querySelector('.swiper-slide-active') || container.querySelector('.swiper-slide');
        if (activeSlide) {
            animateSlideLayers(activeSlide);
        }
    }
};

window.sliderPrev = function (containerEl) {
    const container = typeof containerEl === 'string' ? document.querySelector(containerEl) : containerEl;
    if (container?.__revSwiper) container.__revSwiper.slidePrev();
};

window.sliderNext = function (containerEl) {
    const container = typeof containerEl === 'string' ? document.querySelector(containerEl) : containerEl;
    if (container?.__revSwiper) container.__revSwiper.slideNext();
};

// Auto-run on DOM ready and Livewire navigation
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initRevSliders();
            initCulturalCategoryCarousels();
            initProductCarousels();
        });
    } else {
        initRevSliders();
        initCulturalCategoryCarousels();
        initProductCarousels();
    }

    document.addEventListener('livewire:navigated', () => {
        initRevSliders();
        initCulturalCategoryCarousels();
        initProductCarousels();
    });

    document.addEventListener('livewire:initialized', () => {
        initRevSliders();
        initCulturalCategoryCarousels();
        initProductCarousels();
    });
}
