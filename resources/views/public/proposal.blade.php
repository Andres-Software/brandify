<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $proposal->description ?? $settings->company_name }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #020617; /* slate-950 */
            color: #e2e8f0; /* slate-200 */
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .topbar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            background-color: #0f172a; /* slate-900 */
            border-bottom: 1px solid {{ \App\Support\BarColor::hex($settings->bar_color) }};
            flex-shrink: 0;
        }

        .topbar img {
            height: 2rem;
            width: auto;
            display: block;
        }

        .topbar .company-name {
            font-size: 1rem;
            font-weight: 600;
            color: #f8fafc; /* slate-50 */
            letter-spacing: 0.01em;
        }

        .topbar .accent-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            background-color: {{ \App\Support\BarColor::hex($settings->bar_color) }};
            flex-shrink: 0;
        }

        main {
            flex: 1;
            display: flex;
            min-height: 0;
        }

        main iframe {
            flex: 1;
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
</head>
<body>
    @if ($settings->show_topbar)
        <header class="topbar">
            @if ($settings->logo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($settings->logo_path) }}" alt="{{ $settings->company_name }}">
            @else
                <span class="accent-dot"></span>
            @endif
            @if (filled($settings->company_name))
                <span class="company-name">{{ $settings->company_name }}</span>
            @endif
        </header>
    @endif

    <main>
        <iframe
            src="{{ $proposal->embed_src }}"
            title="{{ $proposal->description ?? $settings->company_name ?? '' }}"
            allow="fullscreen"
        ></iframe>
    </main>
</body>
</html>
