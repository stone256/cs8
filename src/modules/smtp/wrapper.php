<?php

/**
 * this is simple symfony/mailer wrapper for cs8 
 * composer require symfony/mailer (option 0);
 */

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Email;



class smtp_wrapper
{

    public $smtp;
    protected $mailer;

    function __construct()
    {
        $this->smtp = _config('smtp');
        $this->set_server();
    }

    protected function set_server()
    {
        $transport = (new EsmtpTransport($this->smtp['host'], $this->smtp['port'], false))
            ->setUsername($this->smtp['username'])
            ->setPassword($this->smtp['password']);
        $this->mailer = new Mailer($transport);
    }


    /**
     * email data:
     * $data = [
        'from' => '',
        'to' => '',
        'subject' => '',
        'body' => '',
        'text' => '',
        'html' => '',
        'attachments' => [['path' => '', 'name' => '']],
    ])
     */

    function mail($data)
    {
        //build email
        $email = (new Email())
            ->from($data['from'])
            ->to($data['to'])
            ->subject($data['subject'])
            ->text($data['text'] ?? '')
            ->html($data['html'] ?? '');
        foreach ($data['attachments'] ?? []  as $attach) {
            $email->attachFromPath($attach['path'], $attach['name']);
        }
        //send
        return $this->mailer->send($email);
    }
}
