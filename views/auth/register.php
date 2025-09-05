
<?php include __DIR__ . '/../partials/header.php'; ?>
<h1>Register</h1>
<form method="post">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
  <label>Name <input name="name" required></label>
  <label>Email <input name="email" type="email" required></label>
  <label>Password <input name="password" type="password" required></label>
  <label>Confirm Password <input name="password_confirm" type="password" required></label>
  <label>Role 
    <select name="role">
      <option value="company">Company</option>
      <option value="influencer">Influencer</option>
    </select>
  </label>
  <button class="btn" type="submit">Create Account</button>
</form>
<?php include __DIR__ . '/../partials/footer.php'; ?>
