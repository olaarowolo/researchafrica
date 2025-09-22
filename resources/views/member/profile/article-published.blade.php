@extends('layouts.profile')
@section('page-name', 'Published Article')

@section('styles')
<style>
    a {
        color: #252525;
        text-decoration: none;
    }

    a:hover {
        text-decoration: none;
    }
</style>
@endsection


@section('page-name', 'Profile')

@section('content')


<div id="content" class="p-4 p-md-5">
    <x-profile-bar  />
    <div class="container mt-5">
        <div class="table-responsive">
            <table class="table table-bordered text-center" style="place-content: center;">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Article Title</th>
                        <th scope="col">Author Name</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $Particles = auth('member')->user()->purchasedArticle()->latest()->paginate(3);
                    @endphp
                    @forelse ($Particles as $purchased)
                        @if($article = $purchased->article)
                        <tr class="">
                            <td>{{ $loop->iteration }}</td>
                            <td scope="col">{{ $article->title ?? '' }}</td>
                            <td scope="col">{{ $article->member->fullname }}</td>
                            <td scope="col"><strong>
                                &#x20A6; {{ number_format($article->amount, 0) }} </strong></td>
                            <td scope="col">
                                @if($article->access_type == 1)
                                <div class="em-btn px-3 py-1 bg-dark">
                                    {{\App\Models\Article::ACCESS_TYPE[$article->access_type]}}
                                </div>
                                @else
                                <div class="em-btn px-3 py-1 bg-danger">
                                    {{\App\Models\Article::ACCESS_TYPE[$article->access_type]}}
                                </div>
                                @endif
                            </td>
                            <td scope="col">
                                <a class="em-btn px-3 py-1 text-light bg-dark" href="{{ route('member.view-article', $article->id) }}">View</a>
                            </td>
                        </tr>
                        @endif
                    @empty
                    <tr>
                        <caption>
                            <h3 class="text-center">
                                No Purchased Article
                            </h3>
                        </caption>
                    </tr>

                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="">
            @if ($Particles->isNotEmpty())
                {!! $Particles->links() !!}
            @endif
        </div>
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
