<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/pacientes.class.php';

$_respuestas=new respuestas;

$_pacientes=new pacientes;

if ($_SERVER['REQUEST_METHOD']=="GET"){
    //lee todos los pacientes
    if (isset($_GET["page"])){
        $pagina=$_GET["page"];
        $listaPacientes = $_pacientes->listaPacientes ($pagina);
        header("Content-Type: application/jason");
        echo json_encode($listaPacientes);
        http_response_code(200);
    //lee paciente por id    
    }else if (isset($_GET["id"])) {
        $pacienteId=$_GET["id"];
        $datosPaciente = $_pacientes->obetnerPaciente($pacienteId);
        header("Content-Type: application/jason");
        echo json_encode($datosPaciente);
        http_response_code(200);   
    }
} else if ($_SERVER['REQUEST_METHOD']=="POST"){
    //recibe informacion
    $postBody = file_get_contents("php://input");
    //enviamos al manejador
    $datosArray = $_pacientes->recibirPaciente($postBody);
    //devuelve respuesta
      
    header('Content-type: application/json');
    if (isset($datosArray["result"]["error_id"])){
        $responseCode=$datosArray["result"]["error_id"];
        http_response_code($responseCode);
    } else {
        http_response_code(200);
    }
    echo json_encode($datosArray);
}else if ($_SERVER['REQUEST_METHOD']=="PUT") {
       //recibe informacion
       $postBody = file_get_contents("php://input");
       //enviar datos al manejador
       $datosArray = $_pacientes->updatePaciente($postBody);
       
       header('Content-type: application/json');
       if (isset($datosArray["result"]["error_id"])){
           $responseCode=$datosArray["result"]["error_id"];
           http_response_code($responseCode);
       } else {
           http_response_code(200);
       }
       echo json_encode($datosArray);

}else if ($_SERVER['REQUEST_METHOD']=="DELETE") {
             //recibe informacion
             $postBody = file_get_contents("php://input");
             //enviar datos al manejador
             $datosArray = $_pacientes->eliminar($postBody);
             
            header('Content-type: application/json');
             if (isset($datosArray["result"]["error_id"])){
                 $responseCode=$datosArray["result"]["error_id"];
                 http_response_code($responseCode);
             } else {
                 http_response_code(200);
             }
             echo json_encode($datosArray);
      
}else{
    header('Content-type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}
        
    
    




?>