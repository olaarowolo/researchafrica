@extends('layouts.profile')
@section('page-name', $article->title)

@section('content')
    <div id="content" class="p-4 p-md-5">
        <x-profile-bar />


        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger my-2">{{ $error }}</div>
            @endforeach
        @endif

        @php
            $status = $article->last->status;
            $member_id = (int) auth('member')->user()->member_type_id;
            $is_open_comment = $member_id == 1 || $member_id == 6 || $member_id == 3 || $member_id == 2 ? true : false;
        @endphp

        <div class="card shadow mb-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="w-75">
                    <h3 class="mb-4">
                        {{ $article->title ?? '' }}
                    </h3>
                    <div class="">
                        <i class="fa-sharp fa-solid fa-calendar-days"></i>
                        {{ date('M j, Y - H:i', strtotime($article->created_at ?? now())) }}
                        | <i class="fa-sharp fa-solid fa-folders"></i> Category:
                        {{ $article->article_category->category_name ?? '' }} | Status:
                        <x-article-status article="{{ $article->id }}" status="{{ $status }}"
                            member="{{ $article->member_id }}" />
                    </div>
                    <div class="pt-2 d-flex flex-wrap align-items-center gap-2">
                        <strong class="mr-2">Keywords:</strong>
                        <div class="d-flex align-items-center">
                            @foreach ($article->article_keywords as $keyword)
                                <span class="badge bg-dark text-light mr-1">{{ $keyword->title ?? '' }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="">
                        <span class="kb-font-semibold">DOI Link:</span> <a href="{{ $article->doi_link }}" target="_blank"
                            class="text-dark">{{ $article->doi_link ?? '' }}</a>
                    </div>

                </div>

                @if ($article->article_status != 3)

                    @if (auth('member')->user()->member_type_id == 2 && $status == 2)
                        <div class="">
                            <button type="button" class="em-btn bg-dark" data-toggle="modal" data-target="#exampleModal">
                                Send to Reviewer
                            </button>
                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('member.send-review', $article) }}" id="reviewForm"
                                            method="post">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Choose Reviewer</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label class="required"
                                                        for="member_id">{{ trans('cruds.article.fields.member') }}</label>
                                                    <br>
                                                    <select
                                                        class="form-control select2 {{ $errors->has('member_id') ? 'is-invalid' : '' }}"
                                                        name="member_id" id="member_id" style="width: 100%;" required>
                                                        <option value=""> {{ trans('global.pleaseSelect') }}
                                                        </option>
                                                        @foreach ($reviewer1 as $member)
                                                            <option value="{{ $member->id ?? '' }}"
                                                                {{ (old('member_id') ? old('member_id') : $article->member->id ?? '') == $member->id ? 'selected' : '' }}>
                                                                {{ $member->title . '. ' . $member->fullname }} -
                                                                {{ $member->email_address }} </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('member_id'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('member_id') }}
                                                        </div>
                                                    @endif
                                                    <span
                                                        class="help-block">{{ trans('cruds.article.fields.member_helper') }}</span>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="em-btn" data-dismiss="modal">Close</button>
                                                <button type="button" class="em-btn bg-dark reviewSend">Send
                                                    to Reviewer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (auth('member')->user()->member_type_id == 3 && $status == 4)
                        <div class="">
                            <button type="button" class="em-btn bg-dark editorSend">
                                Send back to Editor
                            </button>
                            <form action="{{ route('member.editor.accept.second', $article) }}" method="post">
                                @csrf
                            </form>
                        </div>
                    @endif

                    @if (auth('member')->user()->member_type_id == 2 && $status == 8)
                        <div class="">
                            <button type="button" class="em-btn bg-dark" data-toggle="modal" data-target="#exampleModal">
                                Send to Final Reviewer
                            </button>

                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('member.send-final-review', $article) }}" id="reviewForm"
                                            method="post">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Choose Reviewer</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label class="required"
                                                        for="member_id">{{ trans('cruds.article.fields.member') }}</label>
                                                    <br>
                                                    <select
                                                        class="form-control select2 {{ $errors->has('member_id') ? 'is-invalid' : '' }}"
                                                        name="member_id" id="member_id" style="width: 100%;" required>
                                                        <option value="">
                                                            {{ trans('global.pleaseSelect') }}
                                                        </option>
                                                        @foreach ($reviewer2 as $member)
                                                            <option value="{{ $member->id ?? '' }}"
                                                                {{ (old('member_id') ? old('member_id') : $article->member->id ?? '') == $member->id ? 'selected' : '' }}>
                                                                {{ $member->title . '. ' . $member->fullname }} -
                                                                {{ $member->email_address }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('member_id'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('member_id') }}
                                                        </div>
                                                    @endif
                                                    <span
                                                        class="help-block">{{ trans('cruds.article.fields.member_helper') }}</span>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="em-btn" data-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="button" class="em-btn bg-dark reviewSend">
                                                    Send to Reviewer
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (auth('member')->user()->member_type_id == 6 && $status == 6)
                        <div class="">
                            <button type="button" class="em-btn bg-dark editorSend">
                                Send back to Editor
                            </button>
                            {{-- <form action="{{ route('member.send-editor', $article) }}" method="post">@csrf</form> --}}
                            <form action="{{ route('member.editor.accept.third', $article) }}" method="post">@csrf
                            </form>
                        </div>
                    @endif

                    @if (auth('member')->user()->member_type_id == 2 && $status == 12)
                        <button type="button" class="em-btn btn-dark" data-mdb-toggle="modal"
                            data-mdb-target="#exampleModal">
                            Send to Publisher
                        </button>
                    @endif

                    @if (auth('member')->user()->member_type_id == 5 && $status == 9)
                        <div class="">
                            <button type="button" class="em-btn bg-dark publishArticle">
                                Publish Article
                            </button>
                            <form action="{{ route('member.articles.publish', $article) }}" method="post">@csrf</form>
                        </div>
                    @endif
                @endif

            </div>

            <div class="card-body kb-prose kb-max-w-none">
                <h3 class="card-title">Abstract</h3>
                {!! $article->last->abstract ?? '' !!}
                <p></p>
                <a class="em-btn bg-dark" href="{{ $article->last->upload_paper->getUrl() }}" target="_blank">Download
                    Uploaded Article</a>
                <a class="em-btn bg-dark" href="{{ route('download-review-doc', $article) }}" target="_blank">
                    Download Last Review Document
                </a>
            </div>
        </div>

        <div>

            <hr>

            <div class="card shadow mb-5">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>
                        Comments
                    </h4>
                    @if ($article->article_status != 3 && $is_open_comment)
                        <div class="">
                            <button type="button" class="AddCommentShow em-btn bg-dark">Add
                                Comment</button>
                        </div>
                    @endif
                </div>


                <div class="card-body">
                    <div class="px-4">
                        @if ($is_open_comment)
                            <div class="commentBox" style="display: none">
                                <form action="{{ route('member.comment.store', $article->id) }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Comment</label>
                                        <textarea class="form-control" name="message" id="message" rows="3" placeholder="Message ...."></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="correction_upload" class="form-label">Upload Document</label>
                                        <input type="file" class="form-control" name="correction_upload"
                                            id="correction_upload" placeholder="Choose File" aria-describedby=""
                                            accept=".doc, .docx">
                                        <div id="" class="form-text"></div>
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
                                                {{ auth('member')->id() == $article->member_id
                                                    ? $comment->member->member_type->name ?? ''
                                                    : $comment->member->fullname . '(' . $comment->member->member_type->name . ')' }}
                                                says:</div>
                                            <div class="">
                                                <i class="fa-sharp fa-solid fa-calendar-days"></i>
                                                {{ date('M j, Y - H:i', strtotime($comment->created_at ?? now())) }}
                                            </div>
                                        </div>
                                        @if ($comment->correction_upload)
                                            <div class="ml-2 text-success">
                                                <small>
                                                    <a href="{{ route('download-comment-doc', $comment) }}" class="text-success">
                                                        <i class="fa fa-file" aria-hidden="true"></i>
                                                        Click To Document Attached
                                                    </a>
                                                </small>
                                            </div>
                                        @endif
                                        @php
                                            $string = $comment->message ?? '';
                                        @endphp
                                        {{ $string }}
                                    </div>
                                    <div class="">
                                        @if (auth('member')->id() == $article->member_id && $article->article_status != 3)
                                            <a href="{{ route('member.comments.index', ['article' => $article->id, 'comment' => $comment->id]) }}"
                                                class="em-btn bg-dark"> View
                                                Comment</a>
                                        @endif
                                    </div>
                                </div>
                                <hr>
                            @empty
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="text-opacity-50" style="opacity: .5;">No
                                        comment yet</h3>
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Article
                            Details</h5>
                        <i type="button" class="fas fa-close px-2 rounded bg-danger text-light" data-mdb-dismiss="modal"
                            aria-label="Close"></i>
                    </div>
                    <div class="modal-body">

                        @if (auth('member')->user()->member_type_id == 2 && $status == 12)
                            <form action="{{ route('member.update-amount', $article->id) }}" method="POST"
                                class="row" enctype='multipart/form-data'>
                                @csrf
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="volume" class="form-label">Volume</label>
                                        <input type="text" class="form-control" name="volume"
                                            value="{{ old('volume', $article->volume ?? '') }}" id="volume"
                                            aria-describedby="helpId" placeholder="Article Volume">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="issue_no" class="form-label">Article
                                            Issue No</label>
                                        <input type="text" class="form-control" name="issue_no"
                                            value="{{ old('issue_no', $article->issue_no ?? '') }}" id="issue_no"
                                            aria-describedby="helpId" placeholder="Article Issue No">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="doi_link" class="form-label">DOI
                                            Link</label>
                                        <input type="url" class="form-control" name="doi_link"
                                            value="{{ old('doi_link', $article->doi_link ?? '') }}" id="doi_link"
                                            aria-describedby="helpId"
                                            placeholder="E.g- https://doi.org/10.47366/sabia.v5n1a3">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pdf_doc" class="form-label"> Article PDF Document (required) </label>
                                        <input type="file" class="form-control" name="pdf_doc" value=""
                                            id="pdf_doc" aria-describedby="helpId" placeholder="Add PDF"
                                            accept=".pdf" required>
                                    </div>
                                </div>
                                @if ($article->access_type == 2)
                                    <div class="col-md-6">
                                        <div class="mb-3 form-group">
                                            <label for="amount" class="required">Amount</label>
                                            <input type="number" name="amount"
                                                value="{{ old('amount', $article->amount ?? '') }}" id="amount"
                                                class="form-control" placeholder="Enter Amount" />
                                        </div>
                                    </div>
                                @endif



                                <x-submit-button class="bg-dark my-3 mx-auto right-2" label="Update" />
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @endsection



    @section('scripts')
        <script>
            $(function() {
                $('.reviewSend').click(function(e) {
                    e.preventDefault();
                    let thisBtn = $(this);
                    let reviewForm = $('#reviewForm');


                    if (!$('#member_id').val()) {
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure you want to send to the Reviewer ?',
                        // text: '',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Send',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            reviewForm.submit();
                        }
                    })

                });

                $('.editorSend').click(function(e) {
                    e.preventDefault();
                    let thisBtn = $(this);
                    Swal.fire({
                        title: 'Are you sure you want to send to the Editor ?',
                        // text: '',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Send',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            thisBtn.text('Sending...');
                            thisBtn.siblings('form').submit();
                        }
                    })

                });

                $('.publishArticle').click(function(e) {
                    e.preventDefault();
                    let thisBtn = $(this);

                    Swal.fire({
                        title: 'Are you sure you want to Publish ?',
                        // text: '',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Publish',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            thisBtn.text('Publishing...');
                            thisBtn.siblings('form').submit();
                        }
                    })

                });

                $('.AddCommentShow').click(function(e) {
                    e.preventDefault();

                    $('.commentBox').show(0300, () => {
                        $('.AddCommentShow').hide();
                    });
                });



            });
        </script>
    @endsection
