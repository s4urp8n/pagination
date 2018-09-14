<?php

use Zver\Pagination;
use Zver\PaginationInterface;

class PaginationTest extends PHPUnit\Framework\TestCase
{

    use \Zver\Package\Helper;

    public function testSettersAndGetters()
    {
        $pagination = Pagination::create();

        $currentPageFunction = function () {
            return 1;
        };

        $pageUrlFunction = function ($number) {
            return 'http://example.com?page=' . $number;
        };

        $pagination->setCurrentPageCallback($currentPageFunction);
        $pagination->setPageUrlCallback($pageUrlFunction);

        /**
         * Default values
         */
        $this->assertSame($pagination->getItemsPerPage(), 10);
        $this->assertSame($pagination->getPagesCount(), 0);
        $this->assertSame($pagination->getItemsCount(), 0);

        /**
         * Modifyed values
         */
        for ($i = 1; $i < 50; $i++) {
            foreach ($this->_generateObjects($i) as $object) {

                /**
                 * Same page url callback
                 */
                $this->assertSame($pageUrlFunction($i), $pagination->getPageUrl($i));

                $pagination->setItemsPerPage($i)
                           ->setItems($object);

                $this->assertSame($pagination->getPagesCount(), ceil(($i + 1) / $i));
                $this->assertSame($pagination->getItemsCount(), $i + 1);
                $this->assertSame($pagination->getItemsPerPage(), $i);
                $this->assertSame(
                    $pagination->getItems(0, $i + 1), is_array($object)
                    ? $object
                    : $object->data
                );

                /**
                 * Check same type of object items and pagination items
                 */
                $pagination->showItems(
                    function ($items) use ($object, $i) {
                        $this->assertSame(
                            gettype(
                                is_array($object)
                                    ? $object
                                    : $object->getPaginationItems(0, $i + 1)
                            ), gettype($items)
                        );
                    }
                );

                $pagination->showPages(
                    function ($pages, Pagination $pagination) use ($object, $i, $pageUrlFunction) {
                        $this->assertTrue(is_array($pages));

                        foreach ($pages as $page) {
                            $this->assertTrue($page instanceof \Zver\Page);
                            $this->assertTrue($page->isValid());

                            if ($page->getNumber() == $pagination->getCurrentPage()) {
                                $this->assertTrue($page->isActive());
                            }
                        }
                    }
                );
            }

        }

    }

    public function testPagesCount()
    {
        for ($i = 1; $i < 50; $i++) {
            $this->assertEquals(
                Pagination::create()
                          ->setItemsPerPage(1)
                          ->setItems(range(0, $i))
                          ->getPagesCount(), count(range(0, $i))
            );
        }
    }

    public function testExceptions1()
    {
        $this->expectException('Zver\Exceptions\Pagination\CurrentPageCallbackNotSetException');
        Pagination::create()
                  ->getCurrentPage();
    }

    public function testExceptions2()
    {
        $this->expectException('Zver\Exceptions\Pagination\PageUrlCallbackNotSetException');
        Pagination::create()
                  ->getPageUrl(1);
    }

    public function testExceptions3()
    {
        $this->expectException('\InvalidArgumentException');
        Pagination::create()
                  ->setItemsPerPage(-10);
    }

    public function testExceptions4()
    {
        $this->expectException('\InvalidArgumentException');
        Pagination::create()
                  ->setItemsPerPage(0);
    }

    public function testExceptions5()
    {
        $this->expectException('\InvalidArgumentException');
        Pagination::create()
                  ->setItems('dsdsd');
    }

    protected function _generateObjects($i)
    {
        return [
            range($i, $i * 2),
            new PaginationTestObject($i),
        ];
    }

    protected function chainableTest()
    {

        Pagination::create()
                  ->setCurrentPageCallback(
                      function () {
                      }
                  )
                  ->setPageUrlCallback(
                      function () {
                      }
                  )
                  ->setItems([1, 2, 3, 4])
                  ->setItemsPerPage(1)
                  ->showItems(
                      function () {
                      }
                  )
                  ->showPages(
                      function () {
                      }
                  );

    }
}