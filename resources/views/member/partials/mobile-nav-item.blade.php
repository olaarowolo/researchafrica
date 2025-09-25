@if (!empty($item['children']))
    <div id="{{ \Illuminate\Support\Str::slug($item['title']) }}-toggle" class="px-4 py-4 border-b border-gray-200 cursor-pointer">
        <div class="flex justify-between items-center text-gray-700 font-medium">
            <span class="flex items-center">@if(!empty($item['icon']))<i class="{{ $item['icon'] }} mr-2"></i>@endif{{ $item['title'] }}</span>
            <svg id="{{ \Illuminate\Support\Str::slug($item['title']) }}-arrow" class="w-4 h-4 transform rotate-0 transition duration-300" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
    <div id="{{ \Illuminate\Support\Str::slug($item['title']) }}-content" class="hidden pt-16 px-4 py-3 bg-gray-50 border-b border-gray-200">
        <ul class="space-y-2 text-sm text-gray-600">
            @foreach ($item['children'] as $child)
                <li class="pt-1 font-semibold text-gray-800"><a href="{{ $child['url'] }}"
                        class="hover:text-blue-600">{{ $child['title'] }}</a></li>
                @if (!empty($child['children']))
                    @foreach ($child['children'] as $subChild)
                        <li><a href="{{ $subChild['url'] }}" class="block py-1 hover:text-blue-600 flex items-center">
                            @if(!empty($subChild['icon']))<i class="{{ $subChild['icon'] }} mr-2 w-4 text-center"></i>@endif
                            {{ $subChild['title'] }}</a>
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>
    </div>
@else
    <li><a href="{{ $item['url'] }}" class="block hover:text-blue-600 flex items-center" @if($item['target_blank']) target="_blank" rel="noopener noreferrer" @endif>
        @if(!empty($item['icon']))<i class="{{ $item['icon'] }} mr-2"></i>@endif
        {{ $item['title'] }}</a>
    </li>
@endif