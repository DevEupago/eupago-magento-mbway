<?php

namespace Eupago\MbWay\Test\Unit\Block;

use Eupago\MbWay\Block\Info;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{
    private $object;

    protected function setUp(): void
    {
        parent::setUp();
    }
    public function testInfoInstance()
    {
        $this->assertInstanceOf(Info::class, $this->object);
    }
}
