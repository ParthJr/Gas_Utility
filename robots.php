<?php
/**
 * StayFlow — Dynamic Robots.txt Generator
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

header('Content-Type: text/plain; charset=utf-8');

$domain = defined('CANONICAL_DOMAIN') ? rtrim(CANONICAL_DOMAIN, '/') : 'https://stayflow.antideploy.app';

echo "User-agent: *" . PHP_EOL;
echo "Allow: /" . PHP_EOL . PHP_EOL;
echo "# Disallow Internal SaaS & Admin Areas" . PHP_EOL;
echo "Disallow: /super-admin/" . PHP_EOL;
echo "Disallow: /admin/" . PHP_EOL;
echo "Disallow: /resident/" . PHP_EOL;
echo "Disallow: /staff/" . PHP_EOL;
echo "Disallow: /api/" . PHP_EOL;
echo "Disallow: /pg-management-system/" . PHP_EOL;
echo "Disallow: /config/" . PHP_EOL;
echo "Disallow: /services/" . PHP_EOL . PHP_EOL;
echo "# Explicitly Allow Public Content & Marketing" . PHP_EOL;
echo "Allow: /blog/" . PHP_EOL;
echo "Allow: /whitepaper/" . PHP_EOL;
echo "Allow: /whitepapers/" . PHP_EOL;
echo "Allow: /property-management-software-*.php" . PHP_EOL;
echo "Allow: /uploads/blogs/" . PHP_EOL;
echo "Allow: /uploads/whitepapers/" . PHP_EOL . PHP_EOL;
echo "Sitemap: " . $domain . "/sitemap.xml" . PHP_EOL;
