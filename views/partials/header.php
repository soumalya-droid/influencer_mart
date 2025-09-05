
<?php $cfg = require __DIR__ . '/../../config/config.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($cfg['app_name']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
  <?php include __DIR__ . '/nav.php'; ?>
  <main class="container">
    <?php if ($e = flash('error')): ?>
      <div class="alert error"><?= htmlspecialchars($e) ?></div>
    <?php endif; ?>
    <?php if ($s = flash('success')): ?>
      <div class="alert success"><?= htmlspecialchars($s) ?></div>
    <?php endif; ?>
