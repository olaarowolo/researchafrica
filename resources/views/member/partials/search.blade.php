<div class="svgBack py-3">
    <div class="container p-2">
        <div class=" align-items-baseline">
            <div class="row justify-content-center">
                <form class="rounded col-md-9" action="/search"
                    style="display: flex;align-items: center;gap: 2px;">
                    <input type="text"
                        value="{{ request()->has('q') ? request('q') : ''  }}"
                        class="w-100 px-2 fs-4 py-2 focus:kb-ring-0 rounded"
                        placeholder="Search...." name="q" style="border:2px solid gray;">
                    <button type="submit"
                        class="em-btn bg-dark text-white d-flex align-items-center">
                        <i class="fa fa-search fa-lg"></i>
                        Search
                    </button>
                </form>
            </div>

            <div class="d-flex justify-content-center">
                <a href="{{route('member.advance-search')}}" role="button"
                    class="text-light font-weight-bold fs-5 kb-underline em-btn bg-dark">Try
                    Advanced
                    Search</a>
            </div>

        </div>
    </div>
</div>
