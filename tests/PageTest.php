<?php

use Zver\Page;

class PageTest extends PHPUnit\Framework\TestCase
{

    use \Zver\Package\Helper;

    public function testExceptions()
    {
        $this->expectException('Zver\Exceptions\Page\InvalidPageNumberException');
        Page::create()
            ->setNumber(0);
    }

    public function testExceptions3()
    {
        $this->expectException('Zver\Exceptions\Page\InvalidPageNumberException');
        Page::create(0);
    }

    public function testSettersAndGetters()
    {

        $url = 'http://example.com/page=';

        for ($i = 1; $i < 10; $i++) {
            $page = Page::create();

            $this->assertFalse($page->isValid());

            $page->setNumber($i);

            $this->assertSame($page->getNumber(), $i);

            $this->assertFalse($page->isValid());

            $page->setUrl($url . $i);

            $this->assertSame($page->getUrl(), $url . $i);

            $this->assertTrue($page->isValid());

            $this->assertFalse($page->isActive());

            $this->assertTrue(
                $page->setActive($i)
                     ->isActive()
            );

            $this->assertTrue(
                $page->setActive(true)
                     ->isActive()
            );

            $this->assertFalse(
                $page->setActive(0)
                     ->isActive()
            );

            $this->assertFalse(
                $page->setActive(false)
                     ->isActive()
            );

            $this->assertTrue(
                $page->setActive(-10)
                     ->isActive()
            );

            $this->assertTrue(
                $page->setActive('str')
                     ->isActive()
            );

        }

    }

    public function testConstructor()
    {

        $url = 'http://example.com/page=';

        for ($i = 1; $i < 10; $i++) {
            $page = Page::create($i, $url . $i, $i);

            $this->assertSame($page->getNumber(), $i);
            $this->assertSame($page->getUrl(), $url . $i);
            $this->assertTrue($page->isActive());
        }

    }

}