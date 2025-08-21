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
  @media (max-width: 600px) {
    header { flex-direction: column; gap: 1rem; }
    nav a { margin-left: 0; margin-right: 1rem; }
  }

  .container {
    max-width: 1000px;
    margin: 2rem auto;
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    flex: 1;
  }

  /* Tracking header */
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

  /* Two column layout - FIXED: Equal height columns */
  .content { 
    display: grid; 
    grid-template-columns: 1fr 2fr; 
    gap: 2rem;
    align-items: stretch; /* This makes both columns equal height */
  }

  /* Delivery box - FIXED: Full height with flex layout */
  .delivery-box {
    background: #f3f4f6;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: flex-start; /* Align content to top */
    height: 100%; /* Take full available height */
  }
  
  .delivery-content {
    flex-shrink: 0; /* Don't shrink the main delivery info */
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
    flex-grow: 1; /* This makes the summary area expand to fill remaining space */
    display: flex;
    align-items: flex-start; /* Align text to top of expanded area */
  }

  /* Timeline container */
  .timeline-container {
    display: flex;
    flex-direction: column;
    height: 100%; /* Take full available height */
  }

  /* Timeline */
  h2 { margin: 0 0 1rem; font-size: 1.2rem; color: #111827; }
  .timeline { 
    list-style: none; 
    margin: 0; 
    padding: 0; 
    border-left: 3px solid #2563eb;
    flex-grow: 1; /* Allow timeline to grow */
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

  /* Responsive design */
  @media (max-width: 768px) {
    .content {
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }
    
    .delivery-box {
      height: auto; /* On mobile, don't force height matching */
    }
  }
</style>
</head>
<body>

@include('partials.header')

<div class="container">
<!-- Tracking header -->
<div class="tracking-header">
  <div style="display: flex; align-items: center; gap: 0.5rem;">
    <div class="tracking-number" id="tracking-number">Loading...</div>
    <!-- Copy icon -->
    <svg onclick="copyTracking()" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#2563eb" viewBox="0 0 24 24" style="cursor:pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">
      <path d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 18H8V7h11v16z"/>
    </svg>
  </div>
</div>

  <!-- Main content -->
  <div class="content">
    <!-- Left: Delivery info -->
    <div class="delivery-box">
      <div class="delivery-content">
        <div class="delivery-label">Expected Delivery on</div>
        <div class="delivery-day" id="delivery-day"></div>
        <div class="delivery-date" id="delivery-date"></div>
        <div class="delivery-month" id="delivery-month"></div>
        <div class="delivery-time" id="delivery-time"></div>
      </div>
      <div class="status-summary" id="summary">Latest status loading...</div>
    </div>

    <!-- Right: Timeline -->
    <div class="timeline-container">
      <h2>Tracking History</h2>
      <ul class="timeline" id="events"></ul>
    </div>
  </div>
</div>

<script>
const urlParams = new URLSearchParams(window.location.search);
const trackingId = urlParams.get('tracking_id');

const deliveryBox = document.querySelector('.delivery-box');
const deliveryLabel = document.querySelector('.delivery-label');
const trackingNumberEl = document.getElementById('tracking-number');
const deliveryDayEl = document.getElementById('delivery-day');
const deliveryDateEl = document.getElementById('delivery-date');
const deliveryMonthEl = document.getElementById('delivery-month');
const deliveryTimeEl = document.getElementById('delivery-time');
const summaryEl = document.getElementById('summary');
const eventsList = document.getElementById('events');

function copyTracking() {
  navigator.clipboard.writeText(trackingNumberEl.textContent);
}

function formatDateTimeDisplay(dateTime) {
  const d = new Date(dateTime.replace(' ', 'T'));
  if (isNaN(d)) return dateTime;
  return d.toLocaleString('en-US', {
    year: 'numeric', month: 'long', day: 'numeric',
    hour: 'numeric', minute: '2-digit', hour12: true
  });
}

function setDeliveryColors(status) {
  switch ((status || '').toLowerCase()) {
      case 'in_transit':
          deliveryBox.style.backgroundColor = '#e0f2fe';
          deliveryLabel.style.color = '#2563eb';
          break;
      case 'delayed':
          deliveryBox.style.backgroundColor = '#fee2e2';
          deliveryLabel.style.color = '#dc2626';
          break;
      case 'delivered':
          deliveryBox.style.backgroundColor = '#d1fae5';
          deliveryLabel.style.color = '#047857';
          break;
      default:
          deliveryBox.style.backgroundColor = '#f3f4f6';
          deliveryLabel.style.color = '#6b7280';
  }
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

function formatStatus(status) {
  return status ? status.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : '';
}

if (trackingId) {
  fetch(`/api/trackings/${trackingId}`)
    .then(res => { if (!res.ok) throw new Error('Not found'); return res.json(); })
    .then(data => {
      // Set tracking number
      trackingNumberEl.textContent = data.tracking_number || trackingId;

      // Delivery box color & label
      setDeliveryColors(data.status);
      deliveryLabel.textContent = (data.status || '').toLowerCase() === 'delivered' ? 'Delivered On' : 'Expected Delivery on';

      // Expected delivery info
      const eta = new Date(data.expected_delivery_date);
      if (!isNaN(eta)) {
          deliveryDayEl.textContent = eta.toLocaleDateString('en-US', { weekday: 'long' }).toUpperCase();
          deliveryDateEl.textContent = eta.getDate();
          deliveryMonthEl.textContent = eta.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
          deliveryTimeEl.textContent = "by 10:00 PM";
      }

      // Timeline events
      eventsList.innerHTML = '';
      const fragment = document.createDocumentFragment();

      // Always show Label Created first
      const liFirst = document.createElement('li');
      liFirst.innerHTML = `<div class="event-status" style="color:#2563eb">Label Created</div>
                           <div class="event-location">${data.origin.city}, ${data.origin.state}</div>
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
        summaryEl.textContent = `Pick up from ${latest.location} at ${formatDateTimeDisplay(latest.date)}`;
      } else {
        summaryEl.textContent = 'Latest status loading...';
      }

      eventsList.appendChild(fragment);
    })
    .catch(() => {
      trackingNumberEl.textContent = 'Invalid Tracking Number';
      deliveryDayEl.textContent = '';
      deliveryDateEl.textContent = '';
      deliveryMonthEl.textContent = '';
      deliveryTimeEl.textContent = '';
      summaryEl.textContent = '';
      eventsList.innerHTML = '';
    });
} else {
  trackingNumberEl.textContent = 'No Tracking ID Provided';
}
</script>

@include('partials.footer')

</body>
</html>