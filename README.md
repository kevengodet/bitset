# BitSet [![Build Status](https://travis-ci.org/adagiolabs/bitset.svg?branch=master)](https://travis-ci.org/adagio/bitset)

Drop in replacement for bitset extension.

Also try to reproduce the implementation details of the extension.

## API

The API is the same as in the extension, with the addition of a Factory to use the best available implementation.

Factory:

```php
Factory::create($bits = 64): BitSet
Factory::fromArray(array $array): BitSet
Factory::fromRawValue(string $raw): BitSet
Factory::fromString(string $str): BitSet
```

BitSet adapters:

```php
BitSet::set($from = -1, $to = 0): void
BitSet::get($index): boolean
BitSet::clear($from = -1, $to = 0): void
BitSet::size(): int
BitSet::cardinality(): int
BitSet::isEmpty(): boolean
BitSet::length(): int
BitSet::andNotOp(BitSet $set): void
BitSet::andOp(BitSet $set): void
BitSet::orOp(BitSet $set): void
BitSet::xorOp(BitSet $set): void
BitSet::nextClearBit($index): int|false
BitSet::nextSetBit($index): int|false
BitSet::previousClearBit($index): int|false
BitSet::previousSetBit($index): int|false
BitSet::getRawValue(): string
BitSet::toArray(): array
```

## Usage

Factory:

```php
<?php
use Adagio\BitSet\Factory;

// Factory::create()

$b = Factory::create(); // Default is 64 bits
var_dump($b->__toString()); // string(64) "0000000000000000000000000000000000000000000000000000000000000000"
$b = Factory::create(8);
var_dump($b->__toString()); // string(8) "00000000"

// Factory::fromArray()

$b = Factory::fromArray([1, 6, 17, 2]);
var_dump($b->__toString()); // string(24) "011000100000000001000000"
var_dump($b->toArray()); // array(4) { [0]=>int(1), [1]=>int(2), [2]=>int(6), [3]=>int(17) }

// Factory::fromRawValue()

$b = Factory::fromRawValue(base64_decode('IA=='));
var_dump($b->__toString()); // string(8) "00000100"
var_dump(base64_encode($b->getRawValue())); // string(4) "IA=="


// Factory::fromString()

$b = Factory::fromString('0110');
var_dump($b->__toString()); // string(8) "01100000"
```

Adapters:

```php
<?php
use Adagio\BitSet\Factory;

// BitSet::get()

$b = Factory::create(); // 64 bits is fine
$b->set(5);
var_dump($b->get(5)); // bool(true)
var_dump($b->get(20)); // bool(false)
$b->set(20);
var_dump($b->get(20)); // bool(true)

// BitSet::set()

$b = Factory::create(8);
var_dump($b->__toString()); // string(8) "00000000"
$b->set(2);
$b->set(2, 4);
$b->set(0);
var_dump($b->__toString()); // string(8) "10111000"
$b->set(); // Set all bits on
var_dump($b->__toString()); // string(8) "11111111"

// BitSet::clear()

$b = Factory::create();
$b->set(50);
$b->set(63);
var_dump($b->get(50)); // bool(true)
$b->clear(50);
var_dump($b->get(50)); // bool(false)

// BitSet::cardinality()

$b = Factory::create();
$b->set(5);
$b->set(33);
$b->set(63);
var_dump($b->cardinality()); // 3

// BitSet::size()

$b = Factory::create();
var_dump($b->size()); // int(64)
$b = Factory::create(8);
$b->set(2, 4);
$b->clear(3);
var_dump($b->size()); // int(8)

// BitSet::isEmpty()

$b = Factory::create(); // 64 bits is fine
var_dump($b->isEmpty()); // bool(true)
$b->set(32);
var_dump($b->isEmpty()); // bool(false)

// BitSet::length()

$b = Factory::create(); // 64 bits is fine
$b->set(33);
var_dump($b->length()); // int(34)

// BitSet::andNotOp()

$b = Factory::create(); // 64 bits is fine
$c = Factory::create();
$b->set(2);
$b->set(6);
$c->set(2);
$b->andNotOp($c);
var_dump($b->__toString()); // string(64) "0000001000000000000000000000000000000000000000000000000000000000"

// BitSet::andOp()

$b = Factory::create();
$b->set(2);
$b->set(6);
$c = Factory::create();
$c->set(2);
$c->set(50);
$b->andOp($c);
var_dump($b->__toString()); // string(64) "0010000000000000000000000000000000000000000000000000000000000000"

// BitSet::xorOp()

$b = Factory::create(); // 64 bits is fine
$c = Factory::create();
$b->set(2);
$b->set(6);
$c->set(2);
$b->xorOp($c);
var_dump($b->__toString()); // string(64) "0000001000000000000000000000000000000000000000000000000000000000"

// BitSet::nextClearBit()

$b = Factory::create(); // 64 bits is fine
$b->set(20);
var_dump($b->nextClearBit(20)); // int(21)
var_dump($b->nextClearBit(18)); // int(19)

// BitSet::nextSetBit()

$b = Factory::create(); // 64 bits is fine
$b->set(20);
var_dump($b->nextSetBit(20)); // bool(false)
var_dump($b->nextSetBit(18)); // int(20)

// BitSet::orOp()

$b = Factory::create(); // 64 bits is fine
$c = Factory::create();
$b->set(2);
$b->set(6);
$c->set(2);
$c->set(9);
$b->orOp($c);
var_dump($b->__toString()); // string(64) "0010001001000000000000000000000000000000000000000000000000000000"

// BitSet::previousClearBit()

$b = Factory::create(); // 64 bits is fine
$b->set(20);
$b->set(18);
var_dump($b->previousClearBit(20)); // int(19)
var_dump($b->previousClearBit(18)); // int(17)

// BitSet::previousSetBit()

$b = Factory::create(); // 64 bits is fine
$b->set(20);
$b->set(18);
var_dump($b->previousSetBit(20)); // int(18)
var_dump($b->previousSetBit(18)); // bool(false)
$b->set(1);
var_dump($b->previousSetBit(5)); // int(1)

// BitSet::toArray()

$b = Factory::create(); // 64 bits is fine. tired of seeing this comment yet?
$b->set(5);
$b->set(22);
var_dump($b->toArray()); // array(2) { [0]=> int(5), [1]=> int(22) }

// BitSet::getRawValue()

$b = Factory::create(8);
$b->set(5);
var_dump(base64_encode($b->getRawValue())); // string(4) "IA=="
var_dump(bin2hex($b->getRawValue())); // string(2) "20"
```
