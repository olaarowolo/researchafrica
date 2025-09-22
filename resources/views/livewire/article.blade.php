


@push('js')
<script>
    document.addEventListerner("Livewire:load", () => {
        let el = $('.tokenizer')

        initSelect();

        Livewire.hook("message.precessed", (message, component) => {
            initSelect()
        })

        // Livewire.on("setArticleKeywords", values => {
        //     el.val
        // })

        el.on('change', function(e){
            @this.set("articleKeywords", el.select2('val'))
        })

        function initSelect() {
            el.select2({
                placeholder: "Select Article Keywords",
                allowClear: !el.attr('required'),
                tags: true,
                tokenSeparators: [',', ' '],
            });
         }
    })
</script>
@endpush
