@extends('admin.includes.masterpage-admin')

@section('content')
<style>
    #postlist,
    #postlist th,
    #postlist td,
    #postlist td *,
    #postlist th * {
        font-size: 12px !important;
        line-height: 1.3 !important;
    }
    #postlist th, #postlist td {
        padding: 5px 8px !important;
        vertical-align: middle !important;
        white-space: nowrap;
    }
    #postlist thead th.sorting,
    #postlist thead th.sorting_asc,
    #postlist thead th.sorting_desc {
        padding-right: 20px !important;
        background-position: right 4px center !important;
        background-size: 12px !important;
    }
    #postlist td.col-category {
        max-width: 110px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        cursor: help;
    }
    #postlist img {
        width: 28px !important;
        height: 28px !important;
    }
    #postlist .badge {
        font-size: 10px !important;
        padding: 2px 5px !important;
    }
    .card-body .form-label {
        font-size: 11px !important;
        margin-bottom: 3px !important;
        font-weight: 600;
    }
    .card-body select.form-control,
    .card-body select.form-control option {
        font-size: 12px !important;
        padding: 4px 8px !important;
        height: auto !important;
    }

    /* Forced status colors — theme overrides bg-success/bg-secondary/etc,
       so these use unclaimed class names with !important to guarantee color. */
    .status-badge {
        display: inline-block;
        font-size: 10px !important;
        font-weight: 600 !important;
        padding: 3px 8px !important;
        border-radius: 4px !important;
        color: #fff !important;
        border: none !important;
    }
    .status-in-stock     { background-color: #198754 !important; } /* green */
    .status-low-stock     { background-color: #ffc107 !important; color: #212529 !important; } /* yellow */
    .status-out-of-stock  { background-color: #dc3545 !important; } /* red */
    .status-unknown       { background-color: #6c757d !important; } /* gray */

    .status-synced        { background-color: #198754 !important; } /* green */
    .status-pending       { background-color: #6c757d !important; } /* gray */

    .status-source-digikey { background-color: #dc3545 !important; } /* red */
    .status-source-mouser  { background-color: #0d6efd !important; } /* blue */
    .status-source-manual  { background-color: #6c757d !important; } /* gray */

    #deleteCategorySelect {
        font-size: 12px !important;
        max-height: 110px;
    }
    #btnResetFilters {
        border-radius: 4px !important;
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        padding: 0 !important;
        aspect-ratio: unset !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
    }
</style>
@if(Session::has('message'))
    <div class="alert alert-success alert-dismissable">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        {{ Session::get('message') }}
    </div>
@endif

{{-- ================= SUMMARY CARDS ================= --}}
<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h6 class="mb-1">Total Products</h6>
                <h3 class="mb-0" id="statTotalProducts">—</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="mb-1">Manufacturers</h6>
                <h3 class="mb-0" id="statManufacturers">—</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h6 class="mb-1">Low / Out of Stock</h6>
                <h3 class="mb-0" id="statLowStock">—</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h6 class="mb-1">Avg. Unit Price</h6>
                <h3 class="mb-0" id="statAvgPrice">—</h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Products</h4>
    </div>

    <div class="card-body">

        {{-- ================= FILTERS ================= --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Manufacturer</label>
                <select id="filterManufacturer" class="form-control">
                    <option value="">All Manufacturers</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select id="filterCategory" class="form-control">
                    <option value="">All Categories</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Stock Status</label>
                <select id="filterStock" class="form-control">
                    <option value="">All</option>
                    <option value="in_stock">In Stock</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Specs Synced</label>
                <select id="filterSpecsSynced" class="form-control">
                    <option value="">All</option>
                    <option value="synced">Synced</option>
                    <option value="not_synced">Not Synced</option>
                </select>
            </div>
            <div class="col-auto d-flex align-items-end">
                <button id="btnResetFilters" class="btn btn-outline-secondary" type="button" title="Reset filters">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>

        {{-- ================= BULK ACTIONS ================= --}}
        <div class="card bg-light border mb-3">
            <div class="card-body py-2 px-3">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label d-block mb-1">Selected rows</label>
                        <button id="btnDeleteSelected" class="btn btn-danger btn-sm w-100" type="button" disabled>
                            <i class="fa fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-block mb-1">Delete entire categories (Ctrl/Cmd-click to select multiple)</label>
                        <select id="deleteCategorySelect" class="form-control form-control-sm" multiple>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button id="btnDeleteCategory" class="btn btn-outline-danger btn-sm w-100" type="button" disabled>
                            <i class="fa fa-trash"></i> Delete Categories (<span id="deleteCategoryCount">0</span>)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="postlist" class="table table-striped table-hover dt-responsive nowrap" width="100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAllRows"></th>
                        <th>Image</th>
                        <th>Product Key / Name</th>
                        <th>Manufacturer</th>
                        <th>Category</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Stock Status</th>
                        <th>Specs</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('footer')
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function () {

    // Populate filter dropdowns
    $.get("{{ route('products.filter_options') }}", function (resp) {
        resp.manufacturers.forEach(function (m) {
            $('#filterManufacturer').append(`<option value="${m}">${m}</option>`);
        });
        resp.categories.forEach(function (c) {
            $('#filterCategory').append(`<option value="${c}">${c}</option>`);
            $('#deleteCategorySelect').append(`<option value="${c}">${c}</option>`);
        });
        // Give the multi-select a usable height once options are loaded.
        $('#deleteCategorySelect').attr('size', Math.min(6, Math.max(3, resp.categories.length)));
    });

    const table = $('#postlist').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        order: [[9, 'desc']],
        ajax: {
            url: "{{ route('cached_products_list') }}",
            type: "POST",
            data: function (d) {
                d.manufacturer = $('#filterManufacturer').val();
                d.category     = $('#filterCategory').val();
                d.stock_status = $('#filterStock').val();
                d.specs_synced = $('#filterSpecsSynced').val();
            },
            dataSrc: function (json) {
                if (json.stats) {
                    $('#statTotalProducts').text(json.stats.total_products ?? '—');
                    $('#statManufacturers').text(json.stats.manufacturer_count ?? '—');
                    $('#statLowStock').text(json.stats.low_stock_count ?? '—');
                    $('#statAvgPrice').text(json.stats.avg_price ? '$' + json.stats.avg_price : '—');
                }
                return json.data;
            }
        },
        columns: [
            {
                data: null, orderable: false, searchable: false,
                render: function (data, type, row) {
                    return `<input type="checkbox" class="rowCheckbox" value="${row.id}">`;
                }
            },
            {
                data: 'image_url', orderable: false, searchable: false,
                render: function (data) {
                    return data
                        ? `<img src="${data}" alt="" style="width:40px;height:40px;object-fit:contain;border:1px solid #eee;border-radius:4px;">`
                        : `<div style="width:40px;height:40px;background:#f5f5f5;border-radius:4px;"></div>`;
                }
            },
            {
                data: 'product_key',
                render: function (data, type, row) {
                    const label = row.name ?? data ?? '-';
                    const mfrSlug = (row.manufacturer || '')
                        .toString()
                        .trim()
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    const partNo = encodeURIComponent(data ?? '');
                    const url = mfrSlug && data
                        ? `https://simplytronix.com/product/${mfrSlug}/${partNo}`
                        : `/admin/products/${row.id}`;
                    return `<a href="${url}" target="_blank" rel="noopener" class="fw-bold">${label}</a>`;
                }
            },
            { data: 'manufacturer' },
            {
                data: 'category',
                className: 'col-category',
                render: function (data) {
                    const val = data ?? '-';
                    return `<span title="${val.toString().replace(/"/g, '&quot;')}">${val}</span>`;
                }
            },
            {
                data: 'unit_price',
                render: function (data) {
                    return data !== null ? '$' + data : '—';
                }
            },
            { data: 'quantity' },
            {
                data: 'stock_status', orderable: false,
                render: function (data) {
                    const map = {
                        in_stock:     '<span class="status-badge status-in-stock">In Stock</span>',
                        low_stock:    '<span class="status-badge status-low-stock">Low Stock</span>',
                        out_of_stock: '<span class="status-badge status-out-of-stock">Out of Stock</span>'
                    };
                    return map[data] || '<span class="status-badge status-unknown">Unknown</span>';
                }
            },
            {
                data: 'specs_synced', orderable: true, searchable: false,
                render: function (data) {
                    return data
                        ? '<span class="status-badge status-synced">Synced</span>'
                        : '<span class="status-badge status-pending">Pending</span>';
                }
            },
            { data: 'created_at' },
            { data: 'updated_at' },
            {
                data: null, orderable: false, searchable: false,
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-sm btn-outline-primary btn-edit-row" data-id="${row.id}"><i class="fa fa-edit"></i></button>`;
                }
            }
        ]
    });

    $('#filterManufacturer, #filterCategory, #filterSource, #filterStock, #filterSpecsSynced').on('change', function () {
        table.ajax.reload();
    });

    $('#btnResetFilters').on('click', function () {
        $('#filterManufacturer, #filterCategory, #filterSource, #filterStock, #filterSpecsSynced').val('');
        table.ajax.reload();
    });

    // ---- Row selection ----
    let selectedIds = new Set();

    function updateBulkDeleteState() {
        $('#selectedCount').text(selectedIds.size);
        $('#btnDeleteSelected').prop('disabled', selectedIds.size === 0);
    }

    $('#postlist').on('change', '.rowCheckbox', function () {
        const id = $(this).val();
        if (this.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
            $('#selectAllRows').prop('checked', false);
        }
        updateBulkDeleteState();
    });

    $('#selectAllRows').on('change', function () {
        const checked = this.checked;
        $('.rowCheckbox').prop('checked', checked).each(function () {
            const id = $(this).val();
            if (checked) selectedIds.add(id); else selectedIds.delete(id);
        });
        updateBulkDeleteState();
    });

    // Reset selection state whenever the table redraws (new page / filter / sort)
    table.on('draw', function () {
        $('#selectAllRows').prop('checked', false);
        $('.rowCheckbox').each(function () {
            $(this).prop('checked', selectedIds.has($(this).val()));
        });
    });

    // ---- Delete Selected ----
    $('#btnDeleteSelected').on('click', function () {
        if (selectedIds.size === 0) return;
        if (!confirm(`Delete ${selectedIds.size} selected product(s)? This cannot be undone.`)) return;

        $.ajax({
            url: "{{ route('products.bulk_delete') }}",
            type: 'POST',
            data: { ids: Array.from(selectedIds) },
            success: function (resp) {
                alert(resp.message || 'Deleted.');
                window.location.reload();
            },
            error: function (xhr) {
                alert('Delete failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // ---- Delete by Category ----
    $('#deleteCategorySelect').on('change', function () {
        const categories = $(this).val() || [];
        $('#btnDeleteCategory').prop('disabled', categories.length === 0);
        $('#deleteCategoryCount').text(categories.length);

        // Preview: filter the main table to the first selected category so the
        // admin can see roughly what will be deleted before confirming.
        // (The DataTables category filter only supports one value at a time.)
        $('#filterCategory').val(categories.length === 1 ? categories[0] : '').trigger('change');
    });

    $('#btnDeleteCategory').on('click', function () {
        const categories = $('#deleteCategorySelect').val() || [];
        if (categories.length === 0) return;

        const preview = categories.length <= 5 ? categories.join(', ') : categories.slice(0, 5).join(', ') + `, +${categories.length - 5} more`;
        if (!confirm(`Delete ALL products in ${categories.length} categor${categories.length === 1 ? 'y' : 'ies'}?\n\n${preview}\n\nThis cannot be undone.`)) return;

        $.ajax({
            url: "{{ route('products.delete_by_category') }}",
            type: 'POST',
            data: { categories: categories },
            success: function (resp) {
                alert(resp.message || 'Deleted.');
                window.location.reload();
            },
            error: function (xhr) {
                alert('Delete failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // ---- Quick Edit Modal ----
    $('#postlist').on('click', '.btn-edit-row', function () {
        const rowData = table.row($(this).closest('tr')).data();
        if (!rowData) return;

        $('#editProductId').val(rowData.id);
        $('#editProductName').val(rowData.name || '');
        $('#editProductManufacturer').val(rowData.manufacturer || '');
        $('#editProductCategory').val(rowData.category || '');
        $('#editProductUnitPrice').val(rowData.unit_price || '');
        $('#editProductQuantity').val(rowData.quantity || '');

        $('#editProductModal').modal('show');
    });

    $('#editProductForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#editProductId').val();

        $.ajax({
            url: `/admin/products/${id}`,
            type: 'PATCH',
            data: {
                name:         $('#editProductName').val(),
                manufacturer: $('#editProductManufacturer').val(),
                category:     $('#editProductCategory').val(),
                unit_price:   $('#editProductUnitPrice').val(),
                quantity:     $('#editProductQuantity').val(),
            },
            success: function (resp) {
                $('#editProductModal').modal('hide');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                alert('Update failed: ' + (xhr.responseJSON?.message || 'Check the form and try again.'));
            }
        });
    });
});
</script>

{{-- ================= QUICK EDIT MODAL ================= --}}
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editProductForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editProductId">
                    <div class="form-group mb-2">
                        <label class="form-label">Name</label>
                        <input type="text" id="editProductName" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Manufacturer</label>
                        <input type="text" id="editProductManufacturer" class="form-control">
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Category</label>
                        <input type="text" id="editProductCategory" class="form-control">
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Unit Price</label>
                        <input type="number" step="0.0001" id="editProductUnitPrice" class="form-control">
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" id="editProductQuantity" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
