<?php

namespace DynamicOOOS\Payum\ISO4217\Tests;

use DynamicOOOS\Payum\ISO4217\Currency;
class CurrencyTest extends \DynamicOOOS\PHPUnit_Framework_TestCase
{
    public function testShouldAllowGetInfoSetInConstructor()
    {
        $currency = new Currency('theName', 'theAlpha3', 'theNumeric', 'theExp', 'theCountry');
        $this->assertSame('theName', $currency->getName());
        $this->assertSame('theAlpha3', $currency->getAlpha3());
        $this->assertSame('theNumeric', $currency->getNumeric());
        $this->assertSame('theExp', $currency->getExp());
        $this->assertSame('theCountry', $currency->getCountry());
    }
}
