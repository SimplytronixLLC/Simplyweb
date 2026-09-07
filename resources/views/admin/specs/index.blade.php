@extends('admin.includes.masterpage-admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Specs Editor</h4>
    </div>
    <div class="card-body">

        <div class="row mb-4">
            <div class="col-md-9">
                <input type="text"
                    id="productKey"
                    class="form-control"
                    placeholder="Enter Product Key">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary btn-block"
                    onclick="loadProduct()">
                    Load
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label>Paste Raw Specs</label>
            <textarea
                id="rawSpecs"
                class="form-control"
                rows="8"
                placeholder="Paste specs here (one per line as Key: Value, or alternating lines)"></textarea>
            <br>
            <button class="btn btn-success" onclick="importSpecs()">
                Import Specs
            </button>
        </div>

        <hr>

        <div id="specContainer"></div>
        <br>

        <button class="btn btn-info" onclick="addSpec()">
            Add Row
        </button>
        <button class="btn btn-primary" onclick="saveSpecs()">
            Save Specs
        </button>

    </div>
</div>
@endsection

@section('footer')
<script>
let specs = [];

function render() {
    let html = '';
    specs.forEach((s, i) => {
        html += `
        <div class="row mb-2">
            <div class="col-md-5">
                <input class="form-control"
                    value="${s.name}"
                    onchange="specs[${i}].name = this.value">
            </div>
            <div class="col-md-5">
                <input class="form-control"
                    value="${s.value}"
                    onchange="specs[${i}].value = this.value">
            </div>
            <div class="col-md-2">
                <button class="btn btn-danger" onclick="removeSpec(${i})">X</button>
            </div>
        </div>`;
    });
    $('#specContainer').html(html);
}

function addSpec() {
    specs.push({ name: '', value: '' });
    render();
}

function removeSpec(i) {
    specs.splice(i, 1);
    render();
}

function importSpecs() {
    let raw = $('#rawSpecs').val().split('\n').map(x => x.trim()).filter(x => x);
    specs = [];

    // Try "Key: Value" single-line format first
    let parsed = raw.filter(line => line.includes(':'));
    if (parsed.length > 0) {
        parsed.forEach(line => {
            let [name, ...rest] = line.split(':');
            specs.push({ name: name.trim(), value: rest.join(':').trim() });
        });
    } else {
        // Fall back to alternating-line format
        for (let i = 0; i < raw.length; i += 2) {
            specs.push({ name: raw[i] || '', value: raw[i + 1] || '' });
        }
    }

    render();
    toastr.success(specs.length + ' specs imported');
}

function loadProduct() {
    $.post(
        "{{ route('admin.specs.load') }}",
        {
            _token: "{{ csrf_token() }}",
            product_key: $('#productKey').val()
        },
        function (r) {
            if (r.success) {
                specs = r.specs;
                render();
                toastr.success('Loaded');
            } else {
                toastr.error('Part not found');
            }
        }
    ).fail(function () {
        toastr.error('Failed to load product');
    });
}

function saveSpecs() {
    // Must use $.ajax with contentType: 'application/json' so Laravel
    // correctly parses the specs array (required|array validation).
    $.ajax({
        url: "{{ route('admin.specs.save') }}",
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            _token: "{{ csrf_token() }}",
            product_key: $('#productKey').val(),
            specs: specs
        }),
        success: function (r) {
            toastr.success('Saved successfully');
        },
        error: function (xhr) {
            let msg = xhr.responseJSON?.message ?? 'Save failed';
            toastr.error(msg);
        }
    });
}

render();
</script>
@endsection