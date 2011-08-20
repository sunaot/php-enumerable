<?php
require_once __DIR__.'/Enumerable.php';
require_once __DIR__.'/SortMethod.php';
class EnumeratorBreakException extends Exception {}
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

    function find($callback) {
        $results = null;
        $f = function($item) use (&$results, $callback) {
            if ($callback($item) === true) {
                $results = $item;
                throw new EnumeratorBreakException;
            };
        };
        $this->breakableEach($f);
        return $results;
    }

    function detect($callback) {
        return $this->find($callback);
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

    function first($count = 1) {
        if ($count === 1) {
            return $this->firstAsItself();
        } else {
            return $this->firstAsAliasOfTake($count);
        }
    }

    protected function firstAsItself() {
        $taken = $this->take(1);
        if (empty($taken)) {
            return null;
        } else {
            return array_shift($taken);
        }
    }

    protected function firstAsAliasOfTake($count) {
        return $this->take($count);
    }

    function last() {
        $count = $this->count();
        $dropped = $this->drop($count-1);
        if (empty($dropped)) {
            return null;
        } else {
            return array_shift($dropped);
        }
    }

    function take($count) {
        $results = array();
        $f = function($item) use (&$results, $count) {
            $results[] = $item;
            if (count($results) >= $count) throw new EnumeratorBreakException;
        };
        $this->breakableEach($f);
        return $results;
    }

    function drop($count) {
        $results = array();
        $index = 0;
        $f = function($item) use (&$results, &$index, $count) {
            if ($index >= $count) $results[] = $item;
            $index++;
        };
        $this->iterator->each($f);
        return $results;
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

    function sort() {
        $method = new SortMethod;
        return $this->sortBy($method->compareMethod());
    }

    function sortBy($callback) {
        $ary = $this->toArray();
        $results = usort($ary, function($a, $b) use ($callback) {
            return $callback($a, $b);
        });
        if (!$results) throw new Exception('usort error');
        return new static($ary);
    }

    protected function breakableEach($callback) {
        try {
            $this->iterator->each($callback);
        } catch (EnumeratorBreakException $e) {
            return;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
function w() {
    $argv = func_get_args();
    return new Enumerator($argv);
}
