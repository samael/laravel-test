<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Tickets</title>
    <style>
        body { font-family: "Segoe UI", Tahoma, sans-serif; margin: 0; background: #f4f6f8; color: #1d2630; }
        .container { max-width: 1200px; margin: 0 auto; padding: 24px; }
        .header { margin-bottom: 18px; }
        .card { background: #fff; border: 1px solid #d9e1e7; border-radius: 12px; padding: 16px; }
        .filters { display: grid; grid-template-columns: repeat(5, minmax(120px, 1fr)); gap: 10px; margin-bottom: 16px; }
        .filters .actions { display: flex; gap: 8px; align-items: end; }
        label { display: block; font-size: 12px; color: #617383; margin-bottom: 4px; }
        input, select, button, a.btn-link { width: 100%; border: 1px solid #c7d3de; border-radius: 8px; padding: 9px 10px; font: inherit; box-sizing: border-box; }
        button { background: #1f6fb2; color: #fff; border: none; cursor: pointer; }
        a.btn-link { display: inline-block; text-align: center; background: #fff; color: #1f6fb2; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #e8edf2; vertical-align: top; }
        th { font-size: 12px; color: #607283; text-transform: uppercase; letter-spacing: .04em; }
        .status { display: inline-block; padding: 4px 8px; border-radius: 999px; font-size: 12px; }
        .status-new { background: #e8f2ff; color: #185ea0; }
        .status-at-work { background: #fff4dd; color: #9a6406; }
        .status-processed { background: #e8f8ea; color: #1d7b39; }
        .muted { color: #6f8090; font-size: 13px; }
        .pagination { margin-top: 14px; }
        @media (max-width: 980px) { .filters { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 640px) { .filters { grid-template-columns: 1fr; } table { font-size: 14px; } }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Tickets</h1>
        <p class="muted">Admin panel for managers and admins</p>
    </div>

    <div class="card">
        <form method="GET" action="{{ route('admin.tickets.index') }}" class="filters">
            <div>
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All statuses</option>
                    @foreach ($statusOptions as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="email">Customer email</label>
                <input id="email" name="email" type="text" value="{{ $filters['email'] ?? '' }}" placeholder="example@mail.com">
            </div>
            <div>
                <label for="phone">Customer phone</label>
                <input id="phone" name="phone" type="text" value="{{ $filters['phone'] ?? '' }}" placeholder="+380...">
            </div>
            <div>
                <label for="date_from">Date from</label>
                <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div>
                <label for="date_to">Date to</label>
                <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="actions">
                <button type="submit">Apply</button>
            </div>
            <div class="actions">
                <a class="btn-link" href="{{ route('admin.tickets.index') }}">Reset</a>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Topic</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($tickets as $ticket)
                <tr>
                    <td>#{{ $ticket->id }}</td>
                    <td>{{ $ticket->topic }}</td>
                    <td>{{ $ticket->client?->name ?? 'N/A' }}</td>
                    <td>
                        <div>{{ $ticket->client?->email ?? 'N/A' }}</div>
                        <div class="muted">{{ $ticket->client?->phone ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <span class="status status-{{ str_replace(' ', '-', $ticket->status) }}">{{ ucfirst($ticket->status) }}</span>
                    </td>
                    <td>{{ optional($ticket->date_at)->format('Y-m-d H:i') }}</td>
                    <td><a href="{{ route('admin.tickets.show', $ticket) }}">View</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="muted">No tickets found for current filters.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
</body>
</html>
