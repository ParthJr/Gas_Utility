<?php
/**
 * PG-Core Engine — Feature Upgrade screen (403 Forbidden)
 */

if (!isset($featName)) {
    $featName = 'This feature';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upgrade Required - PG-Core Engine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .upgrade-card {
            max-width: 550px;
            width: 100%;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            background: #ffffff;
            padding: 40px;
            text-align: center;
            margin: 20px;
        }
        .icon-box {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #ffeef0;
            color: #dc3545;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>

<div class="upgrade-card">
    <div class="icon-box">
        <i class="bi bi-shield-lock-fill"></i>
    </div>
    <h3 class="fw-bold text-dark mb-2">Feature Upgrade Required</h3>
    <p class="text-muted mb-4">
        The feature <strong><?= htmlspecialchars($featName) ?></strong> is not included in your current subscription plan. 
        Upgrade your plan to unlock full capabilities and streamline your PG operations.
    </p>
    
    <div class="d-flex flex-column gap-2">
        <a href="<?= APP_URL ?>/admin/plans.php" class="btn btn-warning py-2 rounded-3 fw-bold text-dark">
            <i class="bi bi-rocket-takeoff me-1"></i> Upgrade Plan Now
        </a>
        <a href="javascript:history.back()" class="btn btn-light py-2 rounded-3 text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Go Back
        </a>
    </div>
</div>

</body>
</html>
