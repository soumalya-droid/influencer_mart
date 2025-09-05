
<?php
require_once __DIR__ . '/BaseController.php';
class HomeController extends BaseController {
  public function index() {
    // recent campaigns
    $stmt = $this->db->query("SELECT c.id, c.title, c.budget, c.status, u.name AS company_name
      FROM campaigns c JOIN users u ON u.id=c.company_id
      WHERE c.status='open' ORDER BY c.id DESC LIMIT 10");
    $campaigns = $stmt->fetchAll();
    return $this->view('home', compact('campaigns'));
  }
}
