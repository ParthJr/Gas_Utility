<?php
require_once __DIR__ . '/config/database.php';

$db = getDB();
$passAdmin = password_hash('password123', PASSWORD_BCRYPT);
$passSuper = password_hash('SuperAdmin@2026', PASSWORD_BCRYPT);

// Unlock and reset
$db->exec("UPDATE users SET failed_login_attempts = 0, locked_until = NULL");
$db->prepare("UPDATE users SET password = ? WHERE username = 'admin'")->execute([$passAdmin]);
$db->prepare("UPDATE users SET password = ? WHERE username = 'superadmin'")->execute([$passSuper]);
$db->prepare("UPDATE users SET password = ? WHERE role = 'tenant' OR phone = '9081949461'")->execute([$passAdmin]);

echo "<h2>✅ Success! All accounts unlocked and passwords reset.</h2>";
echo "<p>Admin: admin / password123</p>";
echo "<p>Super Admin: superadmin / SuperAdmin@2026</p>";
echo "<p>Resident: 9081949461 / password123</p>";
echo "<p><a href='index.php'>Go to Login</a></p>";