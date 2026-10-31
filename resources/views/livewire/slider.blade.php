<div>
@if($slider && $items->isNotEmpty())
    @php
        $sliderId = 'rev-slider-' . ($slider->code ?: $slider->id) . '-' . uniqid();
        $desktopHeight = $slider->desktop_height ?: '650px';
        $tabletHeight = $slider->tablet_height ?: '520px';
    @endphp

    <style>
        #{{ $sliderId }} {
            height: {{ $desktopHeight }};
        }
        @media (max-width: 1024px) {
            #{{ $sliderId }} {
                height: {{ $tabletHeight }};
            }
        }
        @media (max-width: 640px) {
            #{{ $sliderId }} {
                height: clamp(160px, 44vw, 210px);
            }
        }
    </style>

    <div class="relative w-full overflow-hidden select-none group rev-slider-wrapper"
         id="{{ $sliderId }}-wrapper"
         role="region"
         aria-roledescription="carousel"
         aria-label="{{ $slider->name }}">

        <!-- Main Swiper Slider Container -->
        <div class="swiper rev-slider-container w-full h-full"
             id="{{ $sliderId }}"
             data-autoplay="{{ $slider->autoplay ? 'true' : 'false' }}"
             data-autoplay-speed="{{ $slider->autoplay_speed ?: 5000 }}"
             data-transition-speed="{{ $slider->transition_speed ?: 800 }}"
             data-effect="{{ $slider->transition_effect ?: 'fade' }}"
             data-pause-on-hover="{{ $slider->pause_on_hover ? 'true' : 'false' }}">

            <div class="swiper-wrapper">
                @foreach($items as $index => $item)
                    @php
                        $isFirst = $index === 0;
                        $bg = $item->background_settings ?? [];
                    @endphp

                    <div class="swiper-slide relative w-full h-full overflow-hidden"
                         data-slide-index="{{ $index }}"
                         role="group"
                         aria-roledescription="slide"
                         aria-label="{{ $item->title }} ({{ $index + 1 }} of {{ $items->count() }})">

                        @if($item->isSingleImage())
                            {{-- ========================================= --}}
                            {{-- TYPE 1: SINGLE IMAGE BANNER               --}}
                            {{-- ========================================= --}}
                            @if($item->link_url)
                                <a href="{{ $item->link_url }}"
                                   @if($item->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                   class="block w-full h-full relative cursor-pointer group/link">
                            @else
                                <div class="w-full h-full relative">
                            @endif

                                <picture class="block w-full h-full">
                                    @if($item->mobile_image)
                                        <source media="(max-width: 640px)" srcset="{{ $item->mobile_image_url }}">
                                    @endif
                                    <img src="{{ $item->desktop_image_url }}"
                                         alt="{{ $item->title }}"
                                         class="w-full h-full object-cover object-center transition-transform duration-1000 ease-out group-hover/link:scale-[1.02]"
                                         @if($isFirst) loading="eager" fetchpriority="high" @else loading="lazy" @endif />
                                </picture>

                            @if($item->link_url)
                                </a>
                            @else
                                </div>
                            @endif

                        @else
                            {{-- ========================================= --}}
                            {{-- TYPE 2: ANIMATED CONTENT BANNER (REVOLUTION) --}}
                            {{-- ========================================= --}}
                            @php
                                $copy = $item->text_content ?? [];
                                $mainTitleSize = is_numeric($copy['main_title_font_size'] ?? null)
                                    ? min(160, max(16, (int) $copy['main_title_font_size']))
                                    : null;
                                $subtitleSize = is_numeric($copy['second_title_font_size'] ?? null)
                                    ? min(100, max(12, (int) $copy['second_title_font_size']))
                                    : null;
                            @endphp
                            <div class="rev-content-banner w-full h-full relative flex items-center"
                                 style="{{ $item->background_style }}">

                                <div class="rev-editorial-wash absolute inset-0 pointer-events-none"></div>

                                {{-- Optional Background Overlay if image background --}}
                                @if(($bg['type'] ?? '') === 'image' && !empty($bg['overlay_opacity']) && $bg['overlay_opacity'] > 0)
                                    <div class="absolute inset-0 bg-black pointer-events-none"
                                         style="opacity: {{ ($bg['overlay_opacity'] ?? 40) / 100 }};"></div>
                                @endif

                                <div class="rev-slider-inner rev-editorial-inner relative z-10 w-full">
                                    <div class="rev-editorial-copy">
                                        <p class="rev-editorial-eyebrow rev-layer"
                                           data-effect="slide_right"
                                           data-delay="{{ $animations['eyebrow']['delay'] ?? 0.1 }}"
                                           data-duration="{{ $animations['eyebrow']['duration'] ?? 0.65 }}">
                                            <span></span>Maitighar<span></span>
                                        </p>

                                        <h1 class="rev-editorial-title rev-layer"
                                            @if($mainTitleSize) style="--rev-desktop-title-size: {{ $mainTitleSize }}px" @endif
                                            data-effect="slide_right"
                                            data-delay="{{ $animations['title']['delay'] ?? 0.2 }}"
                                            data-duration="{{ $animations['title']['duration'] ?? 0.8 }}">
                                            {{ $copy['main_title'] ?? $item->title }}
                                        </h1>

                                        @if(filled($copy['second_title'] ?? null))
                                            <h2 class="rev-editorial-subtitle rev-layer"
                                                @if($subtitleSize) style="--rev-desktop-subtitle-size: {{ $subtitleSize }}px" @endif
                                                data-effect="slide_right"
                                                data-delay="{{ $animations['subtitle']['delay'] ?? 0.35 }}"
                                                data-duration="{{ $animations['subtitle']['duration'] ?? 0.7 }}">
                                                {{ $copy['second_title'] }}
                                            </h2>
                                        @endif

                                        <div class="rev-editorial-divider rev-layer"
                                             data-effect="slide_right" data-delay="0.45" data-duration="0.6"><i></i></div>

                                        <a href="{{ $item->link_url ?: route('all-products') }}"
                                           @if($item->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                           class="rev-editorial-cta rev-layer"
                                           data-effect="slide_right" data-delay="0.55" data-duration="0.7">
                                            Explore Collection <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

            {{-- Slider Navigation Arrows --}}
            @if($slider->show_navigation && $items->count() > 1)
                <button type="button"
                        class="swiper-button-prev rev-nav-btn rev-nav-prev !hidden sm:!flex absolute top-1/2 left-3 sm:left-5 -translate-y-1/2 z-20 items-center justify-center focus:outline-none"
                        aria-label="Previous Slide">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <button type="button"
                        class="swiper-button-next rev-nav-btn rev-nav-next !hidden sm:!flex absolute top-1/2 right-3 sm:right-5 -translate-y-1/2 z-20 items-center justify-center focus:outline-none"
                        aria-label="Next Slide">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            @endif

            {{-- Slider Pagination Dots --}}
            @if($slider->show_pagination && $items->count() > 1)
                <div class="swiper-pagination rev-pagination !bottom-4 sm:!bottom-6 z-20 flex items-center justify-center gap-2"></div>
            @endif

        </div>
    </div>
@endif
</div>

