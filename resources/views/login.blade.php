<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in | CDO Portal</title>
    <style>
        :root {
            color-scheme: light;
            --navy: #172b3a;
            --navy-deep: #10212d;
            --red: #d51f36;
            --gold: #f2b84b;
            --ink: #17212b;
            --muted: #66717d;
            --line: #dde3e8;
            --canvas: #edf1f3;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 28px;
            color: var(--ink);
            background:
                radial-gradient(circle at 12% 18%, rgb(255 255 255 / 90%) 0 5%, transparent 30%),
                linear-gradient(135deg, #f5f7f8 0%, var(--canvas) 100%);
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }

        .portal {
            width: min(1040px, 100%);
            min-height: 620px;
            display: grid;
            grid-template-columns: 43% 57%;
            overflow: hidden;
            border: 1px solid rgb(255 255 255 / 85%);
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 24px 70px rgb(23 43 58 / 16%);
        }

        .welcome {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            padding: 48px;
            color: #fff;
            background:
                linear-gradient(145deg, rgb(23 43 58 / 96%), rgb(16 33 45 / 98%)),
                var(--navy);
        }

        .welcome::before {
            position: absolute;
            right: -205px;
            bottom: -215px;
            width: 520px;
            height: 520px;
            content: "";
            border: 72px solid rgb(242 184 75 / 8%);
            border-radius: 50%;
        }

        .welcome::after {
            position: absolute;
            top: 34%;
            right: -46px;
            width: 190px;
            height: 190px;
            content: "";
            border: 1px solid rgb(255 255 255 / 9%);
            border-radius: 50%;
        }

        .welcome-top,
        .welcome-copy {
            position: relative;
            z-index: 1;
        }

        .welcome-logo {
            width: 132px;
            height: 132px;
            display: grid;
            place-items: center;
            border: 1px solid rgb(255 255 255 / 12%);
            border-radius: 28px;
            background: rgb(255 255 255 / 95%);
            box-shadow: 0 16px 32px rgb(0 0 0 / 14%);
        }

        .welcome-logo img {
            width: 112px;
            height: 112px;
            display: block;
            border-radius: 18px;
            object-fit: cover;
        }

        .eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 16px;
            color: #d9e2e7;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            width: 28px;
            height: 3px;
            content: "";
            border-radius: 999px;
            background: var(--gold);
        }

        .welcome h1 {
            max-width: 350px;
            margin: 0;
            font-size: clamp(38px, 5vw, 58px);
            line-height: .98;
            letter-spacing: -.055em;
        }

        .welcome-copy p:last-child {
            max-width: 330px;
            margin: 18px 0 0;
            color: #bdcbd3;
            font-size: 15px;
            line-height: 1.65;
        }

        .login-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px clamp(36px, 7vw, 82px);
        }

        .login-content {
            width: min(100%, 390px);
        }

        .login-heading {
            margin: 0 0 34px;
        }

        .login-heading h2 {
            margin: 0 0 8px;
            font-size: clamp(30px, 4vw, 38px);
            line-height: 1.1;
            letter-spacing: -.045em;
        }

        .login-heading p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
        }

        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            color: #303b46;
            font-size: 13px;
            font-weight: 750;
        }

        .field input {
            width: 100%;
            height: 52px;
            padding: 0 15px;
            border: 1px solid var(--line);
            border-radius: 9px;
            outline: none;
            color: var(--ink);
            background: #fbfcfd;
            font: inherit;
            font-size: 14px;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }

        .field input::placeholder {
            color: #a1aab3;
        }

        .field input:focus {
            border-color: #758c9d;
            background: #fff;
            box-shadow: 0 0 0 4px rgb(23 43 58 / 8%);
        }

        .field input.is-invalid {
            border-color: #c53030;
            background: #fffafa;
        }

        .field-error {
            margin: 6px 0 0;
            color: #b42318;
            font-size: 12px;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap input {
            padding-right: 64px;
        }

        .show-password {
            position: absolute;
            top: 50%;
            right: 7px;
            transform: translateY(-50%);
            border: 0;
            border-radius: 7px;
            padding: 8px 9px;
            color: #526575;
            background: transparent;
            font: inherit;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
        }

        .show-password:hover {
            background: #edf1f3;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin: 6px 0 24px;
            font-size: 12px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
        }

        .remember input {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: var(--navy);
        }

        .forgot {
            color: #34546b;
            font-weight: 800;
            text-decoration: none;
        }

        .forgot:hover {
            color: var(--red);
        }

        .sign-in {
            width: 100%;
            min-height: 52px;
            border: 0;
            border-radius: 9px;
            color: #fff;
            background: var(--navy);
            box-shadow: 0 10px 22px rgb(23 43 58 / 16%);
            font: inherit;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: background .15s, transform .15s, box-shadow .15s;
        }

        .sign-in:hover {
            background: var(--navy-deep);
            box-shadow: 0 12px 26px rgb(23 43 58 / 22%);
        }

        .sign-in:active {
            transform: translateY(1px);
        }

        .show-password:focus-visible,
        .forgot:focus-visible,
        .sign-in:focus-visible {
            outline: 3px solid rgb(242 184 75 / 65%);
            outline-offset: 3px;
        }

        @media (max-width: 760px) {
            body {
                min-height: auto;
                padding: 14px;
            }

            .portal {
                max-width: 480px;
                min-height: 0;
                grid-template-columns: 1fr;
                border-radius: 18px;
            }

            .welcome {
                min-height: 210px;
                padding: 28px;
            }

            .welcome-top {
                display: flex;
                justify-content: flex-start;
            }

            .welcome-logo {
                width: 68px;
                height: 68px;
                border-radius: 16px;
            }

            .welcome-logo img {
                width: 58px;
                height: 58px;
                border-radius: 12px;
            }

            .welcome h1 {
                max-width: 300px;
                margin-top: 22px;
                font-size: 34px;
            }

            .welcome-copy p:last-child {
                display: none;
            }

            .login-panel {
                padding: 34px 24px 38px;
            }

            .login-heading {
                margin-bottom: 28px;
            }
        }

        @media (max-width: 420px) {
            .form-options {
                align-items: flex-start;
            }

            .login-panel {
                padding-inline: 20px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
    <main class="portal">
        <aside class="welcome" aria-label="CDO Portal introduction">
            <div class="welcome-top">
                <div class="welcome-logo">
                    <img src="{{ asset('cdo-logo.png') }}" alt="CDO logo">
                </div>
            </div>

            <div class="welcome-copy">
                <p class="eyebrow">CDO Portal</p>
                <p>Sign in to access your records, updates, and staff tools in one secure workspace.</p>
            </div>
        </aside>

        <section class="login-panel" aria-labelledby="login-title">
            <div class="login-content">
                <header class="login-heading">
                    <h2 id="login-title">Welcome back</h2>
                    <p>Enter your account details to continue.</p>
                </header>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="field">
                        <label for="email">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="name@example.com"
                            autocomplete="username"
                            required
                            autofocus
                            class="@error('email') is-invalid @enderror"
                            aria-invalid="@error('email') true @else false @enderror"
                        >
                        @error('email')
                            <p class="field-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="password-wrap">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                                class="@error('password') is-invalid @enderror"
                                aria-invalid="@error('password') true @else false @enderror"
                            >
                            <button
                                class="show-password"
                                type="button"
                                id="toggle-password"
                                aria-controls="password"
                                aria-pressed="false"
                            >
                                Show
                            </button>
                        </div>
                        @error('password')
                            <p class="field-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-options">
                        <label class="remember" for="remember">
                            <input type="checkbox" id="remember" name="remember" value="1">
                            <span>Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="forgot" href="{{ route('password.request') }}">Forgot password?</a>
                        @endif
                    </div>

                    <button class="sign-in" type="submit">Sign in</button>
                </form>
            </div>
        </section>
    </main>

    <script>
        const password = document.getElementById('password');
        const toggle = document.getElementById('toggle-password');

        toggle.addEventListener('click', () => {
            const show = password.type === 'password';
            password.type = show ? 'text' : 'password';
            toggle.textContent = show ? 'Hide' : 'Show';
            toggle.setAttribute('aria-pressed', String(show));
        });
    </script>
</body>
</html>
