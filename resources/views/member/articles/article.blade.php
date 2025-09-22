@extends('layouts.profile')
@section('page-name', $article->title)

@section('content')
<div id="content" class="p-4 p-md-5">
    <x-profile-bar />

    <div class="card shadow mb-5">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>
                Comments
            </h4>
            @if (auth('member')->user()->member_type_id == 2)

            <div class="">
                <button type="button" id="AddCommentShow" class="em-btn bg-dark">Add Comment</button>
            </div>
            @endif
        </div>

        <div class="card-body">
            <div class="px-4">

                @if (auth('member')->user()->member_type_id == 2)

                <div class="commentBox" style="display: none">
                    <form action="{{ route('member.comment.store', $article->id) }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="message" class="form-label">Comment</label>
                            <textarea class="form-control" name="message" id="message" rows="3"></textarea>
                        </div>

                        <x-submit-button />
                    </form>
                    <hr>

                </div>

                @endif

                <div class="my-3">
                    @forelse ($article->comments as $comment)
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="text-dark w-75 mx-auto mx-md-0">
                            <div>
                                <div class="text-dark font-weight-bold">
                                    {{ $comment->member->member_type->name ?? '' }} says:</div>
                                <div class=""><i class="fa-sharp fa-solid fa-calendar-days"></i>
                                    {{date('M j, Y - H:i', strtotime($article->created_at ?? now()))}}
                                </div>
                            </div>
                            @php
                            $string = $comment->message ?? '';
                            @endphp
                            {!!$string !!}
                        </div>
                        <div class="">
                            @if (auth('member')->id() == $article->member_id)
                            <a href="{{ route('member.comments.index', ['article' => $article->id, 'comment' => $comment->id]) }}"
                                class="em-btn bg-dark"> View
                                Comment</a>
                            @endif
                        </div>
                    </div>
                    <hr>
                    @empty
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="text-opacity-50" style="opacity: .5;">No comment yet</h3>
                    </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    <div class="card shadow mb-5">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-4">
                    {{ $article->title ?? '' }}
                </h3>
                <div class="">
                    <i class="fa-sharp fa-solid fa-calendar-days"></i>
                    {{date('M j, Y - H:i', strtotime($article->created_at ?? now()))}}
                    | <i class="fa-sharp fa-solid fa-folders"></i> Category: {{
                    $article->article_category->category_name ?? '' }}
                </div>
            </div>
            <div class="">
                <button type="button" class="em-btn bg-dark">Send to review</button>
            </div>
        </div>

        <div class="card-body">
            <h3 class="card-title">Abstract</h3>
            {!! $article->last->abstract ?? '' !!}
            <p></p>
            <a class="em-btn bg-dark" href="{{ $article->last->upload_paper->getUrl() }}" target="_blank">Download
                Uploaded Article</a>
        </div>
    </div>

</div>

@endsection



@section('scripts')
<script>
    $(function () {
    // $('.deleteBtn').click(function (e) {
    //     e.preventDefault();
    //     let thisBtn = $(this);

    //     Swal.fire({
    //         title: 'Deleting.',
    //         text: 'Are you sure ?',
    //         icon: 'question',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Delete'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //         thisBtn.siblings('form').submit();
    //         }
    //     })

    // });

        $('#AddCommentShow').click(function (e) {
            e.preventDefault();

            $('.commentBox').show(0300, ()=> {
                $('#AddCommentShow').hide();
            });


        });

    });
</script>
@endsection
