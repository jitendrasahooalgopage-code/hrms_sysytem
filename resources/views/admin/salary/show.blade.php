@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4 bg-white">
    <div class="mb-4 no-print d-flex justify-content-between align-items-center border-bottom pb-2">
        <a href="{{ route('salary-slip.index') }}" class="btn btn-link text-secondary p-0 font-weight-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Salary Slips Overview
        </a>
        <div>
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm mr-2 px-3 rounded-pill">
                <i class="fas fa-print mr-1"></i> Print Summary
            </button>
            <a href="{{ route('salary-slip.pdf', $slip->id) }}" target="_blank" class="btn btn-success btn-sm px-3 rounded-pill">
                <i class="fas fa-file-pdf mr-1"></i> Open Official PDF
            </a>
        </div>
    </div>

    <div class="payslip-box-layout border mx-auto p-4 md:p-5 bg-white shadow-sm rounded mb-5" style="max-width: 850px; color: #333;">
        
        <div class="row align-items-center mb-2">
            <div class="col-md-6 col-12 text-center text-md-left mb-3 mb-md-0">
                <div class="brand-logo-wrapper">
    <img src="{{ asset('img/photos/app_logo.png') }}" alt="AlgoPage Logo" style="height: 50px; width: auto; object-fit: contain;">
</div>
            </div>
            <div class="col-md-6 col-12 text-center text-md-right address-text-block small text-muted" style="line-height: 1.4; font-size: 11px;">
                <strong class="text-dark">AlgoPage Pvt. Ltd.</strong><br>
                Plot No - A-1, Govind Vihar, Near Jaya Durga Nagar,<br>
                Bomikhal, Jharapada, Bhubaneswar, Odisha 751010<br>
                Phone: +91-9348222048 | Email: info@algopage.com
            </div>
        </div>

        <div class="w-100 bg-primary my-2" style="height: 3px; background-color: #0066ff !important;"></div>

        <div class="w-100 text-white text-center font-weight-bold py-2 text-uppercase rounded mb-3" 
             style="background-color: #0066ff !important; font-size: 14px; letter-spacing: 1px;">
            Salary Slip
        </div>

        <div class="row justify-content-center mb-3 text-muted small">
            <div class="col-auto">
                Pay Period: <strong class="text-dark">{{ $slip->month }} {{ $slip->year }}</strong>
            </div>
            <div class="col-auto text-gray-300">|</div>
            <div class="col-auto">
                Pay Date: <strong class="text-dark">{{ $slip->pay_date ? \Carbon\Carbon::parse($slip->pay_date)->format('d F Y') : 'Pending' }}</strong>
            </div>
            <div class="col-auto text-gray-300">|</div>
            <div class="col-auto">
                Status: <span class="badge {{ $slip->status === 'Paid' ? 'badge-success' : 'badge-warning' }} px-2 font-weight-bold">{{ $slip->status }}</span>
            </div>
        </div>

        <table class="table table-sm table-bordered meta-profile-grid-table mb-4" style="font-size: 12px; table-layout: fixed; width: 100%;">
            <tbody>
                <tr>
                    <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle" style="width: 22%;">Employee Name</td>
                    <td class="font-weight-bold py-2 px-3 text-dark align-middle" style="width: 28%;">{{ $slip->employee->firstname }} {{ $slip->employee->lastname }}</td>
                    <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle" style="width: 22%;">Employee ID</td>
                    <td class="font-weight-bold py-2 px-3 text-dark align-middle" style="width: 28%;">{{ $slip->employee->unique_id }}</td>
                </tr>
                <tr>
                    <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Designation</td>
                    <td class="font-weight-bold py-2 px-3 text-dark align-middle">{{ $slip->employee->designation?->title ?? 'N/A' }}</td>
                    <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Department</td>
                    <td class="font-weight-bold py-2 px-3 text-dark align-middle">{{ $slip->employee->department?->title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Date of Joining</td>
                    <td class="font-weight-bold py-2 px-3 text-dark align-middle">
                        {{ $slip->employee->doj ? \Carbon\Carbon::parse($slip->employee->doj)->format('d M Y') : 'N/A' }}
                    </td>
                    <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">PAN</td>
                    <td class="font-weight-bold py-2 px-3 text-dark align-middle text-uppercase">{{ $slip->employee->pan_number ?? 'ABCDE1234F' }}</td>
                </tr>
                <tr>
                    <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Bank Name</td>
                    <td class="font-weight-bold py-2 px-3 text-dark align-middle">{{ $slip->employee->bank_name ?? 'HDFC Bank' }}</td>
                    <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Bank A/C No.</td>
                    <td class="font-weight-bold py-2 px-3 text-dark align-middle">{{ $slip->employee->bank_account_number ?? 'XXXXXXXX4821' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="row no-gutters border rounded mb-4" style="overflow: hidden;">
            
            <div class="col-md-6 border-right">
                <table class="table table-sm table-borderless mb-0 financial-sub-ledger-table">
                    <thead>
                        <tr class="bg-light border-bottom">
                            <th class="text-primary font-weight-bold py-2 px-3" style="font-size: 12px; letter-spacing: 0.5px;">EARNINGS</th>
                            <th class="text-right text-primary font-weight-bold py-2 px-3" style="font-size: 12px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-bottom">
                            <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Basic Salary</td>
                            <td class="px-3 py-2 text-right align-middle font-weight-bold text-dark" style="width: 45%;">
                                ₹ {{ number_format($slip->basic_salary, 2, '.', ',') }}
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">House Rent Allowance</td>
                            <td class="px-3 py-2 text-right align-middle font-weight-bold text-dark">
                                ₹ {{ number_format($slip->house_rent_allowance, 2, '.', ',') }}
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Conveyance Allowance</td>
                            <td class="px-3 py-2 text-right align-middle font-weight-bold text-dark">
                                ₹ {{ number_format($slip->conveyance_allowance, 2, '.', ',') }}
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Medical Allowance</td>
                            <td class="px-3 py-2 text-right align-middle font-weight-bold text-dark">
                                ₹ {{ number_format($slip->medical_allowance, 2, '.', ',') }}
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Special Allowance</td>
                            <td class="px-3 py-2 text-right align-middle font-weight-bold text-dark">
                                ₹ {{ number_format($slip->special_allowance, 2, '.', ',') }}
                            </td>
                        </tr>
                        <tr class="bg-light font-weight-bold border-top">
                            <td class="px-3 py-3 align-middle text-dark" style="font-size: 13px;">Gross Earnings</td>
                            <td class="px-3 py-3 text-right align-middle font-weight-bold text-dark h6 mb-0">
                                ₹ {{ number_format($slip->gross_earnings, 2, '.', ',') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-6">
                <table class="table table-sm table-borderless mb-0 financial-sub-ledger-table">
                    <thead>
                        <tr class="bg-light border-bottom">
                            <th class="text-secondary font-weight-bold py-2 px-3" style="color: #555 !important; font-size: 12px; letter-spacing: 0.5px;">DEDUCTIONS</th>
                            <th class="text-right text-secondary font-weight-bold py-2 px-3" style="font-size: 12px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-bottom">
                            <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Provident Fund</td>
                            <td class="px-3 py-2 text-right align-middle font-weight-bold text-dark" style="width: 45%;">
                                ₹ {{ number_format($slip->provident_fund, 2, '.', ',') }}
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Professional Tax</td>
                            <td class="px-3 py-2 text-right align-middle font-weight-bold text-dark">
                                ₹ {{ number_format($slip->professional_tax, 2, '.', ',') }}
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">TDS (Income Tax)</td>
                            <td class="px-3 py-2 text-right align-middle font-weight-bold text-dark">
                                ₹ {{ number_format($slip->income_tax, 2, '.', ',') }}
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Other Deductions</td>
                            <td class="px-3 py-2 text-right align-middle font-weight-bold text-dark">
                                ₹ {{ number_format($slip->other_deductions, 2, '.', ',') }}
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">&nbsp;</td>
                            <td class="px-3 py-2 text-right align-middle">&nbsp;</td>
                        </tr>
                        <tr class="bg-light font-weight-bold border-top">
                            <td class="px-3 py-3 align-middle text-dark" style="font-size: 13px;">Total Deductions</td>
                            <td class="px-3 py-3 text-right align-middle font-weight-bold text-dark h6 mb-0">
                                ₹ {{ number_format($slip->total_deductions, 2, '.', ',') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row align-items-center mx-0 rounded py-3 px-4 text-white font-weight-bold mb-4" 
             style="background-color: #0066ff !important;">
            <div class="col-md-6 col-12 text-center text-md-left mb-2 mb-md-0 p-0">
                <div style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Net Pay</div>
                <div class="font-weight-normal text-white-50 small" style="opacity: 0.75; font-weight: 400; font-size: 10px;">Gross Earnings – Total Deductions</div>
            </div>
            <div class="col-md-6 col-12 text-center text-md-right p-0 font-weight-black" style="font-size: 26px; font-weight: 900;">
                ₹ {{ number_format($slip->net_salary, 2, '.', ',') }}
            </div>
        </div>

        @if($slip->remarks)
            <div class="p-3 bg-light rounded border mb-4 text-muted small">
                <strong class="text-dark">Internal Remarks Note:</strong> {{ $slip->remarks }}
            </div>
        @endif

        <table class="table table-sm table-borderless mt-5 signature-block-table" style="width: 100%; border:0;">
            <tbody>
                <tr>
                    <td class="text-center text-muted small py-4" style="width: 50%; font-size: 12px; border:0;">
                        <div class="mx-auto border-top text-dark" style="width: 180px; border-color: #bbb !important; padding-top: 6px;">Employee Signature</div>
                    </td>
                    <td class="text-center text-muted small py-4" style="width: 50%; font-size: 12px; border:0;">
                        <div class="mx-auto border-top text-dark" style="width: 180px; border-color: #bbb !important; padding-top: 6px;">Authorized Signatory</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="w-100 text-center text-muted border-top pt-2 mt-4 text-block-footer" style="font-size: 10px; line-height: 1.5; color:#999 !important;">
            This is a system-generated salary slip and is valid without a physical signature.<br>
            AlgoPage Pvt. Ltd. · Plot No - A-1, Govind Vihar, Bomikhal, Jharapada, Bhubaneswar, Odisha 751010 · info@algopage.com
        </div>
    </div>
</div>

<style>
    .meta-profile-grid-table td { border-color: #e2e8f0 !important; }
    .financial-sub-ledger-table td, .financial-sub-ledger-table th { border-color: #e2e8f0 !important; }
    @media print {
        .no-print { display: none !important; }
        body { padding: 0 !important; background-color: #fff !important; }
        .payslip-box-layout { border: 0 !important; box-shadow: none !important; padding: 0 !important; }
    }
</style>
@endsection