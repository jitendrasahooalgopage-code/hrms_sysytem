<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip_{{ $slip->employee->unique_id }}_{{ $slip->month }}</title>
    <style>
        @page {
            margin: 20px;
        }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            color: #333; 
            line-height: 1.4; 
            margin: 0; 
            padding: 10px; 
            font-size: 12px;
            background-color: #fff;
        }
        .payslip-container { 
            max-width: 100%; 
            margin: auto; 
            padding: 10px; 
            background: #fff;
        }
        
        /* Layout Tables */
        .brand-table, .meta-table, .financial-grid, .ledger-table, .net-pay-table, .signature-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
        }
        
        .company-details { 
            font-size: 11px; 
            text-align: right; 
            color: #555; 
            line-height: 1.4; 
        }
        
        .blue-divider {
            border: none;
            border-top: 3px solid #0066ff;
            margin-bottom: 15px;
            margin-top: 5px;
        }

        /* Banner heading */
        .slip-banner {
            background-color: #0066ff;
            color: #fff;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            padding: 6px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .period-bar {
            text-align: center;
            font-size: 12px;
            color: #555;
            margin-bottom: 15px;
        }
        .period-bar strong { color: #0066ff; }

        /* Profile Details Table */
        .meta-table td { 
            padding: 8px 10px; 
            border: 1px solid #e0e0e0; 
            width: 25%;
            font-size: 11px;
        }
        .meta-label { color: #666; background-color: #fcfcfc; }
        .meta-value { font-weight: 600; color: #111; }

        /* Financial split grid setup */
        .ledger-table { border: 1px solid #cbd5e1; }
        .ledger-table th { 
            background-color: #f0f4f8; 
            color: #0066ff; 
            font-weight: bold; 
            padding: 8px 10px; 
            text-align: left; 
            border-bottom: 2px solid #cbd5e1;
            font-size: 11px;
            text-transform: uppercase;
        }
        .ledger-table td { 
            padding: 8px 10px; 
            font-size: 11px; 
            border-bottom: 1px solid #e2e8f0; 
        }
        .text-right { text-align: right; }
        
        .total-row { 
            font-weight: bold; 
            background-color: #f8fafc; 
        }
        .total-row td { 
            border-top: 1px solid #cbd5e1; 
            color: #111;
        }

        /* Net Pay Banner explicitly rewritten using table cells to support DomPDF */
        .net-pay-table {
            background-color: #0066ff;
            color: #fff;
            margin-top: 10px;
            margin-bottom: 30px;
        }
        .net-pay-title { font-weight: bold; font-size: 14px; text-transform: uppercase; }
        .net-pay-formula { font-size: 10px; color: #e0f0ff; display: block; margin-top: 1px; font-weight: normal; }
        .net-pay-value { font-size: 22px; font-weight: bold; text-align: right; padding-right: 15px; }

        /* Signatures matrix */
        .signature-table td { text-align: center; font-size: 11px; color: #444; width: 50%; }
        .signature-line { width: 180px; margin: 50px auto 6px auto; border: none; border-top: 1px solid #999; }

        /* System footer */
        .system-footer { 
            margin-top: 40px; 
            text-align: center; 
            font-size: 10px; 
            color: #999; 
            line-height: 1.5;
            border-top: 1px solid #eee;
            padding-top: 12px;
        }

        .no-print { margin-bottom: 15px; text-align: right; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>


<div class="payslip-container">
    <table class="brand-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="brand-logo-wrapper">
                    @if(isset($base64Logo) && $base64Logo !== '')
                        <img src="{{ $base64Logo }}" alt="Logo" style="height: 45px; width: auto;">
                    @else
                        <img src="{{ public_path('img/photos/app_logo.png') }}" alt="Logo" style="height: 45px; width: auto;">
                    @endif
                </div>
            </td>
            <td class="company-details" style="vertical-align: middle;">
                <strong style="color: #111; font-size: 12px;">AlgoPage Pvt. Ltd.</strong><br>
                Plot No - A-1, Govind Vihar, Near Jaya Durga Nagar,<br>
                Bomikhal, Jharapada, Bhubaneswar, Odisha 751010<br>
                Phone: +91-9348222048 | Email: info@algopage.com
            </td>
        </tr>
    </table>

    <div class="blue-divider"></div>

    <div class="slip-banner">Salary Slip</div>

    <div class="period-bar">
        Pay Period: <strong>{{ $slip->month }} {{ $slip->year }}</strong> &nbsp;|&nbsp; Pay Date: <strong>{{ $slip->pay_date ? \Carbon\Carbon::parse($slip->pay_date)->format('d F Y') : 'N/A' }}</strong>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Employee Name</td>
            <td class="meta-value">{{ $slip->employee->firstname }} {{ $slip->employee->lastname }}</td>
            <td class="meta-label">Employee ID</td>
            <td class="meta-value">{{ $slip->employee->unique_id }}</td>
        </tr>
        <tr>
            <td class="meta-label">Designation</td>
            <td class="meta-value">{{ $slip->employee->designation?->title ?? 'N/A' }}</td>
            <td class="meta-label">Department</td>
            <td class="meta-value">{{ $slip->employee->department?->title ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Date of Joining</td>
            <td class="meta-value">{{ $slip->employee->doj ? \Carbon\Carbon::parse($slip->employee->doj)->format('d M Y') : 'N/A' }}</td>
            <td class="meta-label">PAN</td>
            <td class="meta-value" style="text-transform: uppercase;">{{ $slip->employee->pan_number ?? 'ABCDE1234F' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Bank Name</td>
            <td class="meta-value">{{ $slip->employee->bank_name ?? 'HDFC Bank' }}</td>
            <td class="meta-label">Bank A/C No.</td>
            <td class="meta-value">{{ $slip->employee->bank_account_number ?? 'XXXXXXXX4821' }}</td>
        </tr>
    </table>

    <table class="financial-grid">
        <tr>
            <td style="width: 50%; padding-right: 8px; vertical-align: top;">
                <table class="ledger-table">
                    <thead>
                        <tr>
                            <th>Earnings</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Basic Salary</td><td class="text-right">₹ {{ number_format($slip->basic_salary, 2) }}</td></tr>
                        <tr><td>House Rent Allowance</td><td class="text-right">₹ {{ number_format($slip->house_rent_allowance, 2) }}</td></tr>
                        <tr><td>Conveyance Allowance</td><td class="text-right">₹ {{ number_format($slip->conveyance_allowance, 2) }}</td></tr>
                        <tr><td>Medical Allowance</td><td class="text-right">₹ {{ number_format($slip->medical_allowance, 2) }}</td></tr>
                        <tr><td>Special Allowance</td><td class="text-right">₹ {{ number_format($slip->special_allowance, 2) }}</td></tr>
                        <tr class="total-row">
                            <td>Gross Earnings</td>
                            <td class="text-right">₹ {{ number_format($slip->gross_earnings, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            
            <td style="width: 50%; padding-left: 8px; vertical-align: top;">
                <table class="ledger-table">
                    <thead>
                        <tr>
                            <th>Deductions</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Provident Fund</td><td class="text-right">₹ {{ number_format($slip->provident_fund, 2) }}</td></tr>
                        <tr><td>Professional Tax</td><td class="text-right">₹ {{ number_format($slip->professional_tax, 2) }}</td></tr>
                        <tr><td>TDS (Income Tax)</td><td class="text-right">₹ {{ number_format($slip->income_tax, 2) }}</td></tr>
                        <tr><td>Other Deductions</td><td class="text-right">₹ {{ number_format($slip->other_deductions, 2) }}</td></tr>
                        <tr><td>&nbsp;</td><td class="text-right">&nbsp;</td></tr>
                        <tr class="total-row">
                            <td>Total Deductions</td>
                            <td class="text-right">₹ {{ number_format($slip->total_deductions, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <table class="net-pay-table">
        <tr>
            <td style="padding: 12px 15px; vertical-align: middle;">
                <div class="net-pay-title">Net Pay</div>
                <div class="net-pay-formula">Gross Earnings - Total Deductions</div>
            </td>
            <td class="net-pay-value" style="vertical-align: middle;">
                ₹ {{ number_format($slip->net_salary, 2) }}
            </td>
        </tr>
    </table>

    @if($slip->remarks)
        <p style="margin-top: 15px; font-size: 11px; color: #4a5568;"><strong>Remarks/Notes:</strong> {{ $slip->remarks }}</p>
    @endif

    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-line"></div>
                Employee Signature
            </td>
            <td>
                <div class="signature-line"></div>
                Authorized Signatory
            </td>
        </tr>
    </table>

    <div class="system-footer">
        This is a system-generated salary slip and is valid without a physical signature.<br>
        <strong>AlgoPage Pvt. Ltd.</strong> · Plot No - A-1, Govind Vihar, Bomikhal, Jharapada, Bhubaneswar, Odisha 751010 · info@algopage.com
    </div>
</div>

</body>
</html>