
<?php include __DIR__ . '/../partials/header.php'; ?>
<h1>Influencer Dashboard</h1>
<p>Welcome, <?= htmlspecialchars($u['name']) ?>!</p>
<h2>Your Bids</h2>
<?php if (!$bids): ?>
  <p>No bids yet.</p>
<?php endif; ?>
<ul>
<?php foreach ($bids as $b): ?>
  <li>Campaign: <?= htmlspecialchars($b['title']) ?> — ₹/ $<?= number_format($b['bid_price'],2) ?> — Status: <?= htmlspecialchars($b['status']) ?></li>
<?php endforeach; ?>
</ul>
<?php include __DIR__ . '/../partials/footer.php'; ?>
