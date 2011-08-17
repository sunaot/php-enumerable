<?php
// This is just a sample code to show how you can use Enumerator method implementations 
// with another iteration strategy and data structure.
class AnotherEnumerator implements Enumerable
{
    private $list;
    function each() {
        // yet another each implementation
    }

    static function create(array $ary) {
      $dont_use = array();
      return new Enumerator($dont_use, new self);
    }
}
