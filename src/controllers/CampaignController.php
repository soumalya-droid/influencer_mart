
<?php
require_once __DIR__ . '/BaseController.php';
class CampaignController extends BaseController {
  public function index() {
    $q = trim($_GET['q'] ?? '');
    if ($q) {
      $stmt = $this->db->prepare("SELECT c.*, u.name AS company_name FROM campaigns c JOIN users u ON u.id=c.company_id WHERE c.status='open' AND (c.title LIKE ? OR c.description LIKE ?) ORDER BY c.id DESC");
      $like = "%$q%";
      $stmt->execute([$like,$like]);
    } else {
      $stmt = $this->db->query("SELECT c.*, u.name AS company_name FROM campaigns c JOIN users u ON u.id=c.company_id WHERE c.status='open' ORDER BY c.id DESC");
    }
    $rows = $stmt->fetchAll();
    return $this->view('campaigns/index', ['campaigns'=>$rows, 'q'=>$q]);
  }

  public function create() {
    require_login(); require_role('company');
    if ($_SERVER['REQUEST_METHOD']==='POST') {
      if (!csrf_check($_POST['csrf'] ?? '')) { http_response_code(400); return 'Bad CSRF'; }
      $title = trim($_POST['title'] ?? '');
      $desc = trim($_POST['description'] ?? '');
      $budget = floatval($_POST['budget'] ?? 0);
      $aud = trim($_POST['target_audience'] ?? '');
      $start = $_POST['start_date'] ?? null;
      $end = $_POST['end_date'] ?? null;
      if (!$title || !$desc || $budget<=0 || !$start || !$end) {
        flash('error','Please fill all required fields');
      } else {
        $stmt = $this->db->prepare("INSERT INTO campaigns(company_id,title,description,target_audience,budget,start_date,end_date,status) VALUES(?,?,?,?,?,?,?,'open')");
        $stmt->execute([current_user()['id'],$title,$desc,$aud,$budget,$start,$end]);
        flash('success','Campaign created');
        return $this->redirect('dashboard');
      }
    }
    return $this->view('campaigns/create');
  }

  public function show() {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) { http_response_code(404); return 'Not found'; }
    $stmt = $this->db->prepare("SELECT c.*, u.name AS company_name FROM campaigns c JOIN users u ON u.id=c.company_id WHERE c.id=?");
    $stmt->execute([$id]);
    $c = $stmt->fetch();
    if (!$c) { http_response_code(404); return 'Not found'; }
    // bids
    $bids = $this->db->prepare("SELECT b.*, u.name FROM bids b JOIN users u ON u.id=b.influencer_id WHERE b.campaign_id=? ORDER BY b.id DESC");
    $bids->execute([$id]);
    $bidRows = $bids->fetchAll();
    return $this->view('campaigns/show', ['c'=>$c,'bids'=>$bidRows]);
  }
}
