
<?php $u = current_user(); ?>
<nav class="nav">
  <a class="brand" href="<?= base_url('') ?>">InfluenceHub</a>
  <a href="<?= base_url('campaigns') ?>">Campaigns</a>
  <?php if ($u): ?>
    <a href="<?= base_url('dashboard') ?>">Dashboard</a>
    <a href="<?= base_url('profile/edit') ?>">Profile</a>
    <form action="<?= base_url('auth/logout') ?>" method="post" style="display:inline;">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
      <button class="link" type="submit">Logout (<?= htmlspecialchars($u['name']) ?>)</button>
    </form>
  <?php else: ?>
    <a href="<?= base_url('auth/login') ?>">Login</a>
    <a href="<?= base_url('auth/register') ?>">Register</a>
  <?php endif; ?>
</nav>
