<?php
require 'vendor/autoload.php';

use Mark\App;
use GraphQL\GraphQL;
use GraphQL\Error\FormattedError;
use Apollo\Federation\FederatedSchema;
use Workerman\Protocols\Http\Response;
use GraphQL\Type\Definition\ObjectType;
use Androsyz\MetaforgeGql\Middleware\Middleware as Mware;
use Androsyz\PhpSdk\Graphql\Graphql as GqlSdk;

// Controller for serving gql
class Gql {
    static function index(){
        return file_get_contents('graphiql.html');
    }

    // Query processing
    static function query($request, $schema){
        try {
            $rawInput = $request->post('query');
            $variables = $request->post('variables') ?? null;
            $context = $request->data;
            $result = GraphQL::executeQuery($schema, $rawInput, null, $context, $variables);
            return json_encode($result->toArray());
        } catch (\Exception $e) {
            return json_encode(['errors' => FormattedError::createFromException($e)]);
        }
    }

    static function readEntity($dir) {
        $entities = scandir($dir);
        foreach ($entities as $key => $value) {
            if (in_array($value, ['.','..'])) 
                unset($entities[$key]);
            else if (Spyc::YAMLLoad($dir.$value) != null)
                $yml[str_replace(".yml", "", $value)] = Spyc::YAMLLoad($dir.$value);
        }

        return $yml;
    }

}

class Middleware {
    var $mwares = [];

    function register($class){
        array_push($this->mwares, $class);
    }

    function serve($r){
        foreach ($this->mwares as $mware) {
            $r = $mware->serve($r);
        }
        return $r;
    }
}

// Env 
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
try {
	$dotenv->load();
} catch (Exception $e) {
	// No env
}

// DB
global $db;
$db = new \Workerman\MySQL\Connection(
    @$_ENV['DB_HOST'],
    @$_ENV['DB_PORT'],
    @$_ENV['DB_USER'],
    @$_ENV['DB_PASS'],
    @$_ENV['DB_NAME']
);

// Read Yml
$dir = 'metadata/';
$yml = Gql::readEntity($dir);

// Init
$api = new App('http://0.0.0.0:5000');
$api->count = 8;

// Init Schema 
$sdk = new GqlSdk();
$qry = $sdk->generateSchema($db, $yml);
$q = $qry['query'];
$m = $qry['mutation'];

$metaSchema = new FederatedSchema([
    'query' => new ObjectType([
        'name' => 'Query',
        'fields' => $q,
    ]),
    'mutation' => new ObjectType([
        'name' => 'Mutation',
        'fields' => $m,
    ]),
]);

// graphiql
$api->get('/', function ($req) {
    return Gql::index();
});

// gql
$api->any('/gql', function ($req) use ($metaSchema) {
    // Middleware 
    $m = new Middleware();
    $m->register(new Mware());

    $processedRequest = $m->serve($req);
    if ($processedRequest instanceof Response) {
        return $processedRequest;
    }

    return new Response(
        200, 
        [
            "Access-Control-Allow-Origin" => "*",
            "Access-Control-Allow-Methods" => "POST, GET, OPTIONS",
            "Access-Control-Allow-Headers" => "Content-Type",
        ], 
        Gql::query(
            $m->serve($req),
            $metaSchema,
        ),
        
    );
});

$api->start();
