<!DOCTYPE html>
<html lang="sk" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Chyba!')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-950 text-white h-full overflow-hidden relative selection:bg-indigo-500 selection:text-white">

<div class="min-h-screen flex items-center justify-center p-4">

    <div class="glass max-w-2xl w-full rounded-2xl p-8 md:p-14 text-center shadow-2xl relative overflow-hidden group">
        {{ $slot }}
    </div>
</div>

</body>
</html>
