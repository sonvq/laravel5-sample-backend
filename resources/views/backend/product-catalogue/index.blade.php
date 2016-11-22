@extends ('backend.layouts.master')

@section ('title', trans('labels.backend.product-catalogue.title'))

@section('after-styles-end')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
@stop

@section('page-header')
    <h1>
        {{ trans('labels.backend.product-catalogue.title') }}        
    </h1>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('labels.backend.product-catalogue.title_list') }}</h3>

            <div class="box-tools pull-right">
                {{ link_to_route('admin.product-catalogue.create', trans('labels.backend.product-catalogue.add_new'), [], ['class' => 'btn btn-primary btn-xs']) }}
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->

        <div class="box-body">
            <div class="table-responsive">
                <table id="product-catalogue-table" class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>{{ trans('labels.backend.product-catalogue.table.id') }}</th>
                            <th>{{ trans('labels.backend.product-catalogue.table.image') }}</th>
                            <th>{{ trans('labels.backend.product-catalogue.table.user_id') }}</th>
                            <th>{{ trans('labels.backend.product-catalogue.table.name') }}</th>
                            <th>{{ trans('labels.backend.product-catalogue.table.category_name') }}</th>
                            <th>{{ trans('labels.backend.product-catalogue.table.status') }}</th>
                            <th>{{ trans('labels.backend.product-catalogue.table.description') }}</th>
                            <th>{{ trans('labels.general.actions') }}</th>
                        </tr>
                    </thead>
                    
                    <tfoot>
                        <tr>
                            <th>{{ trans('labels.backend.product-catalogue.table.id') }}</th>
                            <th>{{ trans('labels.backend.product-catalogue.table.image') }}</th>
                            <th>{{ trans('labels.backend.product-catalogue.table.user_id') }}</th>
                            <th class="field_name_searchable">{{ trans('labels.backend.product-catalogue.table.name') }}</th>
                            <th>{{ trans('labels.backend.product-catalogue.table.category_name') }}</th>
                            <th class="field_status_filterable">{{ trans('labels.backend.product-catalogue.table.status') }}</th>
                            <th>{{ trans('labels.backend.product-catalogue.table.description') }}</th>
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
            {!! history()->renderType('ProductCatalogue') !!}
        </div><!-- /.box-body -->
    </div><!--box box-success-->
@stop

@section('after-scripts-end')
    {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}

    <script>
        $(function() {
            function capitalize(string) {
                return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
            }
            var selectBoxValue = null;
            $('#product-catalogue-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.product-catalogue.get") }}',
                    type: 'get',
                    data: {status: 1, trashed: false}
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'thumbnail', name: 'thumbnail', searchable: false, sortable: false, render: function(data, type, row) { return '<img class="image-datatable" src="' + data + '" />'; }},
                    {data: 'user_id', name: 'user_id'},
                    {data: 'name', name: 'name'},
                    {data: 'category_name', name: 'category_name'},
                    {data: 'status', name: 'status'},
                    {data: 'description', name: 'description'},
                    {data: 'actions', name: 'actions', searchable: false, sortable: false}
                ],
                order: [[0, "desc"]],
                searchDelay: 500,
                initComplete: function () {  
                    $('#product-catalogue-table_wrapper').prepend('<div class="row" id="search-filter-container"></div>');
                    
                    var resetLink = $('<a class="btn btn-primary btn-sm reset-btn">Reset</a>')
                        .prependTo( $('#search-filter-container') )
                        .on('click', function () {
                            $('.name-search-field').val('');
                            $('.status-search-field').val('');
                            selectBoxValue = '';
                            $('#product-catalogue-table').DataTable().search( '' ).columns().search( '' ).draw();
                        });
                        
                    this.api().columns().every(function () {                        
                        var column = this;
                        var title = $(column.footer()).text();
                        var currentClass = $(column.footer()).attr("class");
                        // - Filter by status
                        if (currentClass === 'field_status_filterable') {
                            var select = $('<select class="form-control input-sm filter-dropdown-field"><option value="">' + title + '</option></select>')
                                .addClass('status-search-field')
                                .prependTo( $('#search-filter-container') )
                                .on('change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );
                                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                                    selectBoxValue = $(this).val();
                                });

                            column.data().unique().sort().each( function ( d, j ) {
                                select.append('<option value="' + d + '">' + capitalize(d) + '</option>')
                            });
                        }                                                                        
                    });
                    
                    this.api().columns().every(function () {                        
                        var column = this;
                        var title = $(column.footer()).text();
                        var currentClass = $(column.footer()).attr("class");
                        // - Filter by Name
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
                                        
                    //$(column.footer()).empty();  
                },
                "fnDrawCallback": function( oSettings ) {
                    if (selectBoxValue != null) {
                        $('.status-search-field').val(selectBoxValue);
                    }
                }
            });
        });
    </script>
@stop