<?php

declare(strict_types=1);

namespace Larafly\Apidoc\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class PaginateResponse extends ApiResponse
{
    public function toResponse($request): JsonResponse
    {
        // deal paginate
        if ($this->data instanceof LengthAwarePaginator) {
            return new JsonResponse([
                'code' => $this->code,
                'message' => $this->message,
                'data' => $this->transform($this->data->items()), // get data
                'meta' => [
                    'current_page' => $this->data->currentPage(),
                    'last_page' => $this->data->lastPage(),
                    'per_page' => $this->data->perPage(),
                    'total' => $this->data->total(),
                ],
            ]);
        }

        return parent::toResponse($request);
    }
}
