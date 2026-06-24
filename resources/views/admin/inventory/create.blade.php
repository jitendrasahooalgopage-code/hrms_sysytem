@extends('layouts.admin')

@section('title')
    Add Inventory
@endsection

@section('header')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h1 class="h3 mb-1">
            Add Inventory
        </h1>

        <small class="text-muted">
            Create a new inventory item
        </small>

    </div>

    <a href="{{ route('inventory.index') }}"
       class="btn btn-secondary">

        <i class="fas fa-arrow-left"></i>

        <span class="ps-1">
            Back
        </span>

    </a>

</div>

@endsection


@section('content')

<div class="card shadow-sm border-0">

    <div class="card-header">

        <h5 class="mb-0">
            Inventory Information
        </h5>

    </div>

    <div class="card-body">

        <form action="{{ route('inventory.store') }}"
              method="POST">

            @csrf

            {{-- Asset Type --}}
            <div class="mb-3">

                <label class="form-label">

                    Asset Type

                </label>

                <input
                    type="text"
                    name="asset_type"
                    class="form-control"
                    list="asset-types"
                    placeholder="Ex: Laptop, Desktop, Mobile, Bag, Headphone"
                    required>

                <datalist id="asset-types">

                    <option value="Laptop">
                    <option value="Desktop">
                    <option value="Mobile">
                    <option value="Mouse">
                    <option value="Keyboard">
                    <option value="Bag">
                    <option value="Headphone">
                    <option value="Charger">
                    <option value="Tablet">
                    <option value="Power Bank">
                    <option value="Webcam">

                </datalist>

            </div>


            {{-- Notes --}}
            <div class="mb-3">

                <label class="form-label">

                    Notes

                </label>

                <textarea
                    name="message"
                    rows="4"
                    class="form-control"
                    placeholder="Write remarks, purchase details, warranty information etc."></textarea>

            </div>


            {{-- Status --}}
            <div class="mb-4">

                <label class="form-label">

                    Status

                </label>

                <select
                    name="status"
                    class="form-control">

                    <option value="Available">

                        Available

                    </option>

                    <option value="Assigned">

                        Assigned

                    </option>

                    <option value="Damaged">

                        Damaged

                    </option>

                    <option value="Lost">

                        Lost

                    </option>

                    <option value="Repair">

                        Repair

                    </option>

                </select>

            </div>


            {{-- Buttons --}}
            <div class="d-flex gap-2">

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fas fa-save"></i>

                    Save Inventory

                </button>

                <a href="{{ route('inventory.index') }}"
                   class="btn btn-light border">

                    Cancel

                </a>

            </div>

        </form>

    </div>

</div>

@endsection