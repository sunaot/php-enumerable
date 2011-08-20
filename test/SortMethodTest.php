<?php
require_once __DIR__.'/../lib/SortMethod.php';
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
class SortMethodTest extends PHPUnit_Framework_TestCase 
{
    function testComparePhpPrimitives() {
        $method = new SortMethod;
        $this->assertEquals(-1, $method->comparePhpPrimitives(1, 2));
        $this->assertEquals( 0, $method->comparePhpPrimitives(2, 2));
        $this->assertEquals( 1, $method->comparePhpPrimitives(3, 1));
    }

    function testPolymorphicCompare() {
        $method = new SortMethod;
        $func = $method->polymorphicCompare();
        $this->assertEquals(-1, $func(1, 2));
        $this->assertEquals(-1, $func('a', 'b'));
        $this->assertEquals( 1, $func('z', 'b'));
        $this->assertEquals( 0, $func(1, 1.0)); 
    }

    function testComparableObjectSort() {
        $method = new SortMethod;
        $this->assertEquals(-1, $method->compare(new ComparableInt(1), new ComparableInt(2)));
        $this->assertEquals( 0, $method->compare(new ComparableInt(2), new ComparableInt(2)));
        $this->assertEquals( 1, $method->compare(new ComparableInt(3), new ComparableInt(2)));
    }
}
