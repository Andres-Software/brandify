<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
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
            align-items: center;
            justify-content: center;
            background-color: #020617; /* slate-950 */
            color: #e2e8f0; /* slate-200 */
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .card {
            text-align: center;
            padding: 2.5rem;
        }

        .card img {
            height: 3rem;
            width: auto;
            margin-bottom: 1.5rem;
        }

        .card p {
            color: #94a3b8; /* slate-400 */
            margin: 0;
        }

        .card a {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 0.375rem;
            background-color: #10b981; /* emerald-500 */
            color: #022c22; /* emerald-950 */
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}">
        <p>Gerencie e compartilhe suas propostas.</p>
        <a href="{{ url('/admin') }}">Acessar o painel</a>
    </div>
</body>
</html>
