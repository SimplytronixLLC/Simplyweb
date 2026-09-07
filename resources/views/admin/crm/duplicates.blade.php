@extends('admin.includes.masterpage-admin')

@section('content')

@if(Session::has('status'))
<div class="alert alert-success alert-dismissable">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    {{ Session::get('status') }}
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger alert-dismissable">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    {{ Session::get('error') }}
</div>
@endif

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Duplicate Contacts</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            These contacts share an email address with another contact already in the CRM.
            Automation is paused on flagged duplicates until you merge them into the primary
            record or dismiss the flag as a false match.
        </p>
        @if($groups->isNotEmpty())
        <form method="POST" action="{{ route('admin.crm.duplicates.merge_all') }}" onsubmit="return confirm('Merge ALL flagged duplicates across every group into their primary records? This cannot be undone.');" style="margin-top:10px;">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger">Merge all groups now</button>
        </form>
        @endif
    </div>

    <div class="card-body">
        @if($groups->isEmpty())
        <div style="text-align:center; padding:48px 0; color:#a0a4ab;">
            <div style="font-size:32px; margin-bottom:8px;">✅</div>
            No duplicates waiting for review.
        </div>
        @else
            @foreach($groups as $primaryId => $dupes)
                @php $primary = $dupes->first()->duplicateOf; @endphp
                @if($primary)
                <div class="card mb-3" style="border:1px solid #e6e8eb;">
                    <div class="card-body">
                        <div style="font-size:12px; text-transform:uppercase; color:#6b7280; letter-spacing:0.4px; margin-bottom:8px;">
                            Primary record
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; background:#f7f8fa; border-radius:5px; padding:10px 14px; margin-bottom:14px;">
                            <div>
                                <strong>{{ $primary->name ?: 'No name' }}</strong>
                                <span style="color:#6b7280; font-size:13px;"> — {{ $primary->email }}</span>
                                <span class="badge badge-light" style="margin-left:6px;">{{ $primary->source }}</span>
                                <span class="badge badge-secondary" style="margin-left:4px;">{{ ucfirst($primary->stage) }}</span>
                            </div>
                            <div style="font-size:12px; color:#a0a4ab;">#{{ $primary->id }}</div>
                        </div>

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <div style="font-size:12px; text-transform:uppercase; color:#6b7280; letter-spacing:0.4px;">
                                Flagged as possible duplicate(s) — {{ $dupes->count() }}
                            </div>
                            <form method="POST" action="{{ route('admin.crm.duplicates.merge_group', $primary->id) }}" onsubmit="return confirm('Merge all {{ $dupes->count() }} flagged duplicate(s) into #{{ $primary->id }}? This cannot be undone.');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Merge all {{ $dupes->count() }} into primary</button>
                            </form>
                        </div>

                        @foreach($dupes as $dupe)
                        <div style="display:flex; justify-content:space-between; align-items:center; border:1px solid #f0d9a8; background:#fffaf0; border-radius:5px; padding:10px 14px; margin-bottom:8px;">
                            <div>
                                <strong>{{ $dupe->name ?: 'No name' }}</strong>
                                <span style="color:#6b7280; font-size:13px;"> — {{ $dupe->email }}</span>
                                <span class="badge badge-warning" style="margin-left:6px;">{{ $dupe->source }}</span>
                                <span style="font-size:11px; color:#a0a4ab; margin-left:8px;">#{{ $dupe->id }} · created {{ $dupe->created_at->format('M j, Y') }}</span>
                            </div>
                            <div style="display:flex; gap:6px;">
                                <form method="POST" action="{{ route('admin.crm.duplicates.merge', $dupe->id) }}" onsubmit="return confirm('Merge contact #{{ $dupe->id }} into #{{ $primary->id }}? This will move its activity history over and delete the duplicate row. This cannot be undone.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">Merge into primary</button>
                                </form>
                                <form method="POST" action="{{ route('admin.crm.duplicates.dismiss', $dupe->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Not a duplicate</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach
        @endif
    </div>
</div>

@endsection
