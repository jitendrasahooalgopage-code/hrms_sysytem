@extends('layouts.admin')

@section('title')
    Inventory
@endsection

@section('header')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h1 class="h3 mb-1">
            Inventory Management
        </h1>

        <small class="text-muted">
            Manage all inventory items
        </small>

    </div>

    <a href="{{ route('inventory.create') }}"
       class="btn btn-primary">

        <i class="fas fa-plus"></i>

        <span class="ps-1">
            Add Inventory
        </span>

    </a>

</div>

@endsection


@section('content')

<div class="card shadow-sm border-0">

    <div class="card-header">

        <h5 class="mb-0">
            Inventory List
        </h5>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                <tr>

                    <th>ID</th>

                    <th>Asset Type</th>

                    <th>Status</th>

                    <th>Created Date</th>

                    <th>Action</th>

                </tr>

                </thead>

                <tbody>

                @forelse($inventories as $inventory)

                    <tr>

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            <span class="badge bg-primary">

                                {{ $inventory->asset_type }}

                            </span>

                        </td>

                        <td>

                            @if($inventory->status=='Available')

                                <span class="badge bg-success">

                                    Available

                                </span>

                            @elseif($inventory->status=='Assigned')

                                <span class="badge bg-warning">

                                    Assigned

                                </span>

                            @elseif($inventory->status=='Damaged')

                                <span class="badge bg-danger">

                                    Damaged

                                </span>

                            @else

                                <span class="badge bg-secondary">

                                    {{ $inventory->status }}

                                </span>

                            @endif

                        </td>

                        <td>

                            {{ $inventory->created_at->format('d M Y') }}

                        </td>

                        <td class="d-flex gap-2">

    <a href="{{ route('inventory.edit',$inventory->id) }}"
       class="btn btn-info btn-sm">

        <i class="fas fa-edit"></i>

    </a>

    <form action="{{ route('inventory.destroy',$inventory->id) }}"
          method="POST">

        @csrf
        @method('DELETE')

        <button
            onclick="return confirm('Delete Inventory?')"
            class="btn btn-danger btn-sm">

            <i class="fas fa-trash"></i>

        </button>

    </form>

</td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5"
                            class="text-center py-5">

                            No Inventory Found

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection