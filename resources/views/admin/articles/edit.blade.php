@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.article.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.articles.update', [$article->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label class="required" for="member_id">{{ trans('cruds.article.fields.member') }}</label>
                    <select class="form-control select2 {{ $errors->has('member') ? 'is-invalid' : '' }}" name="member_id"
                        id="member_id" required>
                        <option value=""> {{ trans('global.pleaseSelect') }} </option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id ?? '' }}"
                                {{ (old('member_id') ? old('member_id') : $article->member->id ?? '') == $member->id ? 'selected' : '' }}>
                                {{ $member->title . '. ' . $member->fullname }} - {{ $member->email_address }} </option>
                        @endforeach
                    </select>
                    @if ($errors->has('member'))
                        <div class="invalid-feedback">
                            {{ $errors->first('member') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.article.fields.member_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="title">{{ trans('cruds.article.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text"
                        name="title" id="title" value="{{ old('title', $article->title) }}" required>
                    @if ($errors->has('title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.article.fields.title_helper') }}</span>
                </div>

                <livewire:edit-category :article="$article" />
                <div class="form-group">
                    <label for="other_authors">{{ trans('cruds.article.fields.other_authors') }}</label>
                    <input class="form-control {{ $errors->has('other_authors') ? 'is-invalid' : '' }}"
                        type="text" name="other_authors" id="other_authors"
                        value="{{ old('other_authors', $article->other_authors) }}">
                    @if ($errors->has('other_authors'))
                        <div class="invalid-feedback">
                            {{ $errors->first('other_authors') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.article.fields.other_authors_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="corresponding_authors">{{ trans('cruds.article.fields.corresponding_authors') }}</label>
                    <input class="form-control {{ $errors->has('corresponding_authors') ? 'is-invalid' : '' }}"
                        type="text" name="corresponding_authors" id="corresponding_authors"
                        value="{{ old('corresponding_authors', $article->corresponding_authors) }}">
                    @if ($errors->has('corresponding_authors'))
                        <div class="invalid-feedback">
                            {{ $errors->first('corresponding_authors') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.article.fields.corresponding_authors_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="institute_organization">{{ trans('cruds.article.fields.institute_organization') }}</label>
                    <input class="form-control {{ $errors->has('institute_organization') ? 'is-invalid' : '' }}"
                        type="text" name="institute_organization" id="institute_organization"
                        value="{{ old('institute_organization', $article->institute_organization) }}">
                    @if ($errors->has('institute_organization'))
                        <div class="invalid-feedback">
                            {{ $errors->first('institute_organization') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.article.fields.institute_organization_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="abstract">{{ trans('cruds.article.fields.abstract') }}</label>
                    <textarea class="form-control textarea {{ $errors->has('abstract') ? 'is-invalid' : '' }}" name="abstract"
                        id="abstract">{!! old('abstract', $article->last->abstract) !!}</textarea>
                    @if ($errors->has('abstract'))
                        <div class="invalid-feedback">
                            {{ $errors->first('abstract') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.article.fields.abstract_helper') }}</span>
                </div>


                <div class="form-group">
                    <label class="" for="upload_paper">{{ trans('cruds.article.fields.upload_paper') }} <span
                            class="text-danger">*</span></label>
                    <br />
                    <input type="file" class="{{ $errors->has('upload_paper') ? 'is-invalid' : '' }}" id="upload_paper"
                        accept=".doc,.docx" name="upload_paper">
                    @if ($errors->has('upload_paper'))
                        <div class="invalid-feedback">
                            {{ $errors->first('upload_paper') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.article.fields.upload_paper_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="" for="pdf_doc">{{ trans('cruds.article.fields.pdf_doc') }} <span
                            class="text-danger">*</span></label>
                    <br />
                    <input type="file" class="{{ $errors->has('pdf_doc') ? 'is-invalid' : '' }}" id="pdf_doc"
                        accept=".pdf" name="pdf_doc">
                    @if ($errors->has('pdf_doc'))
                        <div class="invalid-feedback">
                            {{ $errors->first('pdf_doc') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.article.fields.pdf_doc_helper') }}</span>
                </div>

                <div class="form-group">
                    <div class="d-flex mb-1">
                        @foreach ($article->article_keywords->pluck('title', 'id') as $id => $title)
                            <div name="{{ $id }}" class="badge bg-dark text-light mx-1 cancel"
                                style="cursor: pointer">
                                <span>{{ $title }}</span> <i class="fa fa-close" aria-hidden="true"></i>
                            </div>
                        @endforeach
                    </div>
                    <label for="keywords">{{ trans('cruds.article.fields.keywords') }}</label>
                    <select class="form-control tokenizer {{ $errors->has('keywords') ? 'is-invalid' : '' }}"
                        type="text" name="keywords[]" id="keywords" multiple>
                        @foreach ($article_keywords as $id => $key)
                            <option value="{{ $key }}"> {{ $key }} </option>
                        @endforeach
                    </select>

                    @if ($errors->has('keywords'))
                        <div class="invalid-feedback">
                            {{ $errors->first('keywords') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.article.fields.keywords_helper') }}</span>
                </div>

                <div class="form-group">
                    <label for="access_type" class="required">Access</label>
                    <select class="form-control select2 {{ $errors->has('access_type') ? 'is-invalid' : '' }}"
                        name="access_type" id="access_type" wire:model='access_type'>
                        @foreach (\App\Models\Article::ACCESS_TYPE as $id => $access)
                            <option value="{{ $id }}"
                                {{ old('access_type', $article->access_type == $id ? 'selected' : '') }}>
                                {{ $access }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('access_type'))
                        <div class="invalid-feedback">
                            {{ $errors->first('access_type') }}
                        </div>
                    @endif
                </div>

                <div class="mb-3" style="display: none;" id="amountTab">
                    <label for="amount" class="form-label">Amount </label>
                    <input type="number" class="form-control" name="amount" id="amount"
                        aria-describedby="amounthelpId" placeholder="amount"
                        value="{{ old('amount', $article->amount) }}">
                    <small id="amounthelpId" class="form-text text-muted"></small>
                </div>
                <div class="form-group">
                    <label>{{ trans('cruds.article.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('article_status') ? 'is-invalid' : '' }}"
                        name="article_status" id="article_status">
                        <option value disabled {{ old('article_status', null) === null ? 'selected' : '' }}>
                            {{ trans('global.pleaseSelect') }}</option>
                        @foreach (App\Models\Article::ARTICLE_STATUS as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('article_status', $article->article_status) === (string) $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('article_status'))
                        <div class="invalid-feedback">
                            {{ $errors->first('article_status') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.article.fields.status_helper') }}</span>
                </div>

                <div class="form-group">
                    <label for="publish_date" class="form-label">Published Date </label>
                    <br>
                    <input type="date" class="form-control {{ $errors->has('publish_date') ? 'is-invalid' : '' }}"
                        name="publish_date" value="{{ old('publish_date', $article->publish_date ?? '') }}"
                        id="publish_date" aria-describedby="helpId" placeholder="Article Publish Date">
                    @if ($errors->has('publish_date'))
                        <div class="invalid-feedback">
                            {{ $errors->first('publish_date') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="volume" class="form-label">Volume (required if publishing) </label>
                    <br>
                    <input type="text" class="form-control {{ $errors->has('volume') ? 'is-invalid' : '' }}"
                        name="volume" value="{{ old('volume', $article->volume ?? '') }}" id="volume"
                        aria-describedby="helpId" placeholder="Article Volume">
                    @if ($errors->has('volume'))
                        <div class="invalid-feedback">
                            {{ $errors->first('volume') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="issue_no" class="form-label"> Article Issue No (required if publishing) </label>
                    <input type="text" class="form-control {{ $errors->has('issue_no') ? 'is-invalid' : '' }}"
                        name="issue_no" value="{{ old('issue_no', $article->issue_no ?? '') }}" id="issue_no"
                        aria-describedby="helpId" placeholder="Article Issue No">
                    @if ($errors->has('issue_no'))
                        <div class="invalid-feedback">
                            {{ $errors->first('issue_no') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="doi_link" class="form-label"> DOI Link </label>
                    <input type="url" class="form-control {{ $errors->has('doi_link') ? 'is-invalid' : '' }}"
                        name="doi_link" value="{{ old('doi_link', $article->doi_link ?? '') }}" id="doi_link"
                        aria-describedby="helpId" placeholder="E.g- https://doi.org/10.47366/sabia.v5n1a3">
                    @if ($errors->has('doi_link'))
                        <div class="invalid-feedback">
                            {{ $errors->first('doi_link') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {

            $('.cancel').click(function(e) {
                e.preventDefault();
                let thisCancel = $(this);
                let data = {
                    "article_id": "{{ $article->id }}",
                    "article_keyword_id": thisCancel.attr('name'),
                }

                // thisCancel.remove();




                Swal.fire({
                    title: 'Are you sure ?',
                    //   text: 'text',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('keyword_delete') }}",
                            data: data,
                            dataType: "json",
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            success: function(response) {

                                console.log(response);
                                if (response.status == 200) {
                                    Swal.fire({
                                        title: 'Success',
                                        icon: 'success',
                                        toast: true
                                    })

                                    window.location.reload();
                                }
                            }
                        });
                    }
                })
            });
            @if ($article->access_type === 2)
                $('#amountTab').show(0200);
            @endif

            $('#access_type').change(function(e) {
                e.preventDefault();
                var accessType = $(this).children('option:selected').val();


                if (accessType == 2) {

                    $('#amountTab').show(0200);

                } else {
                    $('#amountTab').hide(0200);
                    $('#amount').val(null);

                }


            });

            $(".tokenizer").select2({
                tags: true,
                tokenSeparators: [',', ';']
            })
        });
    </script>
@endsection
