@extends('layouts.admin')

@section('title') {{ __('Notification Analytical Profile') }} @endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3">{{ __('Notification Delivery Analytics') }}</h1>
    <a href="#" class="btn btn-light border">Back to System Monitor</a>
  </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white"><h5 class="card-title mb-0">Payload Metadata</h5></div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge bg-{{ \App\Models\UserAppNotificastion::iconColorMap()[$notification->type] ?? 'secondary' }} mb-2">
                        <i class="{{ $notification->icon_class }}"></i> {{ $notification->type }}
                    </span>
                    <h4>{{ $notification->title }}</h4>
                    <p class="text-muted">{{ $notification->body }}</p>
                </div>
                <hr>
                <p class="mb-1"><strong>Status:</strong> {{ strtoupper($notification->status) }}</p>
                <p class="mb-1"><strong>Category:</strong> {{ $notification->category }}</p>
                <p class="mb-1"><strong>Author:</strong> {{ $notification->creator->name ?? 'System Process' }}</p>
                @if($notification->sent_at)
                    <p class="mb-1"><strong>Dispatched:</strong> {{ $notification->sent_at->format('M d, Y H:i:s') }}</p>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white"><h5 class="card-title mb-0">Read Performance</h5></div>
            <div class="card-body text-center">
                <h2 class="text-primary mb-0">{{ $readPercentage }}%</h2>
                <p class="text-muted small">Total Read-Through Ratio</p>
                <div class="progress progress-sm mb-3">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $readPercentage }}%" aria-valuenow="{{ $readPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="row g-0 border-top pt-2">
                    <div class="col-4 border-end"><strong>{{ $totalCount }}</strong><br><small class="text-muted">Targets</small></div>
                    <div class="col-4 border-end text-success"><strong>{{ $readCount }}</strong><br><small class="text-muted">Read</small></div>
                    <div class="col-4 text-danger"><strong>{{ $unreadCount }}</strong><br><small class="text-muted">Unread</small></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Dispatched Delivery Recipient Logs</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover my-0">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Employee Identity</th>
                            <th>Read Receipt Status</th>
                            <th>Acknowledge/Read Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notification->recipients as $recipient)
                            <tr>
                                <td>#{{ $recipient->user_id }}</td>
                                <td>
                                    <strong>{{ $recipient->user->employee->firstname ?? 'Unknown' }} {{ $recipient->user->employee->lastname ?? 'User' }}</strong>
                                </td>
                                <td>
                                    @if($recipient->is_read)
                                        <span class="badge bg-success-light text-success"><i class="fas fa-check-double"></i> Read</span>
                                    @else
                                        <span class="badge bg-warning-light text-warning"><i class="fas fa-check"></i> Unread</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $recipient->read_at ? \Carbon\Carbon::parse($recipient->read_at)->format('M d, Y H:i') : '--' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No individual user logs generated yet. Ensure notification has been pushed live.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection