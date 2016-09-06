<?php

namespace Adagio\Bitset\Test\Adapter;

use Adagio\BitSet\Adapter\ArrayBitSet;

class ArrayBitSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Performs a logical AND of target bit set with provided object
     */
    public function testAndOp()
    {
        $b = $this->bitset(); // 64 bits is fine
        $b->set(2);
        $b->set(6);
        $c = $this->bitset();
        $c->set(2);
        $c->set(50);
        $b->andOp($c);
        
        $this->assertEquals("0010000000000000000000000000000000000000000000000000000000000000", (string) $b);
    }
    
    public function testAndNotOp()
    {
        $b = $this->bitset(); // 64 bits is fine
        $b->set(2);
        $b->set(6);
        $c = $this->bitset();
        $c->set(2);
        $c->set(50);
        $b->andNotOp($c);
        
        $this->assertEquals("0000001000000000000000000000000000000000000000000000000000000000", (string) $b);
    }
    
    public function testCardinality()
    {
        $b = $this->bitset(); // 64 bits is fine

        $this->assertEquals(0, $b->cardinality());

        $b->set(2);
        $b->set(6);
        $b->set(50);
        
        $this->assertEquals(3, $b->cardinality());
    }
    
    public function testClear()
    {
        $b = $this->bitset(); // 64 bits is fine
        $b->set(2);
        $b->set(6);
        $b->set(20);
        
        $b->clear(7);
        
        $this->assertEquals("0010001000000000000010000000000000000000000000000000000000000000", (string) $b);
        
        $b->clear(2);
        
        $this->assertEquals("0000001000000000000010000000000000000000000000000000000000000000", (string) $b);
        
        $b->clear(4, 10);
        
        $this->assertEquals("0000000000000000000010000000000000000000000000000000000000000000", (string) $b);        

        $b->set(2);
        $b->set(6);
        $b->set(10);
        
        $b->clear(-1, 0);
        
        $this->assertEquals("0000000000000000000000000000000000000000000000000000000000000000", (string) $b);        
        
        $e = null;
        try {
            $b->clear(70);
        } catch (\Exception $e) {}
        
        $this->assertInstanceOf(\OutOfRangeException::class, $e);
    }

    public function testGet()
    {
        $b = $this->bitset(); // 64 bits is fine
        $b->set(2);
        $b->set(6);
        $b->set(20);

        $this->assertEquals(0, $b->get(1));
        $this->assertEquals(1, $b->get(2));
        $this->assertEquals(0, $b->get(3));
        $this->assertEquals(0, $b->get(4));
        $this->assertEquals(0, $b->get(5));
        $this->assertEquals(1, $b->get(6));
        
        $e = null;
        try {
            $b->get(70);
        } catch (\Exception $e) {}
        
        $this->assertInstanceOf(\OutOfRangeException::class, $e);
    }
    
    public function testIntersects()
    {
        $a = $this->bitset();
        $a->set(2);
        $a->set(3);
        $a->set(5);
        $a->set(7);
        $a->set(11);
        
        $b = $this->bitset();
        $b->set(5);
        $b->set(10);
        $b->set(15);
        $b->set(20);
        $b->set(25);
        
        $c = $this->bitset();
        $c->set(4);
        $c->set(6);
        $c->set(8);
        $c->set(9);
        $c->set(10);
        
        $this->assertTrue($a->intersects($b));
        $this->assertFalse($a->intersects($c));
        $this->assertTrue($b->intersects($c));
    }
    
    public function testIsEmpty()
    {
        $a = $this->bitset();
        $b = $this->bitset();
        $b->set(1);
        $c = $this->bitset();
        $c->set(1);
        $c->clear(1);
        
        $this->assertTrue($a->isEmpty());
        $this->assertFalse($b->isEmpty());
        $this->assertTrue($c->isEmpty());
    }
    
    public function testLength()
    {
        $a = $this->bitset();
        $b = $this->bitset();
        $b->set(0);
        $c = $this->bitset();
        $c->set(63);
        
        $this->assertEquals(0, $a->length());
        $this->assertEquals(1, $b->length());
        $this->assertEquals(64, $c->length());
    }

    public function testNextClearBit()
    {
        $b = $this->bitset();
        $b->set(1);
        $b->set(10);
        $b->set(63);
        
        $this->assertEquals(0, $b->nextClearBit(0));
        $this->assertEquals(2, $b->nextClearBit(1));
        $this->assertEquals(2, $b->nextClearBit(2));
        $this->assertEquals(false, $b->nextClearBit(63));
        
        $e = null;
        try {
            $b->nextClearBit(64);
        } catch (\Exception $e) {}
        
        $this->assertInstanceOf(\InvalidArgumentException::class, $e);
    }

    public function testNextSetBit()
    {
        $b = $this->bitset();
        $b->set(1);
        $b->set(10);
        $b->set(60);
        
        $this->assertEquals(1, $b->nextSetBit(0));
        $this->assertEquals(1, $b->nextSetBit(1));
        $this->assertEquals(10, $b->nextSetBit(2));
        $this->assertEquals(false, $b->nextSetBit(63));
        
        $e = null;
        try {
            $b->nextSetBit(64);
        } catch (\Exception $e) {}
        
        $this->assertInstanceOf(\InvalidArgumentException::class, $e);
    }
    
    public function testOrOp()
    {
        $b = $this->bitset(); // 64 bits is fine
        $b->set(2);
        $b->set(6);
        $c = $this->bitset();
        $c->set(2);
        $c->set(20);
        $b->orOp($c);
        
        $this->assertEquals("0010001000000000000010000000000000000000000000000000000000000000", (string) $b);
    }

    public function testPreviousClearBit()
    {
        $b = $this->bitset();
        $b->set(1);
        $b->set(10);
        $b->set(63);
        
        $this->assertEquals(0, $b->previousClearBit(0));
        $this->assertEquals(0, $b->previousClearBit(1));
        $this->assertEquals(2, $b->previousClearBit(2));
        $this->assertEquals(62, $b->previousClearBit(63));
        
        $e = null;
        try {
            $b->previousClearBit(-1);
        } catch (\Exception $e) {}
        
        $this->assertInstanceOf(\InvalidArgumentException::class, $e);
    }

    public function testPreviousSetBit()
    {
        $b = $this->bitset();
        $b->set(1);
        $b->set(10);
        $b->set(63);
        
        $this->assertEquals(false, $b->previousSetBit(0));
        $this->assertEquals(1, $b->previousSetBit(1));
        $this->assertEquals(1, $b->previousSetBit(2));
        $this->assertEquals(63, $b->previousSetBit(63));
        
        $e = null;
        try {
            $b->previousSetBit(-1);
        } catch (\Exception $e) {}
        
        $this->assertInstanceOf(\InvalidArgumentException::class, $e);
    }
    
    public function testSet()
    {
        $b = $this->bitset(); // 64 bits is fine

        $b->set(20);
        $this->assertEquals("0000000000000000000010000000000000000000000000000000000000000000", (string) $b);        

        $b->set(6);
        $this->assertEquals("0000001000000000000010000000000000000000000000000000000000000000", (string) $b);

        $b->set(2);
        $this->assertEquals("0010001000000000000010000000000000000000000000000000000000000000", (string) $b);
        
        $b->set(8, 18);
        $this->assertEquals("0010001011111111111010000000000000000000000000000000000000000000", (string) $b);

        $b->set();
        $this->assertEquals("1111111111111111111111111111111111111111111111111111111111111111", (string) $b);
        
        $e = null;
        try {
            $b->set(64);
        } catch (\Exception $e) {}
        
        $this->assertInstanceOf(\OutOfRangeException::class, $e);
    }
    
    public function testSize()
    {        
        $a = $this->bitset();
        $b = $this->bitset(62);
        $c = $this->bitset(128);
        
        $this->assertEquals(64, $a->size());
        $this->assertEquals(62, $b->size());
        $this->assertEquals(128, $c->size());
    }
    
    public function testFromString()
    {
        $a = ArrayBitSet::fromString($s = '0010001011111111111010000000000000000000000000000000000000000000');
        $this->assertEquals(64, $a->size());
        $this->assertEquals($s, (string) $a);
        $this->assertEquals(14, $a->cardinality());

        $b = ArrayBitSet::fromString($t = '00100010111111111110100000000000000000000000000000000000000000');
        $this->assertEquals(64, $b->size());
        $this->assertEquals($s, (string) $b);

        $c = ArrayBitSet::fromString('');
        $this->assertEquals(64, $c->size());
        $this->assertEquals('0000000000000000000000000000000000000000000000000000000000000000', (string) $c);
    }

    public function testFromArray()
    {
        $bits = range(8, 18);
        $bits[] = 2;
        $bits[] = 6;
        $bits[] = 20;
        $a = ArrayBitSet::fromArray($bits);
        $this->assertEquals(24, $a->size());
        $this->assertEquals('001000101111111111101000', (string) $a);
        $this->assertEquals(14, $a->cardinality());

        $c = ArrayBitSet::fromArray([]);
        $this->assertEquals(64, $c->size());
        $this->assertEquals('0000000000000000000000000000000000000000000000000000000000000000', (string) $c);
    }

    public function testToArray()
    {
        $bits = range(8, 18);
        $bits[] = 2;
        $bits[] = 6;
        $bits[] = 20;
        $a = ArrayBitSet::fromArray($bits);
        $this->assertEquals($bits, array_values($a->toArray()));

        $b = ArrayBitSet::fromString($s = '');
        $this->assertEquals([], $b->toArray());
    }
    
    public function testXorOp()
    {
        $b = $this->bitset(); // 64 bits is fine
        $b->set(2);
        $b->set(6);
        $c = $this->bitset();
        $c->set(2);
        $c->set(20);
        $b->xorOp($c);
        
        $this->assertEquals("0000001000000000000010000000000000000000000000000000000000000000", (string) $b);
    }
    
    public function testToString()
    {
        $b = $this->bitset();
        $this->assertEquals('0000000000000000000000000000000000000000000000000000000000000000', (string) $b);
        
        $c = $this->bitset(8);
        $this->assertEquals('00000000', (string) $b);
    }
    
    /**
     * Instantiate a BitSet for tests purpose
     * 
     * @return BitSet
     */
    protected function bitset($bits = null)
    {
        return is_null($bits) ? new ArrayBitSet : new ArrayBitSet($bits);
    }
}
