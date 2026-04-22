<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' />

<meta name="description" content="@yield('meta.description', 'Aplikácia dostupná pre viacerých použivateľov, možnosť spravovať obchody, zamestnancov, produkty alebo aj ceny.')">
<meta name="keywords" content="@yield('meta.keywords', 'multi-tenancy, cena, produkty, balíky, množstva, zľavy, obchody, api, obrázky')">

<meta property="og:locale" content="sk_SK" />
<meta property="og:type" content="website" />
<meta property="og:title" content="@yield('og.title', 'Aplikácia pre správu cien.')" />
<meta property="og:description" content="@yield('og.description', 'Aplikácia dostupná pre viacerých použivateľov, možnosť spravovať obchody, zamestnancov, produkty alebo aj ceny.')" />
<meta property="og:url" content="@yield('og.url', config('app.name'))" />
<meta property="og:site_name" content="STORE APP" />

<meta name="author" content="Tomáš Kozelek">
