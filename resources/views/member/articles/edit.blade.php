@extends('layouts.profile')

@section('page-name', 'Edit')

@section('content')


<!-- Page Content  -->
<div id="content" class="p-4 p-md-5">
    <x-profile-bar />
    <x-article-edit :article="$article" :keywords="$keywords" :categories="$categories" />
</div>


@endsection


@section('scripts')
<script>
</script>
@endsection