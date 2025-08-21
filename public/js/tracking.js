const urlParams = new URLSearchParams(window.location.search);
const trackingId = urlParams.get('tracking_id');

function formatCustomDateTimeRandom(dateTimes) {
    const isoDate = dateTimes.replace(' ', 'T');
    const d = new Date(isoDate);
    if (isNaN(d)) return dateTimes;
    const offset = Math.floor(Math.random() * 31) - 15;
    d.setMinutes(d.getMinutes() + offset);
    return d.toLocaleString('en-US', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: 'numeric', minute: '2-digit', hour12: true
    });
}

function formatDateTime(dateTime, randomize = false) {
    const d = new Date(dateTime);
    if (isNaN(d)) return dateTime;
    if (randomize) d.setMinutes(d.getMinutes() + (Math.floor(Math.random() * 31) - 15));
    return d.toLocaleTimeString("en-US", { hour: "numeric", minute: "2-digit", hour12: true }).toLowerCase();
}

function resetTracking() {
    document.getElementById('tracking-number').textContent = 'Invalid Tracking Number';
    document.getElementById('delivery-label').textContent = '';
    document.getElementById('summary').textContent = '';
    document.getElementById('delivery-day').textContent = '';
    document.getElementById('delivery-date').textContent = '';
    document.getElementById('delivery-month').textContent = '';
    document.getElementById('delivery-time').textContent = '';
    document.getElementById('events').innerHTML = '';
}

if (trackingId) {
    fetch(`/api/trackings/${trackingId}`)
        .then(response => { if (!response.ok) throw new Error('Not found'); return response.json(); })
        .then(data => {
            document.getElementById('tracking-number').textContent = data.tracking_number || trackingId;

            const deliveryBox = document.getElementById('delivery-box');
            const deliveryLabel = document.getElementById('delivery-label');

            deliveryLabel.textContent = (data.status === 'delivered') ? 'Delivered On' : 'Expected Delivery on';

            switch ((data.status || '').toLowerCase()) {
                case 'in_transit':
                    deliveryBox.style.backgroundColor = '#e0f2fe';
                    deliveryLabel.style.color = '#2563eb';
                    document.getElementById('delivery-date').style.color = '#2563eb';
                    document.getElementById('delivery-month').style.color = '#2563eb';
                    break;
                case 'delayed':
                    deliveryBox.style.backgroundColor = '#fee2e2';
                    deliveryLabel.style.color = '#dc2626';
                    document.getElementById('delivery-date').style.color = '#dc2626';
                    document.getElementById('delivery-month').style.color = '#dc2626';
                    break;
                case 'delivered':
                    deliveryBox.style.backgroundColor = '#d1fae5';
                    deliveryLabel.style.color = '#059669';
                    document.getElementById('delivery-date').style.color = '#059669';
                    document.getElementById('delivery-month').style.color = '#059669';
                    break;
                default:
                    deliveryBox.style.backgroundColor = '#f3f4f6';
                    deliveryLabel.style.color = '#6b7280';
                    document.getElementById('delivery-date').style.color = '#2563eb';
                    document.getElementById('delivery-month').style.color = '#374151';
            }

            const eta = new Date(data.expected_delivery_date);
            if (!isNaN(eta)) {
                const weekday = eta.toLocaleDateString('en-US', { weekday: 'long' });
                const day = eta.getDate();
                const monthYear = eta.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                document.getElementById('delivery-day').textContent = weekday.toUpperCase();
                document.getElementById('delivery-date').textContent = day;
                document.getElementById('delivery-month').textContent = monthYear;
                document.getElementById('delivery-time').textContent = "by 10:00 PM";
            }

            const eventsList = document.getElementById('events');
            if (data.events && data.events.length > 0) {
                const latest = data.events[0];
                const dateTime = `${latest.date} ${latest.time || ""}`;
                document.getElementById('summary').textContent = `Pick up from ${latest.location} at ${formatCustomDateTimeRandom(dateTime)}`;
                data.events.forEach(event => {
                    const dateTime = `${event.date} ${event.time || ""}`.replace(/\.$/, '').trim();
                    const li = document.createElement('li');
                    li.innerHTML = `<div class="event-status">${event.status}</div>
                                    <div class="event-location">${event.location || ''}</div>
                                    <div class="event-date">${formatCustomDateTimeRandom(dateTime)}</div>`;
                    eventsList.appendChild(li);
                });
            }
        })
        .catch(resetTracking);
} else {
    resetTracking();
}
