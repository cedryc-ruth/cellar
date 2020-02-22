<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        //var_dump($request);
        $response->getBody()->write('Géniaaal!');
        return $response;
    });
    
    $app->get('/api/wines', function(Request $request, Response $response) {
        //Récupérer les données de la BD
        //$data = include('public/wines.json');     //Mock
        
        //Se connecter au serveur de DB
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cellar','root','root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        
            //Préparer la requête
            $query = 'SELECT * FROM wine';
        
            //Envoyer la requête
            $stmt = $pdo->query($query);

            //Extraire les données
            $wines = $stmt->fetchAll(PDO::FETCH_ASSOC);     //var_dump($wines); die;
        } catch(PDOException $e) {
            $wines = [
                [
                    "error" => "Problème de base données",
                    "errorCode" => $e->getCode(),
                    "errorMsg" => $e->getMessage(),
                ]
            ];
        }
        
        //Convertir les données en JSON
        $data = json_encode($wines);
        
        $response->getBody()->write($data);
        return $response
                ->withHeader('content-type', 'application/json')
                ->withHeader('charset', 'utf-8');
    });
    
    $app->get('/api/wines/search/{keyword}', function(Request $request, Response $response, array $args) {
        //DB FIND
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cellar','root','root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $query = "SELECT * FROM wine WHERE name LIKE '%{$args['keyword']}%'";

            $stmt = $pdo->query($query);

            $wines = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $wines = [
                [
                    "error" => "Problème de base données",
                    "errorCode" => $e->getCode(),
                    "errorMsg" => $e->getMessage(),
                ]
            ];
        }
        
        //Convertit en JSON
        $data = json_encode($wines);
        
        $response->getBody()->write($data);
                
        return $response
                ->withHeader('content-type','application/json')
                ->withHeader('charset','utf-8');
    });

    $app->get('/api/wines/{id}', function(Request $request, Response $response, array $args) {
        //DB FIND
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cellar','root','root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $query = "SELECT * FROM wine WHERE id='{$args['id']}'";

            $stmt = $pdo->query($query);

            $wine = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $wine = [
                [
                    "error" => "Problème de base données",
                    "errorCode" => $e->getCode(),
                    "errorMsg" => $e->getMessage(),
                ]
            ];
        }
        
        //Convertit en JSON
        $data = json_encode($wine);
        
        $response->getBody()->write($data);
                
        return $response
                ->withHeader('content-type','application/json')
                ->withHeader('charset','utf-8');
    });
    
    $app->delete('/api/wines/{id}', function(Request $request, Response $response, array $args) {
        //DB FIND
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cellar','root','root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $query = "DELETE FROM wine WHERE id='{$args['id']}'";

            $nbRows = $pdo->exec($query);

            $wine = $nbRows>0 ? true :[
                [
                    "error" => "Aucun enregistrement n'a pas été supprimé.",
                ]
            ];
        } catch(PDOException $e) {
            $wine = [
                [
                    "error" => "Problème de base données",
                    "errorCode" => $e->getCode(),
                    "errorMsg" => $e->getMessage(),
                ]
            ];
        }
        
        //Convertit en JSON
        $data = json_encode($wine);
        
        $response->getBody()->write($data);
                
        return $response
                ->withHeader('content-type','application/json')
                ->withHeader('charset','utf-8');
    });
    
    $app->post('/api/wines', function(Request $request, Response $response) {
        //Se connecter au serveur de DB
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cellar','root','root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            $data = $request->getParsedBody();      //var_dump($data);die;
            /*$data = [
                'name' => 'Chateau...',
                'year' => '2005',
                'grapes' => 'Grapes',
                'country' => 'Belgium',
                'region' => 'Brussels',
                'description' => NULL,
                'picture' => 'chateau.jpg',
            ];*/
            
            //Préparer la requête
            $query = "INSERT INTO `wine` (`name`, `year`, `grapes`, `country`, `region`, `description`, `picture`) "
                    . "VALUES ('{$data['name']}', '{$data['year']}', '{$data['grapes']}',"
                    . " '{$data['country']}', '{$data['region']}', '{$data['description']}', '{$data['picture']}')";
        
            //Envoyer la requête
            $nbRows = $pdo->exec($query);

            //Extraire les données
            $wines = $nbRows>0 ? true :[
                [
                    "error" => "Aucun enregistrement n'a pas été ajouté.",
                ]
            ];
        } catch(PDOException $e) {
            $wines = [
                [
                    "error" => "Problème de base données",
                    "errorCode" => $e->getCode(),
                    "errorMsg" => $e->getMessage(),
                ]
            ];
        }
        
        //Convertir les données en JSON
        $data = json_encode($wines);
        
        $response->getBody()->write($data);
        return $response
                ->withHeader('content-type', 'application/json')
                ->withHeader('charset', 'utf-8');
    });
    
    $app->put('/api/wines/{id}', function(Request $request, Response $response, array $args) {
        //Se connecter au serveur de DB
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cellar','root','root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            $data = $request->getParsedBody();      //var_dump($data);die;
            var_dump($data);die;
            //Préparer la requête
            $query = "UPDATE `wine` SET `name`='{$data['name']}',`year`='{$data['year']}',"
                . "`grapes`='{$data['grapes']}',`country`='{$data['country']}',`region`='{$data['region']}',"
                . "`description`='{$data['description']}',`picture`='{$data['picture']}' "
                . "WHERE id={$args['id']}"; 
        
            //Envoyer la requête
            $nbRows = $pdo->exec($query);

            //Extraire les données
            $wines = $nbRows>0 ? true :[
                [
                    "error" => "Aucun enregistrement n'a pas été modifié.",
                ]
            ];
        } catch(PDOException $e) {
            $wines = [
                [
                    "error" => "Problème de base données",
                    "errorCode" => $e->getCode(),
                    "errorMsg" => $e->getMessage(),
                ]
            ];
        }
        
        //Convertir les données en JSON
        $data = json_encode($wines);
        
        $response->getBody()->write($data);
        return $response
                ->withHeader('content-type', 'application/json')
                ->withHeader('charset', 'utf-8');
    });
    
    $app->group('/users', function (Group $group) {
        $group->get('/', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
    
};

