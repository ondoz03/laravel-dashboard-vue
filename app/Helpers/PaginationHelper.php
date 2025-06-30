<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginationHelper
{
    /**
     * Generate pagination metadata for API responses.
     *
     * @param LengthAwarePaginator $paginator The paginator instance
     * @param int|null $perPage Optional custom per_page value (if different from paginator)
     * @return array The pagination metadata
     */
    public static function getMetaData(LengthAwarePaginator $paginator, ?int $perPage = null): array
    {
        // Use provided perPage or get from paginator
        $perPage = $perPage ?? $paginator->perPage();

        return [
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'last_page' => $paginator->lastPage(),
            'per_page' => (int)$perPage,
            'total' => $paginator->total(),
            'path' => url()->current(),
            'links' => $paginator->linkCollection(),
        ];
    }
}
