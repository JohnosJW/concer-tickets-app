<?php

namespace Tests\Unit;


use App\RandomOrderConfirmationNumberGenerator;
use Tests\TestCase;

/**
 * Class RandomOrderConfirmationNumberGeneratorTest
 * @package Tests\Unit
 */
class RandomOrderConfirmationNumberGeneratorTest extends TestCase
{
    // Must be 24 characters long
    /** @test */
    public function testMustBe24CharactersLong()
    {
        /** @var  $generator */
        $generator = new RandomOrderConfirmationNumberGenerator;

        /** @var  $confirmationNumber */
        $confirmationNumber = $generator->generate();

        $this->assertEquals(24, strlen($confirmationNumber));
    }

    // Can only contain uppercase letters and numbers
    /** @test */
    public function testCanOnlyContainUppercaseLettersAndNumbers()
    {
        /** @var  $generator */
        $generator = new RandomOrderConfirmationNumberGenerator;

        /** @var  $confirmationNumber */
        $confirmationNumber = $generator->generate();

        $this->assertRegExp('/^[A-Z0-9]+$/', $confirmationNumber);
    }

    // Cannot contain ambiguous characters
    /** @test */
    public function testCanNotContainAmbiguousCharacters()
    {
        /** @var  $generator */
        $generator = new RandomOrderConfirmationNumberGenerator;

        /** @var  $confirmationNumber */
        $confirmationNumber = $generator->generate();

        $this->assertFalse(strpos($confirmationNumber, '1'));
//        $this->assertFalse(strpos($confirmationNumber, 'I'));
        $this->assertFalse(strpos($confirmationNumber, '0'));
    }

    // All order confirmation numbers must be unique
    /** @test */
    public function testConfirmationNumbersMustBeUnique()
    {
        /** @var  $generator */
        $generator = new RandomOrderConfirmationNumberGenerator;

        /** @var  $confirmationNumbers */
        $confirmationNumbers = array_map(function ($i) use ($generator) {
            return $generator->generate();
        }, range(1, 100));

        $this->assertCount(100, array_unique($confirmationNumbers));
    }
}
