<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

@include('partials.header')

<main>
    <h2>Contact Us</h2>
    <p>If you have any questions or need assistance with tracking your packages, feel </p>
    <p>free to reach out using the form below.</p>

    <form action="/submit-contact" method="post">
        @csrf
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
        <button type="submit">Send Message</button>
    </form>
</main>

@include('partials.footer')

</body>
</html>
