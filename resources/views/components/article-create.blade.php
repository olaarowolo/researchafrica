<div>
    <div class="card shadow">
        <div class="card-header">
            <h2 class="mb-4">Add New Article</h2>
        </div>


        <div class="card-header px-4">
            <main class="postcontent col-lg-12">
                <div class="form-widget">

                    <div class="form-result"></div>

                    <form class="mb-4" method="post" action="{{ route('member.articles.store') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="form-process">
                            <div class="css3-spinner">
                                <div class="css3-spinner-scaler"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="title" class="required"> Article Title </label>
                                <input type="text" id="title" value="{{ old('title') }}" name="title"
                                    class="form-control required">
                                <span class="text-danger">
                                    @error('title')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>



                            <div class="w-100"></div>
                            <div class="col-md-12">


                                <div class="form-group">
                                    <label class="required" for="article_category_id"> Category </label>
                                    <select class="form-control" id="article_category_id" name="article_category_id">
                                        <option value="" selected disabled>{{ __('Select Category') }}</option>
                                        @foreach ($categories as $id => $label)
                                            <option value="{{ $id }}"
                                                {{ old('article_category_id') == $id ? 'selected' : '' }}>
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
                                    <select class="form-control" id="article_sub_category_id"
                                        name="article_sub_category_id">
                                        <option value="" selected>{{ __('Select Article Category First') }}
                                        </option>
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
                                    Other Authors
                                </label>
                                <input type="text" id="other_authors" name="other_authors"
                                    value="{{ old('other_authors') }}" class="form-control required" />
                                <span class="text-danger">
                                    @error('other_authors')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="corresponding_authors">
                                    Corresponding Author
                                </label>
                                <input type="text" id="corresponding_authors" name="corresponding_authors"
                                    value="{{ old('corresponding_authors') }}" class="form-control required" />
                                <span class="text-danger">
                                    @error('corresponding_authors')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>



                            <div class="col-md-12 form-group">
                                <label for="institute_organization">Institute/Organization</label>
                                <input type="text" id="institute_organization" name="institute_organization"
                                    value="{{ old('institute_organization') }}" class="form-control required">
                                <span class="text-danger">
                                    @error('institute_organization')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="w-100"></div>

                            <div class="col-md-12 form-group">
                                <label for="abstract" class="required">Abstract</label>
                                <textarea class="required form-control textarea" id="abstract" rows="30" name="abstract">{{ old('abstract') }}</textarea>
                                <span class="text-danger">
                                    @error('abstract')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>


                            {{-- <div class="col-md-12">
                                <div class="mb-3">
                                  <label for="publish_date" class="form-label">Publish Date</label>
                                  <input type="text"
                                    class="form-control datepicker" readonly name="publish_date" id="publish_date" aria-describedby="helpId" placeholder="Pick Date">
                                <span class="text-danger">@error('publish_date') {{ $message }} @enderror</span>

                                </div>
                            </div> --}}



                            <div class="col-md-12 form-group">
                                <label for="upload_paper" class="required">Upload Paper (doc, docx) </label>
                                <input type="file" id="upload_paper" name="upload_paper" accept=".doc, .docx"
                                    class="form-control required">
                                <span class="text-danger">
                                    @error('upload_paper')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-12">

                                <div wire:ignore class="form-group">
                                    <label for="articleKeywords" class="required">Keywords</label>
                                    <select
                                        class="form-control tokenizer {{ $errors->has('articleKeywords') ? 'is-invalid' : '' }}"
                                        name="articleKeywords[]" id="articleKeywords" multiple>
                                        @foreach ($keywords as $id => $keyword)
                                            <option value="{{ $keyword }}">{{ $keyword }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('articleKeywords'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('articleKeywords') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="access_type" class="required">Access</label>
                                    <select
                                        class="form-control select2 {{ $errors->has('access_type') ? 'is-invalid' : '' }}"
                                        name="access_type" id="access_type">
                                        <option value="">Select Access</option>
                                        @foreach (\App\Models\Article::ACCESS_TYPE as $id => $access)
                                            <option value="{{ $id }}"
                                                {{ old('access_type') == $id ? 'selected' : '' }}>{{ $access }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('articleaccess_type'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('articleaccess_type') }}
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
            $(function() {
                $('#article_category_id').change(function(e) {
                    e.preventDefault();
                    let thisVal = $(this).val();

                    $.ajax({
                        type: "get",
                        url: "/get-journal/" + thisVal,
                        success: function(response) {
                            if (response) {
                                $('#article_sub_category_id').html(response.data);
                            }
                        }
                    });

                });


                $(".tokenizer").select2({
                    tags: true,
                    tokenSeparators: [','],
                    placeholder: "Select Multiple Keywords"
                })
            });
        </script>
    @endpush
</div>
