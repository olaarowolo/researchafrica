<div class="d-inline">
    @php
        if ($status == '2' || $status == '8' || $status == '12') {
            $anchor = App\Models\EditorAccept::lastArticle($article)
                ->latest()
                ->first();
        } elseif ($status == '4') {
            $anchor = App\Models\ReviewerAccept::lastArticle($article)
                ->latest()
                ->first();
        } elseif ($status == '6') {
            $anchor = App\Models\ReviewerAcceptFinal::lastArticle($article)
                ->latest()
                ->first();
        }
    @endphp

    @if (auth('member')->id() == $member)
        {{ App\Models\SubArticle::STATUS_SELECT[$status] }}
    @else
        @if (
            $status == '1' ||
                $status == '3' ||
                $status == '5' ||
                $status == '7' ||
                $status == '9' ||
                $status == '10' ||
                $status == '11' ||
                $status == '9')
            {{ App\Models\SubArticle::STATUS_SELECT[$status] }}
        @else
            {{ App\Models\SubArticle::STATUS_SELECT[$status] }} -
            {{ $anchor?->member ? $anchor?->member?->fullname : '' }}
        @endif

    @endif
</div>
