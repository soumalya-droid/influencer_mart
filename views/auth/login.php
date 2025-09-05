
<?php include __DIR__ . '/../partials/header.php'; ?>
<h1>Login</h1>
<form method="post">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
  <label>Email <input name="email" type="email" required></label>
  <label>Password <input name="password" type="password" required></label>
  <button class="btn" type="submit">Login</button>
</form>
<p>No account? <a href="<?= base_url('auth/register') ?>">Register</a></p>
<?php include __DIR__ . '/../partials/footer.php'; ?>
