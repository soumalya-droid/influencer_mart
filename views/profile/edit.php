
<?php include __DIR__ . '/../partials/header.php'; ?>
<h1>Edit Profile</h1>
<?php if ($u['role']==='company'): ?>
<form method="post">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
  <label>Company Name <input name="company_name" value="<?= htmlspecialchars($profile['company_name'] ?? '') ?>"></label>
  <label>Contact Info <input name="contact_info" value="<?= htmlspecialchars($profile['contact_info'] ?? '') ?>"></label>
  <label>About <textarea name="bio"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea></label>
  <button class="btn" type="submit">Save</button>
</form>
<?php else: ?>
<form method="post">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
  <label>Bio <textarea name="bio"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea></label>
  <label>Niche <input name="niche" value="<?= htmlspecialchars($profile['niche'] ?? '') ?>"></label>
  <label>Pricing Range <input name="pricing_range" value="<?= htmlspecialchars($profile['pricing_range'] ?? '') ?>"></label>
  <button class="btn" type="submit">Save</button>
</form>
<?php endif; ?>
<?php include __DIR__ . '/../partials/footer.php'; ?>
