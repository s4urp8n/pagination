<?php
namespace Zver
{
    
    class Page
    {
        
        protected $url = null;
        protected $number = null;
        protected $active = false;
        
        public function __construct($number, $url, $active)
        {
            $this->number = $number;
            $this->url = $url;
            $this->active = $active;
        }
        
        public function isActive()
        {
            return $this->active;
        }
        
        public function getLink()
        {
            return $this->url;
        }
        
        public function getNumber()
        {
            return $this->number;
        }
        
    }
}
