@extends('layouts.admin')

@section('title')
    My Asset Requests
@endsection

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="mb-1">
                        My Asset Requests
                    </h4>

                    <small class="text-muted">
                        Track all support requests submitted for your assigned assets
                    </small>
                </div>

            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Request Details</th>

                            <th>Photos</th>

                            <th>Status</th>

                            <th>Created On</th>

                            <th>HR Response</th>

                        </tr>

                    </thead>

                    <tbody>

                    @forelse($requests as $request)

                        <tr>

                            {{-- Request Details --}}
                            <td width="35%">

                                <span class="badge bg-primary mb-2">

                                    {{ $request->request_type }}

                                </span>

                                <br>

                                <strong class="d-block">

                                    {{ $request->subject }}

                                </strong>

                                <small class="text-muted">

                                    {{ \Illuminate\Support\Str::limit($request->message,120) }}

                                </small>

                            </td>

                            {{-- Photos --}}
                            <td width="20%">

                                @if(!empty($request->photos))

                                    <div class="d-flex flex-wrap gap-2">

                                        @foreach($request->photos as $photo)

                                            <a href="{{ asset('storage/'.$photo) }}"
                                               target="_blank">

                                                <img
                                                    src="{{ asset('storage/'.$photo) }}"
                                                    width="65"
                                                    height="65"
                                                    class="rounded border shadow-sm"
                                                    style="object-fit:cover;">

                                            </a>

                                        @endforeach

                                    </div>

                                @else

                                    <span class="text-muted">
                                        No Photos
                                    </span>

                                @endif

                            </td>

                            {{-- Status --}}
                            <td>

                                @if($request->status == 'Pending')

                                    <span class="badge bg-warning text-dark px-3 py-2">

                                        Pending

                                    </span>

                                @elseif($request->status == 'Approved')

                                    <span class="badge bg-success px-3 py-2">

                                        Approved

                                    </span>

                                @elseif($request->status == 'Rejected')

                                    <span class="badge bg-danger px-3 py-2">

                                        Rejected

                                    </span>

                                @elseif($request->status == 'Completed')

                                    <span class="badge bg-primary px-3 py-2">

                                        Completed

                                    </span>

                                @endif

                            </td>

                            {{-- Date --}}
                            <td>

                                <strong>

                                    {{ $request->created_at->format('d M Y') }}

                                </strong>

                                <br>

                                <small class="text-muted">

                                    {{ $request->created_at->format('h:i A') }}

                                </small>

                            </td>

                            {{-- Admin Remark --}}
                            <td width="25%">

                                @if($request->admin_remark)

                                    <div class="alert alert-light border mb-0 py-2 px-3">

                                        <small>

                                            {{ $request->admin_remark }}

                                        </small>

                                    </div>

                                @else

                                    <span class="text-muted">

                                        Waiting for HR response...

                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="text-center py-5">

                                <div class="py-4">

                                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>

                                    <h5>

                                        No Requests Found

                                    </h5>

                                    <p class="text-muted mb-0">

                                        You haven't submitted any asset support requests yet.

                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection