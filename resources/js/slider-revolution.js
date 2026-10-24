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
                    const activeSlide = this.slides[this.activeIndex];
                    if (activeSlide) {
                        animateSlideLayers(activeSlide);
                    }
                },
                slideChangeTransitionStart: function () {
                    const activeSlide = this.slides[this.activeIndex];
                    if (activeSlide) {
                        animateSlideLayers(activeSlide);
                    }
                },
            },
        });

        // Store Swiper instance on element for external controls (like Preview Modal)
        container.__revSwiper = swiper;
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
        document.addEventListener('DOMContentLoaded', initRevSliders);
    } else {
        initRevSliders();
    }

    document.addEventListener('livewire:navigated', () => {
        initRevSliders();
    });

    document.addEventListener('livewire:initialized', () => {
        initRevSliders();
    });
}
