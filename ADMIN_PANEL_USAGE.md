# Admin Panel Usage Conditions

This document describes who can access the admin panel and how to use its main features.

## Access Rules

The admin panel is restricted by role middleware:

- Allowed roles: `manager`, `admin`
- Middleware: `role:manager,admin`

If a user has no required role, access is denied.

## Main Entry Point

- URL: `/admin/tickets`
- Purpose: ticket list and filtering

## Available Admin Routes

- `GET /admin/tickets` - ticket list with filters
- `GET /admin/tickets/{ticket}` - ticket details
- `PATCH /admin/tickets/{ticket}/status` - update ticket status
- `GET /admin/tickets/{ticket}/files/{media}/download` - download attached file

## Ticket List Filters

On the ticket list page, the following filters are available:

- `status`: `new`, `at work`, `processed`
- `date_from`: start date boundary
- `date_to`: end date boundary
- `email`: partial match by customer email
- `phone`: partial match by customer phone

## Ticket Details Page

The details page includes:

- Ticket data: topic, message body, current status, dates
- Customer data: name, email, phone
- Attached files list with download links

## Status Update Rules

Allowed statuses for updates:

- `new`
- `at work`
- `processed`

Any other value is rejected by validation.

## File Download Rules

File download is allowed only when the requested media item belongs to the selected ticket.

If a file does not belong to that ticket, the system returns `404 Not Found`.

## Role Assignment Example

Users must have one of the allowed roles to enter the panel.

Example (Laravel Tinker or application code):

```php
$user->assignRole('manager');
// or
$user->assignRole('admin');
```

## Notes

- The panel is built with Blade UI.
- Filters are persistent between paginated pages via query string.
- Access policy is role-based and enforced at route level.
