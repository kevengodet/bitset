<?php

namespace Adagio\BitSet;

interface BitSet
{
    /**
     * Performs a logical AND of target bit set with provided object
     *
     * @param BitSet $set
     *
     * @return void
     */
    public function andOp(BitSet $set);

    /**
     * Clears all bits in this object whose bit is set in the provided object
     *
     * @param BitSet $set
     *
     * @return void
     */
    public function andNotOp(BitSet $set);

    /**
     * Returns the number of true bits
     *
     * @return int
     */
    public function cardinality();

    /**
     * Sets all bits to false
     *
     * @throws OutOfRangeException
     *
     * @param int $indexOrFromIndex
     * @param int $toIndex
     *
     * @return void
     */
    public function clear($indexOrFromIndex = null, $toIndex = null);

    /**
     * Returns the bool value of the bit at the specified index
     *
     * @throws OutOfRangeException
     *
     * @param int $index
     *
     * @return boolean
     */
    public function get($index);

    /**
     *
     * @return string
     */
    public function getRawValue();

    /**
     * Determines if the provided value has any bits set to true that are also true in this object
     *
     * @param BitSet $set
     *
     * @return boolean
     */
    public function intersects(BitSet $set);

    /**
     * Determines if this value contains no bits
     *
     * @return boolean
     */
    public function isEmpty();

    /**
     * Returns the highest set bit plus one
     *
     * @return int
     */
    public function length();

    /**
     * Returns the index of the next bit after the provided index that is set to false
     *
     * @throws InvalidArgumentException
     *
     * @param int $index
     *
     * @return int|false
     */
    public function nextClearBit($index);

    /**
     * Returns the index of the next bit after the provided index that is set to true
     *
     * @throws InvalidArgumentException
     *
     * @param int $index
     *
     * @return int|false
     */
    public function nextSetBit($index);

    /**
     * Performs a logical OR of this object with the provided argument object
     *
     * @param BitSet $set
     *
     * @return void
     */
    public function orOp(BitSet $set);

    /**
     * Returns the index of the previous bit before the provided index that is set to false
     *
     * @throws InvalidArgumentException
     *
     * @param int $index
     *
     * @return int|false
     */
    public function previousClearBit($index);

    /**
     * Returns the index of the previous bit before the provided index that is set to true
     *
     * @throws InvalidArgumentException
     *
     * @param int $index
     *
     * @return int|false
     */
    public function previousSetBit($index);

    /**
     * Sets the bits from the specified index or range to true
     *
     * @throws OutOfRangeException
     *
     * @param int $indexOrFromIndex
     * @param int $toIndex
     *
     * @return void
     */
    public function set($indexOrFromIndex = -1, $toIndex = 0);

    /**
     * Returns the number of bits of space in use
     *
     * @return int
     */
    public function size();

    /**
     * Returns a new instance of BitSet based on the provided string
     *
     * @param $str
     *
     * @return BitSet
     */
    static public function fromString($str);

    /**
     * Returns a new Bitset instance based on the input array. All positive integers
     * within the array values are considered positions of set bits
     *
     * @param array $inputArray
     *
     * @return BitSet
     */
    static public function fromArray(array $inputArray);

    /**
     * Returns a new instance of BitSet based on the provided string
     *
     * @param $str
     *
     * @return BitSet
     */
    static public function fromRawValue($str);

    /**
     * Returns the on bits as an array
     *
     * @return array
     */
    public function toArray();

    /**
     * Performs an XOR operation against the current object bit set with the specified argument
     *
     * @param BitSet $set
     *
     * @return void
     */
    public function xorOp(BitSet $set);

    /**
     * Returns a human-readable string representation of the bit set
     *
     * @return string
     */
    public function __toString();
}
