@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="max-width: 800px; margin: 0 auto;">
        
      

        <div class="card-body p-4 bg-light bg-opacity-25">
            <form action="{{ route('employee.asset-request.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="employee_asset_id" value="{{ $asset->id }}">

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase tracking-wider">Request Type</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="11"></line></svg>
                        </span>
                        <select name="request_type" class="form-select border-start-0 ps-0 text-dark" style="font-size: 0.95rem;">
                            <option>Return Asset</option>
                            <option>Damage Report</option>
                            <option>Replacement Request</option>
                            <option>Repair Request</option>
                            <option>Other</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase tracking-wider">Subject</label>
                    <input type="text" 
                           name="subject" 
                           class="form-control rounded-3" 
                           placeholder="Briefly describe the core issue or request context...">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase tracking-wider">Detailed Message</label>
                    <textarea name="message" 
                              class="form-control rounded-3" 
                              rows="5" 
                              placeholder="Provide detailed information regarding your request, current hardware condition, or asset changes..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase tracking-wider">Attachment Evidence</label>
                    <div class="upload-dropzone position-relative border border-2 border-dashed rounded-3 p-4 text-center bg-white">
                        <input type="file" 
                               name="photos[]" 
                               multiple 
                               accept="image/*" 
                               class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer"
                               id="photoUploadInput">
                        
                        <div class="upload-prompt-content pointer-events-none py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted mb-2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            <p class="mb-1 fw-bold text-dark">Click or Drag to upload photos</p>
                            <p class="text-muted small mb-2">
                                Tip: Hold <kbd class="bg-secondary text-white px-1 rounded">Ctrl</kbd> + click to select multiple images
                            </p>
                            <span class="badge bg-light text-secondary border border-light-subtle rounded-pill px-2.5 py-1.5 font-monospace small" style="font-size: 0.75rem;">
                                JPEG, PNG supported
                            </span>
                        </div>
                    </div>
                    
                    <div id="file-counter" class="form-text mt-2 px-1 d-none font-monospace small"></div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 border-top pt-4 mt-2">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold d-flex align-items-center gap-2 shadow-sm brand-btn">
                        <span>Submit Request</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .pointer-events-none { pointer-events: none; }
    
    /* Input Form Custom Focus Metrics */
    .form-select, .form-control {
        border-color: #e2e8f0;
        padding: 0.6rem 0.75rem;
    }
    .form-select:focus, .form-control:focus {
        border-color: #94a3b8;
        box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
    }

    /* Premium Custom Drag Box Styles */
    .upload-dropzone {
        border-color: #cbd5e1 !important;
        transition: all 0.2s ease-in-out;
    }
    .upload-dropzone:hover {
        background-color: #f8fafc !important;
        border-color: #3b82f6 !important;
    }

    .brand-btn {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
    }
    .brand-btn:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    }
</style>

<script>
// Dynamic listener updating helper to clearly reflect selected file batch counts
document.getElementById('photoUploadInput').addEventListener('change', function(e) {
    const fileCounter = document.getElementById('file-counter');
    const count = e.target.files.length;
    if (count > 0) {
        fileCounter.className = "form-text mt-2 px-1 font-monospace small text-success";
        fileCounter.textContent = `✔ Ready: ${count} image files selected successfully.`;
        fileCounter.classList.remove('d-none');
    } else {
        fileCounter.classList.add('d-none');
    }
});
</script>
@endsection