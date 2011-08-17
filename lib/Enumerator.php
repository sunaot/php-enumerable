<?php
require_once __DIR__.'/Enumerable.php';
class Enumerator
{
    private $ary; // this is hidden property. use each() when you want to access it.
    private $iterator;
    function __construct(array $ary, Enumerable $iterator = null) {
        $this->ary = $ary;
        $this->iterator = is_null($iterator) ? $this : $iterator;
    }

    function each($callback) {
        foreach ($this->ary as $item) {
            $callback($item);
        }
        return $this;
    }

    function eachWithIndex($callback) {
        $this->reduce(0, function($results, $item) use ($callback) {
            $callback($item, $results); 
            return $results + 1;
        });
        return $this;
    }

    function map($callback) {
        $results = array();
        $f = function($item) use (&$results, $callback) {
            $results[] = $callback($item);
        };
        $this->iterator->each($f);
        return new self($results);
    }

    function collect($callback) {
        return $this->map($callback);
    }

    function reduce($initial, $callback) {
        $results = $initial;
        $f = function($item) use (&$results, $callback) {
            $results = $callback($results, $item);            
        };
        $this->iterator->each($f);
        return $results;
    }

    function inject($initial, $callback) {
        return $this->reduce($initial, $callback);
    }

    function findAll($callback) {
        $results = array();
        $f = function($item) use (&$results, $callback) {
            if ($callback($item) === true) $results[] = $item;
        };
        $this->iterator->each($f);
        return new self($results);
    }

    function select($callback) {
        return $this->findAll($callback);
    }

    function reject($callback) {
        $results = array();
        $f = function($item) use (&$results, $callback) {
            if ($callback($item) === false) $results[] = $item;
        };
        $this->iterator->each($f);
        return new self($results);
    }

    function count() {
        return $this->reduce(0, function($results, $item) {
            return $results + 1;
        });
    }

    function to_a() {
        return $this->toArray();
    }

    function entries() {
        return $this->toArray();
    }

    function toArray() {
        $results = array();
        $f = function($item) use (&$results) { $results[] = $item; };
        $this->iterator->each($f);
        return $results;
    }
}
function w() {
    $argv = func_get_args();
    return new Enumerator($argv);
}
