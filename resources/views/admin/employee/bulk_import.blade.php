@extends('layouts.admin')

@section('title')
    {{ __('Bulk Import Employees') }}
@endsection

@section('content')
<section class="row">
    <div class="col-12 col-lg-8 mx-auto">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i data-feather="upload" class="me-2"></i>{{ __('Bulk Import Employees via Excel') }}
                </h5>
                <a href="{{ Auth::user()->role->slug === 'super-admin'
                    ? route('employee.import.template')
                    : (Auth::user()->role->slug === 'administrator'
                        ? route('admin.employee.import.template')
                        : route('hr.employee.import.template')) }}"
                    class="btn btn-sm btn-outline-success">
                    <i data-feather="download" class="me-1"></i> Download Template
                </a>
            </div>
            <div class="card-body">

                <div class="alert alert-info">
                    <strong>Instructions:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Download the template above and fill in employee data.</li>
                        <li>The <strong>Password</strong> column sets the login password. If left blank, a random secure password is generated automatically.</li>
                        <li>Department and Designation names will be created automatically if they don't exist.</li>
                        <li>Rows with duplicate emails are skipped.</li>
                        <li>Salary details can be updated individually after import.</li>
                    </ul>
                </div>

                <form method="POST"
                    action="{{ Auth::user()->role->slug === 'super-admin'
                        ? route('employee.import')
                        : (Auth::user()->role->slug === 'administrator'
                            ? route('admin.employee.import')
                            : route('hr.employee.import')) }}"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="excel_file" class="form-label fw-bold">Select Excel File (.xlsx, .xls, .csv)</label>
                        <input type="file" name="excel_file" id="excel_file"
                            class="form-control @error('excel_file') is-invalid @enderror"
                            accept=".xlsx,.xls,.csv" required>
                        @error('excel_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="upload-cloud" class="me-1"></i>
                            {{ __('Import Employees') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Expected column reference --}}
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Expected Excel Columns</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Column Header</th>
                            <th>Required?</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Employee Name</td><td><span class="badge bg-danger">Required</span></td><td>Full name (First Last)</td></tr>
                        <tr><td>Department</td><td>Optional</td><td>Created if not exists</td></tr>
                        <tr><td>Designation</td><td>Optional</td><td>Created if not exists</td></tr>
                        <tr><td>Employee code</td><td>Optional</td><td>Auto-generated if blank</td></tr>
                        <tr><td>Blood group</td><td>Optional</td><td>e.g. O+ve, AB-ve</td></tr>
                        <tr><td>Ph Number</td><td>Optional</td><td>Primary phone</td></tr>
                        <tr><td>Emergency Phone Number</td><td>Optional</td><td></td></tr>
                        <tr><td>Official Number</td><td>Optional</td><td>Office phone</td></tr>
                        <tr><td>D.O.J</td><td>Optional</td><td>Date of Joining (YYYY-MM-DD)</td></tr>
                        <tr><td>D.O.B</td><td>Optional</td><td>Date of Birth (YYYY-MM-DD)</td></tr>
                        <tr><td>Password</td><td>Optional</td><td>Login password — auto-generated if blank</td></tr>
                        <tr><td>Official Gmail</td><td>Optional</td><td>Used as login email if provided</td></tr>
                        <tr><td>Personal Gmail</td><td>Optional</td><td>Fallback login email</td></tr>
                        <tr><td>Present Address</td><td>Optional</td><td></td></tr>
                        <tr><td>Permanent Address</td><td>Optional</td><td></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>
@endsection
