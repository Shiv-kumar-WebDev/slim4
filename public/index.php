<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/db.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->get('/friends/all', function (Request $request, Response $response, array $args) {
    $sql = "SELECT * FROM details";

    try{
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->query($sql);
        $friends = $stmt->fetchAll(PDO::FETCH_OBJ);

        $db = null;
        $response->getBody()->write(json_encode($friends));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    }catch(PDOException $e){
        $error = array(
            "message" => $e->getMessage()
        );
        
        $response->getBody()->write(json_encode($error));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
});

$app->get('/friend/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $sql = "SELECT * FROM details WHERE id=$id";

    try{
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->query($sql);
        $friends = $stmt->fetch(PDO::FETCH_OBJ);

        $db = null;
        $response->getBody()->write(json_encode($friends));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    }catch(PDOException $e){
        $error = array(
            "message" => $e->getMessage()
        );
        
        $response->getBody()->write(json_encode($error));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
});

$app->put('/editFriend/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $data = $request->getParsedBody();
    $name = $data['name'];
    $email = $data['email'];
    $mobile = $data['mobile'];

    $sql = "UPDATE details SET
    name = :name,
    email = :email,
    mobile = :mobile
    WHERE id = $id";

    try{
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name',$name);
        $stmt->bindParam(':email',$email);
        $stmt->bindParam(':mobile',$mobile);
        $result = $stmt->execute();

        $db = null;
        $response->getBody()->write(json_encode($result));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    }catch(PDOException $e){
        $error = array(
            "message" => $e->getMessage()
        );
        
        $response->getBody()->write(json_encode($error));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
});

$app->post('/createFriend', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $name = $data['name'];
    $email = $data['email'];
    $mobile = $data['mobile'];

    $sql = "INSERT INTO details (name,email,mobile) VALUES (:name, :email, :mobile)";

    try{
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name',$name);
        $stmt->bindParam(':email',$email);
        $stmt->bindParam(':mobile',$mobile);
        
        $result = $stmt->execute();

        $db = null;
        $response->getBody()->write(json_encode($result));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    }catch(PDOException $e){
        $error = array(
            "message" => $e->getMessage()
        );
        
        $response->getBody()->write(json_encode($error));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
});

$app->delete('/deleteFriend/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $sql = "DELETE  FROM details WHERE id=$id";

    try{
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name',$name);
        $stmt->bindParam(':email',$email);
        $stmt->bindParam(':mobile',$mobile);
        
        $result = $stmt->execute();

        $db = null;
        $response->getBody()->write(json_encode($result));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    }catch(PDOException $e){
        $error = array(
            "message" => $e->getMessage()
        );
        
        $response->getBody()->write(json_encode($error));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
});

$app->run();