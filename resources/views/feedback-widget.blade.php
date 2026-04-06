<!DOCTYPE html>
<html lang="uk" class="{{ $embedded ? 'is-embedded' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Зворотний зв'язок</title>
    <style>
        :root {
            --bg-start: #f7f7ef;
            --bg-end: #dbe7d3;
            --ink: #1f2a1f;
            --muted: #4f5d4f;
            --card: rgba(255, 255, 255, 0.86);
            --line: #b8c7b0;
            --accent: #2f6f4f;
            --accent-2: #d17a22;
            --danger-bg: #fff1ec;
            --danger-line: #f0b6a1;
            --ok-bg: #ecf8ef;
            --ok-line: #9ed3aa;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
        }

        body {
            margin: 0;
            min-height: 100%;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            color: var(--ink);
            background: radial-gradient(circle at 15% 20%, #ffffff 0%, transparent 40%),
                        radial-gradient(circle at 85% 15%, #f6e4cf 0%, transparent 42%),
                        linear-gradient(135deg, var(--bg-start), var(--bg-end));
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .is-embedded body {
            min-height: 0;
            padding: 8px;
            background: transparent;
        }

        .widget {
            width: min(860px, 100%);
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 24px 50px rgba(28, 51, 35, 0.18);
            animation: rise 480ms ease-out;
        }

        .is-embedded .widget {
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(28, 51, 35, 0.12);
        }

        .side {
            padding: 36px 30px;
            background: linear-gradient(155deg, #204834, #2f6f4f);
            color: #f1faee;
            position: relative;
            isolation: isolate;
        }

        .side::before,
        .side::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            z-index: -1;
            opacity: 0.26;
        }

        .side::before {
            width: 210px;
            height: 210px;
            right: -60px;
            top: -70px;
            background: #ffd9b2;
        }

        .side::after {
            width: 160px;
            height: 160px;
            left: -45px;
            bottom: -55px;
            background: #a9d6b7;
        }

        .label {
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-size: 12px;
            margin: 0 0 12px;
            color: #c8edd7;
        }

        .title {
            margin: 0;
            font-size: clamp(26px, 3vw, 34px);
            line-height: 1.15;
        }

        .subtitle {
            margin: 16px 0 0;
            line-height: 1.6;
            color: #def4e8;
            max-width: 32ch;
        }

        .content {
            padding: 28px;
        }

        .notice {
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .notice.ok {
            background: var(--ok-bg);
            border: 1px solid var(--ok-line);
        }

        .notice.err {
            background: var(--danger-bg);
            border: 1px solid var(--danger-line);
        }

        .notice ul {
            margin: 8px 0 0;
            padding-left: 18px;
        }

        form {
            display: grid;
            gap: 12px;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        label {
            display: block;
            font-size: 13px;
            margin: 0 0 4px;
            color: var(--muted);
        }

        input,
        textarea {
            width: 100%;
            border: 1px solid #b8c5b2;
            border-radius: 12px;
            padding: 11px 12px;
            font: inherit;
            background: #ffffff;
            color: #1d2a1e;
            transition: box-shadow 180ms ease, border-color 180ms ease;
        }

        textarea {
            min-height: 130px;
            resize: vertical;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(47, 111, 79, 0.16);
        }

        input.input-error,
        textarea.input-error {
            border-color: #d4623b;
            box-shadow: 0 0 0 3px rgba(212, 98, 59, 0.15);
        }

        button {
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            font: inherit;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(130deg, var(--accent), #275941 65%, var(--accent-2));
            cursor: pointer;
            transition: transform 120ms ease, filter 120ms ease;
        }

        button:hover {
            transform: translateY(-1px);
            filter: brightness(1.03);
        }

        button:active {
            transform: translateY(0);
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.99);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 820px) {
            .widget {
                grid-template-columns: 1fr;
            }

            .row {
                grid-template-columns: 1fr;
            }

            .is-embedded body {
                padding: 0;
            }

            .is-embedded .widget {
                border-radius: 0;
                border-left: 0;
                border-right: 0;
            }
        }
    </style>
</head>
<body>
<main class="widget">
    <section class="side">
        <p class="label">Feedback</p>
        <h1 class="title">Customer feedback</h1>
        <p class="subtitle">
            Describe your request or issue. We will create a ticket and contact you shortly.
        </p>
    </section>

    <section class="content">
        <div id="feedback-success" class="notice ok" role="status" aria-live="polite" style="display:none"></div>
        <div id="feedback-error" class="notice err" role="alert" aria-live="assertive" style="display:none"></div>

        <form id="feedback-form" method="POST" action="{{ route('api.v1.tickets.store') }}">

            <div class="row">
                <div>
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required>
                </div>
            </div>

            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            </div>

            <div>
                <label for="topic">Topic</label>
                <input id="topic" name="topic" type="text" value="{{ old('topic') }}" required>
            </div>

            <div>
                <label for="body">Message</label>
                <textarea id="body" name="body" required>{{ old('body') }}</textarea>
            </div>

            <button id="feedback-submit" type="submit">Submit Request</button>
        </form>
    </section>
</main>
<script>
    (function () {
        function postWidgetHeight() {
            if (window.parent === window) {
                return;
            }

            window.parent.postMessage({
                type: 'feedback-widget:resize',
                height: document.documentElement.scrollHeight
            }, '*');
        }

        window.addEventListener('load', postWidgetHeight);
        window.addEventListener('resize', postWidgetHeight);

        if (window.ResizeObserver) {
            const resizeObserver = new ResizeObserver(postWidgetHeight);
            resizeObserver.observe(document.body);
        }

        postWidgetHeight();

        const form = document.getElementById('feedback-form');
        const submitButton = document.getElementById('feedback-submit');
        const successBox = document.getElementById('feedback-success');
        const errorBox = document.getElementById('feedback-error');
        const fieldNames = ['name', 'phone', 'email', 'topic', 'body'];

        if (!form) {
            return;
        }

        function clearNotices() {
            successBox.style.display = 'none';
            successBox.textContent = '';
            errorBox.style.display = 'none';
            errorBox.innerHTML = '';

            fieldNames.forEach(function (fieldName) {
                if (form[fieldName]) {
                    form[fieldName].classList.remove('input-error');
                }
            });
        }

        function showSuccess(message) {
            successBox.textContent = message;
            successBox.style.display = 'block';
            postWidgetHeight();
        }

        function showErrors(errors, message) {
            if (!errors || Object.keys(errors).length === 0) {
                errorBox.innerHTML = `<strong>${message || 'Submission failed. Please try again.'}</strong>`;
                errorBox.style.display = 'block';
                postWidgetHeight();
                return;
            }

            Object.keys(errors).forEach(function (fieldName) {
                if (form[fieldName]) {
                    form[fieldName].classList.add('input-error');
                }
            });

            const listItems = Object.values(errors)
                .flat()
                .map((message) => `<li>${message}</li>`)
                .join('');

            const title = message || 'Check the entered data:';
            errorBox.innerHTML = `<strong>${title}</strong><ul>${listItems}</ul>`;
            errorBox.style.display = 'block';
            postWidgetHeight();
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            clearNotices();

            submitButton.disabled = true;
            submitButton.textContent = 'Sending...';

            try {
                const payload = {
                    name: form.name.value,
                    phone: form.phone.value,
                    email: form.email.value,
                    topic: form.topic.value,
                    body: form.body.value,
                };

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                let result = {};
                try {
                    result = await response.json();
                } catch (jsonError) {
                    result = {};
                }

                if (!response.ok) {
                    showErrors(result.errors ?? null, result.message ?? null);
                    return;
                }

                showSuccess(result.message ?? 'Request sent successfully.');
                form.reset();
            } catch (error) {
                showErrors(null, 'Network error. Please try again.');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Submit Request';
            }
        });
    })();
</script>
</body>
</html>
