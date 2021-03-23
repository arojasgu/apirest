<?php
    require_once "conexion/conexion.php";
    require_once "respuestas.class.php";

    class pacientes extends conexion {

        //todos los pacientes por grupos
        private $tabla="pacientes";
        private $pacienteid="";
        private $dni="";
        private $nombre="";
        private $direccion="";
        private $codigopostal="";
        private $genero="";
        private $telefono="";
        private $fechanacimiento="0000-00-00";
        private $correo="";
        private $token = "";

        public function listaPacientes($pagina=1){
            $inicio= 0;
            $cantidad=50;
            if($pagina>1){
                $inicio= ($cantidad* ($pagina-1))+1;
                $cantidad=$cantidad *$pagina;
            }

            $query = "SELECT PacienteId,Nombre,DNI,Telefono,correo FROM ".$this->tabla." limit $inicio, $cantidad";
            $datos = parent::obtenerDatos($query);
            return ($datos);
        }

        //Pacientes individuales

        public function obetnerPaciente($id){
            $query = " SELECT * FROM ".$this->tabla." WHERE PacienteId=$id";
            $datos = parent::obtenerDatos($query);
            return ($datos);

        }

        public function recibirPaciente ($json){
            $_respuestas=new respuestas;
            $datos= json_decode($json,true);

                if(!isset($datos['token'])){
                    return $_respuestas->error_401();
                }else{
                    $this->token= $datos['token'];
                    $arrayToken=$this-> buscarToken();
                        if ($arrayToken){
                        } else {
                            return $_respuestas->error_401("Token enviado es inavalido");
                        }

                            if (!isset($datos['nombre'])||!isset($datos['dni'])||!isset($datos['genero'])){
                                return $_respuestas->error_400();
                            } else {
                                $this->nombre=$datos['nombre'];
                                $this->dni=$datos['dni'];
                                $this->genero=$datos['genero'];
                                if(isset($datos['telefono'])){$this->telefono=$datos['telefono'];}
                                if(isset($datos['correo'])){$this->correo=$datos['correo'];}
                                if(isset($datos['direccion'])){$this->direccion=$datos['direccion'];}
                                if(isset($datos['codigopostal'])){$this->codigopostal=$datos['codigopostal'];}
                                if(isset($datos['fechanacimiento'])){$this->fechanacimiento=$datos['fechanacimiento'];}
                                $resp = $this->insertarPaciente();
                
                                    if ($resp){
                                        $_respuesta = $_respuestas->response;
                                        $respuesta["result"]=array(
                                        "pacienteId"=>$resp);
                                        return $respuesta;
                                    }else {
                                        return $_respuestas->error_500();
                                    }
                            }   
                }
           }

        public function insertarPaciente(){
            $query = "INSERT INTO " .$this->tabla." (DNI,Nombre,Direccion,CodigoPostal,Telefono,Genero,FechaNacimiento,Correo)
            values ('".$this->dni ."','".$this->nombre."','".$this->direccion."','".$this->codigopostal."','".$this->telefono."','".$this->genero."','".$this->fechanacimiento."','".$this->correo."')";
            $respuesta = parent::guardarId($query);
            if ($respuesta){
                return $respuesta;
            } else {
                return 0;
            }
        }
        public function updatePaciente($json){
            $_respuestas=new respuestas;
            $datos= json_decode($json,true);
            if(!isset($datos['token'])){
                return $_respuestas->error_401();
            }else{
                $this->token= $datos['token'];
                $arrayToken=$this-> buscarToken();
                if ($arrayToken){
                    if (!isset($datos['pacienteid'])){
                        return $_respuestas->error_400();
                    } else {
                        $this->pacienteid=$datos['pacienteid'];
                        if(isset($datos['nombre'])){$this->nombre=$datos['nombre'];}
                        if(isset($datos['dni'])){$this->dni=$datos['dni'];}
                        if(isset($datos['genero'])){$this->genero=$datos['genero'];}
                        if(isset($datos['telefono'])){$this->telefono=$datos['telefono'];}
                        if(isset($datos['correo'])){$this->correo=$datos['correo'];}
                        if(isset($datos['direccion'])){$this->direccion=$datos['direccion'];}
                        if(isset($datos['codigopostal'])){$this->codigopostal=$datos['codigopostal'];}
                        if(isset($datos['fechanacimiento'])){$this->fechanacimiento=$datos['fechanacimiento'];}
                        $resp = $this->actualizarPaciente();
                        if ($resp){
                            $respuesta = $_respuestas->response;
                            $respuesta["result"]=array(
                            "pacienteid"=>$this->pacienteid
                            );
                            return $respuesta;
                        }else {
                            return $_respuestas->error_500();
                        }
                    }   // 
                    
                } else {
                    return $_respuestas->error_401("Token enviado es inavalido");
                }
            }
        }

        public function actualizarPaciente(){
            $query = "UPDATE " .$this->tabla." SET DNI='".$this->dni ."',Nombre='".$this->nombre."',Direccion='".$this->direccion.
            "',CodigoPostal='".$this->codigopostal."',Telefono='".$this->telefono."',Genero='".$this->genero."',FechaNacimiento='"
            .$this->fechanacimiento."',Correo='".$this->correo."' WHERE PacienteId='".$this->pacienteid."'";
            $resp = parent::guardar($query);
            if ($resp>=1){
                return $resp;
            } else {
                return 0;
            }
    
        }

        public function eliminar($json){
            $_respuestas=new respuestas;
            $datos= json_decode($json,true);
            
            if(!isset($datos['token'])){
                return $_respuestas->error_401();
            }else{
                $this->token= $datos['token'];
                $arrayToken=$this-> buscarToken();
                if ($arrayToken){
                } else {
                    return $_respuestas->error_401("Token enviado es inavalido");
                }
                    if (!isset($datos['pacienteid'])){
                        return $_respuestas->error_400();
                    } else {
                        $this->pacienteid=$datos['pacienteid'];
                        $resp = $this->eliminarPaciente();
                        if ($resp){
                            $respuesta = $_respuestas->response;
                            $respuesta["result"]=array(
                                "pacienteid"=>$this->pacienteid
                            );
                            return $respuesta;
                        }else {
                            return $_respuestas->error_500();
                        }
                    }
        
               
            }
        }

    private function eliminarPaciente (){
        $query = "DELETE FROM ".$this->tabla." WHERE PacienteId='".$this->pacienteid."'";
        $resp = parent::guardar($query);
        if ($resp>=1){
            return $resp;
            } else {
                return 0;
        }
    }

        
    private function buscarToken(){
        $query = "SELECT TokenId,UsuarioId,Estado from usuarios_token where Token='" .$this->token."' and Estado='Activo'"; 
        $resp = parent::guardar($query);
        if ($resp){
            return $resp;
        }else {
            return 0;
        }

    }

    private function actualizaToken($tokenid){
        $date = date("y-m-d H:i");
        $query ="UPDATE usuarios_token SET Fecha='$date' WHERE TokenId='$tokenid'";
        $resp = parent::guardar($query);
        if ($resp>=1){
            return $resp;
        } else {
            return 0;
        }

    }

    }


?>