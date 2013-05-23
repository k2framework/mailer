K2_Mail
=======
Módulo para el envio de correos en K2, ofrece una serie de métodos para configurar y enviar emails con el uso de la lib PHPMailer.

Instalacion
-----------

la instalación más sencilla es mediante composer, agregar el paquete al composer.json del proyecto:

.. code-block:: json

    {
        "require" : {
            "k2/mailer": "dev-master"
        }
    }
                        
                        
Ejecutar el comando:

::
    
    composer install
    
    
Luego de tener los archivos descargados correctamente se debe agregar el módulo en el app/config/modules.php:

```php

<?php //archivo app/config/modules.php

/* * *****************************************************************
 * Iinstalación de módulos
 */
App::modules(array(
    '/' => include APP_PATH . '/modules/Index/config.php',
    include composerPath('k2/mailer', 'K2/Mailer'),
));
```

Con esto ya hemos registrado el módulo en nuestra aplicación.

Configuracion
-------------

En el archivo **app/config/config.ini** debemos crear la configuración de conexion a la cuenta de correo, estos son los parametros disponibles:

```php

;archivo app/config/config.ini

[k2_mailer]
debug = On|Off ;opcional, habilita el módo debug para ve mensajes de error en desarrollo.
transport = smtp|sendmail|mail|qmail|gmail ;parametro obligarotio, debe tener alguna de esas opciones.
host = ;servidor de correo al que nos vamos a conectar ;opcional, solo si es smtp
port = ;puerto de la conexion al servidor de correo. ;opcional, solo si es smtp
fromname = Nombre del Remitente ;Obligatorio
fromemail = correo@dominio.com ;correo del remitente, Obligatorio
username = nombre de usuario ;opcional, solo si es smtp
password = clave de usuario ;opcional, solo si es smtp
enable = On ;indica si se envia ó no el correo, ideal para pruebas sin envio de correo. Opcional, On por defecto
bcc[] = correo_oculto@dominio.com ;dir de correo a la que le llegan todos los correos enviados. Opcional
```

Con esto ya podremos usar el servicio de envio de correos.

Ejemplo de Uso:
---------------
```php

<?php

namespace Registro\Controller;

use K2\Kernel\App;
use K2\Mailer\Mailer;
use K2\Kernel\Controller\Controller;
use K2\Mailer\Exception\MailerException;

class RegistroController extends Controller
{
    protected function send(Mailer $mailer)
    {
        try{
            if ( $mailer->send() )
            {
                App::get("flash")->success("Se envió el correo exitosamente...!!!");
            }else{
                App::get("flash")->warning("Nó se pudo enviar el correo");
            }

        }catch(MailerException $e){
            App::get("flash")->error("Error al enviar el correo: " . $e->getMessage());
        }
    }

    public function correoBasico_action()
    {

        $mailer = App::get("k2_mailer")
                            ->setSubject("Este es el asunto del correo...!!!")
                            ->setBody("<h2>Título mensaje</h2><p>Contenido del mensaje...</p>")
                            ->addRecipient('correo@gmail.com');
        
        $this->send($mailer);
    }

    public function enviarCorreo2_action($usuarioId)
    {
         $usuario = Usuarios::findByID($usuarioId);

        //obtenemos el contenido de la url email_templates/usuarios/registro/{id}
        //el cual es el html que se enviará por correo.

        $response = $this->getRouter()->forward("email_templates/usuarios/registro/$usuarioId");

        if ( 200 === $response->getStatus() ){ //si la respuesta es exitosa.
            $email = App::get("k2_mailer")
                                ->setSubject("Registro Exitoso")
                                ->setBody($response); //tambien puede recibir un objeto Response

            $email->addRecipient($usuario->email, $usuario->nombres);

            $this->send($mailer);
            
        }else{ //si hubo un error.
            App::get("flash")->error("No se Pudo enviar el Correo...!!!");
        }
    }
    
    public function usandoTwig_action($usuarioId)
    {
         $usuario = Usuarios::findByID($usuarioId);
         
         //acá se hace uso del método fromView de la clase Mailer, este método busca una vista twig y carga los
         //bloques subject y html para cargar el asunto y el cuerpo del mensaje para el correo respectivamente.
         //el segundo parametro del método fromView son las variables que se pasan a la vista.
         
         $mailer = App::get("k2_mailer")
                            ->addRecipient($usuario->email, $usuario->nombres)
                            ->fromView("@Modulo/vista.twig", array(
                                'usuario' => $usuario,
                            ));
        
        $this->send($mailer);
         
    }
}
```
