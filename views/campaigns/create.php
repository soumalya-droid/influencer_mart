
<?php include __DIR__ . '/../partials/header.php'; ?>
<h1>Post Campaign</h1>
<form method="post">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
  <label>Title <input name="title" required></label>
  <label>Description <textarea name="description" required></textarea></label>
  <label>Target Audience <input name="target_audience" placeholder="e.g., 18-30, US, tech-savvy"></label>
  <label>Budget <input name="budget" type="number" step="0.01" required></label>
  <label>Start Date <input name="start_date" type="date" required></label>
  <label>End Date <input name="end_date" type="date" required></label>
  <button class="btn" type="submit">Create Campaign</button>
</form>
<?php include __DIR__ . '/../partials/footer.php'; ?>
