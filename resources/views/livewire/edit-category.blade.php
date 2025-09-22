<div class="{{ $row ? 'col-md-12' : '' }}">
    <div class="form-group">
        <label class="required" for="article_category_id"> Category </label>
        <select class="form-control" id="article_category_id" name="article_category_id" wire:model="category_id">
            <option>{{ __('Select Category') }}</option>

            @foreach ($categories as $id => $label)
                <option value="{{ $id }}"
                    {{ old('article_category_id', $category_id) == $id ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <span class="text-danger">
            @error('article_category_id')
                {{ $message }}
            @enderror
        </span>
    </div>

    <div class="form-group">
        <label class="required" for="article_sub_category_id"> Journal </label>
        <select class="form-control" id="article_sub_category_id" name="article_sub_category_id">
            @if ($sub_categories->isEmpty())
                <option value="" selected>{{ __('Select Article Category First') }}</option>
            @endif
            @foreach ($sub_categories as $id => $label)
                <option value="{{ $id }}"
                    {{ old('article_sub_category_id', $sub_category_id) == $id ? 'selected' : '' }}>
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
