# Ticket Statistics API Guide

This document explains how to use the ticket statistics endpoint.

## Endpoint

- Method: `GET`
- URL: `/api/v1/tikets/statistics`

## Query Parameters

- `period` (optional): one of `day`, `week`, `month`
- Default value: `day`

Examples:

```http
GET /api/v1/tikets/statistics
GET /api/v1/tikets/statistics?period=day
GET /api/v1/tikets/statistics?period=week
GET /api/v1/tikets/statistics?period=month
```

## Success Response

- Status: `200 OK`
- Content type: `application/json`

Example:

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

## Fields Description

- `data.period`: selected statistics period (`day`, `week`, `month`)
- `data.from`: start datetime boundary for the selected period (ISO 8601)
- `data.to`: end datetime boundary for the selected period (ISO 8601)
- `data.total`: total number of tickets in the selected period
- `data.by_status.new`: number of tickets with status `new`
- `data.by_status.at work`: number of tickets with status `at work`
- `data.by_status.processed`: number of tickets with status `processed`

## Validation Error Response

If `period` has an unsupported value:

- Status: `422 Unprocessable Entity`

Example:

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

## cURL Examples

```bash
curl -X GET "https://your-domain/api/v1/tikets/statistics?period=day" \
  -H "Accept: application/json"
```

```bash
curl -X GET "https://your-domain/api/v1/tikets/statistics?period=month" \
  -H "Accept: application/json"
```

## JavaScript Example

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

## Notes

- Period calculation is based on server time.
- Statistics are calculated using the ticket `date_at` field.
- The endpoint currently uses path `/api/v1/tikets/statistics` (with `tikets` spelling) to match project routing.
