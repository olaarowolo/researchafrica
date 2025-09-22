<nav id="sidebar">
    @php
    $user = auth('member')->user();
    @endphp
    <div class="p-4 pt-5">
        <div class="text-center mb-4">
            <a href="#" class="img logo rounded-circle mb-5"
                style="background-image: url({{ auth('member')->user()->profile_picture ? auth('member')->user()->profile_picture->getUrl() : '/lib/avata.png' }});"></a>
            <h5 class="text-white text-center">{{ $user->fullname ?? '' }}</h5>
            <p class=" mb-1 text-white text-center">{{ $user->country->name }}, {{ $user->state->name ?? '' }}</p>

            <a href="{{ route('member.profile.edit') }}" class="em-btn bg-danger px-3 py-0 mb-3">Edit
                Profile</a>
            @if($user->member_type_id == 4)
            <button class="em-btn bg-primary px-3 py-0" id="becomeAuthor"> Become an author </button>
            <form action="{{route('member.become-author')}}" method="post">@csrf</form>
            @endif
        </div>
        {{-- <p class=" mb-4 text-white text-center">Ikeja</p> --}}
        <ul class="list-unstyled components mb-5">
            <li>
                <a href="{{ route('home') }}"><i class="fa-sharp fa-solid fa-house"></i> Journals Website</a>
            </li>
            {{-- <li class="active">
                <a href="{{ route('member.profile') }}"><i class="fa-sharp fa-solid fa-users"></i> Profile</a>
            </li> --}}
            @if($user->member_type_id == 1)
            @endif
            @if($user->member_type_id == 2)
    
            <li>
                <a href="{{ route('member.profile') }}"><i class="fa-sharp fa-solid fa-list"></i> Editor's Page</a>
            </li>
            @endif

            @if($user->member_type_id == 2)
            @endif
            @if($user->member_type_id == 1)
            <li>
                <a href="{{ route('member.profile') }}"> <i class="fa-sharp fa-solid fa-repeat"></i>
                    Article Under
                    Review</a>
                </li>
                @endif
                @if($user->member_type_id == 3)
                <li>
                    <a href="{{ route('member.profile') }}"><i class="fa-sharp fa-solid fa-list"></i> Reviewer's Page</a>
                </li>
                @endif
                @if($user->member_type_id == 6)
                <li>
                    <a href="{{ route('member.profile') }}"><i class="fa-sharp fa-solid fa-list"></i> Reviewer's Page</a>
                </li>
                @endif
                <li>
                    <a href="{{ route('member.purchased-article') }}">
                        <i class="fa fa-book" aria-hidden="true"></i>
                        Purchased Article
                    </a>
                </li>
                <li>
                    <a href="{{ route('member.view-bookmark') }}">
                        <i class="fa fa-bookmark" aria-hidden="true"></i>
                        Bookmarks
                    </a>
                </li>
                <li>
                    <a href="{{ route('member.profile.edit') }}"><i class="fa-sharp fa-solid fa-user"></i> Edit Profile</a>
            </li>
            {{-- <li>
                <a href="{{ route('member.profile.edit') }}"><i class="fa-sharp fa-solid fa-user"></i> Security</a>
            </li> --}}
            {{-- Author --}}
            {{-- @if($user->member_type_id == 3)
            <li>
                <a href="{{ route('member.profile') }}"><i class="fa-sharp fa-solid fa-list"></i> Reviewer's Page</a>
            </li>
            <li>
            @endif --}}
                <a href="javascript:void(0)" id="logoutLink"><i class="fa-sharp fa-solid fa-right-from-bracket"></i> Log
                    Out</a>
                <form action="{{ route('member.log-out') }}" method="post">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</nav>

@push('component')

<script>
    $('#logoutLink').click(function (e) {
        e.preventDefault();
        let thisLink = $(this);

        Swal.fire({
          title: 'Logout?',
          text: 'Are you sure ?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Logout'
        }).then((result) => {
          if (result.isConfirmed) {
            thisLink.siblings('form').submit();
          }
        })
    });

    $(function () {
        $('#becomeAuthor').click(function (e) {
            e.preventDefault();
            let thisBtn = $(this);
            Swal.fire({
              title: 'About to become an author?',
            //   text: '',
              icon: 'question',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Confirm'
            }).then((result) => {
              if (result.isConfirmed) {
                thisBtn.siblings('form').submit();
              }
            })
        });
    });
</script>

@endpush

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-MK4S7W7B2F"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-MK4S7W7B2F');
</script>