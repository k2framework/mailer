<?php

namespace K2\Mail;

use KumbiaPHP\Di\Container\ContainerInterface;

require_once __DIR__ . '/phpmailer/class.phpmailer.php';

class Mailer
{

    /**
     * 
     * @var ContainerInterface
     */
    protected $container;

    /**
     *
     * @var PHPMailer
     */
    protected $mailer;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->mailer = new \PHPMailer();
        $this->mailer->Host = $container
                ->getParameter('k2.mailer.host');
        $this->mailer->Username = $container
                ->getParameter('k2.mailer.username');
        $this->mailer->Password = $container
                ->getParameter('k2.mailer.password');
        $this->mailer->Password = $container
                ->getParameter('k2.mailer.password');
    }

    public function setSubject($subject)
    {
        $this->mailer->Subject = $subject;
        return $this;
    }

    public function setBody($body)
    {
        $this->mailer->Body = $body;
        $this->mailer->AltBody = stripslashes($body);
        return $this;
    }

    public function send()
    {
        $result = $this->mailer->send();
        return $result;
    }

    public function getError()
    {
        return $this->mailer->ErrorInfo;
    }

}