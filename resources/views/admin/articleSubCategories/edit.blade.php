@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Edit {{ trans('cruds.articleCategory.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.article-sub-categories.update", $articleCategory->id) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div class="form-group">
                <label for="parent_id" class="form-label required">Journal Category</label>
                <select class="form-control" name="parent_id" id="parent_id">
                    @foreach ($articleCategories as $id => $category)
                        <option value="{{ $id }}" {{old('parent_id', $id) == $id ? 'selected' : ''}}>{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="required" for="category_name">{{ trans('cruds.articleCategory.fields.category_name') }}</label>
                <input class="form-control {{ $errors->has('category_name') ? 'is-invalid' : '' }}" type="text" name="category_name" id="category_name" value="{{ old('category_name', $articleCategory->category_name) }}" required>
                @if($errors->has('category_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('category_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.articleCategory.fields.category_name_helper') }}</span>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <div class="mb-3">
                      <label for="issn" class="form-label">ISSN No.</label>
                      <input type="text"
                        class="form-control" name="issn" value="{{ old('issn', $articleCategory->issn ?? '') }}" id="issn" aria-describedby="helpId" placeholder="ISSN Number">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                      <label for="online_issn" class="form-label">Online ISSN No.</label>
                      <input type="text"
                        class="form-control" name="online_issn" value="{{ old('online_issn', $articleCategory->online_issn ?? '') }}" id="online_issn" aria-describedby="helpId" placeholder="Online ISSN Number">
                    </div>
                </div> 
                <div class="col-md-4">
                    <div class="mb-3">
                      <label for="doi_link" class="form-label">DOI Link</label>
                      <input type="url"
                        class="form-control" name="doi_link" value="{{ old('doi_link', $articleCategory->doi_link ?? '') }}" id="doi_link" aria-describedby="helpId" placeholder="E.g- https://doi.org/10.47366/sabia.v5n1a3">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                      <label for="journal_url" class="form-label">Journal URL</label>
                      <input type="url"
                        class="form-control" name="journal_url" value="{{ old('journal_url') }}" id="journal_url" aria-describedby="helpId" placeholder="e.g- https://researchafricapublications.com/...">
                    </div>
                </div>
           
            {{-- Start Upload Cover  --}}
               </div>
            <div class="form-group">
                <label class="required" for="cover_image">Journal Cover Image</label>
                <div class="drop-zone">
                    <input class="form-control {{ $errors->has('cover_image') ? 'is-invalid' : '' }}" type="file" name="cover_image" id="cover_image" value="{{ old('cover_image', '') }}" accept="image/*" style="display: none;">
                    <div class="drop-zone-text">
                        <i class="fas fa-cloud-upload-alt"></i>
                        Drag and drop files here or click to select
                    </div>
                </div>
                @if($errors->has('cover_image'))
                    <div class="invalid-feedback">
                        {{ $errors->first('cover_image') }}
                    </div>
                @endif
            </div>
            {{-- End Upload Cover  --}}

            {{-- Upload CSS  --}}

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-**************" crossorigin="anonymous" />

            {{-- End Upload CSS  --}}
            <script>
                const dropZone = document.querySelector('.drop-zone');
            
                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropZone.classList.add('drop-zone-hover');
                });
            
                dropZone.addEventListener('dragleave', () => {
                    dropZone.classList.remove('drop-zone-hover');
                });
            
                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('drop-zone-hover');
                    const fileInput = document.querySelector('#cover_image');
                    fileInput.files = e.dataTransfer.files;
                });
            
                const fileInput = document.querySelector('#cover_image');
                dropZone.addEventListener('click', () => {
                    fileInput.click();
                });
            
                fileInput.addEventListener('change', () => {
                    const files = fileInput.files;
                    // Handle the selected files here, for example, display file names or trigger form submission.
                });
            </script>

            
            {{-- End Upload Java Script  --}}

            {{-- <div class="form-group">
                <label class="required" for="cover_image">Upload Cover Image</label>
                <input class="form-control {{ $errors->has('cover_image') ? 'is-invalid' : '' }}" type="file" name="cover_image" id="cover_image" value="{{ old('cover_image', '') }}"  accept="image/*">
                @if($errors->has('cover_image'))
                    <div class="invalid-feedback">
                        {{ $errors->first('cover_image') }}
                    </div>
                @endif --}}
                
                <div class="mb-3">
                    <label for="description" class="form-label required">Description</label>
                    <textarea class="form-control textarea" name="description" id="description">{{ old('category_name', $articleCategory->description) }}</textarea>
                  </div>
      
                  <div class="mb-3">
                    <label for="aim_scope" class="form-label required">Aim and Scope</label>
                    <textarea class="form-control textarea" name="aim_scope" id="aim_scope">{{ old('category_name', $articleCategory->aim_scope) }}</textarea>
                  </div>
      
                  <div class="mb-3">
                    <label for="editorial_board" class="form-label required">Editorial Board</label>
                    <textarea class="form-control textarea" name="editorial_board" id="editorial_board">{{ old('category_name', $articleCategory->editorial_board) }}</textarea>
                  </div>
      
                  <div class="mb-3">
                    <label for="submission" class="form-label required">Submission</label>
                    <textarea class="form-control textarea" name="submission" id="submission">{{ old('category_name', $articleCategory->submission) }}</textarea>
                  </div>
      
                  <div class="mb-3">
                    <label for="subscribe" class="form-label required">Subscribe</label>
                    <textarea class="form-control textarea" name="subscribe" id="subscribe">{{ old('category_name', $articleCategory->subscribe) }}</textarea>
                  </div>
      
                  <div class="form-group">
                      <label>{{ trans('cruds.articleCategory.fields.status') }}</label>
                      <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                          <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                          @foreach(App\Models\ArticleCategory::STATUS_SELECT as $key => $label)
                              <option value="{{ $key }}" {{ old('status', $articleCategory->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                          @endforeach
                      </select>
                      @if($errors->has('status'))
                          <div class="invalid-feedback">
                              {{ $errors->first('status') }}
                          </div>
                      @endif
                      <span class="help-block">{{ trans('cruds.articleCategory.fields.status_helper') }}</span>
                  </div>
                  <div class="form-group">
                      <button class="btn btn-danger" type="submit">
                          {{ trans('global.save') }}
                      </button>
                  </div>
            </div>

           
        </form>
    </div>
</div>



@endsection
