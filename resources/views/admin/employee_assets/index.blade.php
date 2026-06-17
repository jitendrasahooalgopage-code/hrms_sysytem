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

                        <td>
                            <div class="d-inline-flex gap-2 align-items-center">

                                <a href="{{ route('employee-assets.edit', $asset->id) }}"
                                   class="btn btn-sm action-btn btn-light-edit"
                                   title="Edit Allocation"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="{{ route('employee-assets.requests', $asset->id) }}"
                                   class="btn btn-sm action-btn btn-light-requests"
                                   title="View Support Requests"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-clipboard-list"></i>
                                </a>

                                <form action="{{ route('employee-assets.destroy', $asset->id) }}"
                                      method="POST"
                                      class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <a href="#"
                                       class="btn btn-sm action-btn btn-light-delete"
                                       title="Delete Record"
                                       data-bs-toggle="tooltip"
                                       onclick="event.preventDefault(); if(confirm('Are you absolutely sure you want to delete this asset record?')) this.closest('form').submit();">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </form>

                            </div>
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

<style>
    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px !important;
        transition: all 0.2s ease-in-out;
        border: 1px solid transparent;
        background-color: #f8fafc;
    }

    /* Edit Interaction State Profile Rules */
    .btn-light-edit {
        color: #0284c7;
        border-color: #e0f2fe;
    }
    .btn-light-edit:hover {
        background-color: #0284c7 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.2);
    }

    /* Requests Log Interaction State Profile Rules */
    .btn-light-requests {
        color: #d97706;
        border-color: #fef3c7;
    }
    .btn-light-requests:hover {
        background-color: #d97706 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(217, 119, 6, 0.2);
    }

    /* Delete Action Interaction State Profile Rules */
    .btn-light-delete {
        color: #dc2626;
        border-color: #fee2e2;
    }
    .btn-light-delete:hover {
        background-color: #dc2626 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.2);
    }
</style>

@endsection

@section('script')
<script>
    // Bootstrapping engine initialization scripts for responsive tooltips
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection