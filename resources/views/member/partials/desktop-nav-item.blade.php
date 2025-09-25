@if (!empty($item['children']))
    <li class="relative group">
        <button class="hover:text-blue-600 transition flex items-center text-sm md:text-xs sm:text-xs" data-menu-toggle="{{ \Illuminate\Support\Str::slug($item['title']) }}">
            @if(!empty($item['icon']))<i class="{{ $item['icon'] }} mr-1"></i>@endif
            {{ $item['title'] }}
            <svg class="inline-flex ml-1 w-4 h-4 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div data-menu-content="{{ \Illuminate\Support\Str::slug($item['title']) }}"
            class="mega-menu absolute {{ $item['mega_menu_position'] ?? 'left-0' }} mt-3 w-max max-w-xl lg:max-w-4xl bg-white/90 backdrop-blur-lg shadow-xl rounded-xl p-4 hidden z-50">
            <div class="grid {{ $item['mega_menu_cols'] ?? 'grid-cols-2' }} gap-4 text-sm text-gray-600">
                @foreach ($item['children'] as $child)
                    <div>
                        <a href="{{ $child['url'] }}">
                            <h5 class="font-semibold text-gray-900 mb-2 hover:text-blue-600">{{ $child['title'] }}</h5>
                        </a>
                        @if (!empty($child['children']))
                            <ul class="space-y-2">
                                @foreach ($child['children'] as $subChild)
                                    <li>
                                        <a href="{{ $subChild['url'] }}" class="hover:text-blue-600 flex items-center">
                                            @if(!empty($subChild['icon']))<i class="{{ $subChild['icon'] }} mr-2"></i>@endif
                                            {{ $subChild['title'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </li>
@else
    <li><a href="{{ $item['url'] }}" class="hover:text-blue-600 transition flex items-center" @if($item['target_blank']) target="_blank" rel="noopener noreferrer" @endif><i class="{{ $item['icon'] }} mr-2"></i>{{ $item['title'] }}</a></li>
@endif