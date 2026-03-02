@if ($hasLinks())
    <div class="mt-auto pt-2 border-t border-gray-200">
        <h3 class="text-lg font-semibold text-center text-gray-800 mb-4">
            Nájdete nás aj na
        </h3>
        <div class="flex justify-center items-center space-x-6">
            @foreach($socials as $key => $url)
                @if ($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" title="{{ ucfirst($key) }}"
                       class="text-gray-500 hover:text-{{ $colors[$key] ?? 'blue-500' }} hover:scale-125 transition">
                        <i class="fa-lg {{ $key !== 'website' ? 'fa-brands fa-' . $key : 'fa-solid fa-globe' }}"></i>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
@endif
