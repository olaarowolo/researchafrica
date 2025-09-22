@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.article.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.articles.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.id') }}
                            </th>
                            <td>
                                {{ $article->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.member') }}
                            </th>
                            <td>
                                {{ $article->member->email_address ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.title') }}
                            </th>
                            <td>
                                {{ $article->title }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.article_category') }}
                            </th>
                            <td>
                                {{ $article->article_category->category_name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.author_name') }}
                            </th>
                            <td>
                                {{ $article->member->fullname ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.other_authors') }}
                            </th>
                            <td>
                                {{ $article->other_authors }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.corresponding_authors') }}
                            </th>
                            <td>
                                {{ $article->corresponding_authors }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.institute_organization') }}
                            </th>
                            <td>
                                {{ $article->institute_organization }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.upload_paper') }}
                            </th>
                            <td>
                                <a href="{{ $article->last->upload_paper->getUrl() }}" target="_blank">
                                    View File
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.keywords') }}
                            </th>
                            <td>
                                @foreach ($article->article_keywords as $item)
                                    <span class="badge bg-primary text-light">
                                        {{ $item->title }}
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.status') }}
                            </th>
                            <td>
                                {{ App\Models\Article::ACCESS_TYPE[$article->access_type] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.amount') }}
                            </th>
                            <td>
                                NGN {{number_format($article->amount ?? 0)}}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.article.fields.status') }}
                            </th>
                            <td>
                                {{ App\Models\Article::ARTICLE_STATUS[$article->article_status] ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.articles.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- {{ dd($article->comments) }} --}}

    <div class="card">
        <div class="card-header">
            {{ trans('global.relatedData') }}
        </div>
        <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
            <li class="nav-item">
                <a class="nav-link" href="#article_comments" role="tab" data-toggle="tab">
                    {{ trans('cruds.comment.title') }}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" role="tabpanel" id="article_comments">
                @includeIf('admin.articles.relationships.comments', ['comments' => $article->comments])
            </div>
        </div>
    </div>
@endsection
