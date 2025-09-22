<div class="my-3 row justify-content-center">
    @if ($count != 0 && request()->has('q') && request('q'))
    <h4>Your Search result ({{ $count }})</h4>
    <br />
    <div class="col-md-4">
        @if (request('q') && request()->has('q'))
        <div class="p-2 border border-2 rounded border-secondary">
            <!-- Horizontal under breakpoint -->
            <ul class="list-group list-group-flush">

                <a href="{{ request()->fullUrlWithQuery(['with_category' => null]) }}"
                    class="list-group-item d-flex justify-content-between">
                    <span>All Category</span>
                    <span>({{ $count }})</span>
                </a>

                @foreach ($categories as $item)
                <a href="{{ request()->fullUrlWithQuery(['with_category' => $item->id]) }}"
                    class="list-group-item d-flex justify-content-between">
                    <span>{{ $item->category_name ?? '' }}</span>
                    <span>({{ $item->categories_count }})</span>
                </a>
                @endforeach
            </ul>
        </div>
        @else
        <div class="p-2 border border-2 rounded border-secondary">
            <!-- Horizontal under breakpoint -->
            <ul class="list-group list-group-flush">
                {{-- @foreach ($categories as $item)

                <a href="{{ request()->fullUrlWithQuery(['with_category' => $item->id]) }}"
                    class="list-group-item d-flex justify-content-between">
                    <span>{{$item->category_name ?? ''}}</span>
                    <span>({{$item->article_count}})</span>
                </a>
                @endforeach --}}
            </ul>
        </div>

        @endif
    </div>
    @endif
    <div class="p-1 col-md-8 p-md-3">
        @if (request('q') && request()->has('q'))
        <div class='mb-2 d-flex align-items-center'>
            <a href="{{ request()->fullUrlWithQuery(['type' => 'article', 'page' => null]) }}"
                class="menu-link {{ request('type') !== 'journal' ? 'active-a' : '' }}">Articles</a> |
            <a href="{{ request()->fullUrlWithQuery(['type' => 'journal', 'page' => null]) }}"
                class="menu-link {{ request('type') === 'journal' ? 'active-a' : '' }}">Journal</a>
        </div>
        <hr>
        @endif
        @forelse ($journals as $journal)
        <div class="my-3 d-flex align-items-center kb-gap-3">
            {{-- <div class="rounded kb-border kb-bg-slate-500/60">
                <div class="kb-grid kb-h-24 kb-w-20 kb-place-content-center">
                    <a href="{{ route('member.cat-sub', ['cat' => $journal->parent_id, 'sub' => $journal->id, 'journal' => Str::snake($journal->category_name), ]) }}"
                        class="">
                        @if ($journal->cover_image)
                        @else
                        <span class="p-2 d-block kb-text-5xl kb-font-extrabold kb-uppercase hover:kb-text-black">
                            {{ substr($journal->category_name, 0, 2) }}
                        </span>
                        @endif
                    </a>
                </div>
            </div> --}}

            <div class="kb-shadow hover:kb-border-2 hover:kb-scale-105 kb-rounded-lg ">
                <img src="{{ $journal->cover_image?  $journal->cover_image->getUrl() : '' }}" alt=""
                    class="kb-h-24 kb-w-20 kb-object-fill">
            </div>
            <div class="">
                <h3 class="kb-text-2xl kb-font-extrabold kb-underline hover:kb-text-orange-500">
                    <a href="{{ route('member.cat-sub', [ 'cat' =>  $journal->parent_id, 'sub' => $journal->id, 'journal' =>  Str::snake($journal->category_name), ]) }}"
                        class="hover:kb-text-orange-500">{{ $journal->category_name ?? '' }}</a>
                </h3>
                @php

                $article = \App\Models\Article::where('article_category_id', $journal->parent_id)
                ->where('article_sub_category_id', $journal->id)
                ->publish();

                $total = $article->count();
                $last = $article->latest()->first();
                $first = $article->first();

                @endphp

                <ul class="kb-flex kb-flex-col kb-flex-wrap kb-gap-1">
                    <li>
                        @if (!is_null($last) || !is_null($first))

                        <span class="kb-font-semibold"> Latest Content: </span> {{ $last->volume ?? 'none' }},
                        {{ !is_null($last) ? date('F Y', strtotime($last->publish_date ?? '-')) : '<strong
                            class="text-danger">No Article</strong>' }}
                        <br>
                        <span class="kb-font-semibold">Content Available from:</span> {{ !is_null($first) ? date('F Y',
                        strtotime($first->publish_date ??  'none')) : 'none' }}

                        {{-- {{ $last->publish_date ?? '' }} --}}

                        @else
                        <strong class="text-danger">
                            No Article
                        </strong>
                        @endif
                        <br>
                        
                        @if($journal->issn)
                        <span class="kb-font-semibold">ISSN:</span> {{ $journal->issn ?? '' }}
                        @endif
                        @if($journal->online_issn) | <span
                            class="kb-font-semibold">Online ISSN: </span> {{ $journal->online_issn }}
                        <br>
                        @endif
                        @if($journal->doi_link)
                        <span class="kb-font-semibold">Title DOI:</span> <a href="{{ $journal->doi_link }}" target="_blank">{{ $journal->doi_link ?? '' }}</a>
                        @endif

                    </li>
                </ul>
            </div>
        </div>
        <hr>
        @empty
        <div class="mx-auto w-100">
            <p class="text-center fs-3 text-danger">
                Journal Not Found
            </p>
        </div>
        @endforelse

        @if ($journals->isNotEmpty())
        <div class="d-flex justify-content-center">
            <div class="">
                {{ $journals->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
