<?php

// Configuracion de acceso CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
	die();
}

require_once 'vendor/autoload.php';

$app  = new \Slim\Slim();

$db = new mysqli("localhost","root","pass","dbName");

$app -> get("/pruebas",function() use($app,$db){
    
    echo "Hola mundo desde slim";
    var_dump($db);
}); 

/* consultar productos*/ 
$app -> get("/producto",function()use($app,$db) {

    $sql = "SELECT * FROM productos order by id desc";

    $query = $db -> query($sql);

    $productos = array();
    while($producto  = $query->fetch_assoc()){

        $productos [] = $producto;

    }

    $result = array(
        'status' => 'ok', 
        'code' => '200',
        'data' =>$productos
    );

    echo  json_encode($result); 

    
});

/*Consultar producto id */ 
$app -> get("/producto/:id",function($id)use($app,$db) {


    $sql = "SELECT * FROM productos where id = '{$id}'";

    $query = $db -> query($sql);

    $result = array(
        'status' => 'error', 
        'code' => '404',
        'mensaje' =>'producto no disponible'
    );

    if($query -> num_rows == 1){

        $producto = $query -> fetch_assoc();

        $result = array(
            'status' => 'ok', 
            'code' => '200',
            'mensaje' =>'producto disponible',
            'producto' => $producto
        );


    }

    echo  json_encode($result); 
  
});

/* Eliminar producto*/ 
$app -> delete("/producto/eliminar/:id",function($id)use($app,$db) {

    $sql = "DELETE FROM productos where id = '{$id}'";

    $query = $db -> query($sql);

    $result = array(
        'status' => 'error', 
        'code' => '404',
        'mensaje' =>'producto no disponible'
    );

    if($query){
        $result = array(
            'status' => 'ok', 
            'code' => '200',
            'mensaje' =>'producto eliminado con exito',
            );
    }

    echo  json_encode($result); 
  
});

/* Editar producto*/
$app -> put("/producto/editar",function() use($app,$db){

    $data=json_decode($app->request()->getBody(), true);

    if(!isset($data["imagen"])){
        $data["imagen"] = null; 
    }

    $query = "UPDATE productos SET ".
            "nombre =       '{$data['nombre']}',".
            "descripcion =  '{$data['descripcion']}',".
            "precio =       '{$data['precio']}',".
            "imagen =       '{$data['imagen']}'".
            "WHERE id =     '{$data['id']}'";
    
    $update = $db -> query($query);
    
    $result = array(
        'status' => 'error', 
        'code' => '404',
        'mensaje' =>'Error al actualziar el producto'
    );

    if($update){

        $result = array(
            'status' => 'ok', 
            'code' => '200',
            'mensaje' =>'producto actualizado'
        );

    } 
        
   echo json_encode($result);

});

/*Subir imagen a producto*/ 
$app -> post("/producto/subirImagen",function() use($app,$db){
  
    $result = array(
        'status' => 'error', 
        'code' => '404',
        'mensaje' =>'Error al subir la imagen del producto'
    );

    if(isset($_FILES['uploads'])){
        
        $piramideUpload = new PiramideUploader();

        $upload = $piramideUpload -> upload (
            'image', // prefijo  
            "uploads", //nombre del parametro por el cual llega el archivo 
            "files", // directorio
            array('image/jpeg','image/png') // formatos permitidos
        );

        $file = $piramideUpload->getInfoFile();
        $file_name = $file ['complete_name'] ;

       if(isset($update) && $upload["uploads"]== false ){

        $result = array(
            'status' => 'error', 
            'code' => '404',
            'mensaje' =>'El archivo no se a podido subir'
        );
    

       }else{

        $result = array(
            'status' => 'ok', 
            'code' => '200',
            'mensaje' =>'Archivo subido con éxito',
            'file' => $file_name
        );
    
       }
        
    }

    echo json_encode($result);

});



/* insertar producto*/
$app -> post("/producto",function() use($app,$db){

    /* Capturara datos de la peticion*/

    //$json = $app->request->post('json');
    // $data = json_decode($json,true);

    $data=json_decode($app->request()->getBody(), true);

    if(!isset($data["imagen"])){
        $data["imagen"] = null; 
    }

    $query = "INSERT INTO productos (nombre,descripcion,precio,imagen) values (".
            "'{$data['nombre']}',".
            "'{$data['descripcion']}',".
            "'{$data['precio']}',".
            "'{$data['imagen']}'".
            ");";
    
    $insert = $db -> query($query);
    
    $result = array(
        'status' => 'error', 
        'code' => '404',
        'mensaje' =>'Error al crear el producto'
    );

    if($insert){

        $result = array(
            'status' => 'ok', 
            'code' => '200',
            'mensaje' =>'producto creado'
        );

    } 
        
   echo json_encode($result);

});


$app->run();

?>