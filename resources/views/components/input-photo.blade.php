@php
$name = Str::snake(strtolower($name));
$photo_url = $photo ?? null
@endphp
<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        <p>Choose {{ Str::title(str_replace('_', ' ', $name)) }}</p>
        <div class="mb-3 gallery" style="cursor: pointer">
            <div style="max-width: 150px;max-height:150px">
                <img src="{{ $photo_url == '' || $photo_url == null  ? '/lib/avata.png' : $photo_url }}" alt="{{ $photo_url ?? 'No image' }}"  class="w-100 h-auto rounded-3 shadow">
            </div>
        </div>

        <input type="file" class="form-control" hidden name="{{ $name }}" id="{{ $name }}" placeholder="select File"
            aria-describedby="fileHelpId">
    </label>
</div>
<span>@error($name) {{ $message }} @enderror</span>


@push('components')
<script>
    $('#{{ $name }}').on('change', function() {
        let thisInput = $(this);
        let gallery = thisInput.siblings('div.gallery');
        gallery.children().hide();
        $('#text').removeClass('d-none').addClass('d-block');
        imagesPreview(this, gallery);
    });


    var imagesPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            var filesAmount = input.files.length;
            // console.log(filesAmount);
            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    $($.parseHTML('<div class="border shadow rounded-3 preview " style="height: 150px;width:150px;background-size: 100% 100%;background-position:cover;">')).css('background-image', 'url("'+event.target.result+'")').appendTo(placeToInsertImagePreview);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    };
</script>
@endpush
