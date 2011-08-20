<?php
require_once __DIR__.'/../lib/Enumerator.php';
require_once __DIR__.'/../lib/Comparable.php';
class ComparableInt implements Comparable
{
    protected $value;
    function __construct($init = 0) {
        $this->value = $init;
    }

    function compare($other) {
        return
        ($this->value === $other->value ? 0 :
        ($this->value < $other->value ?  -1 : 
        ($this->value > $other->value ?   1 : 0)));
    }
}
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

    function testFind() {
        $actual = w(1, 2, 3)->find(function($i) {
            return ($i % 2) !== 0;
        }); 
        $this->assertEquals(1, $actual);
    }

    /**
     * @test
     */
    function FindReturnsNullWhenNotFound() {
        $actual = w(1, 2, 3)->find(function($i) {
            return $i === 5;
        }); 
        $this->assertEquals(null, $actual);
    }

    /**
     * @test
     */
    function FindShouldBreakWithFirstMatch() {
        $counter = $this->getMock('Counter', array('call'));
        $counter->expects($this->once())
                ->method('call');
        // never reach 3 (3 is also match with the condition)
        $actual = w(1, 2, 3)->find(function($i) use ($counter) {
            $matched = (($i % 2) !== 0);
            if ($matched) $counter->call();
            return $matched;
        }); 
        $this->assertEquals(1, $actual);
    }

    function testFindAll() {
        $actual = w(1, 2, 3)->findAll(function($i) {
            return ($i % 2) === 0;
        })->toArray(); 
        $this->assertEquals(array(2), $actual);
    }

    /**
     * @test
     */
    function FindAllReturnsEmptyWhenNotFound() {
        $actual = w(1, 2, 3)->findAll(function($i) {
            return $i === 5;
        })->toArray(); 
        $this->assertEquals(array(), $actual);
    }

    function testFirst() {
        $actual = w(1, 2, 3)->first();
        $this->assertEquals(1, $actual);
    }

    /**
     * @test
     */
    function FirstObjectOfEmptyShouldBeNull() {
        $actual = w()->first();
        $this->assertEquals(null, $actual);
    }

    function testLast() {
        $actual = w(1, 2, 3)->last();
        $this->assertEquals(3, $actual);
    }

    function testTake() {
        $actual = w(1, 2, 3)->take(2);
        $this->assertEquals(array(1,2), $actual);
    }

    function testDrop() {
        $actual = w(1, 2, 3)->drop(1);
        $this->assertEquals(array(2,3), $actual);
    }

    function testCount() {
        $actual = w(1, 2, 3)->count();
        $this->assertEquals(3, $actual);
    }

    function testReject() {
        $actual = w(1, 2, 3)->reject(function($i) {
            return ($i % 2) === 0;
        })->toArray();
        $this->assertEquals(array(1,3), $actual);
    }

    function testEachWithIndex() {
        $assert = $this;
        w(1, 2, 3)->eachWithIndex(function($item, $index) use ($assert) {
            if ($index === 2) $assert->assertEquals(3, $item);
        });
    }

    function testSort() {
        $actual = w(2, 3, 1)->sort()->toArray();
        $this->assertEquals(array(1,2,3), $actual);
    }

    function testObjectSort() {
        $actual = w(new ComparableInt(2), 
                    new ComparableInt(3), 
                    new ComparableInt(1))->sort()->toArray();
        $this->assertEquals(array(new ComparableInt(1),
                                  new ComparableInt(2),
                                  new ComparableInt(3)), $actual);
    }
}
