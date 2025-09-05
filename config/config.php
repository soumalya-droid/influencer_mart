
<?php
// Copy this file as-is and set your DB credentials
return [
  'db_host' => getenv('DB_HOST') ?: 'localhost',
  'db_name' => getenv('DB_NAME') ?: 'u269008503_influencermart',
  'db_user' => getenv('DB_USER') ?: 'u269008503_system',
  'db_pass' => getenv('DB_PASS') ?: '6pnaW:Rz$',
  'base_url' => '/', // adjust if in subfolder, e.g., '/influencer/'
  'session_name' => 'influ_sess',
  'app_name' => 'InfluenceHub'
];
