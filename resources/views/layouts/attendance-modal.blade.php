{{-- ═══════════════════════════════════════════════
     ENTERPRISE ATTENDANCE MODAL LAYER (REDESIGNED)
     ═══════════════════════════════════════════════ --}}

<div class="modal fade custom-hrms-modal" id="attendanceModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog m-auto mt-5" style="max-width: 490px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden; background: #ffffff;">

            <div class="modal-header border-bottom align-items-center px-4 py-3" style="border-color: #f1f5f9 !important;">
                <div class="d-flex align-items-center gap-3">
                    <span id="modalIconWrap" class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background-color: #f0fdf4; color: #16a34a;">
                        <i id="modalIcon" data-feather="log-in" style="width: 20px; height: 20px;"></i>
                    </span>
                    <div>
                        <h5 class="modal-title mb-0 fw-bold text-dark" id="attendanceModalLabel" style="font-size: 1.05rem; letter-spacing: -0.01em;">Check In</h5>
                        <small class="text-muted d-block" style="font-size: 0.78rem;">Enterprise Location Verification</small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.75rem;"></button>
            </div>

            <div class="modal-body p-4">  

                {{-- Section 1: Session Checkin Status Badge --}}
                <div id="checkinTimeBadge" class="d-none alert py-2.5 px-3 mb-3 d-flex align-items-center gap-2.5 border-0" style="font-size: 0.85rem; background-color: #fffbeb; color: #b45309; border-radius: 12px; font-weight: 500;">
                    <i data-feather="clock" style="width: 16px; height: 16px; flex-shrink: 0; color: #d97706;"></i>
                    <span>🕒 Checked in at <strong id="checkinTimeText" class="fw-bold"></strong></span>
                </div>

                {{-- Section 2: Geo-Tracking Leaflet Map Stage Area (~280px Height) --}}
                <div class="position-relative overflow-hidden mb-3 border" style="height: 280px; border-radius: 14px; border-color: #e2e8f0 !important; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.04);">
                    <div id="attendanceMap" style="width: 100%; height: 100%; z-index: 1;"></div>
                    
                    <div id="mapLoadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white" style="z-index: 1000; background-color: rgba(255, 255, 255, 0.94) !important;">
                        <div class="spinner-border mb-2.5 text-primary" role="status" style="width: 1.5rem; height: 1.5rem; border-width: 0.2em; color: #4f46e5 !important;"></div>
                        <small class="text-secondary fw-medium" style="font-size: 0.82rem;">📍 Ready to Check In. Fetching your GPS location…</small>
                    </div>
                    
                    <div id="mapPlaceholder" class="position-absolute top-0 start-0 w-100 h-100 d-none flex-column align-items-center justify-content-center bg-light" style="z-index: 999;">
                        <i data-feather="map-pin" class="text-muted mb-1" style="width: 32px; height: 32px; opacity: 0.4;"></i>
                        <small class="text-muted">Map initialization pending</small>
                    </div>
                </div>

                {{-- Section 4: Modern Alert Card for Errors --}}
                <div id="locationError" class="alert d-none py-2.5 px-3 mb-3 border-0" style="font-size: 0.85rem; background-color: #fef2f2; color: #dc2626; border-radius: 12px;">
                    <div class="d-flex align-items-start gap-2">
                        <i data-feather="alert-triangle" style="width: 16px; height: 16px; flex-shrink: 0;" class="mt-0.5"></i>
                        <span id="locationErrorText" class="fw-medium"></span>
                    </div>
                </div>

                {{-- Section 3: Premium Geocode Address Info Card --}}
                <div id="locationInfo" class="d-none mb-0 border p-3" style="border-radius: 14px; background-color: #f8fafc; border-color: #e2e8f0 !important;">
                    <div class="d-flex align-items-start gap-2.5">
                        <i data-feather="navigation" class="mt-0.5" style="width: 16px; height: 16px; color: #4f46e5; flex-shrink: 0;"></i>
                        <div class="w-100">
                            <div class="fw-bold text-dark mb-1" id="locationInfoTitle" style="font-size: 0.85rem; letter-spacing: 0.01em;">Location Detected</div>
                            <div id="locationAddress" class="text-secondary mb-2" style="font-size: 0.825rem; line-height: 1.45;"></div>
                            
                            <div class="d-flex border-top pt-2 mt-1" style="border-color: #e2e8f0 !important; font-size: 0.72rem; color: #64748b;">
                                <div class="font-monospace">COORDINATES: <span id="locationCoords" class="fw-semibold"></span></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer px-4 py-3 border-top d-flex align-items-center justify-content-end gap-2" style="background-color: #f8fafc; border-color: #f1f5f9 !important;">
                <button type="button" id="refreshLocationBtn" class="btn btn-white border d-flex align-items-center gap-1.5 shadow-sm px-3 py-2 fw-medium text-dark hrms-btn-action" style="font-size: 0.825rem; border-radius: 10px; background: #ffffff; border-color: #d1d5db !important;">
                    <i data-feather="refresh-cw" style="width: 14px; height: 14px;"></i> Refresh Location
                </button>
                <button type="button" id="submitAttendanceBtn" class="btn d-flex align-items-center gap-1.5 px-3 py-2 fw-medium text-white hrms-btn-submit" disabled style="font-size: 0.825rem; border-radius: 10px; background-color: #4f46e5; border: 1px solid #4f46e5;">
                    <i id="submitBtnIcon" data-feather="log-in" style="width: 14px; height: 14px;"></i>
                    <span id="submitBtnText">Check In Now</span>
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Premium Toast Notification Component Overlay --}}
<div class="position-fixed top-0 end-0 p-3" style="z-index: 100000;">
    <div id="attendanceToast" class="toast align-items-center border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px; background: #ffffff;">
        <div class="d-flex align-items-center p-2.5">
            <div class="toast-body d-flex align-items-center gap-2 fw-semibold text-dark" id="toastBody" style="font-size: 0.85rem;"></div>
            <button type="button" class="btn-close me-2 m-auto shadow-none" id="toastCloseBtn" aria-label="Close" style="font-size: 0.72rem;"></button>
        </div>
    </div>
</div>

{{-- External Open-Source GIS Leaflet Drivers --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

{{-- Scoped Styles for animations and transitions --}}
<style>
/* Modern Slide Down + Fade Keyframe Interpolation */
.custom-hrms-modal.modal.fade .modal-dialog {
    transform: translateY(-40px);
    transition: transform 250ms cubic-bezier(0.16, 1, 0.3, 1), opacity 250ms ease-in-out;
    opacity: 0;
}
.custom-hrms-modal.modal.show .modal-dialog {
    transform: translateY(0);
    opacity: 1;
}

/* Premium Backdrop Blur Customization */
.custom-hrms-modal.modal-backdrop, 
.modal-backdrop.show {
    background-color: rgba(15, 23, 42, 0.6) !important;
    backdrop-filter: blur(6px) !important;
    transition: opacity 250ms ease-in-out;
}

/* Hover effects for actionable elements */
.hrms-btn-action:hover {
    background-color: #f9fafb !important;
    border-color: #9ca3af !important;
}
.hrms-btn-submit:hover:not(:disabled) {
    background-color: #4338ca !important;
    border-color: #4338ca !important;
}
.hrms-btn-submit:disabled {
    background-color: #e2e8f0 !important;
    border-color: #e2e8f0 !important;
    color: #94a3b8 !important;
    cursor: not-allowed;
}
</style>