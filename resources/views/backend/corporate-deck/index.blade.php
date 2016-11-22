@extends ('backend.layouts.master')

@section ('title', trans('labels.backend.corporate-deck.title'))

@section('after-styles-end')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
@stop

@section('page-header')
    <h1>
        {{ trans('labels.backend.corporate-deck.title') }}        
    </h1>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('labels.backend.corporate-deck.title_list') }}</h3>

            <div class="box-tools pull-right">
                {{ link_to_route('admin.corporate-deck.create', trans('labels.backend.corporate-deck.add_new'), [], ['class' => 'btn btn-primary btn-xs']) }}
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->

        <div class="box-body">
            <div class="table-responsive">
                <table id="corporate-deck-table" class="table table-condensed table-hover table-middle-align">
                    <thead>
                        <tr>                  
                            <th>{{ trans('labels.backend.corporate-deck.table.id') }}</th>
                            <th>{{ trans('labels.backend.corporate-deck.table.image') }}</th>
                            <th>{{ trans('labels.backend.corporate-deck.table.pdf') }}</th>
                            <th>{{ trans('labels.backend.corporate-deck.table.user_id') }}</th>
                            <th>{{ trans('labels.backend.corporate-deck.table.name') }}</th>
                            <th>{{ trans('labels.backend.corporate-deck.table.status') }}</th>
                            <th>{{ trans('labels.backend.corporate-deck.table.description') }}</th>
                            <th>{{ trans('labels.general.actions') }}</th>
                        </tr>
                    </thead>
                    
                    <tfoot>
                        <tr>                  
                            <th>{{ trans('labels.backend.corporate-deck.table.id') }}</th>
                            <th>{{ trans('labels.backend.corporate-deck.table.image') }}</th>
                            <th>{{ trans('labels.backend.corporate-deck.table.pdf') }}</th>
                            <th>{{ trans('labels.backend.corporate-deck.table.user_id') }}</th>
                            <th class="field_name_searchable">{{ trans('labels.backend.corporate-deck.table.name') }}</th>
                            <th class="field_status_filterable">{{ trans('labels.backend.corporate-deck.table.status') }}</th>
                            <th>{{ trans('labels.backend.corporate-deck.table.description') }}</th>
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
            {!! history()->renderType('CorporateDeck') !!}
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
            
            $('#corporate-deck-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.corporate-deck.get") }}',
                    type: 'get',
                    data: {status: 1, trashed: false}
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'thumbnail', name: 'thumbnail', searchable: false, sortable: false, render: function(data, type, row) { return '<img class="image-datatable" src="' + data + '" />'; }},
                    {data: 'pdf', name: 'pdf', searchable: false, sortable: false, render: function(data, type, row) { return '<i class="fa fa-download"></i> <a download href="' + data + '">Download</a>'; }},
                    {data: 'user_id', name: 'user_id'},
                    {data: 'name', name: 'name'},
                    {data: 'status', name: 'status'},
                    {data: 'description', name: 'description'},
                    {data: 'actions', name: 'actions', searchable: false, sortable: false}
                ],
                order: [[0, "desc"]],
                searchDelay: 500,
                initComplete: function () {
                    $('#corporate-deck-table_wrapper').prepend('<div class="row" id="search-filter-container"></div>');
                    
                    var resetLink = $('<a class="btn btn-primary btn-sm reset-btn">Reset</a>')
                        .prependTo( $('#search-filter-container') )
                        .on('click', function () {
                            $('.name-search-field').val('');
                            $('.status-search-field').val('');
                            selectBoxValue = '';
                            $('#corporate-deck-table').DataTable().search( '' ).columns().search( '' ).draw();
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