<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {

        if(!empty($_SESSION)) {
            header('Location: /cita');
        }

        $alertas = [];
        $auth = new Usuario();

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                $usuario = Usuario::where("email", $auth->email);

                if($usuario) {
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        // autenticar el usuario
                        // session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . ' ' . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        if($usuario->admin === '1') {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('location: /admin');
                        } else {
                            header('location: /cita');
                        }
                        debuguear($_SESSION);
                    }
                } else {
                    Usuario::setAlerta('error', "Usuario No encontrado");
                } 

            }
        } 

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas,
            "usuario" => $auth
        ]);
    } 

    public static function logout() {
        // session_start();

        $_SESSION = [];

        if (empty($_SESSION)) {
            header('Location: /');
        }
    } 

    public static function olvide(Router $router) {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            
            if( empty($alertas) ) {
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === '1') {
                    $usuario->crearToken();
                    $usuario->guardar();

                    // TODO: Enviar el email

                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    Usuario::setAlerta('exito', 'Revisa tu email');
                    $alertas = Usuario::getAlertas();


                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                    $alertas = Usuario::getAlertas();
                }
            }
        }

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    } 

    public static function recuperar( Router $router) {

        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta("error", "token no valido");
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;
                
                $usuario->guardar();

                if($usuario) {
                    header('Location:/');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            "alertas" => $alertas,
            "error" => $error,
        ]);
    }

    public static function crear(Router $router) {
        $usuario = new Usuario();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === "POST") {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)) {
                echo "Pasaste la validacion";
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el password
                    $usuario->hashPassword();

                    // Generar un token unico
                    $usuario->crearToken();

                    // Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    // Crear el usuario

                    $resultado = $usuario->guardar();

                    if($resultado) {
                        header("Location: /mensaje");
                    }
                }
            }
        }

        $router->render('auth/crear-cuenta', [
            "usuario" => $usuario,
            "alertas" => $alertas
        ]);
    } 
   
    public static function mensaje( Router $router) {
        $router->render("auth/mensaje"); 
    }

    public static function confirmar(Router $router) {
        $alertas = [];
        $token = s($_GET["token"]);
        
        $usuario = Usuario::where("token", $token);

        if(empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta("error", "Token no valido");
        } else {
            $usuario->confirmado = 1;
            $usuario->token = '';
            $usuario->guardar();
            Usuario::setAlerta("exito", "cuenta comprobada correctamente");
        }

        $alertas = Usuario::getAlertas();
        $router->render("auth/confirmar-cuenta", [
            "alertas" => $alertas
        ]);
    }
}