<?php

namespace Tests\Unit;

use App\Http\Helpers\FileHelper;
use PHPUnit\Framework\TestCase;

class FileHelperTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testPropsToArrayTest()
    {
        $data = [
            ['props1' => 'TEST',],
            ['props1' => 'TEST1','props2' => 'TEST'],
            ['props2' => 'TEST','props3' => 'TEST']
        ];
        $this->assertEquals(['props1','props2','props3'],FileHelper::propsToArray($data));
        $data = [
            ['props1' => 'TEST',]
        ];
        $this->assertEquals(['props1'],FileHelper::propsToArray($data));
        $this->assertTrue(true);
    }
}
