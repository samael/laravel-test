# Ticket Management Project

This project is a Laravel-based ticket management system with:

- Feedback widget submission flow (AJAX + API)
- Ticket API with statistics
- Role-based admin panel for managers and admins
- Media attachments via Spatie MediaLibrary
- RBAC via Spatie Permission
- Docker-based local environment

## CI Result

- Workflow: `CI` (GitHub Actions)
- Current result: PASSING
- Latest local verification: `13 passed (75 assertions)`

CI runs automatically on `push` and `pull_request` for `main`, `master`, and `develop` branches.

## Run Locally with Docker

This project includes a Docker setup for local development.

### Requirements

- Docker Desktop (or Docker Engine + Docker Compose)

### Start the application

```bash
docker compose up -d --build
```

### Run initial migrations and seeders

```bash
docker compose exec app php artisan migrate --seed
```

### Open in browser

- App: http://localhost:8000
- MySQL exposed port: `33060`
- Swagger API docs: http://localhost:8000/api/docs

### Useful commands

```bash
# Stop containers
docker compose down

# Stop and remove DB volume (full reset)
docker compose down -v

# Run tests
docker compose exec app php artisan test
```

### Docker Daily Operations and Troubleshooting

Included services:

- `app` - PHP 8.4 container running Laravel on port `8000`
- `db` - MySQL 8.4 container on port `33060`

Daily commands:

```bash
# Start containers
docker compose up -d

# View logs
docker compose logs -f app
docker compose logs -f db

# Run Artisan commands
docker compose exec app php artisan about
docker compose exec app php artisan route:list
```

Full reset:

```bash
docker compose down -v
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

Troubleshooting:

- If `8000` or `33060` is occupied, update ports in `docker-compose.yml`.
- If dependencies are broken, rebuild with:

```bash
docker compose build --no-cache
docker compose up -d
```

- If app key/env issues occur:

```bash
docker compose exec app php artisan key:generate
```

## API Documentation (Swagger)

- Swagger UI: http://localhost:8000/api/docs
- OpenAPI spec file: `public/docs/openapi.yaml`

## Ticket Statistics API Guide

Endpoint:

- Method: `GET`
- URL: `/api/v1/tikets/statistics`

Query parameters:

- `period` (optional): `day`, `week`, `month`
- Default: `day`

Examples:

```http
GET /api/v1/tikets/statistics
GET /api/v1/tikets/statistics?period=day
GET /api/v1/tikets/statistics?period=week
GET /api/v1/tikets/statistics?period=month
```

Success response example:

```json
{
	"data": {
		"period": "week",
		"from": "2026-04-06T00:00:00+00:00",
		"to": "2026-04-12T23:59:59+00:00",
		"total": 14,
		"by_status": {
			"new": 6,
			"at work": 5,
			"processed": 3
		}
	}
}
```

Validation error (`422`) example:

```json
{
	"message": "The period field must be one of day, week, month.",
	"errors": {
		"period": [
			"The period field must be one of day, week, month."
		]
	}
}
```

JavaScript usage:

```javascript
async function getTicketStatistics(period = 'day') {
	const response = await fetch(`/api/v1/tikets/statistics?period=${period}`, {
		headers: {
			Accept: 'application/json',
		},
	});

	const payload = await response.json();

	if (!response.ok) {
		throw new Error(payload.message || 'Failed to load ticket statistics');
	}

	return payload.data;
}
```

Notes:

- Period calculations use server time.
- Statistics are calculated by ticket `date_at`.
- The API path intentionally uses `/api/v1/tikets/statistics` to match routing.

## Feedback Widget Embedding Guide

Basic iframe embed:

```html
<iframe
	src="https://your-domain/feedback-widget"
	style="width:100%;max-width:860px;height:720px;border:0;"
	loading="lazy"
></iframe>
```

Recommended iframe parameters:

- `width: 100%`
- `max-width: 860px`
- `height: 680-760px`
- `border: 0`
- `loading: lazy`

Auto-resize via postMessage:

Widget emits:

- `type: "feedback-widget:resize"`
- `height: <number>`

Parent page script:

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

Container example:

```html
<div style="max-width: 920px; margin: 0 auto; padding: 16px;">
	<iframe
		src="https://your-domain/feedback-widget"
		style="width:100%;height:720px;border:0;border-radius:16px;"
		loading="lazy"
	></iframe>
</div>
```

Security setup:

```env
FEEDBACK_WIDGET_FRAME_ANCESTORS='https://example.com https://app.example.com'
```

Allow all:

```env
FEEDBACK_WIDGET_FRAME_ANCESTORS='*'
```

Checklist:

1. The widget opens in iframe with no blocking errors.
2. Form submission succeeds.
3. Height updates after validation errors.
4. Success message shows after successful submit.

## Ticket Media Library Guide

Overview:

- Ticket model uses Spatie Laravel MediaLibrary.
- Collection: `tickets_files`

Allowed file types:

- `application/pdf`
- `image/jpeg`, `image/png`,
- `application/msword`
- `application/vnd.openxmlformats-officedocument.wordprocessingml.document`
- `application/vnd.ms-excel`
- `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`
- `text/plain`, `text/csv`

Size limits:

- Max size: 10 MB (`config/media-library.php`)
- Programmatic access: `Ticket::maxFileSize()`

Examples:

```php
$ticket->addMedia(request()->file('attachment'))
		->toMediaCollection('tickets_files');

$files = $ticket->getMedia('tickets_files');
$firstFile = $ticket->getFirstMedia('tickets_files');
$url = $ticket->getFirstMediaUrl('tickets_files');

$ticket->deleteMedia($media);
$ticket->clearMediaCollection('tickets_files');
```

Configuration snippet:

```php
'max_file_size' => 1024 * 1024 * 10, // 10 MB
'disk_name' => env('MEDIA_DISK', 'public'),
```

## Spatie Permission (RBAC) Guide

Overview:

- Package: Spatie Laravel Permission
- Roles: `manager`, `admin`

Permissions:

- `view tickets`
- `manage tickets`
- `view reports`
- `manage users`

Role mapping:

- `manager`: view/manage tickets
- `admin`: manage tickets, view reports, manage users, view tickets

Usage examples:

```php
$user = Auth::user();

if ($user->hasRole('admin')) {
		// ...
}

if ($user->hasPermissionTo('manage tickets')) {
		// ...
}
```

Blade directives:

```blade
@role('admin')
		<!-- admin-only content -->
@endrole

@can('manage tickets')
		<!-- permission-protected content -->
@endcan
```

Route middleware example:

```php
Route::get('/tickets', [TicketController::class, 'index'])
		->middleware('role:admin,manager');

Route::post('/tickets', [TicketController::class, 'store'])
		->middleware('permission:manage tickets');
```

Assign roles:

```php
$user->assignRole('manager');
$user->assignRole('admin');
```

Permission tables:

- `roles`
- `permissions`
- `role_has_permissions`
- `model_has_roles`
- `model_has_permissions`

Cache reset:

```bash
php artisan cache:clear
php artisan permission:cache-reset
```

## Admin Panel Usage Conditions

Access rules:

- Allowed roles: `manager`, `admin`
- Middleware: `role:manager,admin`

Main entry point:

- `/admin/tickets`

Available admin routes:

- `GET /admin/tickets`
- `GET /admin/tickets/{ticket}`
- `PATCH /admin/tickets/{ticket}/status`
- `GET /admin/tickets/{ticket}/files/{media}/download`

Filters on ticket list:

- `status`: `new`, `at work`, `processed`
- `date_from`, `date_to`
- `email` (partial)
- `phone` (partial)

Ticket details include:

- Topic, body, status, dates
- Customer data (name, email, phone)
- Attached files with download links

Status update rules:

- Allowed values: `new`, `at work`, `processed`
- Any other value fails validation

File download rule:

- Download is allowed only if media belongs to the selected ticket.
- Otherwise returns `404 Not Found`.

Role assignment example:

```php
$user->assignRole('manager');
// or
$user->assignRole('admin');
```

Notes:

- Admin panel uses Blade UI.
- Filters persist across paginated pages via query string.
- Access policy is role-based at route level.
