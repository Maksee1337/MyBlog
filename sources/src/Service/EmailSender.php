<?php


namespace App\Service;

use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
/**
 * Class EmailSender
 * @package App\Service
 */

class EmailSender
{
    protected $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param string $subject
     * @param string $html
     * @param string $to
     */
    public function send(string $subject, string $html, string $to)
    {
        $email = new Email();
        $email->from('burm.courses@gmail.com');
        $email->to($to);
        $email->subject($subject);
        $email->html($html);
        $this->mailer->send($email);
    }
}