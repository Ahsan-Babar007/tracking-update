<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Tracking Details</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, sans-serif;
    background: #f9fafb;
    color: #111827;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  header {
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #4d148c;
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  header h1 { font-size: 2rem; font-weight: 700; }
  nav a {
    color: white;
    text-decoration: none;
    margin-left: 1.5rem;
    font-weight: 500;
    transition: color 0.3s;
  }
  nav a:hover { color: #ffcc00; }

  .container {
    max-width: 1200px;
    margin: 2rem auto;
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    flex: 1;
  }

  .tracking-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 1rem;
    margin-bottom: 2rem;
  }
  .tracking-number { font-size: 1.3rem; font-weight: bold; color: #2563eb; }
  .copy-btn {
    background: #2563eb;
    color: white;
    border: none;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
  }
  .copy-btn:hover { background: #1e40af; }

  .no-data-message {
    background: #fee2e2;
    color: #b91c1c;
    padding: 1rem;
    text-align: center;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: bold;
    margin-top: 2rem;
  }

  .content { 
    display: grid; 
    grid-template-columns: 1fr 2fr; 
    gap: 2rem;
    align-items: stretch;
  }

  .delivery-box, .timeline-container {
    visibility: hidden;
    min-height: 200px;
  }
  .delivery-box.visible, .timeline-container.visible {
    visibility: visible;
  }

  .delivery-box {
    background: #e0f7fa;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    height: 100%;
  }
  .delivery-label { font-size: 0.9rem; color: #6b7280; margin-bottom: 0.5rem; }
  .delivery-day { font-size: 1.3rem; font-weight: bold; color: #111827; }
  .delivery-date { font-size: 2.5rem; font-weight: bold; margin: 0.3rem 0; color: #2563eb; }
  .delivery-month { font-size: 1rem; color: #374151; }
  .delivery-time { font-size: 1.1rem; margin-top: 0.5rem; font-weight: bold; }
  .status-summary { 
    margin-top: 1rem; 
    font-size: 0.95rem; 
    line-height: 1.4; 
    color: #374151;
    flex-grow: 1;
    display: flex;
    align-items: flex-start;
  }

  .timeline-container {
    display: flex;
    flex-direction: column;
  }

  h2 { margin: 0 0 1rem; font-size: 1.2rem; color: #111827; }
  .timeline { 
    list-style: none; 
    margin: 3px;
    padding: 1.5px;
    border-left: 3px solid #2563eb;
  }
  .timeline li { margin: 1.5rem 0; padding-left: 1rem; position: relative; }
  .timeline li::before {
    content: "";
    position: absolute;
    left: -9px;
    top: 6px;
    width: 12px;
    height: 12px;
    background: #2563eb;
    border-radius: 50%;
  }
  .event-status { font-weight: bold; margin-bottom: 0.3rem; }
  .event-location { font-size: 0.95rem; color: #374151; }
  .event-date { font-size: 0.85rem; color: #6b7280; }

  footer {
    padding: 2rem;
    text-align: center;
    background: #1d0437ff;
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  .back-link {
  font-size: 0.95rem;
  color: #2563eb;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}
.back-link:hover {
  color: #1e40af;
  text-decoration: underline;
}

  @media (max-width: 768px) {
    .content {
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }
    .delivery-box { height: auto; }
  }
</style>
</head>
<body>

@include('partials.header')

<div class="container">
  <div class="tracking-header">
    <div style="display: flex; align-items: center; gap: 0.5rem;">
      <div class="tracking-number" id="tracking-number">Loading...</div>
      <button class="copy-btn" onclick="copyTracking()">Copy</button>
    </div>
      <a href="/" class="back-link">← Return</a>
  </div>

  <div id="no-data-message" class="no-data-message" style="display: none;">
    No tracking data available. Please check the tracking number.
  </div>

  <div class="content">
    <div class="delivery-box" id="delivery-info">
      <div class="delivery-label">Expected Delivery on</div>
      <div class="delivery-day" id="delivery-day"></div>
      <div class="delivery-date" id="delivery-date"></div>
      <div class="delivery-month" id="delivery-month"></div>
      <div class="delivery-time" id="delivery-time"></div>
      <div class="status-summary" id="summary">Latest status loading...</div>
    </div>

    <div class="timeline-container" id="timeline">
      <h2>Tracking History</h2>
      <ul class="timeline" id="events"></ul>
    </div>
  </div>
</div>

<script>
// Main fetch function
async function fetchTrackingData() {
  const urlParams = new URLSearchParams(window.location.search);
  const trackingId = (urlParams.get('tracking_id') || '').replace(/\s+/g, '').trim();
  if (!trackingId) return;

  try {
    const res = await fetch(`/api/trackings/${trackingId}`);
    const data = await res.json();
    if (!res.ok || !data.tracking_number) throw new Error('Tracking data not found');

    document.getElementById('tracking-number').textContent = data.tracking_number || trackingId;
    setDeliveryColors(data.status);
    document.querySelector('.delivery-label').textContent =
      (data.status || '').toLowerCase() === 'delivered' ? 'Delivered On' : 'Expected Delivery on';

    const eta = new Date(data.expected_delivery_date);
    if (!isNaN(eta)) {
      document.getElementById('delivery-day').textContent = eta.toLocaleDateString('en-US', { weekday: 'long' }).toUpperCase();
      document.getElementById('delivery-date').textContent = eta.getDate();
      document.getElementById('delivery-month').textContent = eta.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
      document.getElementById('delivery-time').textContent = "by 10:00 PM";
    }

    const eventsList = document.getElementById('events');
    eventsList.innerHTML = '';
    const fragment = document.createDocumentFragment();

    const liFirst = document.createElement('li');
    liFirst.innerHTML = `<div class="event-status" style="color:#2563eb">Label Created</div>
                         <div class="event-location">${data.origin?.city || ''}, ${data.origin?.state || ''}</div>
                         <div class="event-date">Pending shipment</div>`;
    fragment.appendChild(liFirst);

    if (data.events && data.events.length > 0) {
      data.events.forEach(event => {
        const li = document.createElement('li');
        li.innerHTML = `<div class="event-status" style="color:${getEventColor(event.status)}">${formatStatus(event.status)}</div>
                        <div class="event-location">${event.location || ''}</div>
                        <div class="event-date">${formatDateTimeDisplay(event.date)}</div>`;
        fragment.appendChild(li);
      });

      const latest = data.events[0];
      document.getElementById('summary').textContent =
        `Pick up from ${latest.location} at ${formatDateTimeDisplay(latest.date)}`;
    } else {
      document.getElementById('summary').textContent = 'Latest status loading...';
    }

    eventsList.appendChild(fragment);
    document.getElementById('delivery-info').classList.add('visible');
    document.getElementById('timeline').classList.add('visible');
    document.getElementById('no-data-message').style.display = 'none';

  } catch (error) {
    document.getElementById('tracking-number').textContent = 'No Tracking Data Found';
    document.getElementById('no-data-message').style.display = 'block';
    document.getElementById('summary').textContent = '';
    document.getElementById('delivery-day').textContent = '';
    document.getElementById('delivery-date').textContent = '';
    document.getElementById('delivery-month').textContent = '';
    document.getElementById('delivery-time').textContent = '';
    document.getElementById('events').innerHTML = '';
    document.getElementById('delivery-info').classList.remove('visible');
    document.getElementById('timeline').classList.remove('visible');
  }
}

// Helpers
function formatDateTimeDisplay(dateTime) {
  const d = new Date(dateTime.replace(' ', 'T'));
  if (isNaN(d)) return dateTime;
  return d.toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric',
    hour: 'numeric', minute: '2-digit', hour12: true });
}
function setDeliveryColors(status) {
  const box = document.querySelector('.delivery-box');
  switch ((status || '').toLowerCase()) {
    case 'in_transit': box.style.backgroundColor = '#e0f2fe'; break;
    case 'delayed': box.style.backgroundColor = '#fee2e2'; break;
    case 'delivered': box.style.backgroundColor = '#d1fae5'; break;
    default: box.style.backgroundColor = '#f3f4f6';
  }
}
function formatStatus(status) {
  return status ? status.split('_').map(w => w[0].toUpperCase() + w.slice(1)).join(' ') : '';
}
function getEventColor(status) {
  switch ((status || '').toLowerCase()) {
    case 'label_created': return '#2563eb';
    case 'in_transit': return '#2563eb';
    case 'arrived_at_facility': return '#f97316';
    case 'delivered': return '#059669';
    case 'delayed': return '#dc2626';
    default: return '#6b7280';
  }
}
function copyTracking() {
  const txt = document.getElementById('tracking-number').textContent;
  navigator.clipboard.writeText(txt);
  alert("Tracking number copied!");
}

// Run on page load
document.addEventListener("DOMContentLoaded", fetchTrackingData);
</script>

@include('partials.footer')

</body>
</html>
