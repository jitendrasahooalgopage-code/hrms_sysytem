@extends('layouts.admin')

@section('title')
    {{ __('Apply Leave Record') }}
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f8fafc; min-height: 100vh;">
    
    <div class="pb-3 mb-4 border-bottom" style="border-color: #e2e8f0 !important;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-1" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="{{ route('leaves.index') }}" class="text-decoration-none text-muted">Leave Management</a></li>
                <li class="breadcrumb-item active text-dark font-weight-bold" aria-current="page">Apply Leave</li>
            </ol>
        </nav>
        <h1 class="h3 font-weight-bold text-dark mb-0" style="letter-spacing: -0.5px; font-weight: 800;">Create Leave Request</h1>
    </div>

    <div class="row justify-content-start">
        <div class="col-lg-8 mb-4">
            
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm py-3 px-4 mb-4" style="background-color: #ecfdf5; color: #065f46; border-left: 4px solid #10b981 !important; border-radius: 6px;">
                    <i class="fas fa-check-circle mr-2" style="font-size: 1.1rem;"></i>
                    <span class="font-weight-medium small">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm py-3 px-4 mb-4" style="background-color: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444 !important; border-radius: 6px;">
                    <div class="d-flex align-items-center mb-1 font-weight-bold small">
                        <i class="fas fa-exclamation-circle mr-2" style="font-size: 1.1rem;"></i> Review Following Quota Violations:
                    </div>
                    <ul class="mb-0 pl-4 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card border-0 shadow-sm" style="border-radius: 10px; border: 1px solid #e2e8f0 !important; background: #fff; overflow: hidden;">
                <div class="card-header bg-white border-bottom py-3 px-4" style="border-color: #f1f5f9 !important;">
                    <h5 class="card-title font-weight-bold text-dark mb-0" style="font-size: 0.95rem; font-weight: 700;">Leave Application Intake</h5>
                </div>
                
                <form method="POST" action="{{ Auth::user()->role->slug === 'super-admin' ? route('leaves.store') : (Auth::user()->role->slug === 'administrator' ? route('admin.leaves.store') : (Auth::user()->role->slug === 'employee' ? route('employee.leaves.store') : route('hr.leaves.store'))) }}">
                    @csrf

                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-12 form-group mb-3">
                                <label for="title" class="small font-weight-bold text-muted text-uppercase tracking-wider mb-2 d-block" style="font-size: 0.68rem; letter-spacing: 0.5px;">Leave Brief Title / Subject</label>
                                <input type="text" name="title" id="title" class="form-control input-element-custom" placeholder="e.g., Annual Family Vacation / Out of Station" value="{{ old('title') }}" required style="border-radius: 6px; height: 40px; font-size: 0.88rem; border-color: #cbd5e1;">
                            </div>

                            <div class="col-12 form-group mb-3">
                                <label for="employee_id" class="small font-weight-bold text-muted text-uppercase tracking-wider mb-2 d-block" style="font-size: 0.68rem; letter-spacing: 0.5px;">Employee Profile Reference</label>
                                @if (Auth::user()->role->slug === 'employee')
                                    <input type="hidden" name="employee_id" id="employee_id" value="{{ Auth::user()->employee->id }}">
                                    <input type="text" class="form-control bg-light text-dark font-weight-bold px-3" value="{{ Auth::user()->employee->firstname }} {{ Auth::user()->employee->lastname }} ({{ Auth::user()->employee->unique_id }})" readonly style="border-radius: 6px; height: 40px; font-size: 0.88rem; border-color: #cbd5e1;">
                                @else
                                    <select name="employee_id" id="employee_id" class="form-control input-element-custom" required style="border-radius: 6px; height: 40px; font-size: 0.85rem; border-color: #cbd5e1;">
                                        <option value="" disabled selected>-- Select Target Employee Profile --</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->unique_id }} - {{ $employee->firstname }} {{ $employee->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="col-12 mb-3" id="hierarchy-info" style="display:none">
                                <div class="alert alert-info border-0 py-2.5 px-3 mb-0 d-flex align-items-center" style="background-color: #f0f7ff; color: #1e3a8a; font-size: 0.82rem; border-radius: 6px;">
                                    <i class="fas fa-project-diagram mr-3 text-primary" style="font-size: 1rem;"></i>
                                    <div>
                                        <strong class="d-block mb-0.5">Workflow Route Map Assignment Chain:</strong>
                                        <span id="approver-chain" class="font-weight-medium text-secondary">Loading...</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 mx-0 px-0 width-100-percent" style="width: 100%;">
                                <div class="col-md-6 form-group mb-3 pl-0 pr-md-2">
                                    <label for="start_date" class="small font-weight-bold text-muted text-uppercase tracking-wider mb-2 d-block" style="font-size: 0.68rem; letter-spacing: 0.5px;">Absence Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control input-element-custom" value="{{ old('start_date') }}" required style="border-radius: 6px; height: 40px; font-size: 0.88rem; border-color: #cbd5e1;">
                                </div>

                                <div class="col-md-6 form-group mb-3 pr-0 pl-md-2">
                                    <label for="end_date" class="small font-weight-bold text-muted text-uppercase tracking-wider mb-2 d-block" style="font-size: 0.68rem; letter-spacing: 0.5px;">Absence Concluding Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control input-element-custom" value="{{ old('end_date') }}" required style="border-radius: 6px; height: 40px; font-size: 0.88rem; border-color: #cbd5e1;">
                                </div>
                            </div>

                            <div class="col-12 form-group mb-3">
                                <label for="leave_type" class="small font-weight-bold text-muted text-uppercase tracking-wider mb-2 d-block" style="font-size: 0.68rem; letter-spacing: 0.5px;">Leave Category Policy Type</label>
                                <select name="leave_type" id="leave_type" class="form-control input-element-custom" required style="border-radius: 6px; height: 40px; font-size: 0.85rem; border-color: #cbd5e1;">
                                    <option value="" disabled selected>-- Select Dynamic Quota Type --</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('leave_type') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }} ({{ $type->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 form-group mb-0">
                                <label for="leave_reason" class="small font-weight-bold text-muted text-uppercase tracking-wider mb-2 d-block" style="font-size: 0.68rem; letter-spacing: 0.5px;">Detailed Statement of Reason / Cover Notes</label>
                                <textarea name="leave_reason" class="form-control input-element-custom" id="leave_reason" rows="3" placeholder="Provide context or explanation for documentation validation logs..." style="border-radius: 6px; font-size: 0.88rem; border-color: #cbd5e1; padding: 10px 12px;">{{ old('leave_reason') }}</textarea>
                            </div>

                        </div>
                    </div>
                    
                    <div class="card-footer border-top bg-light-soft py-3 px-4 d-flex justify-content-end" style="border-color: #f1f5f9 !important; background-color: #f8fafc;">
                        <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm" style="background-color: #0066ff; border: none; height: 40px; border-radius: 6px; font-size: 0.88rem;">
                            <i class="fas fa-paper-plane mr-2" style="font-size: 0.8rem;"></i> File Leave Application
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>

<style>
    .input-element-custom:focus {
        border-color: #0066ff !important;
        box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1) !important;
        outline: none;
    }
</style>
@endsection

@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const employeeSelect = document.getElementById('employee_id');

        if (employeeSelect) {
            employeeSelect.addEventListener('change', function () {
                const empId = this.value;
                const infoBox = document.getElementById('hierarchy-info');
                const chainText = document.getElementById('approver-chain');

                if (!empId) {
                    infoBox.style.display = 'none';
                    return;
                }

                fetch(`/employee/employees/${empId}/hierarchy-chain`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.chain && data.chain.length > 0) {
                            chainText.innerHTML = data.chain.map(name => `<span class="badge badge-light border border-gray text-dark px-2.5 py-1 font-weight-bold m-0.5">${name}</span>`).join(' <i class="fas fa-long-arrow-alt-right mx-1.5 text-muted small"></i> `);
                        } else {
                            chainText.innerHTML = '<span class="text-danger font-weight-bold"><i class="fas fa-info-circle mr-1"></i> No hierarchy assigned. HR will handle directly.</span>';
                        }
                        infoBox.style.display = 'block';
                    })
                    .catch(() => {
                        chainText.textContent = 'Could not load corporate hierarchy routing structure maps.';
                        infoBox.style.display = 'block';
                    });
            });

            // Trigger automatically if initialized with an old value state or an asset parameter map string selection
            if (employeeSelect.value) {
                employeeSelect.dispatchEvent(new Event('change'));
            }
        }
    });
</script>
@endsection