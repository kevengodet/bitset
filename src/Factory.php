<?php

namespace Adagio\BitSet;

use Adagio\BitSet\Adapter\NativeBitSet;
use Adagio\BitSet\Adapter\GmpBitSet;
use Adagio\BitSet\Adapter\ArrayBitSet;

final class Factory
{
    // Cannot instantiate this class
    private function __construct() {}

    /**
     * Return the best BitSet implementation based on installed extensions
     *
     * @param int $bits
     *
     * @return BitSet
     */
    static public function create($bits = 64)
    {
        $class = self::findBestClass();

        return new $class($bits);
    }

    /**
     * Returns a new instance of BitSet based on the provided string
     *
     * @param $str
     *
     * @return BitSet
     */
    static public function fromString($str)
    {
        $class = self::findBestClass();

        return $class::fromString($str);
    }

    /**
     * Returns a new Bitset instance based on the input array. All positive integers
     * within the array values are considered positions of set bits
     *
     * @param array $inputArray
     *
     * @return BitSet
     */
    static public function fromArray(array $inputArray)
    {
        $class = self::findBestClass();

        return $class::fromString($inputArray);
    }

    /**
     * Returns a new instance of BitSet based on the provided string
     *
     * @param $str
     *
     * @return BitSet
     */
    static public function fromRawValue($str)
    {
        $class = self::findBestClass();

        return $class::fromRawValue($str);
    }

    /**
     *
     * @return string
     */
    static private function findBestClass()
    {
        return ArrayBitSet::class;
    }
}
