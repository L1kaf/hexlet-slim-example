<?php

// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;

$users = ['mike', 'mishel', 'adel', 'keks', 'kamila'];

$container = new Container();
$container->set('renderer', function () {
    // Параметром передается базовая директория, в которой будут храниться шаблоны
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$usersFile = __DIR__ . '/../data/users.json';

$router = $app->getRouteCollector()->getRouteParser();

$app->get('/', function ($request, $response) {
    $response->getBody()->write('Welcome to Slim!');
    return $response;
    // Благодаря пакету slim/http этот же код можно записать короче
    // return $response->write('Welcome to Slim!');
})->setName('index');;

$app->get('/users', function ($request, $response) use ($users) {
    $userName = $request->getQueryParam('user');
    $filterUsers = array_filter($users, function ($user) use ($userName) {
        return str_contains($user, $userName);
    });
    $params = ['users' => $filterUsers, 'userName' => $userName];
    return $this->get('renderer')->render($response, "users/index.phtml", $params);
})->setName('users');

$app->get('/users/new', function ($request, $response) {
    $params = [
        'user' => ['name' => '', 'email' => '', 'password' => '', 'passwordConfirmation' => '', 'city' => ''],
    ];
    return $this->get('renderer')->render($response, "users/new.phtml", $params);
})->setName('new');

$app->post('/users', function ($request, $response) use ($usersFile) {
    $user = $request->getParsedBodyParam('user');

    $id = uniqid();

    $newUser = [
        'id' => $id,
        'nickname' => $user['name'],
        'email' => $user["email"],
    ];

    $existingUsers = json_decode(file_get_contents($usersFile), true);

    $existingUsers[] = $newUser;

    file_put_contents($usersFile, json_encode($existingUsers, JSON_PRETTY_PRINT));

    return $response->withHeader('Location', '/users')->withStatus(302);
})->setName('creat');

$app->get('/courses/{id}', function ($request, $response, array $args) {
    $id = $args['id'];
    return $response->write("Course id: {$id}");
})->setName('course');;

$app->get('/users/{id}', function ($request, $response, $args) use ($usersFile) {
    $userId = $args['id'];

    $existingUsers = json_decode(file_get_contents($usersFile), true);

    $userExists = false;
    foreach ($existingUsers as $user) {
        if ($user['id'] === $userId) {
            $userExists = true;
            break;
        }
    }

    if (!$userExists) {
        return $response->withStatus(404)
                        ->withHeader('Content-Type', 'text/html')
                        ->write('Page not found');
    }

    $params = ['id' => $userId, 'nickname' => 'user-' . $userId];
    return $this->get('renderer')->render($response, 'users/show.phtml', $params);
})->setName('user');

$app->run();
