@extends('layouts.admin')

@section('title')
    {{ __('Add Leave Records') }}
@endsection

@section('content')
    <section class="row">
        <div class="col-8">
            <form method="POST"
                action="{{ Auth::user()->role->slug === 'super-admin'
                    ? route('leaves.store')
                    : (Auth::user()->role->slug === 'administrator'
                        ? route('admin.leaves.store')
                        : (Auth::user()->role->slug === 'employee'
                            ? route('employee.leaves.store')
                            : route('hr.leaves.store'))) }}">
                @csrf

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Apply Leave</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label for="title">Leave Title</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ old('title') }}" required>
                            </div>

                            {{-- <div class="col-12">
                <label for="employee_id">Employee</label>
                <select name="employee_id" id="employee_id" class="form-control" required>
                  <option value="">-- Select Employee --</option>
                  @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                      {{ $employee->firstname }} {{ $employee->lastname }}
                    </option>
                  @endforeach
                </select>
              </div> --}}

                            <div class="col-12">
                                <label for="employee_id">Employee</label>

                                @if (Auth::user()->role->slug === 'employee')
                                    {{-- Auto selected logged in employee --}}
                                    <input type="hidden" name="employee_id" value="{{ Auth::user()->employee->id }}">

                                    <input type="text" class="form-control"
                                        value="{{ Auth::user()->employee->firstname }} {{ Auth::user()->employee->lastname }}"
                                        readonly>
                                @else
                                    {{-- Show all employees for admin/hr/super-admin --}}
                                    <select name="employee_id" id="employee_id" class="form-control" required>
                                        <option value="">-- Select Employee --</option>

                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->firstname }} {{ $employee->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            {{-- Hierarchy preview — shows after employee is selected --}}
                            <div class="col-12" id="hierarchy-info" style="display:none">
                                <div class="alert alert-info py-2 mb-0" style="font-size:0.85rem">
                                    <strong>Leave will be sent to:</strong>
                                    <span id="approver-chain">Loading...</span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ old('start_date') }}" required>
                            </div>

                            <div class="col-6">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ old('end_date') }}" required>
                            </div>

                            <div class="col-12">
                                <label for="leave_type">Leave Type</label>
                                <select name="leave_type" id="leave_type" class="form-control" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="1" {{ old('leave_type') == 1 ? 'selected' : '' }}>Vacation</option>
                                    <option value="2" {{ old('leave_type') == 2 ? 'selected' : '' }}>Sick Leave
                                    </option>
                                    <option value="3" {{ old('leave_type') == 3 ? 'selected' : '' }}>Emergency Leave
                                    </option>
                                    <option value="4" {{ old('leave_type') == 4 ? 'selected' : '' }}>Involuntary Leave
                                    </option>
                                    <option value="5" {{ old('leave_type') == 5 ? 'selected' : '' }}>Medical Leave
                                    </option>
                                    <option value="6" {{ old('leave_type') == 6 ? 'selected' : '' }}>Casual Leave
                                    </option>
                                    <option value="7" {{ old('leave_type') == 7 ? 'selected' : '' }}>Marriage Leave
                                    </option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="leave_reason">Leave Reason</label>
                                <textarea name="leave_reason" class="form-control" id="leave_reason" rows="3">{{ old('leave_reason') }}</textarea>
                            </div>

                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary px-4">Apply Leave</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('script')
    <script>
        // Show approval chain when employee is selected
        document.getElementById('employee_id').addEventListener('change', function() {
            const empId = this.value;
            const infoBox = document.getElementById('hierarchy-info');
            const chainText = document.getElementById('approver-chain');

            if (!empId) {
                infoBox.style.display = 'none';
                return;
            }

            fetch(`/employee/employees/${empId}/hierarchy-chain`)
                .then(r => r.json())
                <script>
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
            chainText.textContent = data.chain.join(' → ');
          } else {
            chainText.textContent = 'No hierarchy assigned. HR will handle directly.';
          }

          infoBox.style.display = 'block';
        })
        .catch(() => {
          chainText.textContent = 'Could not load hierarchy.';
          infoBox.style.display = 'block';
        });
    });

    // trigger automatically if selected already
    if (employeeSelect.value) {
      employeeSelect.dispatchEvent(new Event('change'));
    }
  }
</script>
@endsection
