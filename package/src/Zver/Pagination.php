<?php

class Pagination
{
    
    protected static $currentUrl = null;
    protected $queryPageParam = 'page';
    protected $itemsPerPage = 10;
    protected $source = [];
    protected $totalItemsCount = [];
    protected $paramsRemove = [];
    
    public function removeQueryParam($paramName)
    {
        $this->paramsRemove[] = $paramName;
        
        return $this;
    }
    
    public function setItems($source)
    {
        if ($source instanceof PaginationInterface)
        {
            $this->source = $source;
            $this->totalItemsCount = $source->getPaginationItemsCount();
            
            return $this;
        }
        
        if (is_array($source))
        {
            $this->source = $source;
            $this->totalItemsCount = count($source);
            
            return $this;
        }
        
        throw new \InvalidArgumentException('Source argument must implements PaginationInterface or be an array');
    }
    
    protected function getItems($offset, $length)
    {
        if ($this->source instanceof PaginationInterface)
        {
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
        static::updateCurrentUrl();
        
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
        for ($i = 0; $i < $this->getPagesCount(); $i++)
        {
            $number = $i + 1;
            $pages[] = new Page($number, $this->getPageUrl($number), $number == $current);
        }
        
        if (count($pages) > 1)
        {
            $callback($pages, $this);
        }
        
        return $this;
    }
    
    protected static function updateCurrentUrl()
    {
        $server = $_SERVER;
        
        $ssl = (!empty($server['HTTPS']) && $server['HTTPS'] == 'on');
        
        $sp = strtolower($server['SERVER_PROTOCOL']);
        
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl)
                ? 's'
                : '');
        
        $port = $server['SERVER_PORT'];
        
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443'))
            ? ''
            : ':' . $port;
        
        $host = (isset($server['HTTP_X_FORWARDED_HOST']))
            ? $server['HTTP_X_FORWARDED_HOST']
            : (isset($server['HTTP_HOST'])
                ? $server['HTTP_HOST']
                : null);
        
        $host = isset($host)
            ? $host
            : $server['SERVER_NAME'] . $port;
        
        static::$currentUrl = explode('?', $protocol . '://' . $host . $server['REQUEST_URI'])[0];
    }
    
    protected function getPageUrl($pageNumber)
    {
        foreach ($this->paramsRemove as $param)
        {
            if (isset($_GET[$param]))
            {
                unset($_GET[$param]);
            }
        }
        
        if (isset($_GET[$this->queryPageParam]))
        {
            unset($_GET[$this->queryPageParam]);
        }
        
        if ($pageNumber != 1)
        {
            $_GET[$this->queryPageParam] = $pageNumber;
        }
        
        $query = http_build_query($_GET);
        
        if (!empty($query))
        {
            return static::$currentUrl . '?' . $query;
        }
        
        return static::$currentUrl;
        
    }
    
    protected function __construct()
    {
        
    }
    
    public function setQueryParamName($name)
    {
        $this->queryPageParam = $name;
        
        return $this;
    }
    
    public function setItemsPerPage($itemsPerPage)
    {
        $itemsPerPage = intval($itemsPerPage);
        
        if ($itemsPerPage < 1)
        {
            throw new \InvalidArgumentException('Items per page number must be 1 or more');
        }
        
        $this->itemsPerPage = $itemsPerPage;
        
        return $this;
    }
    
    public function getQueryParamName()
    {
        return $this->queryPageParam;
    }
    
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }
    
    public function getPagesCount()
    {
        if ($this->getItemsCount() > 0)
        {
            return ceil($this->getItemsCount() / $this->getItemsPerPage());
        }
        
        return 0;
    }
    
    public function getOffset()
    {
        return ($this->getCurrentPage() - 1) * $this->getItemsPerPage();
    }
    
    protected function getCurrentPage()
    {
        if (!empty($_GET[$this->getQueryParamName()]))
        {
            $queryPage = intval($_GET[$this->getQueryParamName()]);
            
            if (is_numeric($queryPage))
            {
                if ($queryPage > $this->getPagesCount())
                {
                    return $this->getPagesCount();
                }
                
                if ($queryPage >= 1)
                {
                    return $queryPage;
                }
            }
        }
        
        return 1;
    }
    
}