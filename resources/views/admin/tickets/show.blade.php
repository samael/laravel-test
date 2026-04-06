<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $ticket->id }}</title>
    <style>
        body { font-family: "Segoe UI", Tahoma, sans-serif; margin: 0; background: #f4f6f8; color: #1d2630; }
        .container { max-width: 1000px; margin: 0 auto; padding: 24px; }
        .top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .card { background: #fff; border: 1px solid #d9e1e7; border-radius: 12px; padding: 16px; margin-bottom: 14px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .muted { color: #6f8090; font-size: 13px; }
        .status { display: inline-block; padding: 4px 8px; border-radius: 999px; font-size: 12px; }
        .status-new { background: #e8f2ff; color: #185ea0; }
        .status-at-work { background: #fff4dd; color: #9a6406; }
        .status-processed { background: #e8f8ea; color: #1d7b39; }
        label { display: block; font-size: 12px; color: #617383; margin-bottom: 4px; }
        select, button { border: 1px solid #c7d3de; border-radius: 8px; padding: 9px 10px; font: inherit; }
        button { background: #1f6fb2; color: #fff; border: none; cursor: pointer; }
        .files li { margin-bottom: 6px; }
        .alert { background: #e8f8ea; border: 1px solid #9fd2ab; color: #1a6d35; border-radius: 8px; padding: 10px 12px; margin-bottom: 12px; }
        .error { background: #fff1ec; border: 1px solid #f0b6a1; color: #913d22; border-radius: 8px; padding: 10px 12px; margin-bottom: 12px; }
        @media (max-width: 720px) { .row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">
    <div class="top">
        <h1>Ticket #{{ $ticket->id }}</h1>
        <a href="{{ route('admin.tickets.index') }}">Back to list</a>
    </div>

    @if (session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="error">
            <strong>Please fix the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="row">
            <div>
                <p class="muted">Topic</p>
                <p>{{ $ticket->topic }}</p>

                <p class="muted">Message</p>
                <p>{{ $ticket->body }}</p>
            </div>
            <div>
                <p class="muted">Status</p>
                <p><span class="status status-{{ str_replace(' ', '-', $ticket->status) }}">{{ ucfirst($ticket->status) }}</span></p>

                <p class="muted">Created for date</p>
                <p>{{ optional($ticket->date_at)->format('Y-m-d H:i') }}</p>

                <p class="muted">Updated at</p>
                <p>{{ optional($ticket->updated_at)->format('Y-m-d H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Customer</h2>
        <p><strong>Name:</strong> {{ $ticket->client?->name ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $ticket->client?->email ?? 'N/A' }}</p>
        <p><strong>Phone:</strong> {{ $ticket->client?->phone ?? 'N/A' }}</p>
    </div>

    <div class="card">
        <h2>Update status</h2>
        <form method="POST" action="{{ route('admin.tickets.update-status', $ticket) }}">
            @csrf
            @method('PATCH')
            <label for="status">Status</label>
            <select id="status" name="status">
                @foreach ($statusOptions as $status)
                    <option value="{{ $status }}" @selected($ticket->status === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit">Save status</button>
        </form>
    </div>

    <div class="card">
        <h2>Attached files</h2>
        <ul class="files">
            @forelse ($ticket->getMedia('tickets_files') as $media)
                <li>
                    {{ $media->file_name }}
                    ({{ number_format($media->size / 1024, 1) }} KB)
                    - <a href="{{ route('admin.tickets.files.download', [$ticket, $media]) }}">Download</a>
                </li>
            @empty
                <li class="muted">No files attached.</li>
            @endforelse
        </ul>
    </div>
</div>
</body>
</html>
