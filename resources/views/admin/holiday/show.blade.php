@extends('layouts.admin')

@section('title')
    {{ __('Holiday Details') }}
@endsection

@section('header')
  <div class="mb-4">
    <h1 class="h3 mb-3">Holiday Details</h1>
  </div>
@endsection

@section('content')
  <section class="row">
    <div class="col-12 col-md-8 offset-md-2">
      
      @forelse($holidays as $holiday)
        <div class="card mb-4 shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="card-title mb-0">Overview: <strong>{{ $holiday->name }}</strong></h5>
            @if($holiday->status)
              <span class="badge bg-success">Active</span>
            @else
              <span class="badge bg-danger">Inactive</span>
            @endif
          </div>
          <div class="card-body">
            <table class="table table-bordered table-striped mb-0">
              <tbody>
                <tr>
                  <th style="width: 30%;">Holiday Name</th>
                  <td>{{ $holiday->name }}</td>
                </tr>
                <tr>
                  <th>Start Date</th>
                  <td>{{ $holiday->start_date ? $holiday->start_date->format('F d, Y') : 'N/A' }}</td>
                </tr>
                <tr>
                  <th>End Date</th>
                  <td>{{ $holiday->end_date ? $holiday->end_date->format('F d, Y') : 'N/A' }}</td>
                </tr>
                <tr>
                  <th>Duration</th>
                  <td><strong>{{ $holiday->no_of_days }}</strong> Days</td>
                </tr>
                <tr>
                  <th>Description</th>
                  <td>
                    @if($holiday->description)
                      {{ $holiday->description }}
                    @else
                      <span class="text-muted">No description provided.</span>
                    @endif
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      @empty
        <div class="card">
          <div class="card-body text-center py-4 text-muted">
            <i class="fas fa-calendar-times fa-2x mb-2"></i>
            <p class="mb-0">No company holidays are scheduled at the moment.</p>
          </div>
        </div>
      @endforelse

    </div>
  </section>
@endsection

@section('script')
@endsection