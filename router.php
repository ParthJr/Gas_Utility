<?php
/**
 * StayFlow Development Server Router for PHP Built-In Server (php -S)
 * Maps direct module URLs (/super-admin/*, /admin/*, /resident/*, /staff/*, /api/*, /onboarding/*)
 * to the SaaS application in /pg-management-system/
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$docRoot = __DIR__;

// 1. Dynamic Sitemap and Robots.txt
if ($uri === '/sitemap.xml') {
    $_SERVER['SCRIPT_FILENAME'] = $docRoot . '/sitemap.php';
    require $docRoot . '/sitemap.php';
    return true;
}
if ($uri === '/robots.txt') {
    $_SERVER['SCRIPT_FILENAME'] = $docRoot . '/robots.php';
    require $docRoot . '/robots.php';
    return true;
}

// 2. If static file directly exists in root, let PHP serve it
if ($uri !== '/' && file_exists($docRoot . $uri) && !is_dir($docRoot . $uri)) {
    return false;
}

// Clean Blog URLs: /blog/{slug} and /blog
if (preg_match('#^/blog/([a-zA-Z0-9_-]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    $_SERVER['SCRIPT_FILENAME'] = $docRoot . '/property-management-software-blog-details.php';
    require $docRoot . '/property-management-software-blog-details.php';
    return true;
}
if ($uri === '/blog' || $uri === '/blog/') {
    $_SERVER['SCRIPT_FILENAME'] = $docRoot . '/property-management-software-blog.php';
    require $docRoot . '/property-management-software-blog.php';
    return true;
}

// Clean Whitepaper URLs: /whitepaper/{slug} and /whitepapers
if (preg_match('#^/(whitepaper|whitepapers)/([a-zA-Z0-9_-]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[2];
    $_SERVER['SCRIPT_FILENAME'] = $docRoot . '/property-management-software-whitepaper-details.php';
    require $docRoot . '/property-management-software-whitepaper-details.php';
    return true;
}
if ($uri === '/whitepapers' || $uri === '/whitepapers/' || $uri === '/whitepaper' || $uri === '/whitepaper/') {
    $_SERVER['SCRIPT_FILENAME'] = $docRoot . '/property-management-software-whitepaper.php';
    require $docRoot . '/property-management-software-whitepaper.php';
    return true;
}

// Clean Team URLs: /team/{slug}, /team-details/{slug}, and /team
if (preg_match('#^/(team|team-details)/([a-zA-Z0-9_-]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[2];
    $_SERVER['SCRIPT_FILENAME'] = $docRoot . '/property-management-software-team-details.php';
    require $docRoot . '/property-management-software-team-details.php';
    return true;
}
if ($uri === '/team' || $uri === '/team/') {
    $_SERVER['SCRIPT_FILENAME'] = $docRoot . '/property-management-software-team.php';
    require $docRoot . '/property-management-software-team.php';
    return true;
}


// 2. Auth standalone pages at root (/login, /login.php, /logout.php, /signup.php, /verify_otp.php, /forgot_password.php, /reset_password.php)
$authPages = ['login', 'login.php', 'logout', 'logout.php', 'signup', 'signup.php', 'verify_otp.php', 'forgot_password.php', 'reset_password.php'];
$cleanUri = trim($uri, '/');
if (in_array($cleanUri, $authPages)) {
    $script = (str_ends_with($cleanUri, '.php')) ? $cleanUri : $cleanUri . '.php';
    $target = $docRoot . '/pg-management-system/' . $script;
    if (file_exists($target)) {
        $_SERVER['SCRIPT_FILENAME'] = $target;
        chdir(dirname($target));
        require $target;
        return true;
    }
}

// 2. Direct SaaS Module Routing
if (preg_match('#^/(super-admin|admin|resident|staff|api|onboarding|services|cron)(/.*)?$#', $uri, $matches)) {
    $saasPath = $docRoot . '/pg-management-system' . $uri;
    
    if (is_dir($saasPath)) {
        $saasPath = rtrim($saasPath, '/') . '/index.php';
    } elseif (!file_exists($saasPath) && file_exists($saasPath . '.php')) {
        $saasPath .= '.php';
    }
    
    if (file_exists($saasPath)) {
        $_SERVER['SCRIPT_FILENAME'] = $saasPath;
        chdir(dirname($saasPath));
        require $saasPath;
        return true;
    }
}

// 3. Subdirectory SaaS Routing (/pg-management-system/...)
if (str_starts_with($uri, '/pg-management-system/')) {
    $target = $docRoot . $uri;
    if (is_dir($target)) {
        $target = rtrim($target, '/') . '/index.php';
    } elseif (!file_exists($target) && file_exists($target . '.php')) {
        $target .= '.php';
    }
    if (file_exists($target)) {
        $_SERVER['SCRIPT_FILENAME'] = $target;
        chdir(dirname($target));
        require $target;
        return true;
    }
}

// 4. Marketing Clean URLs
if ($uri !== '/' && file_exists($docRoot . $uri . '.php')) {
    $_SERVER['SCRIPT_FILENAME'] = $docRoot . $uri . '.php';
    require $docRoot . $uri . '.php';
    return true;
}

// 5. Default homepage
if ($uri === '/' || $uri === '') {
    require $docRoot . '/index.php';
    return true;
}

return false;
