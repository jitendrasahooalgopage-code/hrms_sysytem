@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f8fafc; min-height: 100vh;">
    
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between pb-3 mb-4 border-bottom" style="border-color: #e2e8f0 !important;">
        <div class="mb-3 mb-md-0">
            <h1 class="h3 font-weight-bold text-dark mb-1" style="letter-spacing: -0.5px; font-weight: 800;">Leave Assignment Console</h1>
            <p class="text-muted small mb-0" style="font-size: 0.85rem;">Bulk assign or synchronize dynamic leave policies onto employee profiles.</p>
        </div>
        
        <div class="bg-white p-2 rounded border shadow-sm" style="border-radius: 8px; border-color: #e2e8f0 !important;">
            <form action="{{ route('employee-leave.assignments.index') }}" method="GET" id="yearPickerForm" class="form-inline m-0">
                <i class="far fa-calendar-alt text-primary mr-2 ml-1" style="font-size: 0.9rem;"></i>
                <label for="year" class="mr-2 font-weight-bold text-secondary text-uppercase tracking-wider small" style="font-size: 0.7rem;">Active Policy Year:</label>
                <select name="year" id="year" class="form-control form-control-sm border-0 bg-light text-primary font-weight-bold px-3" style="font-size: 0.85rem; height: 32px; border-radius: 6px;" onchange="document.getElementById('yearPickerForm').submit();">
                    @for($y = date('Y') - 2; $y <= date('Y') + 3; $y++)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }} Policy</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm py-3 px-4 mb-4" style="background-color: #ecfdf5; color: #065f46; border-left: 4px solid #10b981 !important; border-radius: 6px;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm py-3 px-4 mb-4" style="background-color: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444 !important; border-radius: 6px;">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('employee-leave.assignments.assign') }}" method="POST">
        @csrf
        <input type="hidden" name="year" value="{{ $selectedYear }}">

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm rounded-lg" style="border-radius: 10px; border: 1px solid #e2e8f0 !important; background: #fff; overflow: hidden;">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                        <h5 class="font-weight-bold text-dark m-0" style="font-size: 0.95rem; font-weight: 700;">Employee Roll Ledger</h5>
                        <div class="custom-control custom-checkbox font-weight-bold" style="font-size: 0.85rem;">
                            <input type="checkbox" class="custom-control-input" id="selectAllEmployees">
                            <label class="custom-control-label text-primary font-weight-bold" for="selectAllEmployees" style="cursor: pointer;">Select All Employees</label>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 custom-premium-table">
                                <thead class="text-uppercase font-weight-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.8px; background-color: #f8fafc;">
                                    <tr>
                                        <th class="px-4 py-3 text-center" style="width: 8%;">Select</th>
                                        <th class="py-3">Employee Profile</th>
                                        <th class="py-3">Department</th>
                                        <th class="py-3" style="width: 40%;">Current Live Allowances ({{ $selectedYear }})</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.85rem;">
                                    @foreach($employees as $emp)
                                        <tr class="row-hover-transition">
                                            <td class="px-4 py-3 align-middle text-center">
                                                <div class="custom-control custom-checkbox d-inline-block">
                                                    <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="custom-control-input employee-checkbox" id="empCheck{{ $emp->id }}">
                                                    <label class="custom-control-label" for="empCheck{{ $emp->id }}" style="cursor: pointer;"></label>
                                                </div>
                                            </td>
                                            
                                            <td class="py-3 align-middle">
                                                <div class="font-weight-bold text-dark" style="font-size: 0.88rem;">{{ $emp->firstname }} {{ $emp->lastname }}</div>
                                                <div class="text-muted small" style="font-size: 0.75rem;">{{ $emp->unique_id }}</div>
                                            </td>
                                            
                                            <td class="py-3 align-middle">
                                                <span class="text-secondary font-weight-medium" style="color: #4b5563;">{{ $emp->department?->title ?? 'N/A' }}</span>
                                            </td>
                                            
                                            <td class="py-3 align-middle">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    @forelse($emp->leaveAllocations as $alloc)
                                                        <div class="border rounded px-2 py-1 mr-2 mb-1 bg-white" style="border-radius: 6px; font-size: 0.75rem; border-color: #e2e8f0 !important;">
                                                            <span class="font-weight-bold text-dark">{{ $alloc->leaveType->code }}:</span> 
                                                            <span class="text-success font-weight-bold">{{ $alloc->allocated_days }}d</span>
                                                        </div>
                                                    @empty
                                                        <span class="text-muted small italic" style="font-style: italic; color: #9ca3af; font-size: 0.75rem;">Unassigned</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="border-radius: 10px; border: 1px solid #e2e8f0 !important; background: #fff; top: 20px; z-index: 5;">
                    <div class="card-header py-3 px-4 border-bottom d-flex align-items-center bg-white" style="border-color: #f1f5f9 !important;">
                        <i class="fas fa-layer-group text-primary mr-2" style="font-size: 0.9rem;"></i>
                        <h6 class="font-weight-bold m-0 text-dark" style="font-size: 0.88rem; font-weight: 700;">Select Leave Policies</h6>
                    </div>
                    <div class="card-body px-4 py-4">
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small font-weight-bold text-muted text-uppercase tracking-wider" style="font-size: 0.68rem;">Available Leave Quotas</span>
                            <button type="button" class="btn btn-sm btn-link text-primary p-0 font-weight-bold" id="selectAllLeaves" style="font-size: 0.75rem; text-decoration: none;">Select All Types</button>
                        </div>

                        <div class="p-3 border rounded bg-light-soft mb-4" style="background-color: #f8fafc; max-height: 240px; overflow-y: auto; border-radius: 6px;">
                            @forelse($globalPolicies as $policy)
                                <div class="custom-control custom-checkbox mb-2.5">
                                    <input type="checkbox" name="leave_type_ids[]" value="{{ $policy->leave_type_id }}" class="custom-control-input leave-checkbox" id="leavePolicy{{ $policy->leave_type_id }}">
                                    <label class="custom-control-label text-dark d-flex justify-content-between align-items-center pr-1" for="leavePolicy{{ $policy->leave_type_id }}" style="cursor: pointer; font-size: 0.85rem;">
                                        <span>{{ $policy->leaveType->name }}</span>
                                        <span class="badge badge-success px-2 py-1 ml-2" style="font-size: 0.72rem; font-weight: 700; text-decoration: underline;">{{ $policy->days }} Days</span>
                                    </label>
                                </div>
                            @empty
                                <div class="text-center py-3 text-danger small font-weight-bold">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> No global limits configured for {{ $selectedYear }}.
                                </div>
                            @endforelse
                        </div>

                        <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm" style="background-color: #0066ff; border: none; height: 44px; font-size: 0.88rem; border-radius: 6px;">
                            <i class="fas fa-check-double mr-2"></i> Apply Policy To Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectAllEmployees = document.getElementById("selectAllEmployees");
        const employeeCheckboxes = document.querySelectorAll(".employee-checkbox");
        
        const selectAllLeaves = document.getElementById("selectAllLeaves");
        const leaveCheckboxes = document.querySelectorAll(".leave-checkbox");

        // Toggle Employee Matrix Selection Checkboxes 
        selectAllEmployees.addEventListener("change", function() {
            employeeCheckboxes.forEach(cb => {
                cb.checked = selectAllEmployees.checked;
            });
        });

        // Toggle Leave Type Rules Selection Packages Link Button
        let leavesToggleState = false;
        selectAllLeaves.addEventListener("click", function() {
            leavesToggleState = !leavesToggleState;
            leaveCheckboxes.forEach(cb => {
                cb.checked = leavesToggleState;
            });
            selectAllLeaves.innerText = leavesToggleState ? "Deselect All" : "Select All Types";
        });
    });
</script>

<style>
    .row-hover-transition { transition: background-color 0.15s ease-in-out; }
    .row-hover-transition:hover { background-color: #f8fafc !important; }
    .custom-premium-table th, .custom-premium-table td { vertical-align: middle !important; border-top: none !important; border-bottom: 1px solid #f1f5f9; }
</style>
@endsection