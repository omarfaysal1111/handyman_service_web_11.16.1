<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Tracking</title>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    />
    <style>
        body { background: #f8fafc; }
        #map { height: 60vh; min-height: 420px; border-radius: 12px; }
        .card { border: 0; border-radius: 12px; box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08); }
        .status-pill { text-transform: capitalize; }
        .driver-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Driver Tracking</h4>
        <span class="badge bg-secondary">Booking #{{ $bookingId }}</span>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img id="driverImage" class="driver-avatar" src="" alt="Driver">
                    <div>
                        <div class="fw-semibold" id="driverName">Waiting for driver...</div>
                        <small class="text-muted" id="driverContact">-</small>
                    </div>
                </div>

                <div class="mb-2">
                    <small class="text-muted d-block">Request Status</small>
                    <span id="statusPill" class="badge bg-warning status-pill">pending</span>
                </div>

                <div class="mb-2">
                    <small class="text-muted d-block">Pickup Address</small>
                    <span id="pickupAddress">-</span>
                </div>

                <div class="mb-2">
                    <small class="text-muted d-block">Drop Address</small>
                    <span id="dropAddress">-</span>
                </div>

                <div>
                    <small class="text-muted d-block">Last Updated</small>
                    <span id="updatedAt">-</span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card p-2">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""
></script>
<script>
    const bookingId = @json($bookingId);
    const statusUrl = @json(route('driver.tracking.status', ['bookingId' => $bookingId]));

    const map = L.map('map').setView([20.5937, 78.9629], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let driverMarker = null;
    let pickupMarker = null;
    let dropMarker = null;
    let line = null;

    function setStatusClass(status) {
        const statusPill = document.getElementById('statusPill');
        statusPill.className = 'badge status-pill';
        const normalized = (status || '').toLowerCase();

        if (normalized === 'accepted' || normalized === 'arrived' || normalized === 'started') {
            statusPill.classList.add('bg-primary');
        } else if (normalized === 'completed') {
            statusPill.classList.add('bg-success');
        } else if (normalized === 'cancelled') {
            statusPill.classList.add('bg-danger');
        } else {
            statusPill.classList.add('bg-warning');
        }
    }

    function parseLatLng(lat, lng) {
        if (lat === null || lng === null || lat === undefined || lng === undefined) {
            return null;
        }
        return [Number(lat), Number(lng)];
    }

    function upsertMarker(existingMarker, latLng, label) {
        if (!latLng) return existingMarker;
        if (!existingMarker) {
            return L.marker(latLng).addTo(map).bindPopup(label);
        }
        existingMarker.setLatLng(latLng).bindPopup(label);
        return existingMarker;
    }

    async function loadTracking() {
        try {
            const response = await fetch(statusUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const payload = await response.json();

            if (!payload.success || !payload.data) {
                return;
            }

            const data = payload.data;
            const driverLatLng = parseLatLng(data.driver_latitude, data.driver_longitude);
            const pickupLatLng = parseLatLng(data.pickup_latitude, data.pickup_longitude);
            const dropLatLng = parseLatLng(data.drop_latitude, data.drop_longitude);

            document.getElementById('driverName').innerText = data.driver_name || 'Driver';
            document.getElementById('driverContact').innerText = data.driver_contact || '-';
            document.getElementById('pickupAddress').innerText = data.pickup_address || '-';
            document.getElementById('dropAddress').innerText = data.drop_address || '-';
            document.getElementById('updatedAt').innerText = data.updated_at || '-';
            document.getElementById('statusPill').innerText = data.status || 'requested';
            document.getElementById('driverImage').src = data.driver_profile_image || 'https://via.placeholder.com/96x96.png?text=Driver';
            setStatusClass(data.status);

            driverMarker = upsertMarker(driverMarker, driverLatLng, 'Driver');
            pickupMarker = upsertMarker(pickupMarker, pickupLatLng, 'Pickup');
            dropMarker = upsertMarker(dropMarker, dropLatLng, 'Drop');

            if (line) {
                map.removeLayer(line);
                line = null;
            }

            const points = [pickupLatLng, driverLatLng, dropLatLng].filter(Boolean);
            if (points.length > 1) {
                line = L.polyline(points, { color: '#2563eb', weight: 4 }).addTo(map);
            }

            if (points.length > 0) {
                map.fitBounds(L.latLngBounds(points), { padding: [50, 50] });
            }
        } catch (error) {
            console.error('Tracking fetch failed', error);
        }
    }

    loadTracking();
    setInterval(loadTracking, 5000);
</script>
</body>
</html>
