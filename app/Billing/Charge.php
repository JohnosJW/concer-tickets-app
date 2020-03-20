<?php


namespace App\Billing;

/**
 * Class Charge
 * @package App\Billing
 */
class Charge
{
    /** @var  */
    private $data;

    /**
     * Charge constructor.
     * @param $data
     */
    public function __construct($data)
    {
       $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function amount()
    {
        return $this->data['amount'];
    }

    /**
     * @return mixed
     */
    public function cardLastFour()
    {
        return $this->data['card_last_four'];
    }
}