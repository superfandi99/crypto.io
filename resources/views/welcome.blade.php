<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Kriptografi AES</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-50 text-gray-900">

    <x-navbar></x-navbar>
    <x-hero></x-hero>
    <x-about></x-about>

    <section class="py-16 px-4 lg:px-20 max-w-7xl mx-auto grid lg:grid-cols-2 gap-12">
        <x-encrypt></x-encrypt>
        <x-decrypt></x-decrypt>
    </section>

    <x-faq></x-faq>

    <x-footer></x-footer>
</body>

</html>
