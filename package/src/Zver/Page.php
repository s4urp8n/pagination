<?php
namespace Zver
{
    
    use Zver\Exceptions\Page\InvalidPageNumberException;
    use Zver\Exceptions\Page\InvalidPageUrlException;
    
    class Page
    {
        
        protected $url = null;
        protected $number = null;
        protected $active = false;
        
        public static function create($number = null, $url = null, $active = null)
        {
            return new static($number, $url, $active);
        }
        
        public function isValid()
        {
            return !is_null($this->url) && !is_null($this->number);
        }
        
        protected function __construct($number, $url, $active)
        {
            if (!is_null($number))
            {
                $this->setNumber($number);
            }
            
            if (!is_null($url))
            {
                $this->setUrl($url);
            }
            
            if (!is_null($active))
            {
                $this->setActive($active);
            }
            
        }
        
        public function isActive()
        {
            return $this->active;
        }
        
        public function getUrl()
        {
            return $this->url;
        }
        
        public function getNumber()
        {
            return $this->number;
        }
        
        public function setUrl($url)
        {
            $url = filter_var($url, FILTER_VALIDATE_URL);
            
            if (!empty($url))
            {
                $this->url = $url;
            }
            else
            {
                throw new InvalidPageUrlException('"' . $url . '" is not a valid URL');
            }
            
            return $this;
        }
        
        public function setNumber($number)
        {
            $number = intval($number);
            
            if ($number > 0)
            {
                $this->number = $number;
            }
            else
            {
                throw new InvalidPageNumberException('"' . $number . '" is not a valid page number ( must be > 0)');
            }
            
            return $this;
        }
        
        public function setActive($active)
        {
            $this->active = boolval($active);
            
            return $this;
        }
        
    }
}
