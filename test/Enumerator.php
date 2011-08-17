<?php
require_once __DIR__.'/../lib/Enumerator.php';
class EnumeratorTest extends PHPUnit_Framework_TestCase
{
    function testMap() {
        $actual = w(1, 2, 3)->map(function($i) {
            return $i*2;
        })->toArray(); 
        $this->assertEquals(array(2,4,6), $actual);
    }

    function testReduce() {
        $actual = w(1, 2, 3)->reduce(0, function($r, $i) {
            return $r + $i;
        }); 
        $this->assertEquals(6, $actual);
    }

    function testFindAll() {
        $actual = w(1, 2, 3)->findAll(function($i) {
            return ($i % 2) === 0;
        })->toArray(); 
        $this->assertEquals(array(2), $actual);
    }

    function testCount() {
        $actual = w(1, 2, 3)->count();
        $this->assertEquals(3, $actual);
    }

    function testEachWithIndex() {
        $assert = $this;
        w(1, 2, 3)->eachWithIndex(function($item, $index) use ($assert) {
            if ($index === 2) $assert->assertEquals(3, $item);
        });
    }
}
