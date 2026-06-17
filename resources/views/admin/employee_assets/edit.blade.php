@extends('layouts.admin')

@section('title','Edit Asset Assignment')

@section('content')

<div class="container py-4">

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        <div class="card-header text-white p-4 border-0"
             style="background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);">

            <div class="d-flex align-items-center">

                <div class="me-3">
                    <i class="fas fa-laptop fs-2"></i>
                </div>

                <div>
                    <h4 class="mb-1">
                        Edit Asset Assignment
                    </h4>

                    <small class="text-light">
                        Update employee assigned assets
                    </small>
                </div>

            </div>

        </div>

        <div class="card-body p-4">

            <form action="{{ route('employee-assets.update',$employeeAsset->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                {{-- Employee --}}

                <div class="mb-4">

                    <label class="form-label fw-bold">
                        Employee
                    </label>

                    <select
                        name="employee_id"
                        class="form-select"
                        required>

                        <option value="">
                            Select Employee
                        </option>

                        @foreach($employees as $employee)

                            <option
                                value="{{ $employee->id }}"
                                {{ $employeeAsset->employee_id == $employee->id ? 'selected' : '' }}>

                                {{ $employee->firstname }}
                                {{ $employee->lastname }}

                            </option>

                        @endforeach

                    </select>

                </div>

                @php

                    $availableAssets = [

                        'Laptop',
                        'Desktop',
                        'Mouse',
                        'Keyboard',
                        'Mobile'

                    ];

                    $selectedAssets = collect(
                        $employeeAsset->asset_details ?? []
                    )->pluck('asset')->toArray();

                @endphp

                <div class="mb-4">

                    <label class="form-label fw-bold">
                        Assets
                    </label>

                    <div class="row">

                        @foreach($availableAssets as $asset)

                            @php

                                $checked =
                                    in_array(
                                        $asset,
                                        $selectedAssets
                                    );

                                $qty = 1;

                                if($employeeAsset->asset_details){

                                    foreach(
                                        $employeeAsset->asset_details
                                        as $detail
                                    ){

                                        if(
                                            $detail['asset']
                                            == $asset
                                        ){

                                            $qty =
                                            $detail['qty'];

                                        }

                                    }

                                }

                            @endphp

                            <div class="col-md-4 mb-3">

                                <div
                                    class="card asset-card h-100 {{ $checked ? 'active-selected' : '' }}">

                                    <div class="card-body">

                                        <div
                                            class="d-flex justify-content-between">

                                            <div>

                                                <h6 class="mb-0">
                                                    {{ $asset }}
                                                </h6>

                                            </div>

                                            <div>

                                                <input
                                                    type="checkbox"
                                                    class="form-check-input asset-checkbox"
                                                    name="assets[]"
                                                    value="{{ $asset }}"
                                                    {{ $checked ? 'checked' : '' }}>

                                            </div>

                                        </div>

                                        <div
                                            class="qty-wrapper mt-3"
                                            style="{{ $checked ? '' : 'display:none' }}">

                                            <label
                                                class="small text-muted">

                                                Quantity

                                            </label>

                                            <input
                                                type="number"
                                                min="1"
                                                class="form-control"
                                                value="{{ $qty }}"
                                                name="qty[{{ $asset }}]">

                                        </div>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

                {{-- Status --}}

                <div class="mb-4">

                    <label class="form-label fw-bold">
                        Asset Status
                    </label>

                    <select
                        name="status"
                        class="form-select">

                        <option
                            value="Assigned"
                            {{ $employeeAsset->status=='Assigned' ? 'selected' : '' }}>

                            Assigned

                        </option>

                        <option
                            value="Returned"
                            {{ $employeeAsset->status=='Returned' ? 'selected' : '' }}>

                            Returned

                        </option>

                        <option
                            value="Damaged"
                            {{ $employeeAsset->status=='Damaged' ? 'selected' : '' }}>

                            Damaged

                        </option>

                        <option
                            value="Lost"
                            {{ $employeeAsset->status=='Lost' ? 'selected' : '' }}>

                            Lost

                        </option>

                    </select>

                </div>

                {{-- Message --}}

                <div class="mb-4">

                    <label class="form-label fw-bold">
                        Notes
                    </label>

                    <textarea
                        name="message"
                        class="form-control"
                        rows="4">{{ old('message',$employeeAsset->message) }}</textarea>

                </div>

                <div
                    class="d-flex justify-content-end gap-2">

                    <a href="{{ route('employee-assets.index') }}"
                       class="btn btn-light">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-save me-1"></i>

                        Update Asset

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<style>

.asset-card{
    border:1px solid #e5e7eb;
    transition:.3s;
    cursor:pointer;
}

.asset-card:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.active-selected{
    border:2px solid #0d6efd;
    background:#eff6ff;
}

</style>

<script>

document.addEventListener('DOMContentLoaded', function(){

    document
    .querySelectorAll('.asset-checkbox')
    .forEach(function(box){

        box.addEventListener('change', function(){

            let card =
                this.closest('.asset-card');

            let qty =
                card.querySelector('.qty-wrapper');

            if(this.checked){

                card.classList.add(
                    'active-selected'
                );

                qty.style.display='block';

            }else{

                card.classList.remove(
                    'active-selected'
                );

                qty.style.display='none';

            }

        });

    });

});

</script>

@endsection