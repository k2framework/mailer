<?php

namespace K2\Mail;

use KumbiaPHP\Kernel\Response;
use \InvalidArgumentException;
use K2\Mail\Exception\MailException;
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
        $this->mailer = new \PHPMailer(true);
        $this->loadParameters();
        $this->mailer->CharSet = $container->getParameter('config.charset') ? : 'UTF-8';
    }

    public function setSubject($subject)
    {
        $this->mailer->Subject = $subject;
        return $this;
    }

    public function addRecipient($recipient, $name = NULL)
    {
        $this->mailer->AddAddress($recipient, $name);
        return $this;
    }

    public function setBody($body, $isHtml = true)
    {
        if ( $body instanceof Response ){
            $isHtml = 0 === strpos('text/html', $body->headers->get('Content-Type'));
            $body = $body->getContent();
        }
        $this->mailer->Body = $body;
        $this->mailer->AltBody = stripslashes($body);
        $this->mailer->isHTML($isHtml);            
        return $this;
    }

    public function send()
    {
        try{
            $result =  $this->mailer->send();
            $this->mailer->clearAllRecipients();      
            $this->mailer->Body = NULL;
            $this->mailer->AltBody = NULL;
            $this->mailer->Subject = NULL;
            return $result;
        }catch(\Exception $e){
            throw new MailException($e->getMessage(), $e->getCode());
        }
    }

    public function getError()
    {
        return $this->mailer->ErrorInfo;
    }

    protected function loadParameters()
    {
         switch (strtolower($this->container->getParameter('k2.mailer.transport'))) {
            case 'smtp':
                $this->mailer->isSMTP();
                $this->mailer->SMTPAuth = true;
                $this->mailer->SMTPSecure = 'ssl';
                if (null == $this->mailer->Port = $this->container
                        ->getParameter('k2.mailer.port'))
                {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.port</b> en el archivo app/config/config.ini</b>");
                }
                if(null == $this->mailer->Host = $this->container
                        ->getParameter('k2.mailer.host'))
                {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.host</b> en el archivo app/config/config.ini</b>");
                }
                if(null == $this->mailer->Username = $this->container
                        ->getParameter('k2.mailer.username'))
                {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.username</b> en el archivo app/config/config.ini</b>");
                }
                if(null == $this->mailer->Password = $this->container
                        ->getParameter('k2.mailer.password'))
                {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.password</b> en el archivo app/config/config.ini</b>");
                }
                break;
            case 'mail':
                $this->mailer->IsMail();
                break;
            case 'qmail':
                $this->mailer->IsQMail();
                break;
            case 'sendmail':
                $this->mailer->IsSendMail();
                break;
            default:
            if ($this->container->hasParameter('k2.mailer.transport')){
                throw new InvalidArgumentException("No se reconoce el valor para el transport en k2.mailer.transport</b> en el archivo app/config/config.ini</b>");                
            }else{
                throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.transport</b> en el archivo app/config/config.ini</b>");                
            }
        }

        if(null == $fromname = $this->container
                        ->getParameter('k2.mailer.fromname'))
        {
            throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.fromname</b> en el archivo app/config/config.ini</b>");
        }
        if(null == $fromemail = $this->container
                        ->getParameter('k2.mailer.fromemail'))
        {
            throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.fromemail</b> en el archivo app/config/config.ini</b>");
        }
        $this->mailer->SetFrom($fromemail, $fromname);
    }

}