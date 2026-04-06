<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'period' => $this->resource['period'],
            'from' => $this->resource['from']->toIso8601String(),
            'to' => $this->resource['to']->toIso8601String(),
            'total' => $this->resource['total'],
            'by_status' => $this->resource['by_status'],
        ];
    }
}
