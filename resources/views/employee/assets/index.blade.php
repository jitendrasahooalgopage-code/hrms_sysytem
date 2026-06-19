@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead class="bg-light text-secondary text-uppercase small tracking-wider" style="font-size: 0.75rem;">
                    <tr>
                        <th class="ps-4 py-3" style="width: 8%;">#</th>
                        <th class="py-3" style="width: 47%;">Asset Details & Inventory</th>
                        <th class="py-3" style="width: 15%;">Status</th>
                        <th class="pe-4 py-3 text-end" style="width: 30%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                        <tr>
                            <td class="ps-4 fw-medium text-secondary font-monospace" style="font-size: 0.95rem;">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            
                            <td>
                                <div class="d-flex flex-column gap-3 py-2">
                                    @php
                                        // Handle normalization if asset_details isn't automatically cast to an array
                                        $details = is_string($asset->asset_details) ? json_decode($asset->asset_details, true) : ($asset->asset_details ?? []);
                                    @endphp

                                    @foreach($details as $item)
                                        @php
                                            $cleanName = trim($item['asset'] ?? $asset->asset_name);
                                        @endphp
                                        <div class="asset-item-group">
                                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                                <span class="badge bg-light text-dark border border-light-subtle rounded-3 px-3 py-2 fw-semibold small-badge d-inline-flex align-items-center gap-2">
                                                    <span class="fs-6">
                                                        @switch($cleanName)
                                                            @case('Laptop') 💻 @break
                                                            @case('Desktop') 🖥 @break
                                                            @case('Mouse') 🖱 @break
                                                            @case('Keyboard') ⌨ @break
                                                            @case('Mobile') 📱 @break
                                                            @default 📦
                                                        @endswitch
                                                    </span>
                                                    <span class="text-dark fw-bold">{{ $cleanName }}</span>
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-0.5 rounded-2 font-monospace" style="font-size: 0.75rem;">
                                                        x{{ $item['qty'] ?? 1 }}
                                                    </span>
                                                </span>
                                            </div>

                                            @if(!empty($item['items']))
                                                <div class="ps-2 text-muted row g-1" style="font-size: 0.825rem; max-width: 500px;">
                                                    @foreach($item['items'] as $index => $subItem)
                                                        <div class="col-12 d-flex flex-wrap align-items-center gap-1 mb-1">
                                                            <span class="text-secondary font-monospace fw-bold">#{{ $index + 1 }}:</span>
                                                            
                                                            @if(isset($subItem['serial_no']))
                                                                <span class="bg-white text-dark px-2 py-0.5 rounded border small">SN: <strong class="font-monospace text-secondary">{{ $subItem['serial_no'] }}</strong></span>
                                                            @endif

                                                            @if(isset($subItem['cpu_serial_no']))
                                                                <span class="bg-white text-dark px-2 py-0.5 rounded border small">CPU: <strong class="font-monospace text-secondary">{{ $subItem['cpu_serial_no'] }}</strong></span>
                                                            @endif

                                                            @if(isset($subItem['monitor_serial_no']))
                                                                <span class="bg-white text-dark px-2 py-0.5 rounded border small">Mon: <strong class="font-monospace text-secondary">{{ $subItem['monitor_serial_no'] }}</strong></span>
                                                            @endif

                                                            @if(isset($subItem['imei']))
                                                                <span class="bg-white text-dark px-2 py-0.5 rounded border small">IMEI: <strong class="font-monospace text-secondary">{{ $subItem['imei'] }}</strong></span>
                                                            @endif

                                                            @if(isset($subItem['sim_provider']))
                                                                <span class="bg-white text-dark px-2 py-0.5 rounded border small">SIM: <strong class="text-secondary">{{ $subItem['sim_provider'] }}</strong></span>
                                                            @endif

                                                            @if(isset($subItem['plan_days']))
                                                                <span class="bg-white text-dark px-2 py-0.5 rounded border small">Plan: <strong class="text-secondary">{{ $subItem['plan_days'] }} Days</strong></span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            
                            <td>
                                <span class="badge px-2.5 py-1.5 rounded-pill fw-semibold status-pill {{ $asset->status === 'Assigned' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                    <span class="status-dot {{ $asset->status === 'Assigned' ? 'bg-success' : 'bg-secondary' }}"></span>
                                    {{ $asset->status }}      
                                </span>
                            </td>

                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-2 justify-content-end align-items-center">
                                    
                                    <a href="{{ route('employee.asset-request.index', $asset->id) }}"
                                       class="btn btn-sm btn-white border border-light-subtle text-secondary rounded-2 px-3 py-1.5 action-btn d-inline-flex align-items-center gap-1.5 text-decoration-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        </svg>
                                        <span>See All Requests</span>
                                    </a>

                                    <a href="{{ route('employee.asset-request.create', $asset->id) }}" class="btn btn-sm btn-primary rounded-2 px-3 py-1.5 brand-btn d-inline-flex align-items-center gap-1.5 text-decoration-none fw-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                        <span>Request Support</span>
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" class="text-muted mb-3 opacity-50"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="2" y1="7" x2="7" y2="7"></line><line x1="2" y1="17" x2="7" y2="17"></line><line x1="17" y1="17" x2="22" y2="17"></line><line x1="17" y1="7" x2="22" y2="7"></line></svg>
                                    <h6 class="mb-1 fw-bold text-dark">No Corporate Assets Mapped</h6>
                                    <p class="mb-0 small text-muted">You do not have any operational assets assigned to your profile at this moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Premium Table Interaction Overrides */
    .custom-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .custom-table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    
    /* Small Badge Styling Rules */
    .small-badge {
        font-size: 0.85rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        background-color: #ffffff !important;
    }
    
    /* Premium Status Pill Structure */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
    }
    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    
    /* Interactive Button Transitions */
    .action-btn {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #ffffff;
        font-size: 0.82rem;
        font-weight: 500;
    }
    .action-btn:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .brand-btn {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
        font-size: 0.82rem;
        transition: all 0.2s ease;
    }
    .brand-btn:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }

    /* Custom tracking block styles */
    .asset-item-group {
        border-left: 2px solid #e2e8f0;
        padding-left: 0.75rem;
    }
    .asset-item-group:focus-within {
        border-left-color: #3b82f6;
    }
</style>
@endsection