@extends('admin.includes.masterpage-admin')

@section('content')

@if(Session::has('message'))
<div class="alert alert-success alert-dismissable">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    {{ Session::get('message') }}
</div>
@endif

<div class="card">
    <div class="card-header">
        <ul class="nav" id="routetab" role="tablist">
            <li class="nav-item">
                <a class="nav-link status stats active" data-id="open" href="#">
                    <h4 class="card-title">Open</h4>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link status" data-id="complete" href="#">
                    <h4 class="card-title">Complete</h4>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="postlist" class="table table-striped dt-responsive" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order Id</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>Part Number</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Fix DataTable dropdown width issue */
#postlist td select.status-dropdown {
    min-width: 110px;
    width: auto;
    padding-right: 20px;
    white-space: nowrap;
}

/* Prevent DataTable from shrinking the column */
#postlist td {
    vertical-align: middle;
}
</style>


@endsection

@section('footer')

<script>
$(document).ready(function () {

    /* =========================
       DATATABLE INITIALIZATION
       ========================= */
    var dataTable = $('#postlist').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10,25,50,100,5000],[10,25,50,100,"All"]],
        dom: 'Blfrtip',
        buttons: ['copy','csv','excel','pdf','print'],

        ajax: {
            url: "{{ route('quotation_list') }}",
            type: "POST",
            data: function (data) {
                data.status = $('.stats').length ? $('.stats').data('id') : 'open';
                data._token = $('meta[name="csrf-token"]').attr('content');
            }
        },

        columns: [
            { data: "id", orderable: false },
            { data: "order_id", orderable: false },
            { data: "name", orderable: false },
            { data: "email", orderable: false },
            { data: "phone", orderable: false },
            { data: "company", orderable: false },
            { data: "part_number", orderable: false },
            { data: "quantity", orderable: false },

            {
                data: "status",
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <select class="form-control form-control-sm status-dropdown"
                                data-id="${row.id}">
                            <option value="open" ${data === 'open' ? 'selected' : ''}>Open</option>
                            <option value="complete" ${data === 'complete' ? 'selected' : ''}>Complete</option>
                            <option value="delete">Delete</option>
                        </select>
                    `;
                }
            },

            { data: "created_at", orderable: false }
        ]
    });

    /* =========================
       STATUS UPDATE AJAX
       ========================= */
    $(document).on('change', '.status-dropdown', function () {

        let status = $(this).val();
        let id = $(this).data('id');

        if (status === 'delete') {
            if (!confirm('Are you sure you want to delete this quotation?')) {
                dataTable.draw(false);
                return;
            }
        }

        $.ajax({
            url: "{{ route('quotation_update_status') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: id,
                status: status
            },
            success: function () {
                dataTable.draw(false);
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                alert('Status update failed');
            }
        });
    });

    /* =========================
       TAB FILTER
       ========================= */
    $('.status').on('click', function (e) {
        e.preventDefault();
        $('.status').removeClass('stats active');
        $(this).addClass('stats active');
        dataTable.draw();
    });

});
</script>

@endsection
