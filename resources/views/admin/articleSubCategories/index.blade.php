@extends('layouts.admin')
@section('content')
@can('article_category_create')
<div class="row" style="margin-bottom: 10px;">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route('admin.article-sub-categories.create') }}">
            Create Article Journal
        </a>
    </div>
</div>
@endcan
<div class="card">
    <div class="card-header">

    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable datatable-ArticleCategory">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.articleCategory.fields.id') }}
                        </th>
                        <th>
                            Article Category
                        </th>
                        <th>
                            Article Journal
                        </th>
                        <th>
                            Cover Image
                        </th>
                        <th>
                            {{ trans('cruds.articleCategory.fields.status') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td></td>
                        <td>
                            <select class="search" strict="true">
                                <option value>
                                    {{ trans('global.all') }}
                                </option>
                                @foreach (App\Models\ArticleCategory::STATUS_SELECT as $key => $item)
                                <option value="{{ $item }}">
                                    {{ $item }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articleCategories as $key => $articleCategory)
                    <tr data-entry-id="{{ $articleCategory->id }}">
                        <td>

                        </td>
                        <td>
                            {{ $articleCategory->id ?? '' }}
                        </td>
                        <td>
                            {{ $articleCategory->category->category_name ?? '' }}
                        </td>
                        <td>
                            {{ $articleCategory->category_name ?? '' }}
                        </td>
                        <td>
                            @if ($articleCategory->cover_image)
                                <a href="{{ $articleCategory->cover_image ? $articleCategory->cover_image->getUrl() : '' }}">
                                    View File
                                </a>
                            @else
                                <em>No Cover Image</em>
                            @endif
                        </td>
                        <td>
                            {{ App\Models\ArticleCategory::STATUS_SELECT[$articleCategory->status] ?? '' }}
                        </td>
                        <td>
                            @can('article_category_show')
                            <a class="btn btn-xs btn-primary"
                                href="{{ route('admin.article-sub-categories.show', $articleCategory->id) }}">
                                {{ trans('global.view') }}
                            </a>
                            @endcan

                            @can('article_category_edit')
                            <a class="btn btn-xs btn-info"
                                href="{{ route('admin.article-sub-categories.edit', $articleCategory->id) }}">
                                {{ trans('global.edit') }}
                            </a>
                            @endcan

                            @can('article_category_delete')
                            <form style="display: inline-block;"
                                action="{{ route('admin.article-sub-categories.destroy', $articleCategory->id) }}"
                                method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');">
                                <input name="_method" type="hidden" value="DELETE">
                                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                                <input class="btn btn-xs btn-danger" type="submit" value="{{ trans('global.delete') }}">
                            </form>
                            @endcan

                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<blade ___scripts_0___ />
@endsection
