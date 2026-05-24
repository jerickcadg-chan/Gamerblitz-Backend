<?php

/**
 * This is a custom implementation of League\Fractal\Pagination\IlluminatePaginatorAdapter
 *
 * As of March 2019 Fractal doesn't have a paginator for Laravel simplePaginate()
 */

namespace App\Serializers;

use Illuminate\Contracts\Pagination\Paginator;
use League\Fractal\Pagination\PaginatorInterface;

/**
 * A paginator adapter for illuminate/pagination.
 *
 * @author Danny Herran <me@dannyherran.com>
 */
class Pagination implements PaginatorInterface
{
    /**
     * The paginator instance.
     *
     * @var \Illuminate\Contracts\Pagination\Paginator
     */
    protected $paginator;

    /**
     * Create a new illuminate pagination adapter.
     *
     *
     * @return void
     */
    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Get the current page.
     */
    public function getCurrentPage(): int
    {
        return $this->paginator->currentPage();
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function getLastPage(): int
    {
        return $this->paginator->hasMorePages() ? $this->getCurrentPage() + 1 : $this->getCurrentPage();
    }

    /**
     * Get the total.
     */
    public function getTotal(): int
    {
        return $this->paginator->total();
    }

    /**
     * Get the count.
     */
    public function getCount(): int
    {
        return $this->paginator->count();
    }

    /**
     * Get the number per page.
     */
    public function getPerPage(): int
    {
        return $this->paginator->perPage();
    }

    /**
     * Get the url for the given page.
     *
     * @param  int  $page
     */
    public function getUrl($page): string
    {
        return $this->paginator->url($page);
    }

    /**
     * Get the paginator instance.
     *
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }
}
