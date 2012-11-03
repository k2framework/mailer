K2_Mail
=======

.. contents: Módulo para el envio de correos en K2, ofrece una serie de métodos para configurar y enviar emails con el uso de la lib PHPMailer.

Instalacion
-----------

Solo debemos descargar e instalar la lib en **vendor/K2/Mail** y registrarla en el `AppKernel <https://github.com/manuelj555/k2/blob/master/doc/app_kernel.rst>`_::

    //archivo app/AppKernel.php

    protected function registerNamespaces()
    {
        return array(
            'modules'   => __DIR__ . '/modules/',
            'KumbiaPHP' => __DIR__ . '/../../vendor/kumbiaphp/kumbiaphp/src/',
            'vendor'    => __DIR__ . '/../../vendor/',
            ...
            'K2/Mail'   => __DIR__ . '/modules/',
        );
    }