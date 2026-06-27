/**
 * Enterprise Attendance Engine Architecture Module
 * Handles GIS Operations, Geolocation, Application state cycles, and Bootstrap Overlays.
 */

// Modern Decoupled DOM Event Listeners Engine
document.addEventListener('DOMContentLoaded', () => {
    const targetModal = document.getElementById('attendanceModal');
    if (targetModal) {
        targetModal.addEventListener('hidden.bs.modal', function () {
            if (attendanceMapInstance) {
                attendanceMapInstance.remove();
                attendanceMapInstance = null;
                attendanceMarkerInstance = null;
            }

            currentPositionCoords = null;
            currentResolvedAddress = '';

            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }

    // Programmatic Event Handling setup completely intercepts timing race conditions
    const checkinBtnElement = document.getElementById('navCheckinBtn');
    if (checkinBtnElement) {
        // Debug Logger 1
        console.log('[Attendance Engine]: Check-In button found in DOM. Binding event listener.');
        checkinBtnElement.addEventListener('click', function(e) {
            e.preventDefault();
            window.openAttendanceModal('checkin');
        });
    } else {
        console.warn('[Attendance Engine]: CRITICAL - #navCheckinBtn was not found in the DOM.');
    }

    const checkoutBtnElement = document.getElementById('navCheckoutBtn');
    if (checkoutBtnElement) {
        checkoutBtnElement.addEventListener('click', function(e) {
            e.preventDefault();
            window.openAttendanceModal('checkout');
        });
    }

    // Run startup sync
    loadAttendanceStatusFromServer();
});

// Isolated Operational Module Context Cache
let mapInstance = null;
let markerInstance = null;
let currentMode = 'checkin';
let currentCoords = null;
let currentAddress = '';
let isSubmitting = false;
let activeLogId = null;
let activeCheckinTime = null;
let toastBsInstance = null;

const config = {
    checkin: {
        label: 'Check In',
        featherIcon: 'log-in',
        color: '#198754',
        bgClass: 'bg-success bg-opacity-10',
        textClass: 'text-success',
        btnClass: 'btn-success',
        infoClass: 'bg-success bg-opacity-10',
        infoTitle: 'Location detected',
        endpoint: '/attendance/checkin' // Fallback endpoint or standard tracking path
    },
    checkout: {
        label: 'Check Out',
        featherIcon: 'log-out',
        color: '#fd7e14',
        bgClass: 'bg-warning bg-opacity-10',
        textClass: 'text-warning',
        btnClass: 'btn-warning',
        infoClass: 'bg-warning bg-opacity-10',
        infoTitle: 'Location detected',
        endpoint: '/attendance/checkout'
    }
};

/**
 * Synchronizes and coordinates the UI button configurations inside the Navbar layout
 * @param {boolean} isCheckedIn 
 */
function updateNavbarButtonState(isCheckedIn) {
    const checkinBtn = document.getElementById('navCheckinBtn');
    const checkoutBtn = document.getElementById('navCheckoutBtn');

    if (!checkinBtn || !checkoutBtn) return;

    if (isCheckedIn) {
        checkinBtn.classList.add('disabled', 'opacity-50');
        checkinBtn.style.pointerEvents = 'none';
        checkinBtn.style.cursor = 'not-allowed';
        checkinBtn.title = 'Already checked in — check out first';

        checkoutBtn.classList.remove('disabled', 'opacity-50');
        checkoutBtn.style.pointerEvents = '';
        checkoutBtn.style.cursor = '';
        checkoutBtn.title = 'Check Out';
    } else {
        checkinBtn.classList.remove('disabled', 'opacity-50');
        checkinBtn.style.pointerEvents = '';
        checkinBtn.style.cursor = '';
        checkinBtn.title = 'Check In';

        checkoutBtn.classList.add('disabled', 'opacity-50');
        checkoutBtn.style.pointerEvents = 'none';
        checkoutBtn.style.cursor = 'not-allowed';
        checkoutBtn.title = 'Check Out — check in first';
    }
}

/**
 * Communicates with backend controllers to fetch existing shift logs
 */
async function syncAttendanceStatus() {
    try {
        const response = await fetch('/attendance/status', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();

        if (data.is_checked_in && data.active_log) {
            activeLogId = data.active_log.id;
            activeCheckinTime = data.active_log.checkin_at;
            updateNavbarButtonState(true);
        } else {
            activeLogId = null;
            activeCheckinTime = null;
            updateNavbarButtonState(false);
        }
    } catch (error) {
        console.error('[Attendance Engine]: Failed synchronization with state engine.', error);
    }
}

/**
 * Resets contextual UI sub-elements inside the container template view layer
 */
function resetModalUI() {
    const errEl = document.getElementById('locationError');
    const infoEl = document.getElementById('locationInfo');
    const placeholderEl = document.getElementById('mapPlaceholder');
    const overlayEl = document.getElementById('mapLoadingOverlay');
    const submitBtn = document.getElementById('submitAttendanceBtn');
    const refreshBtn = document.getElementById('refreshLocationBtn');

    if (errEl) errEl.classList.add('d-none');
    if (infoEl) infoEl.classList.add('d-none');
    if (placeholderEl) placeholderEl.classList.add('d-none');
    if (overlayEl) overlayEl.classList.remove('d-none');
    if (submitBtn) submitBtn.disabled = true;
    if (refreshBtn) refreshBtn.disabled = false;
}

/**
 * Explicit global exposure method to initialize and deploy the core container overlays safely
 * @param {string} mode - 'checkin' | 'checkout'
 */
window.openAttendanceModal = function(mode) {
    currentMode = mode;
    currentCoords = null;
    currentAddress = '';
    isSubmitting = false;

    const cfg = config[mode];

    // Dom element configuration manipulations
    const titleLabel = document.getElementById('attendanceModalLabel');
    const iconWrap = document.getElementById('modalIconWrap');
    const modalIcon = document.getElementById('modalIcon');
    const submitBtn = document.getElementById('submitAttendanceBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const submitBtnIcon = document.getElementById('submitBtnIcon');
    const timeBadge = document.getElementById('checkinTimeBadge');

    if (titleLabel) titleLabel.textContent = cfg.label;
    if (iconWrap) iconWrap.className = `d-inline-flex align-items-center justify-content-center rounded-circle ${cfg.bgClass}`;
    if (modalIcon) {
        modalIcon.setAttribute('data-feather', cfg.featherIcon);
        modalIcon.className = cfg.textClass;
    }
    if (submitBtn) {
        submitBtn.className = `btn btn-sm d-flex align-items-center gap-1 ${cfg.btnClass}`;
        submitBtn.disabled = true;
    }
    if (submitBtnText) submitBtnText.textContent = cfg.label + ' Now';
    if (submitBtnIcon) submitBtnIcon.setAttribute('data-feather', cfg.featherIcon);

    if (mode === 'checkout' && activeCheckinTime) {
        const dt = new Date(activeCheckinTime);
        const textStr = dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }) + ', ' + dt.toLocaleDateString();
        const textEl = document.getElementById('checkinTimeText');
        if (textEl) textEl.textContent = textStr;
        if (timeBadge) {
            timeBadge.classList.remove('d-none');
            timeBadge.classList.add('d-flex');
        }
    } else {
        if (timeBadge) {
            timeBadge.classList.add('d-none');
            timeBadge.classList.remove('d-flex');
        }
    }

    resetModalUI();
    if (typeof feather !== 'undefined') feather.replace();

    const targetModal = document.getElementById('attendanceModal');
    if (targetModal) {
        const modalInstance = bootstrap.Modal.getOrCreateInstance(targetModal);
        modalInstance.show();
    }

    window.fetchLocation();
};

/**
 * Queries standard browser geolocation positioning infrastructure
 */
window.fetchLocation = function() {
    resetModalUI();

    if (!navigator.geolocation) {
        showLocationError('Geolocation is not supported by your browser workflow.');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        onLocationSuccess,
        onLocationError, 
        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        }
    );
};

/**
 * Geolocation Success Callback Route Handler
 */
function onLocationSuccess(position) {
    const lat = position.coords.latitude;
    const lon = position.coords.longitude;
    const accuracy = position.coords.accuracy;

    currentCoords = { lat, lon, accuracy };

    // Reverse Geocoding with OSM Nominatim Engine
    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=jsonv2`, {
        headers: {
            'Accept-Language': 'en',
            'User-Agent': 'ERP-Attendance-System/2.0'
        }
    })
    .then(r => r.json())
    .then(data => {
        currentAddress = data.display_name || `${lat.toFixed(6)}, ${lon.toFixed(6)}`;
    })
    .catch(() => {
        currentAddress = `${lat.toFixed(6)}, ${lon.toFixed(6)}`;
    })
    .finally(() => {
        buildLeafletMap(lat, lon);
        showLocationDataCard(lat, lon);
        const overlayEl = document.getElementById('mapLoadingOverlay');
        const submitBtn = document.getElementById('submitAttendanceBtn');
        if (overlayEl) overlayEl.classList.add('d-none');
        if (submitBtn) submitBtn.disabled = false;
    });
}

/**
 * Geolocation Failure Callback Route Handler
 */
function onLocationError(error) {
    const overlayEl = document.getElementById('mapLoadingOverlay');
    const placeholderEl = document.getElementById('mapPlaceholder');
    
    if (overlayEl) overlayEl.classList.add('d-none');
    if (placeholderEl) {
        placeholderEl.classList.remove('d-none');
        placeholderEl.style.display = 'flex';
    }

    const errorMappings = {
        [error.PERMISSION_DENIED]: 'GPS access authorization denied. Please restore permissions in browser options settings configuration.',
        [error.POSITION_UNAVAILABLE]: 'Position telemetry unavailable. Confirm your internal system GPS configuration is live.',
        [error.TIMEOUT]: 'Location triangulation request connection timed out. Retrying execution.',
    };
    
    showLocationError(errorMappings[error.code] || 'Triangulation system tracking encountered an unrecognized layout state exception.');
    if (typeof feather !== 'undefined') feather.replace();
}

function showLocationError(msg) {
    const errEl = document.getElementById('locationErrorText');
    const container = document.getElementById('locationError');
    if (errEl) errEl.textContent = msg;
    if (container) container.classList.remove('d-none');
    if (typeof feather !== 'undefined') feather.replace();
}

function showLocationDataCard(lat, lon) {
    const cfg = config[currentMode];
    const cardContainer = document.getElementById('locationInfo');
    const titleEl = document.getElementById('locationInfoTitle');
    const addressEl = document.getElementById('locationAddress');
    const coordsEl = document.getElementById('locationCoords');

    if (cardContainer) {
        cardContainer.className = `rounded p-2 mb-3 ${cfg.infoClass}`;
        cardContainer.classList.remove('d-none');
    }
    if (titleEl) {
        titleEl.textContent = cfg.infoTitle;
        titleEl.className = `fw-semibold mb-1 ${cfg.textClass}`;
    }
    if (addressEl) addressEl.textContent = currentAddress || 'Resolving geocode address references…';
    if (coordsEl) coordsEl.textContent = `Lat: ${lat.toFixed(6)}, Lon: ${lon.toFixed(6)}`;
}

/**
 * Deploys Leaflet Map instances dynamically inside target templates
 */
function buildLeafletMap(lat, lon) {
    const cfg = config[currentMode];
    const mapContainer = document.getElementById('attendanceMap');

    if (!mapContainer || typeof L === 'undefined') return;

    if (mapInstance) {
        mapInstance.remove();
        mapInstance = null;
        markerInstance = null;
    }

    mapInstance = L.map(mapContainer, {
        zoomControl: true,
        attributionControl: true
    }).setView([lat, lon], 17);

    L.tileLayer('https://{s}.tile.wikimedia.org/osm-intl/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(mapInstance);

    const mapPinIcon = L.divIcon({
        html: `<div style="width:28px; height:28px; background:${cfg.color}; border:3px solid #fff; border-radius:50% 50% 50% 0; transform:rotate(-45deg); box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>`,
        iconSize: [28, 28],
        iconAnchor: [14, 28],
        className: ''
    });

    markerInstance = L.marker([lat, lon], { icon: mapPinIcon }).addTo(mapInstance);

    setTimeout(() => {
        if (mapInstance) {
            mapInstance.invalidateSize();
        }
    }, 500);
}

/**
 * Transmits telemetry payload parameters back to application server configurations
 */
window.submitAttendance = async function() {
    if (!currentCoords || isSubmitting) return;
    isSubmitting = true;

    const submitBtn = document.getElementById('submitAttendanceBtn');
    const btnTextEl = document.getElementById('submitBtnText');
    const originalText = btnTextEl ? btnTextEl.textContent : 'Confirm';

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving…`;
    }

    const CSRF_ELEMENT = document.querySelector('meta[name="csrf-token"]');
    const routingUri = currentMode === 'checkin' ? '/attendance/checkin' : '/attendance/checkout';

    try {
        const response = await fetch(routingUri, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_ELEMENT ? CSRF_ELEMENT.content : ''
            },
            body: JSON.stringify({
                latitude: currentCoords.lat,
                longitude: currentCoords.lon,
                address: currentAddress,
                accuracy: currentCoords.accuracy
            })
        });

        const data = await response.json();

        if (data.success) {
            const targetModalElement = document.getElementById('attendanceModal');
            if (targetModalElement) {
                bootstrap.Modal.getOrCreateInstance(targetModalElement).hide();
            }

            if (currentMode === 'checkin') {
                activeLogId = data.log_id || null;
                activeCheckinTime = data.checkin_at || new Date().toISOString();
                updateNavbarButtonState(true);
                showToastNotification(data.message || 'Check-in metrics captured successfully.', 'success');
            } else {
                activeLogId = null;
                activeCheckinTime = null;
                updateNavbarButtonState(false);
                const completeDurationStr = data.session_duration ? ` Duration: ${data.session_duration}` : '';
                showToastNotification((data.message || 'Check-out completed.') + completeDurationStr, 'success');
            }
        } else {
            showLocationError(data.message || 'Transactional synchronization tracking error state confirmed.');
            restoreSubmitButton(submitBtn, originalText);
        }
    } catch (error) {
        showLocationError('Network framework channel layer exception timed out. Verification failure.');
        restoreSubmitButton(submitBtn, originalText);
    }
};

function restoreSubmitButton(btn, text) {
    if (!btn) return;
    btn.disabled = false;
    btn.innerHTML = `<i id="submitBtnIcon" data-feather="${config[currentMode].featherIcon}" style="width:14px; height:14px;"></i> <span id="submitBtnText">${text}</span>`;
    if (typeof feather !== 'undefined') feather.replace();
    isSubmitting = false;
}

function showToastNotification(message, type = 'success') {
    const toastElement = document.getElementById('attendanceToast');
    const bodyElement = document.getElementById('toastBody');
    const closeBtn = document.getElementById('toastCloseBtn');

    if (!toastElement || !bodyElement) return;

    toastElement.classList.remove('bg-success', 'bg-danger', 'text-white', 'show');
    toastElement.classList.add(type === 'success' ? 'bg-success' : 'bg-danger', 'text-white');

    bodyElement.innerHTML = `
        <i data-feather="${type === 'success' ? 'check-circle' : 'alert-circle'}" style="width: 16px; height: 16px;"></i>
        <span>${message}</span>
    `;

    if (typeof feather !== 'undefined') feather.replace();

    toastBsInstance = bootstrap.Toast.getOrCreateInstance(toastElement, {
        delay: 4000,
        autohide: true
    });

    if (closeBtn) {
        closeBtn.onclick = () => toastBsInstance.hide();
    }

    toastBsInstance.show();
}

// Global Core Decoupled Event Hook Listeners Register Pipeline Orchestration
document.addEventListener('DOMContentLoaded', () => {
    const checkinTrigger = document.getElementById('navCheckinBtn');
    const checkoutTrigger = document.getElementById('navCheckoutBtn');
    const refreshTrigger = document.getElementById('refreshLocationBtn');
    const executeTrigger = document.getElementById('submitAttendanceBtn');
    const modalViewContainer = document.getElementById('attendanceModal');

    // Attach Clean Declarative Event Listeners
    if (checkinTrigger) {
        checkinTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            if (!checkinTrigger.classList.contains('disabled')) {
                window.openAttendanceModal('checkin');
            }
        });
    }

    if (checkoutTrigger) {
        checkoutTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            if (!checkoutTrigger.classList.contains('disabled')) {
                window.openAttendanceModal('checkout');
            }
        });
    }

    if (refreshTrigger) {
        refreshTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            window.fetchLocation();
        });
    }

    if (executeTrigger) {
        executeTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            window.submitAttendance();
        });
    }

    // Modal Complete Architectural Teardown Layer
    if (modalViewContainer) {
        modalViewContainer.addEventListener('hidden.bs.modal', () => {
            if (mapInstance) {
                mapInstance.remove();
                mapInstance = null;
                markerInstance = null;
            }

            currentCoords = null;
            currentAddress = '';

            // Clean up backdrops left behind by structural framework timing drops
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }

    // Initialize State Logic Setup On Startup Core Cycles Execution
    syncAttendanceStatus();
});