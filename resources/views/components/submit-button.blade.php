@php
$classNames = $class ?? 'bg-dark';
@endphp
<button class="em-btn btn-lg {{$classNames}} text-center m-0 submitBtn" id="login-form-submit"
 type="button">
    <span>{{ $label ?? 'Submit' }}</span>

    <div style="display: none;">
        <div class="d-flex gap-1 align-items-center">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Loading...
        </div>
    </div>


</button>


@push('component')

<script>
    $(function () {

        $('.submitBtn').click(function (e) {
            e.preventDefault();

            // This Button
            let thisBtn = $(this);

            const submitBtn = thisBtn.parent('form').submit();

            if(submitBtn){
                thisBtn.attr('disabled', true).css('cursor', 'not-allowed');
                thisBtn.children('span').hide();
                thisBtn.children('div').css('display', 'block');
            }

        });
    });
</script>

@endpush
