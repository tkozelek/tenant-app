@props(['errorCode', 'title'])

<div class="text-9xl mb-6 font-extrabold text-slate-500">{{ $errorCode }}</div>

<h2 class="text-3xl md:text-4xl font-bold text-white mb-4 tracking-tight">
    {{ $title }}
</h2>

<p class="text-slate-400 text-lg md:text-xl mb-10 max-w-lg mx-auto leading-relaxed">
    {{ $slot }}
</p>
