@extends ('backend.layouts.master')

@section ('title', trans('labels.backend.category.title'))

@section('after-styles-end')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
@stop

@section('page-header')
    <h1>
        {{ trans('labels.backend.category.title') }}        
    </h1>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('labels.backend.category.title_list') }}</h3>

            <div class="box-tools pull-right">
                {{ link_to_route('admin.category.create', trans('labels.backend.category.add_new'), [], ['class' => 'btn btn-primary btn-xs']) }}
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->

        <div class="box-body">
            <div class="table-responsive">
                <table id="category-table" class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>{{ trans('labels.backend.category.table.id') }}</th>
                            <th>{{ trans('labels.backend.category.table.image') }}</th>
                            <th>{{ trans('labels.backend.category.table.user_id') }}</th>
                            <th>{{ trans('labels.backend.category.table.name') }}</th>
                            <th>{{ trans('labels.backend.category.table.created') }}</th>
                            <th>{{ trans('labels.backend.category.table.last_updated') }}</th>
                            <th>{{ trans('labels.general.actions') }}</th>
                        </tr>
                    </thead>
                    
                    <tfoot>
                        <tr>
                            <th>{{ trans('labels.backend.category.table.id') }}</th>
                            <th>{{ trans('labels.backend.category.table.image') }}</th>
                            <th>{{ trans('labels.backend.category.table.user_id') }}</th>
                            <th class="field_name_searchable">{{ trans('labels.backend.category.table.name') }}</th>
                            <th>{{ trans('labels.backend.category.table.created') }}</th>
                            <th>{{ trans('labels.backend.category.table.last_updated') }}</th>
                            <th>{{ trans('labels.general.actions') }}</th>
                        </tr>
                    </tfoot>
                    
                </table>
            </div><!--table-responsive-->
        </div><!-- /.box-body -->
    </div><!--box-->

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('history.backend.recent_history') }}</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><!-- /.box tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            {!! history()->renderType('Category') !!}
        </div><!-- /.box-body -->
    </div><!--box box-success-->
@stop

@section('after-scripts-end')
    {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}

    <script>
        $(function() {
            $('#category-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.category.get") }}',
                    type: 'get',
                    data: {status: 1, trashed: false}
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'thumbnail', name: 'thumbnail', searchable: false, sortable: false, render: function(data, type, row) { return '<img class="image-datatable" src="' + data + '" />'; }},
                    {data: 'user_id', name: 'user_id'},
                    {data: 'name', name: 'name'}, 
                    {data: 'created_at', name: 'created_at'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'actions', name: 'actions', searchable: false, sortable: false}
                ],
                order: [[0, "desc"]],
                searchDelay: 500,
                initComplete: function () {
                    $('#category-table_wrapper').prepend('<div class="row" id="search-filter-container"></div>');
                    
                    var resetLink = $('<a class="btn btn-primary btn-sm reset-btn">Reset</a>')
                        .prependTo( $('#search-filter-container') )
                        .on('click', function () {
                            $('.name-search-field').val('');
                            $('#category-table').DataTable().search( '' ).columns().search( '' ).draw();
                        });
                        
                    this.api().columns().every(function () {                        
                        var column = this;
                        var title = $(column.footer()).text();
                        var currentClass = $(column.footer()).attr("class");                                                               
                                                                  
                        // Search category by name
                        if (currentClass === 'field_name_searchable'){ 
                            var input = document.createElement("input");
                            $(input).addClass('form-control input-sm search-field');
                            $(input).addClass('name-search-field');
                            $(input).attr('placeholder', 'Search ' + title);
                            $(input).prependTo($('#search-filter-container'))
                            .on('keyup change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            });
                        }
                    });
                }
            });
        });
    </script>
@stop