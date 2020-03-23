<?php


namespace App\Mail;


use Illuminate\Support\Facades\Mail;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

/**
 * Class MailBuilder
 * @package App\Mail
 */
class MailBuilder extends Mail
{
    /**
     * @param $mailable
     * @param null $callback
     */
    public static function assertSent($mailable, $callback = null)
    {
       self::sent($mailable, $callback);
    }

    /**
     * @param $mailable
     * @param null $callback
     */
    public static function sent($mailable, $callback = null)
    {

    }
}