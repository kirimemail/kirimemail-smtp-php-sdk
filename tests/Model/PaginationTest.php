<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Tests\Model;

use PHPUnit\Framework\TestCase;
use KirimEmail\Smtp\Model\Pagination;

class PaginationTest extends TestCase
{
    public function testPaginationConstructor()
    {
        $data = [
            'total' => 100,
            'page' => 2,
            'limit' => 10,
            'offset' => 10
        ];

        $pagination = new Pagination($data);

        $this->assertEquals(100, $pagination->getTotal());
        $this->assertEquals(2, $pagination->getPage());
        $this->assertEquals(10, $pagination->getLimit());
        $this->assertEquals(10, $pagination->getOffset());
    }

    public function testPaginationWithNullValues()
    {
        $pagination = new Pagination([
            'total' => null,
            'page' => null,
            'limit' => null,
            'offset' => null
        ]);

        $this->assertNull($pagination->getTotal());
        $this->assertNull($pagination->getPage());
        $this->assertNull($pagination->getLimit());
        $this->assertNull($pagination->getOffset());
    }

    public function testPaginationSetters()
    {
        $pagination = new Pagination();

        $pagination->setTotal(200)
                   ->setPage(3)
                   ->setLimit(25)
                   ->setOffset(50);

        $this->assertEquals(200, $pagination->getTotal());
        $this->assertEquals(3, $pagination->getPage());
        $this->assertEquals(25, $pagination->getLimit());
        $this->assertEquals(50, $pagination->getOffset());
    }

    public function testPaginationToArray()
    {
        $data = [
            'total' => 150,
            'page' => 4,
            'limit' => 15,
            'offset' => 45
        ];

        $pagination = new Pagination($data);
        $array = $pagination->toArray();

        $this->assertEquals($data, $array);
    }

    public function testPaginationJsonSerialization()
    {
        $data = [
            'total' => 300,
            'page' => 5,
            'limit' => 20,
            'offset' => 80
        ];

        $pagination = new Pagination($data);
        $json = json_encode($pagination);
        $decoded = json_decode($json, true);

        $this->assertEquals($data, $decoded);
    }

    public function testPaginationWithEmptyData()
    {
        $pagination = new Pagination();

        $this->assertNull($pagination->getTotal());
        $this->assertNull($pagination->getPage());
        $this->assertNull($pagination->getLimit());
        $this->assertNull($pagination->getOffset());
    }

    public function testPaginationGetTotalPages()
    {
        $pagination = new Pagination(['total' => 100, 'limit' => 10]);
        $this->assertEquals(10, $pagination->getTotalPages());

        $pagination2 = new Pagination(['total' => 95, 'limit' => 10]);
        $this->assertEquals(10, $pagination2->getTotalPages());

        $pagination3 = new Pagination(['total' => 100, 'limit' => 15]);
        $this->assertEquals(7, $pagination3->getTotalPages());
    }

    public function testPaginationGetTotalPagesWithNullLimit()
    {
        $pagination = new Pagination(['total' => 100, 'limit' => null]);
        $this->assertNull($pagination->getTotalPages());
    }

    public function testPaginationGetTotalPagesWithZeroLimit()
    {
        $pagination = new Pagination(['total' => 100, 'limit' => 0]);
        $this->assertNull($pagination->getTotalPages());
    }

    public function testPaginationHasNextPage()
    {
        $pagination = new Pagination(['total' => 100, 'page' => 5, 'limit' => 10]);
        $this->assertTrue($pagination->hasNextPage());

        $pagination2 = new Pagination(['total' => 100, 'page' => 10, 'limit' => 10]);
        $this->assertFalse($pagination2->hasNextPage());

        $pagination3 = new Pagination(['total' => 100, 'page' => 11, 'limit' => 10]);
        $this->assertFalse($pagination3->hasNextPage());
    }

    public function testPaginationHasNextPageWithNullTotalOrLimit()
    {
        $pagination = new Pagination(['page' => 5]);
        $this->assertFalse($pagination->hasNextPage());
    }

    public function testPaginationHasPreviousPage()
    {
        $pagination = new Pagination(['page' => 5]);
        $this->assertTrue($pagination->hasPreviousPage());

        $pagination2 = new Pagination(['page' => 1]);
        $this->assertFalse($pagination2->hasPreviousPage());

        $pagination3 = new Pagination(['page' => 0]);
        $this->assertFalse($pagination3->hasPreviousPage());
    }

    public function testPaginationHasPreviousPageWithNullPage()
    {
        $pagination = new Pagination(['page' => null]);
        $this->assertFalse($pagination->hasPreviousPage());
    }

    public function testPaginationGetNextPage()
    {
        $pagination = new Pagination(['total' => 100, 'page' => 5, 'limit' => 10]);
        $this->assertEquals(6, $pagination->getNextPage());

        $pagination2 = new Pagination(['total' => 100, 'page' => 10, 'limit' => 10]);
        $this->assertNull($pagination2->getNextPage());

        $pagination3 = new Pagination(['page' => null]);
        $this->assertNull($pagination3->getNextPage());
    }

    public function testPaginationGetPreviousPage()
    {
        $pagination = new Pagination(['page' => 5]);
        $this->assertEquals(4, $pagination->getPreviousPage());

        $pagination2 = new Pagination(['page' => 1]);
        $this->assertNull($pagination2->getPreviousPage());

        $pagination3 = new Pagination(['page' => null]);
        $this->assertNull($pagination3->getPreviousPage());
    }
}