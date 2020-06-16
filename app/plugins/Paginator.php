<?php
namespace Fox;

class Paginator {

    private $results;
    private $limit;
    private $current;
    private $total_pages;
    private $paginated;
    private $total_results;

    /**
     * Paginator constructor.
     * @param array $results
     * @param int $page
     * @param int $limit
     */
    public function __construct($results, $page = 1, $limit = 20) {
        $this->results  = $results;
        $this->current  = $page < 1 || !is_numeric($page) ? 1 : $page;
        $this->limit    = $limit;
    }

    public function paginate() {
        $this->total_results = count($this->results);
        $this->paginated     = array_chunk($this->results, $this->limit);
        $this->total_pages   = count($this->paginated);
        return $this;
    }

    public function getResults() {
        if  ($this->current > $this->total_pages) {
            $this->current = $this->total_pages;
        }

        return [
            'total_results' => $this->total_results,
            'total_pages'   => $this->total_pages,
            'current' => $this->current,
            'first'   => 1,
            'before'  => $this->current == 1 ? 1 : $this->current - 1,
            'next'    => $this->current == $this->total_pages ? $this->total_pages : $this->current + 1,
            'last'    => $this->total_pages,
            'items'   => $this->paginated[$this->current - 1]
        ];
    }
}