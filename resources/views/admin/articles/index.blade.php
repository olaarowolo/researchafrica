@extends('layouts.admin')
@section('content')
    @can('article_category_create')
        <div class="row" style="margin-bottom: 10px;">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.articles.create') }}">
                    Create Article
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.article.title_singular') }} {{ trans('global.list') }}
        </div>


        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Article">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.article.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.article.fields.author_name') }}
                            </th>
                            <th>
                                {{ trans('cruds.article.fields.member') }}
                            </th>
                            <th>
                                {{ trans('cruds.article.fields.title') }}
                            </th>
                            <th>
                                {{ trans('cruds.article.fields.article_category') }}
                            </th>
                            <th>
                                {{ trans('cruds.article.fields.other_authors') }}
                            </th>
                            <th>
                                {{ trans('cruds.article.fields.corresponding_authors') }}
                            </th>
                            <th>
                                {{ trans('cruds.article.fields.institute_organization') }}
                            </th>
                            <th>
                                {{ trans('cruds.article.fields.upload_paper') }}
                            </th>
                            <th>
                                {{ trans('cruds.article.fields.keywords') }}
                            </th>
                            <th>
                                Access Type
                            </th>
                            <th>
                                Amount
                            </th>
                            <th>
                                {{ trans('cruds.article.fields.status') }}
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
                                <select class="search">
                                    <option value>{{ trans('global.all') }}</option>
                                    @foreach ($members as $key => $item)
                                        <option value="{{ $item->email_address }}">{{ $item->email_address }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                            </td>
                            <td>
                                <select class="search">
                                    <option value>{{ trans('global.all') }}</option>
                                    @foreach ($article_categories as $key => $item)
                                        <option value="{{ $item->category_name }}">{{ $item->category_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                            </td>
                            <td>
                                <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                            </td>
                            <td>
                            </td>
                            <td>
                                <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                            </td>
                            <td>
                                <select class="search" strict="true">
                                    <option value>{{ trans('global.all') }}</option>
                                    @foreach (App\Models\Article::ACCESS_TYPE as $key => $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                            </td>
                            <td>
                                <select class="search" strict="true">
                                    <option value>{{ trans('global.all') }}</option>
                                    @foreach (App\Models\Article::ARTICLE_STATUS as $key => $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($articles as $key => $article)
                            <tr data-entry-id="{{ $article->id }}">


                                @php
                                    $sub_article = $article->last;
                                @endphp
                                <td>

                                </td>
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    {{ $article->member->fullname ?? '' }}
                                </td>
                                <td>
                                    {{ $article->member->email_address ?? '' }}
                                </td>
                                <td>
                                    {{ $article->title ?? '' }}
                                </td>
                                <td>
                                    {{ $article->article_category->category_name ?? '' }}
                                </td>
                                <td>
                                    {{ $article->other_authors ?? '' }}
                                </td>
                                <td>
                                    {{ $article->corresponding_authors ?? '' }}
                                </td>
                                <td>
                                    {{ $article->institute_organization ?? '' }}
                                </td>
                                <td>
                                    @if ($sub_article->upload_paper)
                                        <a href="{{ $sub_article->upload_paper->getUrl() }}" target="_blank">
                                            {{ trans('global.view_file') }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @foreach ($article->article_keywords as $item)
                                        <span class="badge bg-primary text-light">
                                            {{ $item->title }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>
                                    {{ App\Models\Article::ACCESS_TYPE[$article->access_type] ?? '' }}
                                </td>
                                <td>
                                    @if ($article->access_type === 2)
                                        NGN {{ number_format($article->amount ?? 0) }}
                                    @else
                                        <i>Free</i>
                                    @endif
                                </td>
                                <td>
                                    @can('article_show')
                                        <a class="btn btn-xs btn-primary"
                                            href="{{ route('admin.articles.show', $article->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('article_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.articles.edit', $article->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('article_delete')
                                        <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST"
                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                            style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger"
                                                value="{{ trans('global.delete') }}">
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
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('article_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.articles.massDestroy') }}",
                    className: 'btn-danger',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).nodes(), function(entry) {
                            return $(entry).data('entry-id')
                        });

                        if (ids.length === 0) {
                            alert('{{ trans('global.datatables.zero_selected') }}')

                            return
                        }

                        if (confirm('{{ trans('global.areYouSure') }}')) {
                            $.ajax({
                                    headers: {
                                        'x-csrf-token': _token
                                    },
                                    method: 'POST',
                                    url: config.url,
                                    data: {
                                        ids: ids,
                                        _method: 'DELETE'
                                    }
                                })
                                .done(function() {
                                    location.reload()
                                })
                        }
                    }
                }
                dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'deasc']
                ],
                pageLength: 100,
            });
            let table = $('.datatable-Article:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

            let visibleColumnsIndexes = null;
            $('.datatable thead').on('input', '.search', function() {
                let strict = $(this).attr('strict') || false
                let value = strict && this.value ? "^" + this.value + "$" : this.value

                let index = $(this).parent().index()
                if (visibleColumnsIndexes !== null) {
                    index = visibleColumnsIndexes[index]
                }

                table
                    .column(index)
                    .search(value, strict)
                    .draw()
            });
            table.on('column-visibility.dt', function(e, settings, column, state) {
                visibleColumnsIndexes = []
                table.columns(":visible").every(function(colIdx) {
                    visibleColumnsIndexes.push(colIdx);
                });
            })
        })
    </script>
@endsection
