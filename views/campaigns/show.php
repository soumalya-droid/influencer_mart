
<?php include __DIR__ . '/../partials/header.php'; ?>
<h1><?= htmlspecialchars($c['title']) ?></h1>
<p>Company: <?= htmlspecialchars($c['company_name']) ?></p>
<p>Budget: $<?= number_format($c['budget'],2) ?> | Status: <?= htmlspecialchars($c['status']) ?></p>
<p><?= nl2br(htmlspecialchars($c['description'])) ?></p>
<p>Target Audience: <?= htmlspecialchars($c['target_audience']) ?></p>

<?php $u = current_user(); if ($u && $u['role']==='influencer'): ?>
<h2>Submit a Bid</h2>
<form method="post" action="<?= base_url('bids/submit') ?>">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
  <input type="hidden" name="campaign_id" value="<?= (int)$c['id'] ?>">
  <label>Bid Price <input name="bid_price" type="number" step="0.01" required></label>
  <label>Expected Reach <input name="expected_reach" type="number"></label>
  <label>Proposal <textarea name="proposal" required></textarea></label>
  <button class="btn" type="submit">Submit Bid</button>
</form>
<?php endif; ?>

<h2>Bids</h2>
<?php if (!$bids): ?>
  <p>No bids yet.</p>
<?php endif; ?>
<ul>
<?php foreach ($bids as $b): ?>
  <li>
    <strong><?= htmlspecialchars($b['name']) ?></strong> — $<?= number_format($b['bid_price'],2) ?> — Status: <?= htmlspecialchars($b['status']) ?>
    <?php if ($u && $u['role']==='company' && $u['id']==$c['company_id']): ?>
      <form method="post" action="<?= base_url('bids/approve') ?>" style="display:inline;">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
        <input type="hidden" name="bid_id" value="<?= (int)$b['id'] ?>">
        <button class="btn small" type="submit">Approve</button>
      </form>
      <form method="post" action="<?= base_url('bids/reject') ?>" style="display:inline;">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
        <input type="hidden" name="bid_id" value="<?= (int)$b['id'] ?>">
        <button class="btn small alt" type="submit">Reject</button>
      </form>
      <a class="btn small" href="<?= base_url('chat') . '?with=' . (int)$b['influencer_id'] . '&campaign_id=' . (int)$c['id'] ?>">Chat</a>
    <?php elseif ($u && $u['role']==='influencer' && $u['id']==$b['influencer_id']): ?>
      <a class="btn small" href="<?= base_url('chat') . '?with=' . (int)$c['company_id'] . '&campaign_id=' . (int)$c['id'] ?>">Chat</a>
    <?php endif; ?>
  </li>
<?php endforeach; ?>
</ul>
<?php include __DIR__ . '/../partials/footer.php'; ?>
