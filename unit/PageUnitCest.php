<?php

use Zver\Page;

class PageUnitCest
{
    
    public function testExceptions(UnitTester $I)
    {
        $I->expectException(
            'Zver\Exceptions\Page\InvalidPageNumberException', function ()
        {
            Page::create()
                ->setNumber(0);
        }
        );
        
        $I->expectException(
            'Zver\Exceptions\Page\InvalidPageUrlException', function ()
        {
            Page::create()
                ->setUrl(0);
        }
        );
        
        $I->expectException(
            'Zver\Exceptions\Page\InvalidPageNumberException', function ()
        {
            Page::create(0);
        }
        );
        
        $I->expectException(
            'Zver\Exceptions\Page\InvalidPageUrlException', function ()
        {
            Page::create(1, 'd333');
        }
        );
    }
    
    public function testSettersAndGetters(UnitTester $I)
    {
        
        $url = 'http://example.com/page=';
        
        for ($i = 1; $i < 10; $i++)
        {
            $page = Page::create();
            
            $I->assertFalse($page->isValid());
            
            $page->setNumber($i);
            
            $I->assertSame($page->getNumber(), $i);
            
            $I->assertFalse($page->isValid());
            
            $page->setUrl($url . $i);
            
            $I->assertSame($page->getUrl(), $url . $i);
            
            $I->assertTrue($page->isValid());
            
            $I->assertFalse($page->isActive());
            
            $I->assertTrue(
                $page->setActive($i)
                     ->isActive()
            );
            
            $I->assertTrue(
                $page->setActive(true)
                     ->isActive()
            );
            
            $I->assertFalse(
                $page->setActive(0)
                     ->isActive()
            );
            
            $I->assertFalse(
                $page->setActive(false)
                     ->isActive()
            );
            
            $I->assertTrue(
                $page->setActive(-10)
                     ->isActive()
            );
            
            $I->assertTrue(
                $page->setActive('str')
                     ->isActive()
            );
            
        }
        
    }
    
    public function testConstructor(UnitTester $I)
    {
        
        $url = 'http://example.com/page=';
        
        for ($i = 1; $i < 10; $i++)
        {
            $page = Page::create($i, $url . $i, $i);
            
            $I->assertSame($page->getNumber(), $i);
            $I->assertSame($page->getUrl(), $url . $i);
            $I->assertTrue($page->isActive());
        }
        
    }
    
}