@extends('layouts.app')

@section('title', 'Track Your Package')

@section('styles')
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
    display: none;
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
    margin: 0; 
    padding: 0; 
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

  @media (max-width: 768px) {
    .content {
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }
    .delivery-box {
      height: auto;
    }
  }
</style>
@endsection

@section('content')
<div class="container">
  <div class="tracking-header">
    <div style="display: flex; align-items: center; gap: 0.5rem;">
      <div class="tracking-number" id="tracking-number">Loading...</div>
      <button class="copy-btn" onclick="copyTracking()">Copy</button>
    </div>
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
@endsection

@section('scripts')
<script>
async function fetchTrackingData() {
  const params = new URLSearchParams(window.location.search);
  const id = (params.get('tracking_id')||'').trim();
  if (!id) return;

  try {
    const res = await fetch(`/api/trackings/${id}`);
    const data = await res.json();
    if (!res.ok || !data.tracking_number) throw new Error();

    document.getElementById('tracking-number').textContent = data.tracking_number;
    const label = data.status.toLowerCase()==='delivered'?'Delivered On':'Expected Delivery on';
    document.querySelector('.delivery-label').textContent = label;
    setDeliveryColors(data.status);

    const eta = new Date(data.expected_delivery_date);
    if (!isNaN(eta)) {
      document.getElementById('delivery-day').textContent = eta.toLocaleDateString('en-US',{weekday:'long'}).toUpperCase();
      document.getElementById('delivery-date').textContent = eta.getDate();
      document.getElementById('delivery-month').textContent = eta.toLocaleDateString('en-US',{month:'long',year:'numeric'});
      document.getElementById('delivery-time').textContent = 'by 10:00 PM';
    }

    const events = data.events||[];
    const list = document.getElementById('events');
    list.innerHTML = '';
    const frag = document.createDocumentFragment();

    const first = document.createElement('li');
    first.innerHTML = `<div class="event-status" style="color:#2563eb">Label Created</div>
                       <div class="event-location">${data.origin.city}, ${data.origin.state}</div>
                       <div class="event-date">Pending shipment</div>`;
    frag.appendChild(first);

    if (events.length) {
      events.forEach(ev=>{
        const li = document.createElement('li');
        li.innerHTML = `<div class="event-status" style="color:${getEventColor(ev.status)}">${formatStatus(ev.status)}</div>
                        <div class="event-location">${ev.location||''}</div>
                        <div class="event-date">${formatDateTimeDisplay(ev.date)}</div>`;
        frag.appendChild(li);
      });
      const latest = events[0];
      document.getElementById('summary').textContent = `Pick up from ${latest.location} at ${formatDateTimeDisplay(latest.date)}`;
    }

    list.appendChild(frag);
    document.getElementById('delivery-info').style.display='block';
    document.getElementById('timeline').style.display='block';
  } catch {
    document.getElementById('tracking-number').textContent='Invalid Tracking Number';
    document.getElementById('no-data-message').style.display='block';
    document.getElementById('delivery-info').style.display='none';
    document.getElementById('timeline').style.display='none';
  }
}

function formatDateTimeDisplay(dt) {
  const d=new Date(dt.replace(' ', 'T'));
  return isNaN(d)?dt:d.toLocaleString('en-US',{
    year:'numeric',month:'long',day:'numeric',
    hour:'numeric',minute:'2-digit',hour12:true
  });
}

function setDeliveryColors(status) {
  const bx=document.querySelector('.delivery-box');
  const s=status.toLowerCase();
  bx.style.backgroundColor = s==='in_transit'? '#e0f2fe'
                            : s==='delayed'? '#fee2e2'
                            : s==='delivered'? '#d1fae5'
                            : '#f3f4f6';
}

function formatStatus(st) {
  return st.split('_').map(w=>w.charAt(0).toUpperCase()+w.slice(1)).join(' ');
}

function getEventColor(st) {
  const s=st.toLowerCase();
  return s==='arrived_at_facility'? '#f97316'
       : s==='delivered'? '#059669'
       : s==='delayed'? '#dc2626'
       : '#2563eb';
}

function copyTracking() {
  navigator.clipboard.writeText(document.getElementById('tracking-number').textContent);
}

fetchTrackingData();
</script>
@endsection
