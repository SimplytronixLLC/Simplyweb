@extends('admin.includes.masterpage-admin')

@section('content')
<h2>Hot Leads</h2>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Email</th>
            <th>Total Searches</th>
            <th>Unique Parts</th>
            <th>Last Activity</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($hotLeads as $lead)
            <tr>
                <td><strong>{{ $lead->email }}</strong></td>
                <td>{{ $lead->total_searches }}</td>
                <td>{{ $lead->unique_parts }}</td>
                <td>{{ $lead->last_search }}</td>
                <td>
                    <a href="mailto:{{ $lead->email }}" class="btn btn-sm btn-primary">
                        Contact
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">
                    No hot leads right now 👍
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
