
<?php include __DIR__ . '/../partials/header.php'; ?>
<h1>Company Dashboard</h1>
<p>Welcome, <?= htmlspecialchars($u['name']) ?>!</p>
<p><a class="btn" href="<?= base_url('campaigns/create') ?>">Post New Campaign</a></p>
<h2>Your Campaigns</h2>
<?php if (!$campaigns): ?>
  <p>No campaigns yet.</p>
<?php endif; ?>
<ul>
<?php foreach ($campaigns as $c): ?>
  <li><a href="<?= base_url('campaigns/show') . '?id=' . (int)$c['id'] ?>"><?= htmlspecialchars($c['title']) ?></a> — <?= htmlspecialchars($c['status']) ?> — $<?= number_format($c['budget'],2) ?></li>
<?php endforeach; ?>
</ul>
<?php include __DIR__ . '/../partials/footer.php'; ?>
