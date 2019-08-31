<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\RatePage\Tests;

use C4Y\RatePage\RatePageBundle;
use PHPUnit\Framework\TestCase;

class RateBundleTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new RatePageBundle();

        $this->assertInstanceOf('C4Y\RatePage\RatePageBundle', $bundle);
    }
}
