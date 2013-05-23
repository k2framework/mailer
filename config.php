<?php

namespace K2\Mailer;

use K2\Mailer\Mailer;

return array(
    'name' => 'K2Mailer',
    'namespace' => __NAMESPACE__,
    'path' => __DIR__,
    'services' => array(
        'k2_mailer' => function($c) {
            return new Mailer($c);
        },
    ),
);


