<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class auth extends conexion{

    public function login ($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        if (!isset($datos['usuario']) || !isset($datos['Password'])){
            //error con los datos
            return $_respuestas->error_400();
        }else{
            //datos correctos
            $usuario = $datos['usuario'];
            $password = $datos['Password'];
            $password = parent::encriptar($password);
            $datos = $this->datosUsuario($usuario);
            if($usuario){
                //verifica contraseña
                if ($password == $datos[0]['Password']){
                    if($datos[0]['Estado']=="Activo"){
                        //crea un token
                        $verifica = $this->creaToken($datos[0]['usuarioid']);
                        if($verifica){
                            //si se guardo
                            $result = $_respuestas->response;
                            $result["result"] = array (
                                "token"=>$verifica
                            );
                            return $result;
                        } else {
                            // si no se guardo
                            return $_respuestas->error_500("Error interno no fue posible guardar");    
                        }
                    } else {
                        //Usuario inactivo
                        return $_respuestas->error_200("Usuario inactivo");
                    }
                } else {
                    print_r($password);
                    return $_respuestas->error_200("el password introducido es invalido");
                }
            } else {
                //si no existe el usuario
                return $_respuestas->error_200("El usuario $usuario no existe");
            }
        }
    }

    private function datosUsuario($correo){
        $query ="SELECT usuarioid,Password,Estado from usuarios where usuario='$correo'";
        $datos= parent::obtenerDatos($query);
        if (isset($datos[0]['usuarioid'])){
            return $datos;
        }else {
            return 0;

        }

    }

    private function creaToken($usuarioid){
        $val=true;
        $token=bin2hex(openssl_random_pseudo_bytes(16,$val));
        $date=date("Y-m-d H:i");
        $estado="Activo";
        $query= "INSERT INTO usuarios_token (UsuarioId,Token,Estado,Fecha) values ('$usuarioid','$token','$estado','$date')";
        $verifica = parent::guardar($query);
        if ($verifica){
            return $token;
        } else {
            return 0;
        }
    }
}


?>
