<?php
/**
 * PG-Core Engine / StayFlow — Premium Modern SaaS Login Experience
 * Inspired by luxury SaaS design aesthetics.
 * Preserves 100% of existing authentication, session routing, and OTP functionality.
 */

require_once __DIR__ . '/config/database.php';

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
    <title>Sign In — StayFlow / PG-Core Engine</title>
    
    <!-- PWA & Mobile Meta Tags -->
    <meta name="theme-color" content="#4f46e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    
    <!-- Google Fonts & Bootstrap Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- SweetAlert2 for Toast & Modal Alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #818cf8 100%);
            --primary-glow: rgba(79, 70, 229, 0.28);
            --ink: #0f172a;
            --ink-light: #334155;
            --muted: #64748b;
            --border-subtle: #e2e8f0;
            --border-focus: #4f46e5;
            --card-bg: #ffffff;
            --page-bg: #f8fafc;
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-display: 'Outfit', 'Plus Jakarta Sans', sans-serif;
            --radius-lg: 24px;
            --radius-md: 14px;
            --radius-sm: 10px;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            min-height: 100vh;
            min-height: 100dvh;
            width: 100%;
            font-family: var(--font-main);
            background-color: var(--page-bg);
            color: var(--ink);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Luxury Background Canvas with Ambient Glow Mesh */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            background: 
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.06) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(241, 245, 249, 0.8) 0%, #f8fafc 100%);
        }

        /* Subtle Geometric Grid Matrix */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(rgba(148, 163, 184, 0.18) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
            z-index: 0;
        }

        /* Master Container */
        .auth-container {
            width: 100%;
            max-width: 1060px;
            min-height: 640px;
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 
                0 25px 60px -15px rgba(15, 23, 42, 0.07),
                0 0 0 1px rgba(15, 23, 42, 0.03);
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            overflow: hidden;
            position: relative;
            z-index: 1;
            animation: cardEntrance 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Left Visual Showcase Panel */
        .showcase-panel {
            background: linear-gradient(145deg, #090d16 0%, #0f172a 45%, #1e1b4b 100%);
            padding: 48px 44px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        /* Ambient Glow in Showcase Panel */
        .showcase-panel::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -20%;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .showcase-panel::after {
            content: '';
            position: absolute;
            bottom: -15%;
            left: -15%;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .showcase-header {
            position: relative;
            z-index: 2;
        }

        .brand-logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #ffffff;
            margin-bottom: 24px;
        }

        .stayflow-brand-logo {
            width: auto;
            height: 48px;
            max-width: 210px;
            object-fit: contain;
            display: inline-block;
            transition: transform 0.2s ease;
        }

        .showcase-panel .stayflow-brand-logo,
        .dark .stayflow-brand-logo,
        [data-theme="dark"] .stayflow-brand-logo {
            filter: brightness(1.22) drop-shadow(0 0 1px rgba(255, 255, 255, 0.8)) drop-shadow(0 1px 3px rgba(255, 255, 255, 0.25));
        }

        .auth-mobile-brand {
            display: none;
        }

        @media (max-width: 900px) {
            .auth-mobile-brand {
                display: flex;
                justify-content: center;
                margin-bottom: 18px;
            }
            .auth-mobile-brand .stayflow-brand-logo {
                height: 42px;
                max-width: 175px;
            }
        }

        .brand-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px var(--primary-glow);
            font-size: 20px;
            color: #ffffff;
        }

        .brand-name {
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 20px;
            letter-spacing: -0.5px;
            color: #ffffff;
        }

        .brand-tagline {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.55);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .showcase-content {
            position: relative;
            z-index: 2;
            margin-block: 24px;
        }

        .showcase-title {
            font-family: var(--font-display);
            font-size: 30px;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: -0.8px;
            color: #ffffff;
            margin-bottom: 14px;
        }

        .showcase-desc {
            font-size: 14px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 28px;
            max-width: 380px;
        }

        /* Glassmorphic Feature Chips */
        .feature-chips {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .feature-chip {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.25s ease;
        }

        .feature-chip:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateX(4px);
        }

        .feature-chip-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(99, 102, 241, 0.18);
            color: #818cf8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .feature-chip-text h6 {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 2px;
        }

        .feature-chip-text p {
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.3;
            margin: 0;
        }

        .showcase-footer {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 20px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }

        .showcase-footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.2s;
        }

        .showcase-footer a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        /* Right Authentication Form Panel */
        .auth-panel {
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #ffffff;
        }

        .auth-header {
            margin-bottom: 28px;
        }

        .auth-header h2 {
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.6px;
            margin-bottom: 6px;
        }

        .auth-header p {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.5;
        }

        /* Segmented Role Tabs */
        .segmented-tabs {
            background: #f1f5f9;
            padding: 4px;
            border-radius: var(--radius-md);
            display: flex;
            gap: 4px;
            margin-bottom: 24px;
            border: 1px solid var(--border-subtle);
        }

        .segmented-tabs button {
            flex: 1;
            border: none;
            background: transparent;
            color: var(--muted);
            font-family: var(--font-main);
            font-weight: 700;
            font-size: 13px;
            padding: 10px 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .segmented-tabs button.active {
            background: #ffffff;
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        }

        /* Sub-Mode Switcher for Residents (Password vs OTP) */
        .auth-mode-pill-box {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .auth-mode-pills {
            background: #f8fafc;
            border: 1px solid var(--border-subtle);
            border-radius: 24px;
            padding: 3px;
            display: inline-flex;
            gap: 3px;
        }

        .auth-mode-btn {
            border: none;
            background: transparent;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.18s;
        }

        .auth-mode-btn.active {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
        }

        /* Input Controls */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .form-label {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ink-light);
            display: block;
        }

        .forgot-link {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
            transition: color 0.15s;
        }

        .forgot-link:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .input-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
            display: flex;
            align-items: center;
            z-index: 2;
        }

        .form-input {
            width: 100%;
            height: 48px;
            background: #ffffff;
            border: 1.5px solid var(--border-subtle);
            border-radius: var(--radius-sm);
            padding: 10px 44px 10px 44px;
            font-family: var(--font-main);
            font-size: 14px;
            font-weight: 600;
            color: var(--ink);
            transition: all 0.2s ease;
        }

        .form-input::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        .form-input:hover {
            border-color: #cbd5e1;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--border-focus);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
            background: #ffffff;
        }

        .btn-eye {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            padding: 6px;
            cursor: pointer;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            z-index: 3;
            transition: color 0.15s;
        }

        .btn-eye:hover {
            color: var(--ink);
        }

        /* Checkbox & Remember Me */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
            user-select: none;
        }

        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
            border-radius: 4px;
        }

        .remember-row label {
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
            cursor: pointer;
        }

        /* Premium Submit Button */
        .btn-auth-submit {
            width: 100%;
            height: 50px;
            background: var(--primary-gradient);
            border: none;
            border-radius: var(--radius-sm);
            color: #ffffff;
            font-family: var(--font-main);
            font-weight: 700;
            font-size: 14.5px;
            letter-spacing: -0.2px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 4px 14px var(--primary-glow);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-auth-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        .btn-auth-submit:active {
            transform: scale(0.985);
        }

        .btn-auth-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2.5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Auth Form Footer */
        .auth-footer {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid var(--border-subtle);
            text-align: center;
            font-size: 12px;
            color: var(--muted);
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        /* Responsive Breakpoints */
        @media (max-width: 900px) {
            .auth-container {
                grid-template-columns: 1fr;
                max-width: 480px;
                min-height: auto;
            }
            .showcase-panel {
                display: none;
            }
            .auth-panel {
                padding: 36px 28px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 12px;
            }
            .auth-panel {
                padding: 28px 20px;
            }
            .auth-header h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

<div class="auth-container">
    
    <!-- Left Luxury Visual Showcase Panel -->
    <div class="showcase-panel">
        <div class="showcase-header">
            <a href="/" class="brand-logo-badge logo" title="StayFlow – Smart PG Management System">
                <img src="/images/shared/stayflow-logo.png" alt="StayFlow – Smart PG Management System" class="stayflow-brand-logo">
            </a>
        </div>

        <div class="showcase-content">
            <h1 class="showcase-title">Next-Gen Operating System for PG &amp; Hostels.</h1>
            <p class="showcase-desc">Unify property management, automated rent invoices, digital gate passes, and resident communications into a single command center.</p>
            
            <div class="feature-chips">
                <div class="feature-chip">
                    <div class="feature-chip-icon">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <div class="feature-chip-text">
                        <h6>Automated Rent &amp; Ledgers</h6>
                        <p>Zero-reconciliation billing with instant digital receipts.</p>
                    </div>
                </div>

                <div class="feature-chip">
                    <div class="feature-chip-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="feature-chip-text">
                        <h6>Smart QR Security Passes</h6>
                        <p>Real-time entry/exit logs with automated parent alerts.</p>
                    </div>
                </div>

                <div class="feature-chip">
                    <div class="feature-chip-icon">
                        <i class="bi bi-phone-fill"></i>
                    </div>
                    <div class="feature-chip-text">
                        <h6>Dedicated Resident App</h6>
                        <p>Food opt-ins, service tickets, and online payments in one click.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="showcase-footer">
            <span>&copy; 2026 StayFlow SaaS</span>
            <a href="/property-management-software-pricing.php">View Pricing Plans &rarr;</a>
        </div>
    </div>

    <!-- Right Modern Authentication Form Card -->
    <div class="auth-panel">
        <div>
            <!-- Mobile Brand Logo -->
            <div class="auth-mobile-brand">
                <a href="/" class="logo" title="StayFlow – Smart PG Management System">
                    <img src="/images/shared/stayflow-logo.png" alt="StayFlow – Smart PG Management System" class="stayflow-brand-logo">
                </a>
            </div>

            <!-- Header Title -->
            <div class="auth-header">
                <h2>Welcome Back</h2>
                <p>Enter your account credentials to access your operating console.</p>
            </div>

            <!-- Segmented Role Tabs (Management vs Resident) -->
            <div class="segmented-tabs" role="tablist">
                <button type="button" class="active" id="tabStaff" onclick="switchMainTab('staff')">
                    <i class="bi bi-shield-lock-fill"></i> Management Portal
                </button>
                <button type="button" id="tabResident" onclick="switchMainTab('resident')">
                    <i class="bi bi-phone-fill"></i> Resident App
                </button>
            </div>

            <!-- 1. MANAGEMENT / STAFF FORM -->
            <div id="panelStaff">
                <form id="staffLoginForm" onsubmit="handleLogin(event, 'staff')">
                    <div class="form-group">
                        <div class="form-label-row">
                            <label class="form-label" for="staffIdentifier">Username, Email or Mobile</label>
                        </div>
                        <div class="input-container">
                            <span class="input-icon"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-input" id="staffIdentifier" placeholder="e.g. admin or superadmin" required autocomplete="username">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-label-row">
                            <label class="form-label" for="staffPassword">Password</label>
                            <a href="javascript:void(0)" onclick="handleForgotPassword()" class="forgot-link">Forgot password?</a>
                        </div>
                        <div class="input-container">
                            <span class="input-icon"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-input" id="staffPassword" placeholder="••••••••••••" required autocomplete="current-password">
                            <button type="button" class="btn-eye" onclick="togglePassVisibility('staffPassword', this)" aria-label="Toggle password visibility">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="remember-row">
                        <input type="checkbox" id="rememberStaff" checked>
                        <label for="rememberStaff">Keep me signed in for 7 days</label>
                    </div>

                    <button type="submit" class="btn-auth-submit" id="staffBtn">
                        <span class="btn-label"><i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Management</span>
                        <span class="spinner" style="display: none;"></span>
                    </button>
                </form>
            </div>

            <!-- 2. RESIDENT LOGIN FORM -->
            <div id="panelResident" style="display: none;">
                <!-- Resident Auth Mode Selector (Password vs Quick OTP) -->
                <div class="auth-mode-pill-box">
                    <div class="auth-mode-pills">
                        <button type="button" class="auth-mode-btn active" id="btnModePass" onclick="switchResidentAuthMode('password')">
                            <i class="bi bi-key me-1"></i> Password
                        </button>
                        <button type="button" class="auth-mode-btn" id="btnModeOtp" onclick="switchResidentAuthMode('otp')">
                            <i class="bi bi-envelope-check me-1"></i> Quick OTP
                        </button>
                    </div>
                </div>

                <form id="residentLoginForm" onsubmit="handleLogin(event, 'resident')">
                    
                    <!-- Password Mode Fields -->
                    <div id="residentPasswordFields">
                        <div class="form-group">
                            <div class="form-label-row">
                                <label class="form-label" for="residentIdentifier">Registered Mobile Phone or Email</label>
                            </div>
                            <div class="input-container">
                                <span class="input-icon"><i class="bi bi-phone"></i></span>
                                <input type="text" class="form-input" id="residentIdentifier" placeholder="e.g. 7622008118" required autocomplete="username">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-label-row">
                                <label class="form-label" for="residentPassword">Password</label>
                                <a href="javascript:void(0)" onclick="handleForgotPassword()" class="forgot-link">Forgot password?</a>
                            </div>
                            <div class="input-container">
                                <span class="input-icon"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-input" id="residentPassword" placeholder="••••••••••••" required autocomplete="current-password">
                                <button type="button" class="btn-eye" onclick="togglePassVisibility('residentPassword', this)" aria-label="Toggle password visibility">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- OTP Mode: Email Input -->
                    <div id="residentOtpEmailGroup" style="display: none;">
                        <div class="form-group">
                            <div class="form-label-row">
                                <label class="form-label" for="residentEmail">Registered Email Address</label>
                            </div>
                            <div class="input-container">
                                <span class="input-icon"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-input" id="residentEmail" placeholder="e.g. resident@stayflow.antideploy.app">
                            </div>
                        </div>
                    </div>

                    <!-- OTP Code Input -->
                    <div id="residentOtpInputGroup" style="display: none;">
                        <div class="form-group">
                            <div class="form-label-row">
                                <label class="form-label" for="residentOtp">Enter 6-Digit OTP Code</label>
                            </div>
                            <div class="input-container">
                                <span class="input-icon"><i class="bi bi-shield-lock"></i></span>
                                <input type="text" class="form-input" id="residentOtp" placeholder="••••••" maxlength="6" style="letter-spacing: 6px; text-align: center; font-weight: 800; font-size: 18px;">
                            </div>
                        </div>

                        <div style="text-align: center; margin-bottom: 16px;">
                            <button type="button" id="btnResendOtp" disabled onclick="requestEmailOtp()" style="background: none; border: none; font-size: 12.5px; color: var(--muted); cursor: pointer;">
                                Resend OTP in 60s
                            </button>
                        </div>
                    </div>

                    <div class="remember-row" id="rememberResidentRow">
                        <input type="checkbox" id="rememberResident" checked>
                        <label for="rememberResident">Remember my resident session</label>
                    </div>

                    <button type="submit" class="btn-auth-submit" id="residentBtn">
                        <span class="btn-label"><i class="bi bi-phone-fill me-1"></i> Launch Resident App</span>
                        <span class="spinner" style="display: none;"></span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="auth-footer">
            New to StayFlow? <a href="/property-management-software-pricing.php" style="font-weight: 700; color: var(--primary);">Create Account</a> &bull;
            <a href="/forgot_password.php">Forgot Password?</a><br>
            Visit <a href="/">StayFlow Home</a>
        </div>
    </div>

</div>

<script>
let currentMainTab = 'staff';
let currentResidentAuthMode = 'password';
let otpCooldownTimer = null;
let otpCooldownSeconds = 0;

// Switch Main Role Tab (Staff vs Resident)
function switchMainTab(tab) {
    currentMainTab = tab;
    const tabStaff = document.getElementById('tabStaff');
    const tabResident = document.getElementById('tabResident');
    const panelStaff = document.getElementById('panelStaff');
    const panelResident = document.getElementById('panelResident');

    if (tab === 'staff') {
        tabStaff.classList.add('active');
        tabResident.classList.remove('active');
        panelStaff.style.display = 'block';
        panelResident.style.display = 'none';
        document.getElementById('staffIdentifier').focus();
    } else {
        tabResident.classList.add('active');
        tabStaff.classList.remove('active');
        panelStaff.style.display = 'none';
        panelResident.style.display = 'block';
        switchResidentAuthMode('password');
        document.getElementById('residentIdentifier').focus();
    }
}

// Toggle Password Visibility
function togglePassVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Switch Resident Auth Mode (Password vs OTP)
function switchResidentAuthMode(mode) {
    currentResidentAuthMode = mode;
    const btnPass = document.getElementById('btnModePass');
    const btnOtp = document.getElementById('btnModeOtp');
    const passFields = document.getElementById('residentPasswordFields');
    const otpEmailGroup = document.getElementById('residentOtpEmailGroup');
    const otpInputGroup = document.getElementById('residentOtpInputGroup');
    const mainBtn = document.getElementById('residentBtn');
    const btnLabel = mainBtn.querySelector('.btn-label');
    const rememberRow = document.getElementById('rememberResidentRow');

    if (mode === 'otp') {
        btnOtp.classList.add('active');
        btnPass.classList.remove('active');
        passFields.style.display = 'none';
        otpEmailGroup.style.display = 'block';
        otpInputGroup.style.display = 'none';
        rememberRow.style.display = 'none';

        document.getElementById('residentIdentifier').required = false;
        document.getElementById('residentPassword').required = false;
        document.getElementById('residentEmail').required = true;
        document.getElementById('residentEmail').focus();

        btnLabel.innerHTML = '<i class="bi bi-envelope-fill me-1"></i> Send OTP Code';
    } else {
        btnPass.classList.add('active');
        btnOtp.classList.remove('active');
        passFields.style.display = 'block';
        otpEmailGroup.style.display = 'none';
        otpInputGroup.style.display = 'none';
        rememberRow.style.display = 'flex';

        document.getElementById('residentIdentifier').required = true;
        document.getElementById('residentPassword').required = true;
        document.getElementById('residentEmail').required = false;
        document.getElementById('residentIdentifier').focus();

        btnLabel.innerHTML = '<i class="bi bi-phone-fill me-1"></i> Launch Resident App';
    }
}

// Request OTP
async function requestEmailOtp() {
    const email = document.getElementById('residentEmail').value.trim();
    if (!email) {
        Swal.fire({ icon: 'warning', title: 'Email Required', text: 'Please enter your registered email address.' });
        return;
    }

    const mainBtn = document.getElementById('residentBtn');
    const btnLabel = mainBtn.querySelector('.btn-label');
    const spinner = mainBtn.querySelector('.spinner');

    btnLabel.style.display = 'none';
    spinner.style.display = 'inline-block';
    mainBtn.disabled = true;

    try {
        const response = await fetch('api/auth/send_email_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email })
        });
        const data = await response.json();

        if (response.ok && data.success) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: data.message || 'OTP dispatched to your email!',
                showConfirmButton: false,
                timer: 3000
            });

            document.getElementById('residentOtpInputGroup').style.display = 'block';
            document.getElementById('residentOtpEmailGroup').style.display = 'none';
            document.getElementById('residentOtp').focus();

            btnLabel.innerHTML = '<i class="bi bi-shield-check me-1"></i> Verify &amp; Continue';
            startOtpCooldown();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Request Failed',
                text: data.message || 'Unable to request OTP. Please verify your email.'
            });
        }
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Connection Error', text: 'Could not connect to authentication server.' });
    } finally {
        btnLabel.style.display = 'inline-flex';
        spinner.style.display = 'none';
        mainBtn.disabled = false;
    }
}

// Start OTP Resend Cooldown Timer
function startOtpCooldown() {
    const resendBtn = document.getElementById('btnResendOtp');
    resendBtn.disabled = true;
    otpCooldownSeconds = 60;
    
    if (otpCooldownTimer) {
        clearInterval(otpCooldownTimer);
    }
    
    otpCooldownTimer = setInterval(() => {
        otpCooldownSeconds--;
        if (otpCooldownSeconds <= 0) {
            clearInterval(otpCooldownTimer);
            resendBtn.disabled = false;
            resendBtn.innerText = 'Resend OTP';
            resendBtn.style.color = 'var(--primary)';
            resendBtn.style.fontWeight = '700';
        } else {
            resendBtn.innerText = `Resend OTP in ${otpCooldownSeconds}s`;
            resendBtn.style.color = 'var(--muted)';
            resendBtn.style.fontWeight = '500';
        }
    }, 1000);
}

// Verify OTP
async function verifyEmailOtp() {
    const email = document.getElementById('residentEmail').value.trim();
    const otp = document.getElementById('residentOtp').value.trim();

    if (!otp || otp.length !== 6) {
        Swal.fire({ icon: 'warning', title: 'Input Required', text: 'Please enter the 6-digit OTP code sent to your email.' });
        return;
    }

    const btn = document.getElementById('residentBtn');
    const btnLabel = btn.querySelector('.btn-label');
    const spinner = btn.querySelector('.spinner');

    btnLabel.style.display = 'none';
    spinner.style.display = 'inline-block';
    btn.disabled = true;

    try {
        const response = await fetch('api/auth/verify_email_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email, otp: otp })
        });
        const data = await response.json();

        if (response.ok && data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Welcome Back!',
                text: 'Signing you in...',
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                window.location.href = data.redirect || '/resident';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Verification Failed',
                text: data.message || 'Invalid or expired OTP.'
            });
            btnLabel.style.display = 'inline-flex';
            spinner.style.display = 'none';
            btn.disabled = false;
        }
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Connection Error', text: 'Could not connect to authentication server.' });
        btnLabel.style.display = 'inline-flex';
        spinner.style.display = 'none';
        btn.disabled = false;
    }
}

// Handle Form Submission
async function handleLogin(event, role) {
    event.preventDefault();
    if (role === 'resident' && currentResidentAuthMode === 'otp') {
        const otpInputVisible = document.getElementById('residentOtpInputGroup').style.display !== 'none';
        if (!otpInputVisible) {
            await requestEmailOtp();
        } else {
            await verifyEmailOtp();
        }
        return;
    }

    const btn = document.getElementById(role === 'staff' ? 'staffBtn' : 'residentBtn');
    const btnLabel = btn.querySelector('.btn-label');
    const spinner = btn.querySelector('.spinner');

    btnLabel.style.display = 'none';
    spinner.style.display = 'inline-block';
    btn.disabled = true;

    let ident, pass;
    if (role === 'staff') {
        ident = document.getElementById('staffIdentifier').value.trim();
        pass = document.getElementById('staffPassword').value;
    } else {
        ident = document.getElementById('residentIdentifier').value.trim();
        pass = document.getElementById('residentPassword').value;
    }

    if (!ident || !pass) {
        Swal.fire({ icon: 'warning', title: 'Fields Required', text: 'Please enter both your identifier and password.' });
        btnLabel.style.display = 'inline-flex';
        spinner.style.display = 'none';
        btn.disabled = false;
        return;
    }

    try {
        const response = await fetch('api/auth/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: ident, password: pass })
        });

        let data;
        const textResponse = await response.text();
        try {
            data = JSON.parse(textResponse);
        } catch (jsonErr) {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: textResponse ? textResponse.substring(0, 300) : 'Server returned an invalid response. Please verify database connection.'
            });
            btnLabel.style.display = 'inline-flex';
            spinner.style.display = 'none';
            btn.disabled = false;
            return;
        }

        if (data.success) {
            if (data.requires_otp) {
                Swal.fire({
                    icon: 'info',
                    title: 'Two-Factor Verification',
                    text: data.message || 'A verification code has been dispatched to your email.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = data.redirect || '/verify-otp';
                });
                return;
            }

            Swal.fire({
                icon: 'success',
                title: 'Welcome Back!',
                text: 'Signing you in...',
                timer: 1000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = data.redirect || (role === 'resident' ? '/resident' : '/dashboard');
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: data.message || 'Invalid username/email or password.'
            });
            btnLabel.style.display = 'inline-flex';
            spinner.style.display = 'none';
            btn.disabled = false;
        }
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Connection Error',
            text: err.message || 'Could not connect to authentication server.'
        });
        btnLabel.style.display = 'inline-flex';
        spinner.style.display = 'none';
        btn.disabled = false;
    }
}

// Forgot Password Helper
function handleForgotPassword() {
    window.location.href = 'forgot_password.php';
}
</script>

</body>
</html>
