<?php
/**
 * StayFlow SaaS Platform — Forgot Password Interface
 */

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/services/AuthService.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = getAuthUser();
if ($user) {
    header('Location: ' . AuthService::getHomeRoute($user));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Forgot Password — StayFlow</title>
    
    <meta name="theme-color" content="#4f46e5">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #818cf8 100%);
            --ink: #0f172a;
            --muted: #64748b;
            --border-subtle: #e2e8f0;
            --card-bg: #ffffff;
            --page-bg: #f8fafc;
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-display: 'Outfit', sans-serif;
            --radius-lg: 24px;
            --radius-md: 14px;
            --radius-sm: 10px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            font-family: var(--font-main);
            background-color: var(--page-bg);
            color: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background: 
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.06) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(241, 245, 249, 0.8) 0%, #f8fafc 100%);
        }

        .auth-card {
            width: 100%;
            max-width: 460px;
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            padding: 36px 32px;
            text-align: center;
        }

        .icon-badge {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .auth-title {
            font-family: var(--font-display);
            font-size: 24px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 8px;
        }

        .auth-subtitle {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.5;
            margin-bottom: 28px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: var(--muted);
            font-size: 16px;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            height: 48px;
            padding: 0 14px 0 42px;
            font-size: 14px;
            font-family: inherit;
            border: 1.5px solid var(--border-subtle);
            border-radius: var(--radius-md);
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
        }

        .btn-submit {
            width: 100%;
            height: 48px;
            background: var(--primary-gradient);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-md);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
        }

        .btn-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .back-link {
            display: inline-block;
            margin-top: 24px;
            font-size: 13px;
            color: var(--muted);
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--ink);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="icon-badge">
            <i class="bi bi-key-fill"></i>
        </div>

        <h1 class="auth-title">Reset Your Password</h1>
        <p class="auth-subtitle">
            Enter your account email address and we will dispatch a secure 6-digit verification code.
        </p>

        <form id="forgotForm" autocomplete="off">
            <div class="form-group">
                <label class="form-label" for="email">Account Email</label>
                <div class="input-group">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <span>Send Verification Code</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <a href="/login.php" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to Sign In
        </a>
    </div>

    <script>
        document.getElementById('forgotForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const originalContent = btn.innerHTML;
            const email = document.getElementById('email').value.trim();

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending Code...';

            try {
                const res = await fetch('api/auth/forgot_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();

                Swal.fire({
                    icon: 'info',
                    title: 'Verification Code Dispatched',
                    text: data.message || 'If an account exists, a 6-digit code has been sent.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = data.redirect || '/verify-otp';
                });
            } catch (err) {
                btn.disabled = false;
                btn.innerHTML = originalContent;
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Unable to reach the server. Please try again.'
                });
            }
        });
    </script>
</body>
</html>
