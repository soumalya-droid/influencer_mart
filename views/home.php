
<?php include __DIR__ . '/partials/header.php'; ?>
<h1>Find Influencers. Launch Campaigns.</h1>
<p>Companies post ad campaigns; influencers bid and collaborate.</p>

<h2>Latest Open Campaigns</h2>
<div class="cards">
<?php foreach ($campaigns as $c): ?>
  <div class="card">
    <h3><?= htmlspecialchars($c['title']) ?></h3>
    <p>By <?= htmlspecialchars($c['company_name']) ?> — Budget: $<?= number_format($c['budget'],2) ?></p>
    <a class="btn" href="<?= base_url('campaigns/show') . '?id=' . (int)$c['id'] ?>">View</a>
  </div>
<?php endforeach; ?>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
