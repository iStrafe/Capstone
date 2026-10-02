{{-- Error pages (404, 403, 419...). Plain Blade with inline styles, because an error can happen
     before the React app, the session or the shared page data are available. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trim($__env->yieldContent('title')) }} · AduCats</title>
    <link rel="icon" href="/favicon.ico">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px;
               background: #f4f8fd; color: #0d1b2e; font: 17px/1.6 Figtree, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif; overflow-wrap: anywhere; }
        main { width: 100%; max-width: 520px; background: #fff; border: 1px solid #e3eaf3; border-radius: 28px; padding: 40px 32px; text-align: center; }
        .code { display: inline-block; padding: 4px 14px; border-radius: 999px; background: #e6f2ff; color: #0a4f99; font-weight: 700; font-size: 14px; letter-spacing: .04em; }
        h1 { margin: 16px 0 8px; font: 600 34px/1.15 Fraunces, Georgia, serif; }
        p { margin: 0 0 28px; color: #44506a; }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; }
        a { display: inline-flex; align-items: center; height: 48px; padding: 0 22px; border-radius: 999px; font-weight: 600; text-decoration: none; }
        .primary { background: #006bd6; color: #fff; }
        .primary:hover { background: #0a4f99; }
        .secondary { border: 1.5px solid #c9d5e4; color: #0d1b2e; }
        .secondary:hover { border-color: #1a8cff; }
        a:focus-visible { outline: 3px solid #1a8cff; outline-offset: 2px; }
    </style>
</head>
<body>
    <main>
        <span class="code">Error @yield('code')</span>
        <h1>@yield('heading')</h1>
        <p>@yield('message')</p>
        <div class="actions">
            <a class="primary" href="{{ url('/') }}">Back to home</a>
            <a class="secondary" href="{{ url('/adoptCat') }}">See cats for adoption</a>
        </div>
    </main>
</body>
</html>
