@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        
        <!-- Premium Header Gradient -->
        <div class="card-header bg-dark text-white p-4 border-0 d-flex align-items-center gap-3" 
             style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
            <div class="rounded-3 bg-white bg-opacity-10 p-2 d-inline-flex">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-info"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div>
                <h4 class="mb-1 fw-bold tracking-tight">Assign Corporate Assets</h4>
                <p class="text-muted small mb-0 text-opacity-75">Allocate hardware resources and devices to employee profiles.</p>
            </div>
        </div>

        <div class="card-body p-4 bg-light bg-opacity-25">
            <form action="{{ route('employee-assets.store') }}" method="POST">
                @csrf

                <!-- Employee Selection Dropdown -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase tracking-wider">Select Recipient Employee</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg>
                        </span>
                        <select name="employee_id" class="form-select form-control-lg border-start-0 ps-0 text-dark" required style="font-size: 0.95rem;">
                            <option value="" disabled selected>Choose an employee...</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->firstname }} {{ $employee->lastname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Asset Modern Grid Selector -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase tracking-wider mb-3">
                        Asset Selection & Allocation
                    </label>

                    <div class="row g-3">
                        @php
                            $availableAssets = [
                                ['id' => 'laptop', 'value' => 'Laptop', 'emoji' => '💻', 'desc' => 'Workstations & Notebooks'],
                                ['id' => 'desktop', 'value' => 'Desktop', 'emoji' => '🖥', 'desc' => 'Fixed office monitors & towers'],
                                ['id' => 'mouse', 'value' => 'Mouse', 'emoji' => '🖱', 'desc' => 'Ergonomic or standard pointer devices'],
                                ['id' => 'keyboard', 'value' => 'Keyboard', 'emoji' => '⌨', 'desc' => 'Mechanical or standard layout keyboards'],
                                ['id' => 'mobile', 'value' => 'Mobile', 'emoji' => '📱', 'desc' => 'Company smartphones & testing devices']
                            ];
                        @endphp

                        @foreach($availableAssets as $asset)
                        <div class="col-md-6 col-lg-4">
                            <div class="card asset-card h-100 border border-light-subtle rounded-3 transition-all">
                                <label class="card-body p-3 m-0 d-flex flex-column justify-content-between cursor-pointer style-label" for="{{ $asset['id'] }}">
                                    
                                    <div class="d-flex align-items-start justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="fs-3 bg-light rounded-3 p-2 d-inline-block line-height-1">
                                                {{ $asset['emoji'] }}
                                            </span>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">{{ $asset['value'] }}</h6>
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $asset['desc'] }}</small>
                                            </div>
                                        </div>
                                        <div class="form-check m-0 pointer-events-none">
                                            <input class="form-check-input asset-checkbox custom-check" 
                                                   type="checkbox" 
                                                   name="assets[]" 
                                                   value="{{ $asset['value'] }}" 
                                                   id="{{ $asset['id'] }}">
                                        </div>
                                    </div>

                                    <!-- Smoothly Collapsible Quantity Input -->
                                    <div class="qty-wrapper overflow-hidden mt-2" style="max-height: 0; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); opacity: 0;">
                                        <div class="pt-2 border-top border-light">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="small fw-semibold text-muted">Assign Quantity:</span>
                                                <input type="number" 
                                                       class="form-control form-control-sm text-center fw-bold" 
                                                       name="qty[{{ $asset['value'] }}]" 
                                                       min="1" 
                                                       value="1"
                                                       style="width: 80px; border-radius: 6px;"
                                                       disabled>
                                            </div>
                                        </div>
                                    </div>

                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Handover Message Notes -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase tracking-wider">Internal Handover Notes</label>
                    <textarea name="message" 
                              class="form-control rounded-3" 
                              rows="3" 
                              placeholder="Add optional internal references, condition notes, or hardware serial numbers..."></textarea>
                </div>

                <!-- Modern Interactive Action Bar -->
                <div class="d-flex align-items-center justify-content-end gap-2 border-top pt-4 mt-2">
                    <button type="button" class="btn btn-light px-4 py-2 rounded-3 text-secondary fw-semibold">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold d-flex align-items-center gap-2 shadow-sm brand-btn">
                        <span>Save Allocations</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"></path></svg>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Embedded Micro-Styles & Interaction Logic -->
<style>
    .cursor-pointer { cursor: pointer; }
    .pointer-events-none { pointer-events: none; }
    .line-height-1 { line-height: 1; }
    
    /* Interactive Card UX States */
    .asset-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        background-color: #ffffff;
    }
    .asset-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1 !important;
    }
    
    /* Active Card Visuals */
    .asset-card.active-selected {
        border-color: #0d6efd !important;
        background-color: #f8fafc;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.08);
    }

    /* Custom sizing for form components */
    .form-select, .form-control {
        border-color: #e2e8f0;
    }
    .form-select:focus, .form-control:focus {
        border-color: #94a3b8;
        box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
    }
    .brand-btn {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
    }
    .brand-btn:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.asset-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const card = this.closest('.asset-card');
            const qtyWrapper = card.querySelector('.qty-wrapper');
            const qtyInput = qtyWrapper.querySelector('input[type="number"]');

            if (this.checked) {
                card.classList.add('active-selected');
                
                // Animate showing quantity wrapper open
                qtyWrapper.style.maxHeight = "100px";
                qtyWrapper.style.opacity = "1";
                
                // Allow form data payload submission
                qtyInput.removeAttribute('disabled');
            } else {
                card.classList.remove('active-selected');
                
                // Animate shutting quantity wrapper
                qtyWrapper.style.maxHeight = "0";
                qtyWrapper.style.opacity = "0";
                
                // Block input data payload from being submitted
                qtyInput.setAttribute('disabled', 'true');
            }
        });
    });
});
</script>
@endsection