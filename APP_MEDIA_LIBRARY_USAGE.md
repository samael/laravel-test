# Ticket Media Library Usage Guide

## Overview

Ticket model is configured with Spatie Laravel MediaLibrary for managing file attachments.

**Collection Name:** `tickets_files`

## Allowed File Types

- PDF documents: `application/pdf`
- Images: `image/jpeg`, `image/png`, `image/gif`
- Word documents: `application/msword`, `application/vnd.openxmlformats-officedocument.wordprocessingml.document`
- Excel spreadsheets: `application/vnd.ms-excel`, `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`
- Text files: `text/plain`, `text/csv`

## Size Limits

- **Maximum file size:** 10 MB (defined in `config/media-library.php`)
- Access via: `Ticket::maxFileSize()` → returns 10485760 bytes

## Usage Examples

### Add a File to a Ticket

```php
$ticket = Ticket::find(1);

// From request (form upload)
$ticket->addMedia(request()->file('attachment'))
    ->toMediaCollection('tickets_files');

// From file path
$ticket->addMedia('/path/to/file.pdf')
    ->toMediaCollection('tickets_files');

// With custom name
$ticket->addMedia(request()->file('document'))
    ->usingName('Document Title')
    ->toMediaCollection('tickets_files');
```

### Retrieve Files

```php
// Get all files for ticket
$files = $ticket->getMedia('tickets_files');

// Get first file
$firstFile = $ticket->getFirstMedia('tickets_files');

// Get file URL
$url = $ticket->getFirstMediaUrl('tickets_files');

// Get all URLs
$urls = $ticket->getMediaUrls('tickets_files');
```

### File Information

```php
foreach ($ticket->getMedia('tickets_files') as $media) {
    echo $media->name;                    // Original filename
    echo $media->file_name;               // Stored filename
    echo $media->size;                    // File size in bytes
    echo $media->mime_type;               // MIME type
    echo $media->getUrl();                // Download URL
}
```

### Delete Files

```php
// Delete specific media
$ticket->deleteMedia($media);

// Delete all media from collection
$ticket->clearMediaCollection('tickets_files');

// Delete by media ID
\Spatie\MediaLibrary\MediaCollections\Models\Media::destroy($mediaId);
```

## Controller Example

```php
<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketFileController extends Controller
{
    public function upload(Request $request, Ticket $ticket)
    {
        // Validate file
        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'max:' . (Ticket::maxFileSize() / 1024), // in KB
                'mimes:pdf,doc,docx,jpg,jpeg,png,gif,xls,xlsx,txt,csv',
            ],
        ]);

        // Add media to collection
        $ticket->addMedia($validated['file'])
            ->toMediaCollection('tickets_files');

        return response()->json(['message' => 'File uploaded successfully']);
    }

    public function download(Ticket $ticket, $mediaId)
    {
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);
        
        return response()->download($media->getPath(), $media->file_name);
    }

    public function list(Ticket $ticket)
    {
        $files = $ticket->getMedia('tickets_files');

        return response()->json($files->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'size' => $media->size,
                'mime_type' => $media->mime_type,
                'url' => $media->getUrl(),
            ];
        }));
    }

    public function delete(Ticket $ticket, $mediaId)
    {
        $ticket->deleteMedia(\Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId));

        return response()->json(['message' => 'File deleted successfully']);
    }
}
```

## Helper Methods

### Get Allowed Types

```php
$allowedTypes = Ticket::allowedMediaTypes();
```

### Get Max Size

```php
$maxBytes = Ticket::maxFileSize();
$maxMB = Ticket::maxFileSize() / 1024 / 1024;
```

## Configuration

Global settings are in `config/media-library.php`:

```php
'max_file_size' => 1024 * 1024 * 10, // 10 MB
'disk_name' => env('MEDIA_DISK', 'public'),
```

## Documentation

- [Spatie Laravel MediaLibrary Docs](https://spatie.be/docs/laravel-medialibrary)
- [MediaLibrary Pro](https://medialibrary.pro)
