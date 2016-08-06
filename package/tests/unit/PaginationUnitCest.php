<?php

use Zver\Pagination;
use Zver\PaginationInterface;

class PaginationTestObject implements PaginationInterface
{
    
    public $data = [];
    
    function __construct($i)
    {
        $this->data = range($i, $i * 2);
    }
    
    function getPaginationItems($offset, $length)
    {
        return array_slice($this->data, $offset, $length);
    }
    
    function getPaginationItemsCount()
    {
        return count($this->data);
    }
    
}

class PaginationUnitCest
{
    
    public function testSettersAndGetters(UnitTester $I)
    {
        $pagination = Pagination::create();
        
        $currentPageFunction = function ()
        {
            return 1;
        };
        
        $pageUrlFunction = function ($number)
        {
            return 'http://example.com?page=' . $number;
        };
        
        $pagination->setCurrentPageCallback($currentPageFunction);
        $pagination->setPageUrlCallback($pageUrlFunction);
        
        /**
         * Default values
         */
        $I->assertSame($pagination->getItemsPerPage(), 10);
        $I->assertSame($pagination->getPagesCount(), 0);
        $I->assertSame($pagination->getItemsCount(), 0);
        
        /**
         * Modifyed values
         */
        for ($i = 1; $i < 50; $i++)
        {
            foreach ($this->_generateObjects($i) as $object)
            {
                
                /**
                 * Same current url callback
                 */
                $I->assertSame($currentPageFunction(), $pagination->getCurrentPage());
                
                /**
                 * Same page url callback
                 */
                $I->assertSame($pageUrlFunction($i), $pagination->getPageUrl($i));
                
                $pagination->setItemsPerPage($i)
                           ->setItems($object);
                
                $I->assertSame($pagination->getPagesCount(), ceil(($i + 1) / $i));
                $I->assertSame($pagination->getItemsCount(), $i + 1);
                $I->assertSame($pagination->getItemsPerPage(), $i);
                $I->assertSame(
                    $pagination->getItems(0, $i + 1), is_array($object)
                    ? $object
                    : $object->data
                );
                
                /**
                 * Check same type of object items and pagination items
                 */
                $pagination->showItems(
                    function ($items) use ($object, $I, $i)
                    {
                        $I->assertSame(
                            gettype(
                                is_array($object)
                                    ? $object
                                    : $object->getPaginationItems(0, $i + 1)
                            ), gettype($items)
                        );
                    }
                );
                
                $pagination->showPages(
                    function ($pages, Pagination $pagination) use ($object, $I, $i, $pageUrlFunction)
                    {
                        $I->assertTrue(is_array($pages));
                        
                        foreach ($pages as $page)
                        {
                            $I->assertTrue($page instanceof \Zver\Page);
                            $I->assertTrue($page->isValid());
                            
                            if ($page->getNumber() == $pagination->getCurrentPage())
                            {
                                $I->assertTrue($page->isActive());
                            }
                        }
                    }
                );
            }
            
        }
        
    }
    
    public function testPagesCount(UnitTester $I)
    {
        for ($i = 1; $i < 50; $i++)
        {
            $I->assertEquals(
                Pagination::create()
                          ->setItemsPerPage(1)
                          ->setItems(range(0, $i))
                          ->getPagesCount(), count(range(0, $i))
            );
        }
    }
    
    public function testExceptions(UnitTester $I)
    {
        $I->expectException(
            'Zver\Exceptions\Pagination\CurrentPageCallbackNotSetException', function ()
        {
            Pagination::create()
                      ->getCurrentPage();
        }
        );
        
        $I->expectException(
            'Zver\Exceptions\Pagination\PageUrlCallbackNotSetException', function ()
        {
            Pagination::create()
                      ->getPageUrl(1);
        }
        );
        
        $I->expectException(
            '\InvalidArgumentException', function ()
        {
            Pagination::create()
                      ->setItemsPerPage(-10);
        }
        );
        
        $I->expectException(
            '\InvalidArgumentException', function ()
        {
            Pagination::create()
                      ->setItemsPerPage(0);
        }
        );
        
        $I->expectException(
            '\InvalidArgumentException', function ()
        {
            Pagination::create()
                      ->setItems('dsdsd');
        }
        );
    }
    
    protected function _generateObjects($i)
    {
        return [
            range($i, $i * 2),
            new PaginationTestObject($i),
        ];
    }
    
    protected function chainableTest(UnitTester $I)
    {
        
        Pagination::create()
                  ->setCurrentPageCallback(
                      function ()
                      {
                      }
                  )
                  ->setPageUrlCallback(
                      function ()
                      {
                      }
                  )
                  ->setItems([1, 2, 3, 4])
                  ->setItemsPerPage(1)
                  ->showItems(
                      function ()
                      {
                      }
                  )
                  ->showPages(
                      function ()
                      {
                      }
                  );
        
    }
}