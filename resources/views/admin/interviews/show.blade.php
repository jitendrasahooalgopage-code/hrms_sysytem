@extends('layouts.admin')

@section('title')
    {{ $application->candidate->name }} — Application Detail
@endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="{{ route('positions.index') }}" class="text-muted text-decoration-none small">
        <i class="fas fa-arrow-left me-1"></i> Back to Pipeline
      </a>
      <h1 class="h3 mt-1 mb-0">{{ $application->candidate->name }}</h1>
      <div class="text-muted small">{{ $application->jobPosition->title }} · {{ $application->jobPosition->department }}</div>
    </div>
    <div class="d-flex gap-2">
      @if($application->candidate->hasCv())
        <a href="{{ route('positions.cv.download', $application) }}" class="btn btn-outline-info">
          <i class="fas fa-file-download me-1"></i> Download CV
        </a>
      @endif
      <a href="{{ route('positions.edit', $application) }}" class="btn btn-outline-primary">
        <i class="fas fa-edit me-1"></i> Edit
      </a>
    </div>
  </div>
@endsection

@section('content')

{{-- Stage Progress Bar --}}
<div class="card mb-4">
  <div class="card-body py-3">
    <div class="d-flex align-items-center gap-1" style="overflow-x:auto">
      @php $stageKeys = array_keys($stages); @endphp
      @foreach($stages as $key => $stage)
        @if($key === 'rejected') @continue @endif
        @php
          $isCurrent = $application->stage === $key;
          $stageOrder = array_search($key, $stageKeys);
          $currentOrder = array_search($application->stage, $stageKeys);
          $isPast = $stageOrder < $currentOrder;
        @endphp
        <div class="flex-fill text-center position-relative" style="min-width:80px">
          <div class="rounded-pill py-1 px-2 small fw-semibold
            {{ $isCurrent ? 'bg-'.$stage['color'].' text-white' : ($isPast ? 'bg-success text-white' : 'bg-light text-muted') }}"
            style="font-size:0.75rem">
            @if($isPast)<i class="fas fa-check me-1"></i>@endif
            {{ $stage['label'] }}
          </div>
          @if(!$loop->last)
          <div class="position-absolute top-50 end-0 translate-middle-y text-muted" style="z-index:1">›</div>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</div>

<div class="row g-4">

  {{-- Left Column --}}
  <div class="col-lg-8">

    {{-- Candidate Card --}}
    <div class="card mb-4">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user me-2 text-primary"></i>Candidate</h5>
        <span class="badge bg-{{ $application->stageBadgeColor }} fs-6">{{ $application->stageLabel }}</span>
      </div>
      <div class="card-body">
        <div class="d-flex gap-3 align-items-start">
          <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
               style="width:56px;height:56px;font-size:18px;flex-shrink:0">
            {{ $application->candidate->initials }}
          </div>
          <div class="flex-fill">
            <h5 class="mb-1">{{ $application->candidate->name }}</h5>
            <div class="row g-2 text-muted small">
              <div class="col-sm-6">
                <i class="fas fa-envelope me-1"></i>{{ $application->candidate->email }}
              </div>
              @if($application->candidate->phone)
              <div class="col-sm-6">
                <i class="fas fa-phone me-1"></i>{{ $application->candidate->phone }}
              </div>
              @endif
              @if($application->candidate->linkedin_url)
              <div class="col-sm-6">
                <i class="fab fa-linkedin me-1"></i>
                <a href="{{ $application->candidate->linkedin_url }}" target="_blank" class="text-decoration-none">LinkedIn Profile</a>
              </div>
              @endif
              @if($application->candidate->portfolio_url)
              <div class="col-sm-6">
                <i class="fas fa-globe me-1"></i>
                <a href="{{ $application->candidate->portfolio_url }}" target="_blank" class="text-decoration-none">Portfolio</a>
              </div>
              @endif
              @if($application->expected_salary)
              <div class="col-sm-6">
                <i class="fas fa-rupee-sign me-1"></i>Expected: ₹{{ number_format($application->expected_salary) }}
              </div>
              @endif
              @if($application->available_from)
              <div class="col-sm-6">
                <i class="fas fa-calendar me-1"></i>Available: {{ $application->available_from->format('M d, Y') }}
              </div>
              @endif
              @if($application->source)
              <div class="col-sm-6">
                <i class="fas fa-share-alt me-1"></i>Source: {{ \App\Models\Application::sources()[$application->source] ?? $application->source }}
              </div>
              @endif
              @if($application->assignedTo)
              <div class="col-sm-6">
                <i class="fas fa-user-tie me-1"></i>Assigned to: {{ $application->assignedTo->name }}
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Interview Rounds --}}
    <div class="card mb-4">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-calendar-check me-2 text-info"></i>Interview Rounds</h5>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleModal">
          <i class="fas fa-plus me-1"></i> Schedule Round
        </button>
      </div>

      @forelse($application->interviewRounds as $i => $round)
      <div class="card-body border-bottom">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="badge bg-secondary">Round {{ $i + 1 }}</span>
              <strong>{{ $round->round_name }}</strong>
              <span class="badge bg-{{ $round->outcomeColor }}">{{ $round->outcomeLabel }}</span>
            </div>
            <div class="text-muted small">
              <span class="me-3"><i class="fas fa-video me-1"></i>{{ \App\Models\InterviewRound::modes()[$round->mode] }}</span>
              @if($round->scheduled_at)
              <span class="me-3"><i class="fas fa-clock me-1"></i>{{ $round->scheduled_at->format('M d, Y H:i') }}</span>
              @endif
              @if($round->interviewer)
              <span class="me-3"><i class="fas fa-user-tie me-1"></i>{{ $round->interviewer->name }}</span>
              @endif
              @if($round->rating)
              <span class="text-warning">{{ $round->ratingStars }}</span>
              @endif
            </div>
            @if($round->feedback)
            <div class="mt-2 text-muted small"><strong>Feedback:</strong> {{ $round->feedback }}</div>
            @endif
          </div>

          <button class="btn btn-sm btn-light border" data-bs-toggle="collapse"
                  data-bs-target="#feedback-{{ $round->id }}">
            <i class="fas fa-edit"></i>
          </button>
        </div>

        <div class="collapse mt-3" id="feedback-{{ $round->id }}">
          <form action="{{ route('positions.rounds.update', [$application, $round]) }}" method="POST">
            @csrf @method('PATCH')
            <div class="row g-2">
              <div class="col-md-4">
                <select name="outcome" class="form-select form-select-sm">
                  @foreach(\App\Models\InterviewRound::outcomes() as $ok => $ov)
                    <option value="{{ $ok }}" {{ $round->outcome === $ok ? 'selected' : '' }}>{{ $ov['label'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <select name="rating" class="form-select form-select-sm">
                  <option value="">Rating</option>
                  @for($r = 1; $r <= 5; $r++)
                    <option value="{{ $r }}" {{ $round->rating == $r ? 'selected' : '' }}>{{ $r }} ★</option>
                  @endfor
                </select>
              </div>
              <div class="col-12">
                <textarea name="feedback" class="form-control form-control-sm" rows="2"
                          placeholder="Candidate feedback (visible to team)...">{{ $round->feedback }}</textarea>
              </div>
              <div class="col-12">
                <textarea name="internal_notes" class="form-control form-control-sm" rows="2"
                          placeholder="Internal notes (not shared)...">{{ $round->internal_notes }}</textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-sm btn-success">Save Feedback</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      @empty
      <div class="card-body text-center text-muted py-4">
        <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
        No interviews scheduled yet.
      </div>
      @endforelse
    </div>

    {{-- Activity Timeline --}}
    <div class="card">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-history me-2 text-muted"></i>Activity Timeline</h5>
      </div>
      <div class="card-body">
        @forelse($application->activities as $activity)
        <div class="d-flex gap-3 mb-3">
          <div class="mt-1" style="width:20px;flex-shrink:0;text-align:center">
            <i class="{{ $activity->icon }}" style="font-size:14px"></i>
          </div>
          <div>
            <div class="small">{!! $activity->description !!}</div>
            <div class="text-muted" style="font-size:0.7rem">
              {{ $activity->creator->name ?? 'System' }} · {{ $activity->created_at->diffForHumans() }}
            </div>
          </div>
        </div>
        @empty
        <div class="text-muted text-center small py-2">No activity yet.</div>
        @endforelse
      </div>
    </div>

  </div>

  {{-- Right: Actions --}}
  <div class="col-lg-4">

    {{-- Move Stage --}}
    <div class="card mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-exchange-alt me-2 text-primary"></i>Move Stage</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('positions.stage.update', $application) }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label small text-muted">Current Stage</label>
            <div>
              <span class="badge bg-{{ $application->stageBadgeColor }} fs-6">
                {{ $application->stageLabel }}
              </span>
            </div>
          </div>
          <div class="mb-3">
            <select name="stage" class="form-select">
              @foreach($stages as $key => $stage)
                <option value="{{ $key }}" {{ $application->stage === $key ? 'selected' : '' }}>
                  {{ $stage['label'] }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-arrow-right me-1"></i>Update Stage
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Quick Stats --}}
    <div class="card mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-muted"></i>Overview</h5>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <tr>
            <td class="text-muted ps-3">Status</td>
            <td class="pe-3 text-end">
              <span class="badge bg-{{ $application->status === 'active' ? 'success' : 'secondary' }}">
                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
              </span>
            </td>
          </tr>
          <tr>
            <td class="text-muted ps-3">Applied</td>
            <td class="pe-3 text-end small">{{ $application->created_at->format('M d, Y') }}</td>
          </tr>
          <tr>
            <td class="text-muted ps-3">Rounds Done</td>
            <td class="pe-3 text-end">{{ $application->interviewRounds->count() }}</td>
          </tr>
          <tr>
            <td class="text-muted ps-3">CV</td>
            <td class="pe-3 text-end">
              @if($application->candidate->hasCv())
                <a href="{{ route('positions.cv.download', $application) }}" class="text-info small">
                  <i class="fas fa-download"></i> {{ $application->candidate->cv_original_name }}
                </a>
              @else
                <small class="text-muted">Not uploaded</small>
              @endif
            </td>
          </tr>
        </table>
      </div>
    </div>

    {{-- Danger Zone --}}
    <div class="card border-danger">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('positions.stage.update', $application) }}" method="POST" class="mb-2">
          @csrf
          <input type="hidden" name="stage" value="rejected">
          <div class="d-grid">
            <button type="submit" class="btn btn-outline-danger"
                    onclick="return confirm('Mark this application as Rejected?')">
              <i class="fas fa-times me-1"></i>Reject Application
            </button>
          </div>
        </form>
        <form action="{{ route('positions.destroy', $application) }}" method="POST">
          @csrf @method('DELETE')
          <div class="d-grid">
            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Permanently delete this application?')">
              <i class="fas fa-trash me-1"></i>Delete Application
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

{{-- Schedule Interview Modal --}}
<div class="modal fade" id="scheduleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('positions.rounds.store', $application) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-calendar-plus me-2 text-primary"></i>Schedule Interview</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Round Name <span class="text-danger">*</span></label>
              <input type="text" name="round_name" class="form-control"
                     placeholder="e.g. Technical Round 1" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Type</label>
              <select name="round_type" class="form-select">
                <option value="screening">Screening</option>
                <option value="technical">Technical</option>
                <option value="hr">HR</option>
                <option value="final">Final</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Mode</label>
              <select name="mode" class="form-select">
                @foreach(\App\Models\InterviewRound::modes() as $k => $v)
                  <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Scheduled At <span class="text-danger">*</span></label>
              <input type="datetime-local" name="scheduled_at" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Duration (minutes)</label>
              <input type="number" name="duration_minutes" class="form-control" value="60" min="15" max="480">
            </div>
            <div class="col-md-8">
              <label class="form-label">Interviewer</label>
              <select name="interviewer_id" class="form-select">
                <option value="">-- Select Interviewer --</option>
                @foreach($users as $id => $name)
                  <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Meeting Link</label>
              <input type="url" name="meeting_link" class="form-control"
                     placeholder="https://meet.google.com/...">
            </div>
            <div class="col-12">
              <label class="form-label">Location (if in-person)</label>
              <input type="text" name="location" class="form-control"
                     placeholder="e.g. Conference Room 3B">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-calendar-check me-1"></i>Schedule Interview
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
