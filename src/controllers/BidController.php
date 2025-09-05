
<?php
require_once __DIR__ . '/BaseController.php';
class BidController extends BaseController {
  public function submit() {
    require_login(); require_role('influencer');
    if (!csrf_check($_POST['csrf'] ?? '')) { http_response_code(400); return 'Bad CSRF'; }
    $cid = intval($_POST['campaign_id'] ?? 0);
    $price = floatval($_POST['bid_price'] ?? 0);
    $reach = intval($_POST['expected_reach'] ?? 0);
    $proposal = trim($_POST['proposal'] ?? '');
    if (!$cid || $price<=0 || !$proposal) { flash('error','Invalid bid'); return $this->redirect('campaigns/show?id='.$cid); }
    // prevent duplicate bid
    $chk = $this->db->prepare("SELECT id FROM bids WHERE influencer_id=? AND campaign_id=?");
    $chk->execute([current_user()['id'],$cid]);
    if ($chk->fetch()) { flash('error','You already bid on this campaign'); return $this->redirect('campaigns/show?id='.$cid); }
    $stmt = $this->db->prepare("INSERT INTO bids(campaign_id,influencer_id,bid_price,expected_reach,proposal,status) VALUES(?,?,?,?,?,'pending')");
    $stmt->execute([$cid,current_user()['id'],$price,$reach?:None,$proposal]);
    flash('success','Bid submitted');
    return $this->redirect('campaigns/show?id='.$cid);
  }

  public function approve() {
    require_login(); require_role('company');
    if (!csrf_check($_POST['csrf'] ?? '')) { http_response_code(400); return 'Bad CSRF'; }
    $bid_id = intval($_POST['bid_id'] ?? 0);
    $stmt = $this->db->prepare("SELECT b.*, c.company_id FROM bids b JOIN campaigns c ON c.id=b.campaign_id WHERE b.id=?");
    $stmt->execute([$bid_id]);
    $b = $stmt->fetch();
    if (!$b || $b['company_id'] != current_user()['id']) { http_response_code(403); return 'Forbidden'; }
    $this->db->prepare("UPDATE bids SET status='approved' WHERE id=?")->execute([$bid_id]);
    flash('success','Bid approved');
    return $this->redirect('campaigns/show?id='.$b['campaign_id']);
  }

  public function reject() {
    require_login(); require_role('company');
    if (!csrf_check($_POST['csrf'] ?? '')) { http_response_code(400); return 'Bad CSRF'; }
    $bid_id = intval($_POST['bid_id'] ?? 0);
    $stmt = $this->db->prepare("SELECT b.*, c.company_id FROM bids b JOIN campaigns c ON c.id=b.campaign_id WHERE b.id=?");
    $stmt->execute([$bid_id]);
    $b = $stmt->fetch();
    if (!$b || $b['company_id'] != current_user()['id']) { http_response_code(403); return 'Forbidden'; }
    $this->db->prepare("UPDATE bids SET status='rejected' WHERE id=?")->execute([$bid_id]);
    flash('success','Bid rejected');
    return $this->redirect('campaigns/show?id='.$b['campaign_id']);
  }
}
