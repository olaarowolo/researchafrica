@extends('layouts.profile')

@section('page-name', 'Create')



@section('content')


<!-- Page Content  -->
<div id="content" class="p-4 p-md-5">
    <x-profile-bar />

    <x-article-create :categories="$categories" :keywords="$keywords"/>


</div>


@endsection


@section('scripts')
<script>
    $(function () {
            // $(".tokeni

        });
</script>
@endsection
