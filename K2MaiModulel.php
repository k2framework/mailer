<?php

namespace K2\Mail;

class K2MaiModulel extends \K2\Kernel\Module
{

    public function init()
    {
        $this->container->set('k2_mailer', function($c){
            return new Mailer($c);
        });
    }

}