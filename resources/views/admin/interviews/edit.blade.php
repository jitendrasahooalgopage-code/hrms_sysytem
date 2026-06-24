@extends('layouts.admin')

@section('title')
    Edit — {{ $application->candidate->name }}
@endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="{{ route('positions.show', $application) }}" class="text-muted text-decoration-none small">
        <i class="fas fa-arrow-left me-1"></i> Back to Application
      </a>
      <h1 class="h3 mt-1 mb-0">Edit Application</h1>
    </div>
  </div>
@endsection

@section('content')
<form action="{{ route('applications.update', $application) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="row g-4">

  <div class="col-lg-7">
    <div class="card mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-user me-2 text-primary"></i>Candidate Information</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="candidate_name" class="form-control"
                   value="{{ old('candidate_name', $application->candidate->name) }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email (read-only)</label>
            <input type="email" class="form-control bg-light"
                   value="{{ $application->candidate->email }}" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="candidate_phone" class="form-control"
                   value="{{ old('candidate_phone', $application->candidate->phone) }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">LinkedIn</label>
            <input type="url" name="linkedin_url" class="form-control"
                   value="{{ old('linkedin_url', $application->candidate->linkedin_url) }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Portfolio</label>
            <input type="url" name="portfolio_url" class="form-control"
                   value="{{ old('portfolio_url', $application->candidate->portfolio_url) }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Available From</label>
            <input type="date" name="available_from" class="form-control"
                   value="{{ old('available_from', $application->available_from?->format('Y-m-d')) }}">
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-file-upload me-2 text-info"></i>Update CV</h5>
      </div>
      <div class="card-body">
        @if($application->candidate->hasCv())
        <div class="alert alert-info d-flex align-items-center gap-2 py-2">
          <i class="fas fa-file-alt"></i>
          <span>Current: <strong>{{ $application->candidate->cv_original_name }}</strong></span>
          <a href="{{ route('positions.cv.download', $application) }}" class="ms-auto btn btn-sm btn-outline-info">
            <i class="fas fa-download"></i>
          </a>
        </div>
        @endif
        <div class="border border-dashed rounded-3 p-3 text-center" style="border-color:#0d6efd !important;cursor:pointer"
             onclick="document.getElementById('cv-file').click()">
          <i class="fas fa-cloud-upload-alt text-primary"></i>
          <div class="small mt-1 text-muted">Click to upload new CV (replaces existing)</div>
          <div id="cv-filename" class="small text-success fw-semibold mt-1 d-none"></div>
        </div>
        <input type="file" id="cv-file" name="cv" class="d-none" accept=".pdf,.doc,.docx"
               onchange="document.getElementById('cv-filename').textContent='📎 '+this.files[0].name;document.getElementById('cv-filename').classList.remove('d-none')">
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-briefcase me-2 text-warning"></i>Job Details</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Position</label>
          <select name="job_position_id" class="form-select" required>
            @foreach($positions as $pos)
              <option value="{{ $pos->id }}" {{ $application->job_position_id == $pos->id ? 'selected' : '' }}>
                {{ $pos->title }} — {{ $pos->department }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Source</label>
          <select name="source" class="form-select">
            <option value="">-- Select Source --</option>
            @foreach($sources as $key => $label)
              <option value="{{ $key }}" {{ $application->source === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Expected Salary (₹)</label>
          <input type="number" name="expected_salary" class="form-control"
                 value="{{ old('expected_salary', $application->expected_salary) }}">
        </div>
        <div class="mb-0">
          <label class="form-label">Application Status</label>
          <select name="status" class="form-select">
            <option value="active"    {{ $application->status === 'active'    ? 'selected' : '' }}>Active</option>
            <option value="on_hold"   {{ $application->status === 'on_hold'   ? 'selected' : '' }}>On Hold</option>
            <option value="withdrawn" {{ $application->status === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
            <option value="rejected"  {{ $application->status === 'rejected'  ? 'selected' : '' }}>Rejected</option>
          </select>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-user-tie me-2 text-success"></i>Assignment</h5>
      </div>
      <div class="card-body">
        <select name="assigned_to" class="form-select">
          <option value="">-- Unassigned --</option>
          @foreach($users as $id => $name)
            <option value="{{ $id }}" {{ $application->assigned_to == $id ? 'selected' : '' }}>{{ $name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="d-grid gap-2">
      <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-save me-2"></i>Save Changes
      </button>
      <a href="{{ route('positions.show', $application) }}" class="btn btn-light border">Cancel</a>
    </div>
  </div>

</div>
</form>
@endsection
