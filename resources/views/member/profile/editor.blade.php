@extends('layouts.profile')


@section('page-name', 'Profile')

@section('content')


    <style>
        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }
    </style>

    <div id="content" class="p-4 p-md-5">
        <x-profile-bar />


        <div class="card shadow">

            <div class="card-body px-4 mt-4">
                <!-- Tabs navs -->
                <ul class="nav nav-tabs  mb-3" id="ex1" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active text-dark " id="ex2-tab-1" data-mdb-toggle="tab" href="#ex2-tabs-1"
                            role="tab" aria-controls="ex2-tabs-1" aria-selected="true">
                            <span>New Article</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-dark " id="ex2-tab-2" data-mdb-toggle="tab" href="#ex2-tabs-2"
                            role="tab" aria-controls="ex2-tabs-2" aria-selected="false">
                            <span>Processing</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-dark" id="ex2-tab-3" data-mdb-toggle="tab" href="#ex2-tabs-3" role="tab"
                            aria-controls="ex2-tabs-3" aria-selected="false">
                            Accepted Article
                        </a>
                    </li>
                </ul>
                <!-- Tabs navs -->

                <!-- Tabs content -->
                <div class="tab-content" id="ex2-content">
                    <div class="tab-pane fade show active" id="ex2-tabs-1" role="tabpanel" aria-labelledby="ex2-tab-1">

                        @forelse ($newArticles as $article)
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fa-sharp fa-solid fa-calendar-days"></i>

                                    {{ date('M j, Y - H:i', strtotime($article->created_at ?? now())) }}
                                    <br <p class="">
                                    <strong><span
                                            style="color:red; animation: blink 1s infinite;">{{ $article->last->status == 7 ? 'Returning Article' : (is_null($article->last->comment_id) ? 'New Article' : 'Modify Article') }}</span></strong>
                                    | Submission to: <i class="fa-sharp fa-solid fa-folders"></i> <strong><span
                                            style="color:red;"> {{ $article->article_category->category_name ?? '' }}
                                        </span></strong>> {{ $article->journal_category->category_name ?? '' }}

                                    {{-- | <i class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                                    class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition --}}
                                    </p>
                                    <p style="font-size: 20px;"> {{ $article->title ?? '' }} </p>

                                    <strong>Corresponding Author</strong>: {{ $article->member->fullname ?? '' }}
                                    @if ($article->member->fullname)
                                        <sup>
                                            <a href="https://orcid.org/{{ $article->member?->first()?->orchid_id }}"
                                                style="display: inline;" target="_blank">
                                                <img src="https://orcid.org/assets/vectors/orcid.logo.icon.svg"
                                                    alt="ORCID Logo" style="width: 13px; height: 13px; display: inline;">
                                            </a>
                                        </sup>
                                    @else
                                        <i>None</i>
                                    @endif
                                </div>
                                <div>
                                    <div class="d-flex align-items-center">

                                        {{-- <a href="{{ route('member.comments.index', [$article->id]) }}"
                                    class="em-btn btn-dark">
                                    Comment
                                </a> --}}

                                        @if (is_null($article->editor_accept->member_id))
                                            <button class="em-btn bg-success acceptBtn mr-md-2">
                                                Accept
                                            </button>
                                            <form class="acceptForm"
                                                action="{{ route('member.editor.accept', $article->id) }}" method="post">
                                                @csrf</form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <hr>

                        @empty

                            <h3 class="text-danger my-2 text-center">No New Article</h3>
                        @endforelse
                    </div>
                    <div class="tab-pane fade" id="ex2-tabs-2" role="tabpanel" aria-labelledby="ex2-tab-2">
                        @foreach ($processing as $article)
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="">
                                        <i class="fa-sharp fa-solid fa-calendar-days"></i>
                                        {{ date('M j, Y - H:i', strtotime($article->created_at ?? now())) }} | <i
                                            class="fa-sharp fa-solid fa-folders"></i> Category:
                                        {{ $article->article_category->category_name ?? '' }}
                                    </p>
                                    <p> {{ $article->title ?? '' }} </p>

                                </div>
                                <div>
                                    <div class="">

                                        <a href="{{ route('member.articles.show', $article->id) }}" class="em-btn bg-dark">
                                            View Article
                                        </a>
                                        <div class="text-center">
                                            <small>{{ $article->comments->count() }} Comment(s)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endforeach

                    </div>
                    <div class="tab-pane fade" id="ex2-tabs-3" role="tabpanel" aria-labelledby="ex2-tab-3">
                        @foreach ($processed as $article)
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="">
                                        <i class="fa-sharp fa-solid fa-calendar-days"></i>
                                        {{ date('M j, Y - H:i', strtotime($article->created_at ?? now())) }} | <i
                                            class="fa-sharp fa-solid fa-folders"></i> Category:
                                        {{ $article->article_category->category_name ?? '' }}
                                    </p>
                                    <p> {{ $article->title ?? '' }} </p>

                                </div>
                                <div>
                                    <div class="">

                                        <a href="{{ route('member.articles.show', $article->id) }}" class="em-btn bg-dark">
                                            View Article
                                        </a>
                                        <div class="text-center">
                                            <small>{{ $article->comments->count() }} Comment(s)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endforeach

                    </div>
                </div>
                <!-- Tabs content -->

            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Comment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <textarea class="required form-control" id="template-contactform-message" name="template-contactform-message"
                            rows="10" cols="30"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            style="height: 40px; width: 180px;">Close</button>
                        <button type="button" class="btn btn-primary" style="height: 40px; width: 180px;">Save
                            changes</button>
                    </div>
                </div>
            </div>
        </div>


    </div>

@endsection


@section('scripts')
    <script>
        $(function() {
            $('.deleteBtn').click(function(e) {
                e.preventDefault();
                let thisBtn = $(this);

                Swal.fire({
                    title: 'Deleting.',
                    text: 'Are you sure ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        thisBtn.siblings('form').submit();
                    }
                })

            });

            $('.acceptBtn').click(function(e) {
                e.preventDefault();
                thisBtn = $(this);

                Swal.fire({
                    title: 'Are You sure?',
                    text: 'You won\'t be able to revert this action?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Accept'
                }).then((result) => {
                    if (result.isConfirmed) {
                        thisBtn.siblings('form').submit();
                    }
                })
            });

        });
    </script>
@endsection
