@extends('layouts.profile')
@section('page-name', 'Article List')

@section('content')
<div id="content" class="p-4 p-md-5">
    <x-profile-bar />

    <div class="card shadow">
        <div class="card-header">
          <h2 class="mb-4">Article List</h2>
        </div>

        <div class="card-body px-4">
            @foreach ($articles as $article)

            <p class="">
                <i class="fa-sharp fa-solid fa-calendar-days"></i>
                {{date('M j, Y - H:i', strtotime($article->created_at ?? now()))}} | <i
                    class="fa-sharp fa-solid fa-folders"></i> Category: {{
                $article->article_category->category_name ?? '' }}
                {{-- | <i class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                    class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition --}}
            </p>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p>{{ $article->title ?? '' }}</p>
                </div>
                <div>
                    <div>
                        @if ($article->comments()->count())

                        <a href="comment.html" class="btn btn-dark comment_btn"
                            style="height: 40px; width: 180px;">
                            View Comment <span class="badge badge-light">4</span>
                        </a>
                        @endif

                        <a href="{{ route('member.articles.edit', $article->id) }}" class="em-btn bg-dark">
                            <i class="fa fa-pencil-square" aria-hidden="true"></i> Edit
                        </a>
                        <button class="em-btn bg-danger deleteBtn">
                            <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                        </button>
                        <form action="{{ route('member.articles.destroy', $article->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
            <hr>


            @endforeach
        </div>
    </div>
</div>

@endsection



@section('scripts')
<script>
    $('.deleteBtn').click(function (e) {
            e.preventDefault();
            let thisBtn = $(this);

            Swal.fire({
              title: 'Deleting.',
              text: 'Are you sure ?',
              icon: 'question',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Delete'
            }).then((result) => {
              if (result.isConfirmed) {
                thisBtn.siblings('form').submit();
              }
            })

        });
</script>
@endsection

