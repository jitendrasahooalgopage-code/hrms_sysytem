@extends('layouts.admin')

@section('title')
    Asset Requests
@endsection

@section('content')

<div class="card shadow-sm border-0">

    <div class="card-header d-flex justify-content-between align-items-center">

        <div>
            <h4 class="mb-0">
                Asset Requests
            </h4>

            <small class="text-muted">
                Employee asset support requests
            </small>
        </div>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th>Employee</th>

                        <th>Request Type</th>

                        <th>Subject</th>

                        <th>Photos</th>

                        <th>Status</th>

                        <th>Date</th>

                        <th width="120">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                @forelse($requests as $request)

                    <tr>

                        {{-- Employee --}}
                        <td>

                            <div class="d-flex align-items-center">

                                <div
                                    class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-3"
                                    style="width:45px;height:45px;">

                                    {{ strtoupper(substr($request->employee->firstname ?? 'E',0,1)) }}

                                </div>

                                <div>

                                    <strong>

                                        {{ $request->employee->firstname ?? '' }}
                                        {{ $request->employee->lastname ?? '' }}

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        {{ $request->employee->email ?? '' }}

                                    </small>

                                    <br>

                                    <small class="text-muted">

                                        {{ $request->employee->phone ?? '' }}

                                    </small>

                                </div>

                            </div>

                        </td>

                        {{-- Request Type --}}
                        <td>

                            <span class="badge bg-info">

                                {{ $request->request_type }}

                            </span>

                        </td>

                        {{-- Subject --}}
                        <td>

                            <strong>

                                {{ $request->subject }}

                            </strong>

                            <br>

                            <small class="text-muted">

                                {{ \Illuminate\Support\Str::limit($request->message,80) }}

                            </small>

                        </td>

                        {{-- Photos --}}
                        <td>

                            @if($request->photos)

                                <div class="d-flex flex-wrap gap-2">

                                    @foreach($request->photos as $photo)

                                        <a href="{{ asset('storage/'.$photo) }}"
                                           target="_blank">

                                            <img
                                                src="{{ asset('storage/'.$photo) }}"
                                                width="70"
                                                height="70"
                                                class="rounded border shadow-sm"
                                                style="object-fit:cover;">

                                        </a>

                                    @endforeach

                                </div>

                            @else

                                <span class="text-muted">

                                    No Images

                                </span>

                            @endif

                        </td>

                        {{-- Status --}}
                        <td>

                            @if($request->status == 'Pending')

                                <span class="badge bg-warning">

                                    Pending

                                </span>

                            @elseif($request->status == 'Approved')

                                <span class="badge bg-success">

                                    Approved

                                </span>

                            @elseif($request->status == 'Rejected')

                                <span class="badge bg-danger">

                                    Rejected

                                </span>

                            @elseif($request->status == 'Completed')

                                <span class="badge bg-primary">

                                    Completed

                                </span>

                            @endif

                        </td>

                        {{-- Date --}}
                        <td>

                            {{ $request->created_at->format('d M Y') }}

                            <br>

                            <small class="text-muted">

                                {{ $request->created_at->format('h:i A') }}

                            </small>

                        </td>

                        {{-- Action --}}
                        <td>

                           <a href="{{ route('employee-assets.requests.edit',$request->id) }}"
   class="btn btn-warning btn-sm">

    <i class="fas fa-edit"></i>

    Update

</a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7"
                            class="text-center py-5">

                            <img
                                src="https://cdn-icons-png.flaticon.com/512/7486/7486740.png"
                                width="80">

                            <br><br>

                            <h5>

                                No Requests Found

                            </h5>

                            <small class="text-muted">

                                No employee asset requests available.

                            </small>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection