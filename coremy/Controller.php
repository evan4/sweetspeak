<?php

namespace Mycms;

use steveclifton\phpcsrftokens\Csrf;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

class Controller
{
    public $imageAllowedExtensions = ['gif', 'png', 'jpg', 'jpeg'];
    
    public $session;
    public $twig;

    public function __construct()
    {
        /* $sessionStorage = new NativeSessionStorage(array(
            "name" => "sweetspeak",
            "cookie_lifetime" => 86400
        ), new NativeFileSessionHandler());
        $this->session = new Session($sessionStorage);
        $this->session->start(); */
        
        $loader = new \Twig\Loader\FilesystemLoader(ROOT.'/template');
        $this->twig = new \Twig\Environment($loader, [
            'cache' => false,
            //'cache' => ROOT.'/cache',
            'auto_reload' => true,
            'debug' => true
        ]);
    }

    public function checkImageExtension($ext)
    {
        if (!in_array($ext, $this->imageAllowedExtensions)) {
            echo json_encode(['error'=> 'Допустимы только фото gif, png, jpg, jpeg']);
            die();
        }
    }

    public function sanitizeText($text)
    {
       return filter_var( $text, FILTER_SANITIZE_STRING);
    }
    
    public function checkEmail($email)
    {
        $filteredEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
        
        if ($filteredEmail) {
            return filter_var($filteredEmail, FILTER_SANITIZE_EMAIL);
        }

        return null;
    }

    public function sanitizePassword($text)
    {
        $pass = $this->sanitizeText( $text );

        if(strlen($pass) >= 9 && preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,15}$/', $pass) ){
            return $pass;
        }

        return false;
    }

    public function checkAjax(): bool
    {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            return true;
        }
        
        return false;
    }

    public function validation(array $data): array
    {
        $result = [
            'data' => [],
            'errors' => []
        ];
        
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'email':
                    if($this->checkEmail($value)){
                        $result['data'][$key] = $value;
                    }else {
                        $result['errors']['auth'] = 'Email или пароль некорректен';
                    }
                    break;
                case 'password':
                    if($this->sanitizePassword($value)){
                        $result['data'][$key] = $value;
                    }else{
                        $result['errors']['auth'] = 'Email или пароль некорректен';
                    }
                    break;
                default:
                    
                    if(is_int($value)) {
                        $result['data'][$key] = intval($value);
                    }else {
                        $result['data'][$key] = $this->sanitizeText($value);
                    }
                   
                    break;
            }
        }
        
        return $result;
        
    }

    public function echoForbittenMessage()
    {
        echo json_encode([
            'error' => 'Доступ запрещен'
        ]);
        die();
    }

    public function sendMail(array $mail)
    {
        // Create Transport
        $https['ssl']['verify_peer'] = FALSE;
        $https['ssl']['verify_peer_name'] = FALSE;
        $https['ssl']['allow_self_signed'] = true;
        // Create the Transport
        $transport = (new \Swift_SmtpTransport($_ENV['MAIL_HOST'], $_ENV['MAIL_PORT'], 
        $_ENV['MAIL_ENCRYPTION']
        ))
        ->setAuthMode('login')
        ->setUsername($_ENV['MAIL_USERNAME'])
        ->setPassword($_ENV['MAIL_PASSWORD'])
        ->setStreamOptions($https);
       
        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);
        
        // Create a message
        $message = (new \Swift_Message($mail['theme']));
        $message->setFrom([$_ENV['MAIL_FROM_ADDRESS'] => $_ENV['MAIL_FROM_NAME']]);
        $message->setTo([$mail['email'] => $mail['name']]);
        $message->setBody($mail['msg'], 'text/html');
        
        // Send the message
        $result = $mailer->send($message);
        return $result;
    }

}
