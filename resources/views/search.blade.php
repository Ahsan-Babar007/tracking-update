<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Track Your Package</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        background: linear-gradient(135deg, #eef2ff, #dbeafe);
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

    main {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        text-align: center;
        background: #f0f4ff;
        min-height: 70vh;
    }

    .tracking-form {
        width: 100%;
        max-width: 600px;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    .tracking-form input[type="text"] {
        padding: 1rem 1.2rem;
        font-size: 1.2rem;
        border: 2px solid #cbd5e1;
        border-radius: 12px;
        transition: all 0.3s;
        outline: none;
    }
    .tracking-form input[type="text"]:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2);
    }

    .tracking-form button {
        padding: 1rem;
        font-size: 1.2rem;
        border-radius: 12px;
        border: none;
        background: #2563eb;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
    }
    .tracking-form button:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
    }

    footer {
        padding: 2rem;
        text-align: center;
        background: #1d0437ff;
        color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    @media (max-width: 500px) {
        header { flex-direction: column; gap: 1rem; }
        header h1 { font-size: 1.8rem; }
        nav a { margin-left: 0; margin-right: 1rem; }
        .tracking-form input[type="text"], .tracking-form button { font-size: 1rem; padding: 0.8rem; }
    }
</style>
</head>
<body>

@include('partials.header')

<main>
    <!-- Shipping Logo -->
    <div style="margin-bottom: 1rem;">
       <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="30" y="30" width="40" height="40" rx="5" fill="#F7C948"/>
            <path d="M30 50 H70 M50 30 V70" stroke="#333333" stroke-width="3"/>
            <path d="M50 20 C50 10 60 0 60 0 C60 0 70 10 70 20 C70 30 60 40 60 50 L60 60" fill="#D81E1E"/>
            <circle cx="60" cy="20" r="5" fill="#FFFFFF"/>
            <path d="M20 80 Q30 70 40 80 T60 80 T80 80" stroke="#4A90E2" stroke-width="3" stroke-dasharray="5,5"/>
        </svg>
    </div>

    <h2 style="font-size: 2rem; color: #2563eb; margin-bottom: 1rem;">Track Your Packages</h2>
    <p style="font-size: 1.1rem; color: #4b5563; margin-bottom: 2rem; max-width: 500px;">
        Enter your tracking number below to see the latest status of your shipment instantly.
    </p>

    <form class="tracking-form" action="/tracking" method="get" style="display: flex; flex-direction: row; width: 100%; max-width: 500px; gap: 0.5rem;">
        <input type="text" id="tracking_id" name="tracking_id" placeholder="Enter your tracking number" required>
        <button type="submit">Track Package</button>
    </form>
</main>

@include('partials.footer')

</body>
</html>
