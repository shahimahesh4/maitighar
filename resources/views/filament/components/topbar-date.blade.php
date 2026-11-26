<time class="fi-topbar-date" datetime="{{ now('Asia/Kathmandu')->toDateString() }}">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 18.75V8.625A2.625 2.625 0 016.375 6h11.25a2.625 2.625 0 012.625 2.625V18.75m-16.5 0A2.625 2.625 0 006.375 21h11.25a2.625 2.625 0 002.625-2.25m-16.5 0v-7.5A2.625 2.625 0 016.375 8.625h11.25a2.625 2.625 0 012.625 2.625v7.5" />
    </svg>
    {{ now('Asia/Kathmandu')->format('D, d M Y') }}
</time>
