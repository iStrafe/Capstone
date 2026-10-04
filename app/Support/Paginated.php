<?php

namespace App\Support;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * One pagination shape for every React page:
 *
 *     [
 *         'data' => [...],  // the page's items, resolved through the resource
 *         'meta' => ['currentPage', 'lastPage', 'perPage', 'total', 'from', 'to'],
 *         'links' => [
 *             'prev' => ?string,
 *             'next' => ?string,
 *             // Numbered pages with '...' gaps (url null), as Laravel's own paginator views show them.
 *             'pages' => [['url' => ?string, 'label' => string, 'page' => ?int, 'active' => bool], ...],
 *         ],
 *     ]
 *
 * Page URLs keep the rest of the query string, so several paginators on one page
 * (page, approved_page, ...) and filters don't reset each other.
 */
class Paginated
{
    /**
     * @param  class-string<JsonResource>  $resource
     * @return array{data: array<int, mixed>, meta: array<string, int|null>, links: array<string, mixed>}
     */
    public static function make(LengthAwarePaginator $paginator, string $resource): array
    {
        $paginator->withQueryString();

        // A page past the end (an old link after deletions, or ?page=99999) would show an empty
        // state that says there's nothing here, so go to the last page instead.
        if ($paginator->currentPage() > $paginator->lastPage() && $paginator->total() > 0) {
            throw new HttpResponseException(redirect()->to($paginator->url($paginator->lastPage())));
        }

        $pages = $paginator->linkCollection()->slice(1, -1)->map(fn (array $link) => [
            'url' => $link['url'],
            'label' => $link['label'],
            'page' => $link['page'] ?? null,
            'active' => $link['active'],
        ])->values()->all();

        return [
            'data' => $resource::collection($paginator->getCollection())->resolve(),
            'meta' => [
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
                'pages' => $pages,
            ],
        ];
    }
}
