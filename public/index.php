
<?php
require __DIR__ . '/../lib/db.php';
require __DIR__ . '/../lib/auth.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim((require __DIR__ . '/../config/config.php')['base_url'], '/');
if ($base && str_starts_with($uri, $base)) $uri = substr($uri, strlen($base));

$routes = [
  '' => ['GET','HomeController@index'],
  'auth/login' => ['GET|POST','AuthController@login'],
  'auth/register' => ['GET|POST','AuthController@register'],
  'auth/logout' => ['POST','AuthController@logout'],

  'dashboard' => ['GET','DashboardController@index'],

  'campaigns' => ['GET','CampaignController@index'],
  'campaigns/create' => ['GET|POST','CampaignController@create'],
  'campaigns/show' => ['GET','CampaignController@show'],

  'bids/submit' => ['POST','BidController@submit'],
  'bids/approve' => ['POST','BidController@approve'],
  'bids/reject' => ['POST','BidController@reject'],

  'chat' => ['GET','ChatController@thread'],
  'chat/send' => ['POST','ChatController@send'],
  'chat/poll' => ['GET','ChatController@poll'],

  'profile/edit' => ['GET|POST','ProfileController@edit'],

  'payments' => ['GET','PaymentController@index'], // placeholder
];

// Resolve route
$path = trim($uri, '/');
if (!isset($routes[$path])) {
  // Try without query string fallback to home
  if ($path === '') $path = '';
  elseif ($path === 'index.php') $path = '';
  elseif (!isset($routes[$path])) {
    http_response_code(404);
    echo "404 Not Found";
    exit;
  }
}

list($methods, $handler) = $routes[$path];
$allowed = explode('|', $methods);
if (!in_array($_SERVER['REQUEST_METHOD'], $allowed)) {
  http_response_code(405);
  echo "Method Not Allowed";
  exit;
}

list($controller, $action) = explode('@', $handler);
require_once __DIR__ . '/../src/controllers/' . $controller . '.php';
$ctrl = new $controller($pdo);
echo $ctrl->$action();
