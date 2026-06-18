<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Laravel 12 Блог с нуля')</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .accent{background-image:linear-gradient(135deg,#22d3ee,#a78bfa,#f472b6)}
        .glass{background:rgba(17,24,39,.7);backdrop-filter:blur(12px)}
        .btn{ @apply inline-flex items-center justify-center rounded-xl px-3 py-2 text-sm font-medium }
        .btn-primary{ @apply text-gray-900; background:linear-gradient(135deg,#22d3ee,#a78bfa,#f472b6) }
        .btn-outline{ @apply border border-white/15 text-gray-200 hover:border-white/30 }
        .input{ @apply w-full rounded-xl bg-white/5 border border-white/10 px-3 py-2 text-sm outline-none focus:border-white/30 placeholder:text-gray-400 }
        .badge{ @apply inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs border border-white/15 text-gray-300 }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen">
<header class="border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button id="asideBtn" class="md:hidden btn btn-outline" aria-label="Меню">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            </button>
            <a href="#" class="flex items-center gap-2">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl accent text-gray-900 font-black">A</span>
                <span class="font-semibold tracking-wide">Admin</span>
            </a>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden sm:inline text-sm text-gray-400">Привет, Admin</span>
            <button class="btn btn-outline">Выйти</button>
        </div>
    </div>
</header>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-1 md:grid-cols-12 gap-6">
    <aside id="aside" class="md:col-span-3 lg:col-span-2 glass rounded-2xl p-3 border border-white/10 md:block hidden">
        <nav class="space-y-1">
            <a href="posts-list.html" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-white bg-white/5">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Посты
            </a>
            <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M5 12l7 7 0 117 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Категории
            </a>
        </nav>
    </aside>

    <main class="md:col-span-9 lg:col-span-10 space-y-6">
        @yield('content')
    </main>
</div>
</body>
</html>
