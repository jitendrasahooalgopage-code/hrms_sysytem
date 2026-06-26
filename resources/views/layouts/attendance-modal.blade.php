{{-- ═══════════════════════════════════════════════
     ATTENDANCE MODAL LAYER
     ═══════════════════════════════════════════════ --}}
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-0 shadow">

            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <span id="modalIconWrap" class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:36px; height:36px;">
                        <i id="modalIcon" data-feather="log-in" style="width:18px; height:18px;"></i>
                    </span>
                    <h5 class="modal-title mb-0 fw-semibold" id="attendanceModalLabel">Check In</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body pt-3">

                {{-- Session Checkin Tracking Badge --}}
                <div id="checkinTimeBadge" class="d-none alert py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size: 0.82rem; background: #fff3cd; border: 1px solid #ffc107; color: #856404;">
                    <i data-feather="clock" style="width:14px; height:14px; flex-shrink:0;"></i>
                    <span>Checked in at: <strong id="checkinTimeText"></strong></span>
                </div>

                {{-- Geo-Tracking Leaflet Map Stage Area --}}
                <div class="position-relative rounded overflow-hidden border mb-3" style="height: 220px;">
                    <div id="attendanceMap" style="width: 100%; height: 100%;"></div>
                    <div id="mapLoadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white bg-opacity-90">
                        <div class="spinner-border spinner-border-sm text-secondary mb-2" role="status"></div>
                        <small class="text-muted">Fetching your GPS location…</small>
                    </div>
                    <div id="mapPlaceholder" class="position-absolute top-0 start-0 w-100 h-100 d-none flex-column align-items-center justify-content-center bg-light">
                        <i data-feather="map-pin" class="text-muted mb-1" style="width: 32px; height: 32px; opacity: 0.4;"></i>
                        <small class="text-muted">Map will appear here</small>
                    </div>
                </div>

                {{-- Location Error Display Card --}}
                <div id="locationError" class="alert alert-danger d-none py-2 px-3" style="font-size: 0.82rem;">
                    <i data-feather="alert-triangle" style="width: 14px; height: 14px; vertical-align: -2px;" class="me-1"></i>
                    <span id="locationErrorText"></span>
                </div>

                {{-- Resolved Geocode Address Information Segment --}}
                <div id="locationInfo" class="d-none rounded p-2 mb-3" style="font-size: 0.82rem;">
                    <div class="fw-semibold mb-1" id="locationInfoTitle">Location detected</div>
                    <div id="locationAddress" class="text-muted"></div>
                    <div id="locationCoords" class="mt-1" style="font-size: 0.75rem; opacity: 0.7;"></div>
                </div>

            </div>

            <div class="modal-footer border-0 pt-0 gap-2">
                <button type="button" id="refreshLocationBtn" class="btn btn-light btn-sm d-flex align-items-center gap-1">
                    <i data-feather="refresh-cw" style="width: 14px; height: 14px;"></i> Refresh Location
                </button>
                <button type="button" id="submitAttendanceBtn" class="btn btn-sm d-flex align-items-center gap-1" disabled>
                    <i id="submitBtnIcon" data-feather="log-in" style="width: 14px; height: 14px;"></i>
                    <span id="submitBtnText">Check In Now</span>
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Toast Notification Overlay Component --}}
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="attendanceToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="toastBody"></div>
            <button type="button" class="btn-close me-2 m-auto" id="toastCloseBtn" aria-label="Close"></button>
        </div>
    </div>
</div>

{{-- External Open-Source GIS Leaflet Style Sheets and Mapping Drivers --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>