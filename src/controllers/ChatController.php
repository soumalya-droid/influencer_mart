
<?php
require_once __DIR__ . '/BaseController.php';
class ChatController extends BaseController {
  public function thread() {
    require_login();
    $with = intval($_GET['with'] ?? 0);
    $campaign_id = intval($_GET['campaign_id'] ?? 0);
    if (!$with || !$campaign_id) { http_response_code(400); return 'Missing parameters'; }
    // Ensure participants belong to campaign (company or influencer who bid)
    $c = $this->db->prepare("SELECT company_id FROM campaigns WHERE id=?");
    $c->execute([$campaign_id]);
    $camp = $c->fetch();
    if (!$camp) { http_response_code(404); return 'Campaign not found'; }
    $me = current_user()['id'];

    // Create/find chat
    $stmt = $this->db->prepare("SELECT id FROM chats WHERE campaign_id=? AND ((company_id=? AND influencer_id=?) OR (company_id=? AND influencer_id=?)) LIMIT 1");
    // Determine roles
    $company_id = $camp['company_id'];
    if ($me == $company_id) {
      $stmt->execute([$campaign_id,$company_id,$with,$company_id,$with]);
    } else {
      $stmt->execute([$campaign_id,$company_id,$me,$company_id,$me]);
    }
    $chat = $stmt->fetch();
    if (!$chat) {
      // create if influencer has any bid or is the company
      if ($me == $company_id) {
        $inf = $with;
      } else {
        $inf = $me;
      }
      $ins = $this->db->prepare("INSERT INTO chats(campaign_id,company_id,influencer_id) VALUES(?,?,?)");
      $ins->execute([$campaign_id,$company_id,$inf]);
      $chat_id = $this->db->lastInsertId();
    } else {
      $chat_id = $chat['id'];
    }
    return $this->view('chat/thread', ['chat_id'=>$chat_id,'campaign_id'=>$campaign_id,'with'=>$with]);
  }

  public function send() {
    require_login();
    if (!csrf_check($_POST['csrf'] ?? '')) { http_response_code(400); return 'Bad CSRF'; }
    $chat_id = intval($_POST['chat_id'] ?? 0);
    $msg = trim($_POST['message'] ?? '');
    if (!$chat_id || !$msg) { http_response_code(400); return 'Invalid'; }
    // Authorization: ensure user is part of chat
    $auth = $this->db->prepare("SELECT * FROM chats WHERE id=?");
    $auth->execute([$chat_id]);
    $c = $auth->fetch();
    $me = current_user()['id'];
    if (!$c || ($c['company_id']!=$me && $c['influencer_id']!=$me)) { http_response_code(403); return 'Forbidden'; }
    $ins = $this->db->prepare("INSERT INTO messages(chat_id,sender_id,body) VALUES(?,?,?)");
    $ins->execute([$chat_id,$me,$msg]);
    echo json_encode(['ok'=>true]);
    return '';
  }

  public function poll() {
    require_login();
    $chat_id = intval($_GET['chat_id'] ?? 0);
    $after = intval($_GET['after'] ?? 0);
    $auth = $this->db->prepare("SELECT * FROM chats WHERE id=?");
    $auth->execute([$chat_id]);
    $c = $auth->fetch();
    $me = current_user()['id'];
    if (!$c || ($c['company_id']!=$me && $c['influencer_id']!=$me)) { http_response_code(403); return 'Forbidden'; }
    if ($after>0) {
      $stmt = $this->db->prepare("SELECT m.*, u.name AS sender_name FROM messages m JOIN users u ON u.id=m.sender_id WHERE m.chat_id=? AND m.id>? ORDER BY m.id ASC");
      $stmt->execute([$chat_id,$after]);
    } else {
      $stmt = $this->db->prepare("SELECT m.*, u.name AS sender_name FROM messages m JOIN users u ON u.id=m.sender_id WHERE m.chat_id=? ORDER BY m.id ASC LIMIT 50");
      $stmt->execute([$chat_id]);
    }
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll());
    return '';
  }
}
