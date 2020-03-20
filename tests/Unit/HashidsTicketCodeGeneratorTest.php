<?php


namespace Tests\Unit;


use App\HashidsTicketCodeGenerator;
use App\Ticket;
use Tests\TestCase;

/**
 * Class HashidsTicketCodeGeneratorTest
 * @package Tests\Unit
 */
class HashidsTicketCodeGeneratorTest extends TestCase
{
    /** @test */
    public function testTicketCodesAreAtLeast6CharactersLong()
    {
        /** @var  $ticketCodeGenerator */
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('testsalt1');

        /** @var  $code */
        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertTrue(strlen($code) >= 6);
    }

    /** @test */
    public function testTicketCodesCanOnlyContainsUppercaseLetters()
    {
        /** @var  $ticketCodeGenerator */
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('testsalt1');

        /** @var  $code */
        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertRegExp('/^[A-Z]+$/', $code);
    }

    /** @test */
    public function testTicketCodesForTheSameTicketIdAreTheSame()
    {
        /** @var  $ticketCodeGenerator */
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('testsalt1');

        /** @var  $code */
        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertEquals($code1, $code2);
    }

    /** @test */
    public function testTicketCodesForDifferentTicketIdAreDifferent()
    {
        /** @var  $ticketCodeGenerator */
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('testsalt1');

        /** @var  $code */
        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 2]));

        $this->assertNotEquals($code1, $code2);
    }

    /** @test */
    public function testTicketCodesGeneratedWithDifferentSaltsAreDifferent()
    {
        /** @var  $ticketCodeGenerator1 */
        $ticketCodeGenerator1 = new HashidsTicketCodeGenerator('testsalt1');

        /** @var  $ticketCodeGenerator2 */
        $ticketCodeGenerator2 = new HashidsTicketCodeGenerator('testsalt2');

        /** @var  $code */
        $code1 = $ticketCodeGenerator1->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator2->generateFor(new Ticket(['id' => 1]));

        $this->assertNotEquals($code1, $code2);
    }
}