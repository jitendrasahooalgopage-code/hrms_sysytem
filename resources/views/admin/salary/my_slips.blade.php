@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Self Service</a></li>
                    <li class="breadcrumb-item active text-dark font-weight-semibold" aria-current="page">My Payslips</li>
                </ol>
            </nav>
            <h1 class="h3 font-weight-bold text-dark mb-0" style="letter-spacing: -0.5px;">My Salary Slips</h1>
        </div>
        <div class="text-md-right">
            <span class="badge badge-light border text-muted px-3 py-2 rounded-pill font-weight-medium">
                <i class="fas fa-id-card mr-1 text-primary"></i> {{ $employee->unique_id }}
            </span>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-lg mb-4 bg-light">
        <div class="card-body p-3">
            <div class="row align-items-center text-center text-md-left">
                <div class="col-md-auto mb-2 mb-md-0">
                    <img src="{{ asset('img/photos/app_logo.png') }}" alt="Logo" style="height: 32px; width: auto; object-fit: contain; opacity: 0.8;">
                </div>
                <div class="col-md border-left border-gray-200 pl-md-4">
                    <span class="small text-muted text-uppercase tracking-wider font-weight-bold" style="font-size: 0.7rem;">Employee</span>
                    <h5 class="font-weight-bold text-dark mb-0">{{ $employee->firstname }} {{ $employee->lastname }}</h5>
                </div>
                <div class="col-md border-left border-gray-200">
                    <span class="small text-muted text-uppercase tracking-wider font-weight-bold" style="font-size: 0.7rem;">Department</span>
                    <p class="font-weight-semibold text-dark mb-0">{{ $employee->department?->title ?? 'N/A' }}</p>
                </div>
                <div class="col-md border-left border-gray-200">
                    <span class="small text-muted text-uppercase tracking-wider font-weight-bold" style="font-size: 0.7rem;">Designation</span>
                    <p class="font-weight-semibold text-dark mb-0">{{ $employee->designation?->title ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase text-secondary font-weight-bold" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="px-4 py-3" style="width: 8%;">SL</th>
                            <th class="py-3">Pay Period Month</th>
                            <th class="py-3">Year</th>
                            <th class="py-3 text-right">Net Remitted Payable</th>
                            <th class="py-3 text-center">Disbursal Status</th>
                            <th class="py-3">Payment Date</th>
                            <th class="px-4 py-3 text-center" style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($slips as $key => $slip)
                            <tr>
                                <td class="px-4 py-3 align-middle font-weight-medium text-muted">{{ $key + 1 }}</td>
                                <td class="py-3 align-middle font-weight-bold text-dark">
                                    <i class="far fa-calendar-alt mr-2 text-muted"></i>{{ $slip->month }}
                                </td>
                                <td class="py-3 align-middle text-secondary font-weight-medium">{{ $slip->year }}</td>
                                <td class="py-3 align-middle font-weight-black text-right text-success" style="font-size: 0.95rem;">
                                    ₹ {{ number_format($slip->net_salary, 2, '.', ',') }}
                                </td>
                                <td class="py-3 align-middle text-center">
                                    @if($slip->status === 'Paid')
                                        <span class="badge badge-pill bg-success-light text-success px-3 py-2 font-weight-bold">
                                            <i class="fas fa-check-circle mr-1 small"></i> Paid
                                        </span>
                                    @else
                                        <span class="badge badge-pill bg-warning-light text-warning px-3 py-2 font-weight-bold">
                                            <i class="fas fa-clock mr-1 small"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 align-middle font-weight-medium text-muted">
                                    {{ $slip->pay_date ? \Carbon\Carbon::parse($slip->pay_date)->format('d M Y') : '—' }}
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
    <div class="btn-group" role="group">
        @if($slip->status === 'Paid')
            <a href="{{ route('employee.salary-slip.pdf', $slip->id) }}" target="_blank" class="btn btn-sm btn-outline-primary px-3" title="View Payslip Sheet">
                <i class="fas fa-eye mr-1"></i> View
            </a>
            <a href="{{ route('employee.salary-slip.pdf', $slip->id) }}" target="_blank" class="btn btn-sm btn-primary px-3" style="background-color: #0066ff !important; border-color: #0066ff !important;" title="Download Official PDF">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </a>
        @else
            <button class="btn btn-sm btn-secondary px-3 opacity-50" style="cursor: not-allowed;" disabled title="Locked until authorized & marked Paid by HR">
                <i class="fas fa-lock mr-1 text-light"></i> Locked
            </button>
        @endif
    </div>
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i class="fas fa-file-invoice-dollar fa-3x text-muted opacity-50"></i>
                                    </div>
                                    <h6 class="font-weight-bold text-secondary">No Payslips Released Yet</h6>
                                    <p class="small text-muted mb-0">When your corporate payroll manager processes and distributes slips, they will safely register right here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-light { background-color: rgba(40, 167, 69, 0.08) !important; }
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.08) !important; }
    .font-weight-semibold { font-weight: 600; }
    .font-weight-black { font-weight: 900; }
    .table th { border-top: none !important; border-bottom: 2px solid #edf2f7 !important; }
    .table td { border-top: 1px solid #edf2f7 !important; vertical-align: middle; }
    .rounded-lg { border-radius: 8px !important; }
</style>
@endsection