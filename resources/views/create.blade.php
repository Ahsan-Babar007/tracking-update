<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Tracking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    body {
      font-family: system-ui, sans-serif;
      background: #f9fafb;
      margin: 0;
      padding: 2rem;
      color: #111827;
    }
    h1 {
      text-align: center;
      margin-bottom: 2rem;
      color: #2563eb;
    }
    form {
      background: white;
      max-width: 600px;
      margin: auto;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    label {
      display: block;
      margin: 12px 0 6px;
      font-weight: 600;
      color: #374151;
    }
    input, select {
      width: 100%;
      padding: 10px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      margin-bottom: 10px;
      font-size: 1rem;
    }
    input:focus, select:focus {
      border-color: #2563eb;
      outline: none;
      box-shadow: 0 0 0 2px rgba(37,99,235,0.2);
    }
    button {
      width: 100%;
      background: #2563eb;
      color: white;
      border: none;
      padding: 12px;
      font-size: 1rem;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 1rem;
      transition: background 0.2s;
    }
    button:hover {
      background: #1d4ed8;
    }
    .error {
      color: #b91c1c;
      margin-top: 10px;
    }
    .success {
      color: #059669;
      margin-top: 10px;
    }
    .required::after {
      content: " *";
      color: #dc2626;
    }
  </style>
</head>
<body>
  <h1>Create New Tracking</h1>

  <form id="createTrackingForm">
    <label for="carrier" class="required">Carrier</label>
    <select id="carrier" name="carrier" required>
      <option value="UPS">UPS</option>
      <option value="USPS">USPS</option>
      <option value="Canada Post">Canada Post</option>
    </select>

    <label for="country_type" class="required">Country</label>
    <select id="country_type" name="country_type" required>
      <option value="1">USA</option>
      <option value="2">Canada</option>
    </select>

    <label for="origin_state" class="required">Origin State/Province</label>
    <input type="text" id="origin_state" name="origin_state" required placeholder="e.g., TX or ON" maxlength="2">

    <label for="origin_city">Origin City</label>
    <input type="text" id="origin_city" name="origin_city" placeholder="e.g., Dallas">

    <label for="origin_zip">Origin ZIP/Postal Code</label>
    <input type="text" id="origin_zip" name="origin_zip" placeholder="e.g., 75201">

    <label for="destination_state" class="required">Destination State/Province</label>
    <input type="text" id="destination_state" name="destination_state" required placeholder="e.g., OH or QC" maxlength="2">

    <label for="destination_city">Destination City</label>
    <input type="text" id="destination_city" name="destination_city" placeholder="e.g., Columbus">

    <label for="destination_zip">Destination ZIP/Postal Code</label>
    <input type="text" id="destination_zip" name="destination_zip" placeholder="e.g., 43215">

    <label for="start_date" class="required">Start Date</label>
    <input type="date" id="start_date" name="start_date" required>

    <label for="expected_delivery_date" class="required">Expected Delivery Date</label>
    <input type="date" id="expected_delivery_date" name="expected_delivery_date" required>

    <button type="submit">Create Tracking</button>
    <p id="error" class="error"></p>
    <p id="success" class="success"></p>
  </form>

  <script>
    document.getElementById('createTrackingForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const errorEl = document.getElementById('error');
      const successEl = document.getElementById('success');
      errorEl.textContent = '';
      successEl.textContent = '';

      const formData = new FormData(e.target);
      const data = Object.fromEntries(formData);

      try {
        const response = await fetch('/api/trackings', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
          throw new Error(result.message || 'Failed to create tracking');
        }

        successEl.textContent = '✅ Tracking created successfully! Redirecting...';
        setTimeout(() => {
          window.location.href = `/tracking?tracking_id=${result.tracking_number}`;
        }, 1500);
      } catch (error) {
        errorEl.textContent = error.message;
      }
    });
  </script>
</body>
</html>
