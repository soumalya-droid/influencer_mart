
<?php
require_once __DIR__ . '/BaseController.php';
class AuthController extends BaseController {
  public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!csrf_check($_POST['csrf'] ?? '')) { http_response_code(400); return 'Bad CSRF'; }
      $email = trim($_POST['email'] ?? '');
      $pass = $_POST['password'] ?? '';
      $stmt = $this->db->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
      $stmt->execute([$email]);
      $u = $stmt->fetch();
      if ($u && password_verify($pass, $u['password'])) {
        $_SESSION['user'] = ['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
        return $this->redirect('dashboard');
      }
      flash('error','Invalid credentials');
    }
    return $this->view('auth/login');
  }

  public function register() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!csrf_check($_POST['csrf'] ?? '')) { http_response_code(400); return 'Bad CSRF'; }
      $name = trim($_POST['name'] ?? '');
      $email = trim($_POST['email'] ?? '');
      $role = $_POST['role'] ?? 'influencer';
      $p1 = $_POST['password'] ?? '';
      $p2 = $_POST['password_confirm'] ?? '';
      if (!$name || !$email || !$p1 || $p1 !== $p2 || !in_array($role,['company','influencer'])) {
        flash('error','Please fill all fields correctly');
      } else {
        $exists = $this->db->prepare("SELECT id FROM users WHERE email=?");
        $exists->execute([$email]);
        if ($exists->fetch()) {
          flash('error','Email already registered');
        } else {
          $stmt = $this->db->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)");
          $stmt->execute([$name,$email,password_hash($p1,PASSWORD_BCRYPT),$role]);
          $uid = $this->db->lastInsertId();
          $this->db->prepare("INSERT INTO profiles(user_id) VALUES(?)")->execute([$uid]);
          $_SESSION['user']=['id'=>$uid,'name'=>$name,'email'=>$email,'role'=>$role];
          return $this->redirect('dashboard');
        }
      }
    }
    return $this->view('auth/register');
  }

  public function logout() {
    if (!csrf_check($_POST['csrf'] ?? '')) { http_response_code(400); return 'Bad CSRF'; }
    session_destroy();
    return $this->redirect('');
  }
}
