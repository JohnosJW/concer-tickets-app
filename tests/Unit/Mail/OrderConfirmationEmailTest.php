<?php


namespace Tests\Unit\Mail;


use App\Mail\OrderConfirmationEmail;
use App\Order;
use Tests\TestCase;

/**
 * Class OrderConfirmationEmailTest
 * @package Tests\Unit\Mail
 */
class OrderConfirmationEmailTest extends TestCase
{
    /** @test */
    public function testEmailContainsALinkToTheOrderConfirmationPage()
    {
        /** @var  $order */
        $order = factory(Order::class)->make([
            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);

        /** @var  $email */
        $email = new OrderConfirmationEmail($order);

        /** @var  $rendered */
        $rendered = $this->render($email);

        $this->assertContains(url('/orders/ORDERCONFIRMATION1234'), $rendered);
    }

    /** @test */
    public function testEmailHasASubject()
    {
        /** @var  $order */
        $order = factory(Order::class)->make();

        /** @var  $email */
        $email = new OrderConfirmationEmail($order);

        $this->assertEquals("Your TicketBeast Order", $email->build()->subject);
    }

    /**
     * @param $mailable
     * @return string
     * @throws \Throwable
     */
    private function render($mailable)
    {
        $mailable->build();

        return view($mailable->view, $mailable->buildViewData())->render();
    }
}