@extends('layouts.admin')

@section('title')
    Edit Inventory
@endsection

@section('content')

<div class="card shadow-sm border-0">

    <div class="card-header">

        <h5 class="mb-0">

            Edit Inventory

        </h5>

    </div>

    <div class="card-body">

        <form action="{{ route('inventory.update',$inventory->id) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="mb-3">

                <label>

                    Asset Type

                </label>

                <input
                    type="text"
                    class="form-control"
                    name="asset_type"
                    value="{{ $inventory->asset_type }}">

            </div>

            <div class="mb-3">

                <label>

                    Notes

                </label>

                <textarea
                    class="form-control"
                    rows="4"
                    name="message">{{ $inventory->message }}</textarea>

            </div>

            <div class="mb-3">

                <label>

                    Status

                </label>

                <select
                    class="form-control"
                    name="status">

                    <option value="Available"
                        {{ $inventory->status=='Available' ? 'selected' : '' }}>
                        Available
                    </option>

                    <option value="Assigned"
                        {{ $inventory->status=='Assigned' ? 'selected' : '' }}>
                        Assigned
                    </option>

                    <option value="Damaged"
                        {{ $inventory->status=='Damaged' ? 'selected' : '' }}>
                        Damaged
                    </option>

                    <option value="Lost"
                        {{ $inventory->status=='Lost' ? 'selected' : '' }}>
                        Lost
                    </option>

                    <option value="Repair"
                        {{ $inventory->status=='Repair' ? 'selected' : '' }}>
                        Repair
                    </option>

                </select>

            </div>

            <button class="btn btn-primary">

                Update Inventory

            </button>

        </form>

    </div>

</div>

@endsection