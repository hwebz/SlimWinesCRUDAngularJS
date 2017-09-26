<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

function getConnection() {
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "cellar";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

$app->get('/wines', 'getWines');
$app->get('/wines/{id}', 'getWine');
$app->get('/wines/search/{query}', 'findByName');
$app->post('/wines', 'addWine');
$app->put('/wines/{id}', 'updateWine');
$app->delete('/wines/{id}', 'deleteWine');

function getWines() {
    $sql = "SELECT * FROM wine ORDER BY name";

    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $wines = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($wines);
    } catch (PDOEXception $e) {
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
}

function getWine(Request $request, Response $response) {
    $sql = "SELECT * FROM wine WHERE id = :id";
    $id = $request->getAttribute('id');

    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $wine = $stmt->fetchObject();
        $db = null;
        echo json_encode($wine);
    } catch (PDOException $e) {
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
}

function addWine(Request $request, Response $response) {
    $wine = json_decode($request->getBody());
    $sql = "INSERT INTO wine (name, grapes, country, region, year, description, picture) VALUES (:name, :grapes, :country, :region, :year, :description, :picture)";

    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("name", $wine->name);
        $stmt->bindParam("grapes", $wine->grapes);
        $stmt->bindParam("country", $wine->country);
        $stmt->bindParam("region", $wine->region);
        $stmt->bindParam("year", $wine->year);
        $stmt->bindParam("description", $wine->description);
        $stmt->bindParam("picture", $wine->picture);
        $stmt->execute();
        $wine->id = $db->lastInsertId();
        $db = null;
        echo json_encode($wine);
    } catch (PDOException $e) {
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
}

function updateWine(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $wine = json_decode($request->getBody());
    $sql = "UPDATE wine SET name=:name, grapes=:grapes, country=:country, region=:region, year=:year, description=:description, picture=:picture WHERE id=:id";

    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("name", $wine->name);
        $stmt->bindParam("grapes", $wine->grapes);
        $stmt->bindParam("country", $wine->country);
        $stmt->bindParam("region", $wine->region);
        $stmt->bindParam("year", $wine->year);
        $stmt->bindParam("description", $wine->description);
        $stmt->bindParam("picture", $wine->picture);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode($wine);
    } catch (PDOException $e) {
        echo '{"error": {"text":'.$e->getMessage().'}}';
    }
}

function deleteWine(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM wine WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
    } catch (PDOException $e) {
        echo '{"error": {"text":'.$e->getMessage().'}}';
    }
}

function findByName(Request $request, Response $response) {
    $query = '%'.$request->getAttribute('query').'%';
    $sql = "SELECT * FROM wine WHERE UPPER(name) LIKE :query ORDER BY name";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $wines = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"wine": '.json_encode($wines).'}';
    } catch (PDOException $e) {
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
}