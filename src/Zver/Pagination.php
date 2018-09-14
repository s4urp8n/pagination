<?php

namespace Zver {

    use Zver\Exceptions\Pagination\CurrentPageCallbackNotSetException;
    use Zver\Exceptions\Pagination\PageUrlCallbackNotSetException;

    class Pagination
    {

        protected $itemsPerPage = 10;
        protected $source = [];
        protected $totalItemsCount = 0;
        protected $currentPageCallback = null;
        protected $pageUrlCallback = null;

        public function setItems($source)
        {
            if ($source instanceof PaginationInterface) {
                $this->source = $source;
                $this->totalItemsCount = $source->getPaginationItemsCount();

                return $this;
            }

            if (is_array($source)) {
                $this->source = $source;
                $this->totalItemsCount = count($source);

                return $this;
            }

            throw new \InvalidArgumentException('Source argument must implements PaginationInterface or be an array');
        }

        public function getItems($offset = null, $length = null)
        {

            if (is_null($offset)) {
                $offset = $this->getOffset();
            }

            if (is_null($length)) {
                $length = $this->getItemsPerPage();
            }

            if ($this->source instanceof PaginationInterface) {
                return $this->source->getPaginationItems($offset, $length);
            }

            return array_slice($this->source, $offset, $length);
        }

        public function getItemsCount()
        {
            return $this->totalItemsCount;
        }

        public static function create()
        {
            return new static();
        }

        public function showItems(callable $callback)
        {
            $callback($this->getItems($this->getOffset(), $this->getItemsPerPage()), $this);

            return $this;
        }

        public function showPages(callable $callback)
        {

            $pages = [];
            $number = 0;
            $current = $this->getCurrentPage();
            for ($i = 0; $i < $this->getPagesCount(); $i++) {
                $number = $i + 1;
                $pages[] = Page::create($number, $this->getPageUrl($number), $number == $current);
            }

            if (count($pages) > 1) {
                $callback($pages, $this);
            }

            return $this;
        }

        protected function __construct()
        {

        }

        public function setItemsPerPage($itemsPerPage)
        {
            $itemsPerPage = intval($itemsPerPage);

            if ($itemsPerPage < 1) {
                throw new \InvalidArgumentException('Items per page number must be 1 or more');
            }

            $this->itemsPerPage = $itemsPerPage;

            return $this;
        }

        public function getItemsPerPage()
        {
            return $this->itemsPerPage;
        }

        public function getPagesCount()
        {
            if ($this->getItemsCount() > 0) {
                return ceil($this->getItemsCount() / $this->getItemsPerPage());
            }

            return 0;
        }

        public function getOffset()
        {
            return ($this->getCurrentPage() - 1) * $this->getItemsPerPage();
        }

        public function setCurrentPageCallback(callable $callback)
        {
            $this->currentPageCallback = $callback;

            return $this;
        }

        public function setPageUrlCallback(callable $callback)
        {
            $this->pageUrlCallback = $callback;

            return $this;
        }

        public function getCurrentPage()
        {
            if (!is_callable($this->currentPageCallback)) {
                throw new CurrentPageCallbackNotSetException();
            }

            return call_user_func($this->currentPageCallback);
        }

        public function getPageUrl($number)
        {
            if (!is_callable($this->pageUrlCallback)) {
                throw new PageUrlCallbackNotSetException();
            }

            return call_user_func($this->pageUrlCallback, $number);
        }
    }
}
