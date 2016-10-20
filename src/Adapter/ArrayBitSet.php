<?php

namespace Adagio\BitSet\Adapter;

use Adagio\BitSet\BitSet;
use InvalidArgumentException, OutOfRangeException;

class ArrayBitSet implements BitSet
{
    /**
     *
     * @var array of [position => 1]
     */
    private $bits = [];

    /**
     *
     * @var int
     */
    private $maxLength;

    /**
     * Class constructor
     *
     * @throws InvalidArgumentException
     *
     * @param int $bits # bits max in the set
     */
    public function __construct($bits = 64)
    {
        if ($bits < 0) {
            throw new InvalidArgumentException('The total bits to allocate must be 0 or greater');
        }

        $this->maxLength = $bits;
    }

    /**
     * Performs a logical AND of target bit set with provided object
     *
     * @param BitSet $set
     *
     * @return void
     */
    public function andOp(BitSet $set)
    {
        $this->bits = array_intersect_key($this->bits, array_combine($set->toArray(), $set->toArray()));
    }

    /**
     * Clears all bits in this object whose bit is set in the provided object
     *
     * @param BitSet $set
     *
     * @return void
     */
    public function andNotOp(BitSet $set)
    {
        $this->bits = array_diff_key($this->bits, array_combine($set->toArray(), $set->toArray()));
    }

    /**
     * Returns the number of true bits
     *
     * @return int
     */
    public function cardinality()
    {
        return count($this->bits);
    }

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
    public function clear($indexOrFromIndex = null, $toIndex = null)
    {
	/* Clear all bits and reset */
	if ($indexOrFromIndex == -1 && $toIndex == 0) {
            $this->bits = [];
            return;
        }

        /* Verify the start index is not greater than total bits */
        if ($indexOrFromIndex > $this->maxLength - 1) {
            throw new OutOfRangeException('The requested start index is greater than the total number of bits');
        }

        if (is_null($toIndex)) {
            unset($this->bits[$indexOrFromIndex]);
            return;
        }

        for ($i = $indexOrFromIndex ; $i <= $toIndex ; $i++) {
            if (array_key_exists($i, $this->bits)) {
                unset($this->bits[$i]);
            }
        }
    }

    /**
     * Returns the bool value of the bit at the specified index
     *
     * @throws OutOfRangeException
     *
     * @param int $index
     *
     * @return boolean
     */
    public function get($index)
    {
	/* The bit requested is larger than all bits in this set */
        if ($index > $this->maxLength - 1) {
            throw new OutOfRangeException('The specified index parameter exceeds the total number of bits available');
        }

        if (array_key_exists($index, $this->bits)) {
            return true;
        }

        return false;
    }

    /**
     * Return a little endian hexadecimal representation of the bitset
     *
     * @return string
     */
    public function getHexValue()
    {
        $hex = '';
        $numByte = 0;
        $byte = 0;
        asort($this->bits);
        foreach ($this->bits as $bit) {
            if ($bit > 8 * ($numByte + 1) - 1) {
                $hex .= str_pad(dechex($byte), 2, '0', STR_PAD_LEFT);
                
                // Fill the gaps between the previous bit and $bit
                $gap = max(0, floor($bit / 8) - $numByte - 1);
                $hex .= str_repeat('00', $gap);
                
                $numByte += $gap + 1;
                $byte = 0;
            }
            
            $relativeBit = $bit - 8 * $numByte;
            $byte |= (1 << $relativeBit);
        }
        
        if ($byte) {
            $hex .= str_pad(dechex($byte), 2, '0', STR_PAD_LEFT);
        }
        
        return $hex;
    }

    /**
     *
     * @return string
     */
    public function getRawValue()
    {
        return pack('H*', $this->getHexValue());
    }

    /**
     * Determines if the provided value has any bits set to true that are also true in this object
     *
     * @param BitSet $set
     *
     * @return boolean
     */
    public function intersects(BitSet $set)
    {
        return (bool) count(array_intersect($this->bits, $set->toArray()));
    }

    /**
     * Determines if this value contains no bits
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !(bool) count($this->bits);
    }

    /**
     * Returns the highest set bit plus one
     *
     * @return int
     */
    public function length()
    {
        if (!count($this->bits)) {
            return 0;
        }
        
        end($this->bits);

        return key($this->bits) + 1;
    }

    /**
     * Returns the index of the next bit after the provided index that is set to false
     *
     * @throws InvalidArgumentException
     *
     * @param int $index
     *
     * @return int|false
     */
    public function nextClearBit($index)
    {
        if ($index >= $this->size()) {
            throw new InvalidArgumentException('There are no bits larger than the index provided');
        }

        for ($k = $index ; $k < $this->size() ; $k++) {
            if (!key_exists($k, $this->bits)) {
                return $k;
            }
        }

        return false;
    }

    /**
     * Returns the index of the next bit after the provided index that is set to true
     *
     * @throws InvalidArgumentException
     *
     * @param int $index
     *
     * @return int
     */
    public function nextSetBit($index)
    {
        if ($index >= $this->size()) {
            throw new InvalidArgumentException('There are no bits larger than the index provided');
        }

        for ($k = $this->searchRank($index) ; $k < $this->length() ; $k++) {
            if (key_exists($k, $this->bits)) {
                return $k;
            }
        }

        return false;
    }

    /**
     * Performs a logical OR of this object with the provided argument object
     *
     * @param BitSet $set
     *
     * @return void
     */
    public function orOp(BitSet $set)
    {
        $bits = $set->toArray();
        $this->bits = $this->bits + array_combine($bits, $bits);
    }

    /**
     * Returns the index of the previous bit before the provided index that is set to false
     *
     * @throws InvalidArgumentException
     *
     * @param int $index
     *
     * @return int
     */
    public function previousClearBit($index)
    {
        if ($index < 0) {
            throw new InvalidArgumentException('There are no bits smaller than the index provided (index)');
        }

        for ($k = $index ; $k >= 0 ; $k--) {
            if (!key_exists($k, $this->bits)) {
                return $k;
            }
        }

        return false;
    }

    /**
     * Returns the index of the previous bit before the provided index that is set to true
     *
     * @throws InvalidArgumentException
     *
     * @param int $index
     *
     * @return int
     */
    public function previousSetBit($index)
    {
        if ($index < 0) {
            throw new InvalidArgumentException('There are no bits smaller than the index provided (index)');
        }

        for ($k = $index ; $k >= 0 ; $k--) {
            if (key_exists($k, $this->bits)) {
                return $k;
            }
        }

        return false;
    }

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
    public function set($indexOrFromIndex = -1, $toIndex = 0)
    {
	/* Set all bits */
	if ($indexOrFromIndex == -1 && $toIndex == 0) {
            $bits = range(0, $this->maxLength - 1);
            $this->bits = array_combine($bits, $bits);
            return;
        }

        /* Verify the start index is not greater than total bits */
        if ($indexOrFromIndex > $this->maxLength - 1) {
            throw new OutOfRangeException('The requested start index is greater than the total number of bits');
        }

        if (0 === $toIndex) {
            $this->bits[$indexOrFromIndex] = $indexOrFromIndex;
            return;
        }

        for ($k = $indexOrFromIndex ; $k <= $toIndex ; $k++) {
            $this->bits[$k] = $k;
        }
    }

    /**
     * Returns the number of bits of space in use
     *
     * @return int
     */
    public function size()
    {
        return $this->maxLength;
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
        $len = strlen($str);
        if ($len > 0) {
            $set = new self(8 * ceil($len / 8));
        } else {
            $set = new self;
        }

        for ($i = 0 ; $i < $len ; $i++) {
            /* If the char is explicitly '1', set it as 1. Otherwise, it's 0 */
            if ($str{$i} === '1') {
                $set->set($i);
            }
        }

        return $set;
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
        if (empty($inputArray)) {
            return new self();
        }
        
        $max = max($inputArray);
        $set = new self(8 * ceil($max / 8));
        $r = new \ReflectionObject($set);
        $p = $r->getProperty('bits');
        $p->setAccessible(true);
        $p->setValue($set, array_combine($inputArray, $inputArray));

        return $set;
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

    }

    /**
     * Returns the on bits as an array
     *
     * @return array
     */
    public function toArray()
    {
        return array_keys($this->bits);
    }

    /**
     * Performs an XOR operation against the current object bit set with the specified argument
     *
     * @param BitSet $set
     *
     * @return void
     */
    public function xorOp(BitSet $set)
    {
        $onBits = $set->toArray();
        $bits = array_combine($onBits, $onBits);
        $this->bits = array_diff_key($this->bits + $bits, array_intersect_key($this->bits, $bits));
    }

    /**
     * Returns a human-readable string representation of the bit set
     *
     * @return string
     */
    public function __toString()
    {
        $str = '';
        for ($i = 0 ; $i < $this->maxLength ; $i++) {
            if (key_exists($i, $this->bits)) {
                $str .= '1';
            } else {
                $str .= '0';
            }
        }

        return $str;
    }

    /**
     * Search the index of the given on bit $index or very next on bit
     *
     * @param int $index
     */
    private function searchRank($index)
    {
        $k = $index;
        $end = $this->length();
        while (!key_exists($k, $this->bits) and $k < $end) {
            $k++;
        }

        return $k;
    }
}
