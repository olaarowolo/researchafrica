<div class="">
    
    <div class="card shadow">
        <div class="card-header">
            <h2 class="mb-4">Edit Article</h2>
        </div>

        <div class="card-body px-4">
            @if ($errors->any())
            @foreach ($errors->all() as $error)
            <div class="p-2 bg-danger my-2 text-light rounded-2">
                {{ $error }}
            </div>
            @endforeach
            @endif
            <main class="postcontent col-lg-12">



                <div class="form-widget">

                    <div class="form-result"></div>

                    <form class="mb-4" action="{{ route('member.articles.update', $article->id) }}" method="post"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf

                        <div class="form-process">
                            <div class="css3-spinner">
                                <div class="css3-spinner-scaler"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="title" class="required"> Article Title </label>
                                <input type="text" id="title" value="{{ old('title', $article->title ?? '') }}"
                                    name="title" class="form-control required">
                                <span class="text-danger">@error('title') {{ $message }} @enderror</span>
                            </div>



                            <div class="w-100"></div>
                            
                            <div class="col-md-12">


                                <div class="form-group">
                                    <label class="required" for="article_category_id"> Category </label>
                                    <select class="form-control" id="article_category_id" name="article_category_id" >
                                        <option value="" selected disabled>{{ __('Select Category') }}</option>
                                        @foreach ($categories as $id => $label)
                                        <option value="{{ $id }}" {{ old('article_category_id', $article->article_category_id)==$id ? 'selected' : '' }}>
                                            {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        @error('article_category_id')
                                        {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                            </div>
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label class="required" for="article_sub_category_id"> Journal </label>
                                    <select   class="form-control" id="article_sub_category_id" name="article_sub_category_id">
                                        <option value="" {{ old('article_sub_catgory_id', $article->article_sub_catgory_id) == null ? 'selected' : '' }}>{{ __('Select Article Category First') }}</option>
                                        @foreach ($journals as $id => $label)
                                        <option value="{{ $id }}" {{ old('article_sub_category_id', $article->article_sub_category_id)==$id ? 'selected' : '' }}>
                                            {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        @error('article_sub_category_id')
                                        {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>


                            <div class="col-md-12 form-group">
                                <label for="other_authors">
                                    other Author
                                </label>
                                <input type="text" id="other_authors" name="other_authors"
                                    value="{{ old('other_authors', $article->other_authors ?? '') }}"
                                    class="form-control required" />
                                <span class="text-danger">@error('other_authors') {{ $message }}
                                    @enderror</span>
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="corresponding_authors">
                                    Corresponding Author
                                </label>
                                <input type="text" id="corresponding_authors" name="corresponding_authors"
                                    value="{{ old('corresponding_authors', $article->corresponding_authors ?? '') }}"
                                    class="form-control required" />
                                <span class="text-danger">@error('corresponding_authors') {{ $message }}
                                    @enderror</span>
                            </div>


                            <div class="col-md-12 form-group">
                                <label for="institute_organization">Institute/Organization</label>
                                <input type="text" id="institute_organization" name="institute_organization"
                                    value="{{ old('institute_organization', $article->institute_organization ?? '') }}"
                                    class="form-control required">
                                <span class="text-danger">@error('institute_organization') {{ $message }}
                                    @enderror</span>
                            </div>
                            <div class="w-100"></div>

                            <div class="col-md-12 form-group">
                                <label for="abstract" class="required">Abstract</label>
                                <textarea class="required form-control textarea" id="abstract" rows="30"
                                    name="abstract">{{ old('abstract', $article->last->abstract ?? '') }}</textarea>
                                <span class="text-danger">@error('abstract') {{ $message }} @enderror</span>
                            </div>

                            
                            <div class="col-md-12">
                                <div class="mb-3">
                                  <label for="publish_date" class="form-label">Publish Date</label>
                                  <input type="text"
                                    class="form-control datepicker" readonly name="publish_date" id="publish_date" aria-describedby="helpId" value="{{ old('publish_date', $article->publish_date ?? '') }}" placeholder="Pick Date">
                                <span class="text-danger">@error('publish_date') {{ $message }} @enderror</span>

                                </div>
                            </div>


                            <div class="col-md-12 form-group">
                                <label for="upload_paper" class="required">Upload Paper (doc, docx) </label>
                                <input type="file" id="upload_paper" name="upload_paper" accept=".doc, .docx"
                                    class="form-control required">

                                @if ($errors->has('upload_paper'))
                                <span class="text-danger">@error('upload_paper') {{ $message }} @enderror</span>
                                @else
                                <a href="{{ $article->last->upload_paper->getUrl() }}"
                                    class="text-dark font-weight-bold">Click to get document</a>
                                @endif
                            </div>

                            <div class="col-md-12">

                                <div class="form-group">
                                    <label for="article_keyword_id" class="required">Keywords</label>

                                    <div class="d-flex mb-1">
                                        @foreach ($article->article_keywords->pluck('title', 'id') as $id => $title)
                                        <div name="{{ $id }}" class="badge bg-dark text-light mx-1 cancel"
                                            style="cursor: pointer">
                                            <span>{{ $title }}</span> <i class="fa fa-close" aria-hidden="true"></i>
                                        </div>
                                        @endforeach
                                    </div>
                                    <select
                                        class="form-control tokenizer {{ $errors->has('article_keyword_id') ? 'is-invalid' : '' }}"
                                        name="article_keyword_id[]" id="article_keyword_id" multiple>
                                        @foreach ($keywords as $id => $keyword)
                                        <option value="{{ $id }}">{{ $keyword }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('article_keyword_id'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('article_keyword_id') }}
                                    </div>
                                    @endif
                                    <span class="help-block"> Select multiple keywords </span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="access_type" class="required">Access</label>
                                    <select
                                        class="form-control select2 {{ $errors->has('access_type')? 'is-invalid' : '' }}"
                                        name="access_type" id="access_type" wire:model='access_type'>
                                        @foreach (\App\Models\Article::ACCESS_TYPE as $id => $access)
                                        <option value="{{ $id }}" {{ old('access_type', $article->access_type) == $id ? 'selected' : '' }}>{{ $access }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('access_type'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('access_type') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <x-submit-button class="btn-dark mb-3" label="Submit" />




                    </form>
                </div>

            </main>
        </div>
    </div>

    @push('component')
        <script>
            
            $(function () {
                $('.cancel').click(function (e) {
                    e.preventDefault();
                    let thisCancel = $(this);
                    let data = {
                        "article_id" : "{{ $article->id }}",
                        "article_keyword_id" : thisCancel.attr('name'),
                    }

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
                        success: function (response) {

                            console.log(response);
                            if(response.status == 200){
                                Swal.fire({
                                    title: 'Success',
                                    icon: 'success',
                                    toast:true
                                })

                                window.location.reload();
                            }
                        }
                        });
                    }
                    })
                });

                $(".tokenizer").select2({
                    tags: true,
                    tokenSeparators: [',', ' ']
                })

                
                $('#article_category_id').change(function (e) {
                    e.preventDefault();
                    let thisVal = $(this).val();

                    $.ajax({
                        type: "get",
                        url: "/get-journal/"+thisVal,
                        success: function (response) {
                            if(response){
                                $('#article_sub_category_id').html(response.data);
                            }
                        }
                    });

                });
            });
        </script>
    @endpush
</div>