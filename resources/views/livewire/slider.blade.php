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
                            <div class="rev-content-banner w-full h-full relative flex items-center"
                                 style="{{ $item->background_style }}">

                                {{-- Optional Background Overlay if image background --}}
                                @if(($bg['type'] ?? '') === 'image' && !empty($bg['overlay_opacity']) && $bg['overlay_opacity'] > 0)
                                    <div class="absolute inset-0 bg-black pointer-events-none"
                                         style="opacity: {{ ($bg['overlay_opacity'] ?? 40) / 100 }};"></div>
                                @endif
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

