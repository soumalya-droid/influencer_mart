
<?php
if (session_status() === PHP_SESSION_NONE) {
  $cfg = require __DIR__ . '/../config/config.php';
  session_name($cfg['session_name']);
  session_start();
}

function csrf_token() {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['csrf'];
}

function csrf_check($token) {
  return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token ?? '');
}

function current_user() {
  return $_SESSION['user'] ?? null;
}

function require_login() {
  if (!current_user()) {
    header('Location: ' . base_url('auth/login'));
    exit;
  }
}

function require_role($role) {
  $u = current_user();
  if (!$u || $u['role'] !== $role) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
  }
}

function base_url($path = '') {
  $cfg = require __DIR__ . '/../config/config.php';
  $p = ltrim($path, '/');
  return rtrim($cfg['base_url'], '/') . '/' . $p;
}

function flash($key, $msg=null) {
  if ($msg !== null) { $_SESSION['flash'][$key] = $msg; return; }
  if (!empty($_SESSION['flash'][$key])) {
    $m = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $m;
  }
  return null;
}
