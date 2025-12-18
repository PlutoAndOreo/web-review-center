<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page not found • {{ config('app.name') }}</title>
    <style>
        :root {
            --bg: #0f172a;
            /* slate-900 */
            --card: #111827;
            /* gray-900 */
            --text: #e5e7eb;
            /* gray-200 */
            --accent: #38bdf8;
            /* sky-400 */
            --accent-2: #a78bfa;
            /* violet-400 */
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: radial-gradient(1200px circle at 10% 10%, rgba(56, 189, 248, .12), transparent 40%),
                radial-gradient(1000px circle at 90% 80%, rgba(167, 139, 250, .12), transparent 35%),
                var(--bg);
            font-family: 'Figtree', system-ui, sans-serif;
            color: var(--text);
        }

        .container {
            max-width: 720px;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .logo {
            width: 64px;
            height: 64px;
            margin: 0 auto 0.75rem;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            box-shadow: 0 10px 30px rgba(56, 189, 248, .3), 0 10px 30px rgba(167, 139, 250, .3);
        }

        h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            margin: 0.5rem 0 0.75rem;
            letter-spacing: .2px;
            font-weight: 700;
        }

        p.lead {
            font-size: 1.05rem;
            opacity: .9;
            margin-bottom: 1.25rem
        }

        .card {
            display: inline-block;
            text-align: left;
            width: 100%;
            border-radius: 16px;
            background: rgba(17, 24, 39, .75);
            border: 1px solid rgba(148, 163, 184, .25);
            backdrop-filter: blur(4px);
        }

        .card .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .card .cell {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(148, 163, 184, .2)
        }

        .card .cell+.cell {
            border-left: 1px solid rgba(148, 163, 184, .2)
        }

        .actions {
            display: flex;
            gap: .75rem;
            justify-content: center;
            margin-top: 1.25rem
        }

        .btn {
            padding: .75rem 1rem;
            border-radius: 12px;
            border: 1px solid transparent;
            color: #0b1020;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            gap: .5rem;
            align-items: center;
            box-shadow: 0 10px 20px rgba(56, 189, 248, .25), 0 10px 20px rgba(167, 139, 250, .25);
        }

        .btn.secondary {
            background: transparent;
            color: var(--text);
            border-color: rgba(148, 163, 184, .35);
        }

        .hint {
            font-size: .9rem;
            opacity: .75;
            margin-top: .75rem
        }

        .code-pill {
            display: inline-block;
            padding: .35rem .6rem;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, .35);
            font-weight: 700;
            font-size: .9rem;
            background: rgba(2, 6, 23, .6);
            margin-bottom: .5rem;
        }

        <blade media|%20(max-width%3A%20640px)%20%7B>.card .row {
            grid-template-columns: 1fr
        }

        .card .cell+.cell {
            border-left: none
        }
        }

    </style>
</head>

<body>
    <main class="container" role="main" aria-labelledby="title">
        <div class="logo" aria-hidden="true"></div>
        <span class="code-pill">404</span>
        <h1 id="title">Oops—this page couldn’t be found.</h1>
        <p class="lead">The link might be broken or the page may have been removed.</p>

        <div class="card" role="group" aria-label="Helpful info">
            <div class="row">
                <div class="cell">
                    <strong>Requested URL</strong><br>
                    <code>{{ request()->fullUrl() }}</code>
                </div>
                <div class="cell">
                    <strong>Time</strong><br>
                    <code>{{ now()->format('M d, Y H:i:s') }}</code>
                </div>
            </div>
            <div class="row">
            </div>
        </div>

        <div class="actions">
            {{ url()->previous() }}← Go back</a>
            mailto:support@example.com?subject=Broken%20link&body=I%20found%20a%20404%20at%20{{ urlencode(request()->fullUrl()) }}Report
            issue</a>
        </div>
        <p class="hint">If you think this was a mistake, try refreshing the page or clear your cache.</p>
    </main>
</body>
