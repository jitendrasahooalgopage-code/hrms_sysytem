@extends('layouts.admin')

@section('title')
    {{ __('Asset Management') }}
@endsection

@section('header')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-3">Asset Management</h1>

    <a href="{{ route('employee-assets.create') }}"
       class="btn btn-primary">
        <i class="fas fa-plus"></i>
        <span class="ps-1">Assign Asset</span>
    </a>
</div>
@endsection

@section('content')

<section class="row">
    <div class="col-12">

        <div class="card flex-fill">

            <div class="card-header">
                <h5 class="card-title mb-0">
                    Employee Asset DataTable
                </h5>
            </div>

            <table class="table data-table">

                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Employee Name</th>
                        <th>Assets</th>
                        <th>Status</th>
                        <th>Assigned Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($employeeAssets as $asset)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            <strong>
                                {{ $asset->employee->firstname ?? '' }}
                                {{ $asset->employee->lastname ?? '' }}
                            </strong>
                        </td>

                        <td>

                            @if($asset->asset_details)

                                @foreach($asset->asset_details as $item)

                                    <span class="badge bg-primary">
                                        {{ $item['asset'] }}
                                    </span>

                                    <span class="badge bg-secondary">
                                        Qty: {{ $item['qty'] }}
                                    </span>

                                    <br>

                                @endforeach

                            @else

                                <span class="badge bg-primary">
                                    {{ $asset->asset_name }}
                                </span>

                            @endif

                        </td>

                        <td>

                            @if($asset->status == 'Assigned')
                                <span class="badge bg-success">
                                    Assigned
                                </span>

                            @elseif($asset->status == 'Returned')
                                <span class="badge bg-info">
                                    Returned
                                </span>

                            @elseif($asset->status == 'Damaged')
                                <span class="badge bg-warning">
                                    Damaged
                                </span>

                            @else
                                <span class="badge bg-danger">
                                    Lost
                                </span>
                            @endif

                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($asset->assigned_date)->format('d M Y') }}
                        </td>

                        <td class="d-flex align-items-center">

                            <a href="{{ route('employee-assets.edit',$asset->id) }}"
                               class="btn btn-info btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>

                        

                            <form action="{{ route('employee-assets.destroy',$asset->id) }}"
                                  method="POST">

                                @csrf
                                @method('DELETE')

                                <a href="#"
                                   class="btn btn-danger btn-sm"
                                   onclick="event.preventDefault(); if(confirm('Delete Asset?')) this.closest('form').submit();">
                                    <i class="fas fa-trash-alt"></i>
                                </a>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="text-center">
                            No Asset Records Found
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>
</section>

@endsection

@section('script')
@endsection