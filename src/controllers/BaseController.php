
<?php
class BaseController {
  protected PDO $db;
  protected array $cfg;
  public function __construct(PDO $db) {
    $this->db = $db;
    $this->cfg = require __DIR__ . '/../../config/config.php';
  }
  protected function view($view, $params=[]) {
    extract($params);
    ob_start();
    require __DIR__ . '/../../views/' . $view . '.php';
    return ob_get_clean();
  }
  protected function redirect($path) {
    header('Location: ' . base_url($path));
    exit;
  }
}
