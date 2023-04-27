<?Php

namespace Controllers;

use Model\Servicio;
use Model\Cita;
use Model\CitaServicio;

class APIController {
    public static function index() {
        $servicios = Servicio::all();
        echo json_encode($servicios);
        // debuguear($servicios);
    }

    public static function guardar() {

        // Almacena la cita y devuelve el ID
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();

        $id = $resultado['id'];

        // Almacena la cita y el servicio

        $idServicios = explode(',', $_POST['servicios']);

        foreach($idServicios as $idServicio) {
            $args = [
                'citasid' => $id,
                'serviciosid' => $idServicio
            ];

            $citaServico = new CitaServicio($args);
            $citaServico->guardar();
        }

        $respuesta = [
            'resultado' => $resultado,
        ];


        echo json_encode($respuesta);
    }

    public static function eliminar() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];

            $cita = Cita::find($id);
            $cita->eliminar();
            
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }
}