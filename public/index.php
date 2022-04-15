<?php

// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;

$container = new Container();
$container->set('renderer', function () {
    // Параметром передается базовая директория, в которой будут храниться шаблоны
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});

$users = ['mike', 'mishel', 'adel', 'keks', 'kamila'];

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$app->get('/', function ($request, $response) {
    return $response->write('Welcome to Slim!');
});

$app->get('/users', function ($request, $response) use ($users) {
    $name = $request->getQueryParam('name');
    $filteredNames = array_filter($users, fn($item) => str_contains($item, $name) ? $item : null); 
    $params = ['names' => array_values($filteredNames), 'name' => $name];
    return $this->get('renderer')->render($response, 'users/index.phtml', $params);
});

$app->get('/users/new', function ($request, $response) {
    $id = uniqid();
    $params = [
	    'user' => ['id' => $id, 'nickname' => '', 'email' => '']
   ];
    return $this->get('renderer')->render($response, "users/new.phtml", $params);
});

$app->get('/users/{id}', function ($request, $response, $args) {
    $params = ['id' => $args['id'], 'nickname' => 'user-'. $args['id']];
    return $this->get('renderer')->render($response, 'users/show.phtml', $params);
});

$app->get('/courses/{id}', function ($request, $response, array $args) {
    $id = $args['id'];
    return $response->write("Course id: {$id}");
});

$app->post('/users', function ($request, $response) {
    $user = $request->getParsedBody();
    var_dump($user);
    $file = 'people.json';
    $current = file_get_contents($file);
    empty($current) ? $updated = [] : $updated = json_decode($current, true);
    $newUser = ($user);
    $updated[] =  $newUser;
    var_dump($updated);
    file_put_contents($file, json_encode($updated));
    $params = [
        'user' => $user 
    ];
    return $this->get('renderer')->render($response, "users/new.phtml", $params);
});

$app->run();

