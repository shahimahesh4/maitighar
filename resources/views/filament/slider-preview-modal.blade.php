<div x-data="{
    device: 'desktop',
    loading: true,
    sendToIframe(action) {
        const iframe = this.$refs.previewIframe;
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage({ action: action }, '*');
        }
    },
    reloadIframe() {
        this.loading = true;
        const iframe = this.$refs.previewIframe;
        if (iframe) {
            iframe.src = iframe.src;
        }
    }
}" class="w-full space-y-4">

    <!-- Top Control Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-slate-900 text-white rounded-xl shadow-inner border border-slate-800">
        <!-- Viewport Switcher -->
        <div class="flex items-center gap-1.5 bg-slate-800/90 p-1 rounded-lg border border-slate-700">
            <button type="button"
                    @click="device = 'desktop'"
                    :class="device === 'desktop' ? 'bg-[#EC0205] text-white shadow' : 'text-slate-300 hover:text-white hover:bg-slate-700/60'"
                    class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-md transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Desktop (100%)</span>
            </button>

            <button type="button"
                    @click="device = 'tablet'"
                    :class="device === 'tablet' ? 'bg-[#EC0205] text-white shadow' : 'text-slate-300 hover:text-white hover:bg-slate-700/60'"
                    class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-md transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>Tablet (768px)</span>
            </button>

            <button type="button"
                    @click="device = 'mobile'"
                    :class="device === 'mobile' ? 'bg-[#EC0205] text-white shadow' : 'text-slate-300 hover:text-white hover:bg-slate-700/60'"
                    class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-md transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>Mobile (375px)</span>
            </button>
        </div>

        <!-- Slider Interaction Controls -->
        <div class="flex items-center gap-2">
            <!-- Replay Animation Button -->
            <button type="button"
                    @click="sendToIframe('replay')"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold bg-amber-500 hover:bg-amber-600 active:scale-95 text-slate-950 rounded-lg shadow transition-all"
                    title="Replay GSAP layer animations on the current slide">
                <svg class="w-4 h-4 animate-spin-hover" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Replay Animation</span>
            </button>

            <!-- Slide Navigation Buttons -->
            <div class="flex items-center bg-slate-800 rounded-lg border border-slate-700 p-0.5">
                <button type="button"
                        @click="sendToIframe('prev')"
                        class="p-1.5 text-slate-300 hover:text-white hover:bg-slate-700 rounded-md transition"
                        title="Previous Slide">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <span class="px-2 text-xs font-mono text-slate-400">Slides</span>
                <button type="button"
                        @click="sendToIframe('next')"
                        class="p-1.5 text-slate-300 hover:text-white hover:bg-slate-700 rounded-md transition"
                        title="Next Slide">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>

            <!-- Reload Preview -->
            <button type="button"
                    @click="reloadIframe()"
                    class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition"
                    title="Reload Preview Canvas">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>

            <!-- Open in New Tab -->
            <a href="{{ route('filament.sliders.preview', $slider->id) }}"
               target="_blank"
               rel="noopener noreferrer"
               class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition"
               title="Open Standalone Preview in New Tab">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Live Preview Stage / Canvas -->
    <div class="w-full flex items-center justify-center p-4 sm:p-6 bg-slate-950/80 rounded-2xl border border-slate-800/80 min-h-[520px] overflow-hidden">
        
        <div class="transition-all duration-500 ease-out mx-auto relative rounded-xl shadow-2xl overflow-hidden border border-slate-700/60 bg-slate-900"
             :style="device === 'desktop' ? 'width: 100%; max-width: 1200px; height: 620px;' : (device === 'tablet' ? 'width: 768px; height: 540px;' : 'width: 375px; height: 500px;')">
            
            <!-- Mobile / Tablet Device Notch Decorator -->
            <div x-show="device !== 'desktop'"
                 class="h-6 w-full bg-slate-900 flex items-center justify-center border-b border-slate-800">
                <div class="w-16 h-1.5 rounded-full bg-slate-700"></div>
            </div>

            <!-- Loading overlay while iframe boots up -->
            <div x-show="loading"
                 class="absolute inset-0 z-30 flex flex-col items-center justify-center bg-slate-900 text-white gap-3">
                <svg class="animate-spin w-8 h-8 text-[#EC0205]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-medium text-slate-300">Rendering Revolution Slider Preview...</span>
            </div>

            <!-- Realtime Iframe Preview -->
            <iframe x-ref="previewIframe"
                    src="{{ route('filament.sliders.preview', $slider->id) }}"
                    class="w-full h-full border-0 block"
                    @load="loading = false"></iframe>
        </div>

    </div>

    <div class="flex items-center justify-between text-xs text-slate-400 px-2">
        <span>Embed Code: <code class="font-mono bg-slate-800 text-red-400 px-1.5 py-0.5 rounded">&lt;livewire:slider location="{{ $slider->code }}" /&gt;</code></span>
        <span>Includes Swiper transitions &amp; GSAP layer entrance animations</span>
    </div>
</div>
