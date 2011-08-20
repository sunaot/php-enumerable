<?php
class EnumeratorUncomparableException extends Exception {}
class SortMethod
{
    function compareMethod() {
        $methods = $this;
        return function($a, $b) use ($methods) {
            if ($methods->isPhpPrimitives($a, $b)) {
                return $methods->comparePhpPrimitives($a, $b);
            } else if ($methods->isComparable($a, $b)) {
                return $methods->compare($a, $b);
            } else {
                throw new EnumeratorUncomparableException;
            }
        };
    }

    function comparePhpPrimitives($a, $b) {
        return ($a < $b   ? -1 :
               ($a === $b ?  0 :
               ($a > $b   ?  1 : 
                             0))); // ($a, $b) = (1.0, 1)
    }

    function isPhpPrimitives($a, $b) {
        $numeric = function($a,$b) { return (is_numeric($a) and is_numeric($b)); };
        $string = function($a, $b) { return (is_string($a) and is_string($b)); };
        $bool = function($a, $b) { return (is_bool($a) and is_bool($b)); };
        $array = function($a, $b) { return (is_array($a) and is_array($b)); };
        return $numeric($a,$b) or
               $string($a, $b) or
               $bool($a, $b) or
               $array($a, $b);
    }

    function compare($a, $b) {
        return $a->compare($b);
    }

    function isComparable($a, $b) {
        return (is_a($a, 'Comparable') and is_a($b, 'Comparable'));
    }
}
