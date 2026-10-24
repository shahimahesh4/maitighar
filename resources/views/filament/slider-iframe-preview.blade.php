<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $slider->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800;9..40,900;9..40,1000&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background: #0f172a;
        }
    </style>
</head>
<body>
    <div id="slider-preview-root" class="w-full">
        <livewire:slider :slider="$slider" :isPreview="true" />
    </div>

    @livewireScripts
    <script>
        // Listen to control messages from parent Filament window
        window.addEventListener('message', function (event) {
            const data = event.data;
            if (!data || typeof data !== 'object') return;

            const sliderEl = document.querySelector('.rev-slider-container');
            if (!sliderEl) return;

            if (data.action === 'replay' && typeof window.replaySliderAnimation === 'function') {
                window.replaySliderAnimation(sliderEl);
            } else if (data.action === 'prev' && typeof window.sliderPrev === 'function') {
                window.sliderPrev(sliderEl);
            } else if (data.action === 'next' && typeof window.sliderNext === 'function') {
                window.sliderNext(sliderEl);
            }
        });
    </script>
</body>
</html>
