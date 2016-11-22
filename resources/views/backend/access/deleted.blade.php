@extends ('backend.layouts.master')

@section ('title', trans('labels.backend.access.users.management') . ' | ' . trans('labels.backend.access.users.deleted'))

@section('after-styles-end')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
@stop

@section('page-header')
    <h1>
        {{ trans('labels.backend.access.users.management') }}
        <small>{{ trans('labels.backend.access.users.deleted') }}</small>
    </h1>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('labels.backend.access.users.deleted') }}</h3>

            <div class="box-tools pull-right">
                @include('backend.access.includes.partials.header-buttons')
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->

        <div class="box-body">
            <div class="table-responsive">
                <table id="users-table" class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>{{ trans('labels.backend.access.users.table.id') }}</th>
                            <th>{{ trans('labels.backend.access.users.table.name') }}</th>
                            <th>{{ trans('labels.backend.access.users.table.email') }}</th>
                            <th>{{ trans('labels.backend.access.users.table.confirmed') }}</th>
                            <th>{{ trans('labels.backend.access.users.table.roles') }}</th>
                            <th>{{ trans('labels.backend.access.users.table.created') }}</th>
                            <th>{{ trans('labels.backend.access.users.table.last_updated') }}</th>
                            <th>{{ trans('labels.general.actions') }}</th>
                        </tr>
                    </thead>
                    
                    <tfoot>
                        <tr>
                            <th>{{ trans('labels.backend.access.users.table.id') }}</th>
                            <th class="field_name_searchable">{{ trans('labels.backend.access.users.table.name') }}</th>
                            <th class="field_email_searchable">{{ trans('labels.backend.access.users.table.email') }}</th>
                            <th class="field_confirm_filterable">{{ trans('labels.backend.access.users.table.confirmed') }}</th>
                            <th>{{ trans('labels.backend.access.users.table.roles') }}</th>
                            <th>{{ trans('labels.backend.access.users.table.created') }}</th>
                            <th>{{ trans('labels.backend.access.users.table.last_updated') }}</th>
                            <th>{{ trans('labels.general.actions') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div><!--table-responsive-->
        </div><!-- /.box-body -->
    </div><!--box-->
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
            
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.access.user.get") }}',
                    type: 'get',
                    data: {status: false, trashed: true}
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'confirmed', name: 'confirmed'},
                    {data: 'roles', name: 'roles', sortable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'actions', name: 'actions', searchable: false, sortable: false}
                ],
                order: [[0, "asc"]],
                searchDelay: 500,
                initComplete: function () { 
                    $('#users-table_wrapper').prepend('<div class="row" id="search-filter-container"></div>');
                    
                    var resetLink = $('<a class="btn btn-primary btn-sm reset-btn">Reset</a>')
                        .prependTo( $('#search-filter-container') )
                        .on('click', function () {
                            $('.input-search-field').val('');
                            $('.confirm-search-field').val('');
                            selectBoxValue = '';
                            $('#users-table').DataTable().search( '' ).columns().search( '' ).draw();
                        });
                        
                    this.api().columns().every(function () {                        
                        var column = this;
                        var title = $(column.footer()).text();
                        var currentClass = $(column.footer()).attr("class");
                        // - Filter by status
                        if (currentClass === 'field_confirm_filterable') {
                            var select = $('<select class="form-control input-sm filter-dropdown-field"><option value="">' + title + '</option></select>')
                                .addClass('confirm-search-field')
                                .prependTo( $('#search-filter-container') )
                                .on('change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );
                                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                                    selectBoxValue = $(this).val();
                                });

                            column.data().unique().sort().each( function ( d, j ) {
                                console.log($(d).text());
                                select.append('<option value="' + ($(d).text() === 'Yes' ? 1 : 0)  + '">' + capitalize($(d).text()) + '</option>')
                            });
                        }                                                                        
                    });
                    
                    this.api().columns().every(function () {                        
                        var column = this;
                        var title = $(column.footer()).text();
                        var currentClass = $(column.footer()).attr("class");
                        // - Filter by Name
                        if (currentClass === 'field_email_searchable'){ 
                            var input = document.createElement("input");
                            $(input).addClass('form-control input-sm search-field');
                            $(input).addClass('input-search-field');
                            $(input).attr('placeholder', 'Search ' + title);
                            $(input).prependTo($('#search-filter-container'))
                            .on('keyup change', function () {
                                column.search($(this).val(), false, false, true).draw();
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
                            $(input).addClass('input-search-field');
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
                        $('.confirm-search-field').val(selectBoxValue);
                    }
                }
            });

            $("body").on("click", "a[name='delete_user_perm']", function(e) {
                e.preventDefault();
                var linkURL = $(this).attr("href");

                swal({
                    title: "{{ trans('strings.backend.general.are_you_sure') }}",
                    text: "{{ trans('strings.backend.access.users.delete_user_confirm') }}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "{{ trans('strings.backend.general.continue') }}",
                    cancelButtonText: "{{ trans('buttons.general.cancel') }}",
                    closeOnConfirm: false
                }, function(isConfirmed){
                    if (isConfirmed){
                        window.location.href = linkURL;
                    }
                });
            });

            $("body").on("click", "a[name='restore_user']", function(e) {
                e.preventDefault();
                var linkURL = $(this).attr("href");

                swal({
                    title: "{{ trans('strings.backend.general.are_you_sure') }}",
                    text: "{{ trans('strings.backend.access.users.restore_user_confirm') }}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "{{ trans('strings.backend.general.continue') }}",
                    cancelButtonText: "{{ trans('buttons.general.cancel') }}",
                    closeOnConfirm: false
                }, function(isConfirmed){
                    if (isConfirmed){
                        window.location.href = linkURL;
                    }
                });
            });
		});
	</script>
@stop
