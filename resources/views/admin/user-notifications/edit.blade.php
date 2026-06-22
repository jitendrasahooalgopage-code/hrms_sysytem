@extends('layouts.admin')

@section('title') {{ __('Edit Notification') }} @endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3">{{ __('Edit Notification Template') }} (#{{ $notification->id }})</h1>
    <a href="{{ route('admin.notifications.index') }}" class="btn btn-light border">Back</a>
  </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.notifications.update', $notification) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Notification Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $notification->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Body Content <span class="text-danger">*</span></label>
                        <textarea name="body" rows="4" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $notification->body) }}</textarea>
                        @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                @foreach($categories as $key => $val)
                                    <option value="{{ $key }}" {{ old('category', $notification->category) == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type Preset Asset <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                @foreach($types as $key => $val)
                                    <option value="{{ $key }}" {{ old('type', $notification->type) == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4 border-top pt-3">
                        <h5>Recipient Target Logic</h5>
                        @php
                            $isBroadcast = old('is_broadcast', $notification->is_broadcast ? '1' : '0');
                        @endphp
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input target-toggle" type="radio" name="is_broadcast" id="target_all" value="1" {{ $isBroadcast == '1' ? 'checked' : '' }}>
                                <label class="form-check-input-label" for="target_all">Broadcast to All Users</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input target-toggle" type="radio" name="is_broadcast" id="target_custom" value="0" {{ $isBroadcast == '0' ? 'checked' : '' }}>
                                <label class="form-check-input-label" for="target_custom">Target Specific Demographics</label>
                            </div>
                        </div>

                        <div id="custom-targeting-fields" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Target Roles</label>
                                <select name="target_roles[]" class="form-select" multiple size="4">
                                    @foreach($roles as $slug => $name)
                                        <option value="{{ $slug }}" {{ is_array(old('target_roles', $notification->target_roles)) && in_array($slug, old('target_roles', $notification->target_roles ?? [])) ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Target Specific Employees</label>
                                <select name="target_employee_ids[]" class="form-select" multiple size="5">
                                    @foreach($employees as $id => $name)
                                        <option value="{{ $id }}" {{ is_array(old('target_employee_ids', $notification->target_employee_ids)) && in_array($id, old('target_employee_ids', $notification->target_employee_ids ?? [])) ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 border-top pt-3">
                        <h5>Actions & Links</h5>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Action URL</label>
                            <input type="text" name="action_url" class="form-control" value="{{ old('action_url', $notification->action_url) }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Action Label Button</label>
                            <input type="text" name="action_label" class="form-control" value="{{ old('action_label', $notification->action_label) }}">
                        </div>
                    </div>

                    <div class="row mb-4 border-top pt-3">
                        <div class="col-md-6">
                            <label class="form-label">Scheduled Time</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at', $notification->scheduled_at ? $notification->scheduled_at->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end border-top pt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Draft
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const customTargetGroup = document.getElementById('custom-targeting-fields');
    const toggles = document.querySelectorAll('.target-toggle');

    function checkToggleState() {
        const activeRadio = document.querySelector('.target-toggle:checked');
        if(activeRadio && activeRadio.value === "0") {
            customTargetGroup.style.display = 'block';
        } else {
            customTargetGroup.style.display = 'none';
        }
    }

    toggles.forEach(radio => radio.addEventListener('change', checkToggleState));
    checkToggleState();
});
</script>
@endsection