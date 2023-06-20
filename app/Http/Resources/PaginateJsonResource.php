<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginateJsonResource extends ResourceCollection
{
    private ?string $_resource;

    public function __construct($resource, ?string $class = Resource::class)
    {
        $this->_resource = $class;
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $resource = $this->resource;
        return [
            'items' => $this->collection->transform(
                function ($item) {
                    return new $this->_resource($item);
                }
            ),
            'pagination' => [
                'total' => $resource->total(),
                'count' => $resource->count(),
                'per_page' => $resource->perPage(),
                'current_page' => $resource->currentPage(),
                'total_pages' => $resource->lastPage()
            ]
        ];
    }
}
