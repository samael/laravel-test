# Embedding the Feedback Widget via iframe

This document explains how to embed the feedback page into an external website.

## Basic Embedding

```html
<iframe
  src="https://your-domain/feedback-widget"
  style="width:100%;max-width:860px;height:720px;border:0;"
  loading="lazy"
></iframe>
```

## Recommended iframe Parameters

- `width: 100%` for adaptive container width
- `max-width: 860px` to preserve widget design
- `height: 680-760px` as an initial height
- `border: 0` for a clean look
- `loading: lazy` for faster page loading

## Automatic iframe Resize (postMessage)

The Feedback Widget sends the following message to the parent page:

- `type: "feedback-widget:resize"`
- `height: <number>`

Add this script to the page where the iframe is embedded:

```html
<script>
window.addEventListener('message', function (event) {
  if (!event.data || event.data.type !== 'feedback-widget:resize') {
    return;
  }

  const iframe = document.querySelector('iframe[src*="/feedback-widget"]');
  if (!iframe) {
    return;
  }

  iframe.style.height = event.data.height + 'px';
});
</script>
```

## Container Example

```html
<div style="max-width: 920px; margin: 0 auto; padding: 16px;">
  <iframe
    src="https://your-domain/feedback-widget"
    style="width:100%;height:720px;border:0;border-radius:16px;"
    loading="lazy"
  ></iframe>
</div>
```

## Security Configuration in Laravel

The widget controller uses the following header:

- `Content-Security-Policy: frame-ancestors ...`

The domains allowed to embed the widget are set via an environment variable:

```env
FEEDBACK_WIDGET_FRAME_ANCESTORS='https://example.com https://app.example.com'
```

If embedding must be allowed from anywhere:

```env
FEEDBACK_WIDGET_FRAME_ANCESTORS='*'
```

## Post-Embedding Checklist

1. The widget opens inside the iframe without blocking errors.
2. The form submits successfully.
3. After validation errors, the iframe height updates correctly.
4. After a successful submission, a success message is displayed.
