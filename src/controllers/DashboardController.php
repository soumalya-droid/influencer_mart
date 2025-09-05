
<?php
require_once __DIR__ . '/BaseController.php';
class DashboardController extends BaseController {
  public function index() {
    require_login();
    $u = current_user();
    if ($u['role']==='company') {
      // fetch company campaigns and bids
      $stmt = $this->db->prepare("SELECT * FROM campaigns WHERE company_id=? ORDER BY id DESC");
      $stmt->execute([$u['id']]);
      $campaigns = $stmt->fetchAll();
      return $this->view('dashboard/company', compact('u','campaigns'));
    } else {
      // influencer: bids and open campaigns
      $stmt = $this->db->prepare("SELECT b.*, c.title FROM bids b JOIN campaigns c ON c.id=b.campaign_id WHERE influencer_id=? ORDER BY b.id DESC");
      $stmt->execute([$u['id']]);
      $bids = $stmt->fetchAll();
      return $this->view('dashboard/influencer', compact('u','bids'));
    }
  }
}
