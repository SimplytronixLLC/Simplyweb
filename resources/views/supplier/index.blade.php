@extends('layouts.main')

@section('content')

<div id="supplierApp"></div>

@vite([
'resources/js/supplier/app.js'
])

@endsection