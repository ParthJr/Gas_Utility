<?php
/**
 * Dynamic XML Sitemap Generator for StayFlow
 * Public Domain: https://stayflow.antideploy.app
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/xml; charset=utf-8');

$domain = defined('CANONICAL_DOMAIN') ? rtrim(CANONICAL_DOMAIN, '/') : 'https://stayflow.antideploy.app';
$lastmod = date('Y-m-d');

$pages = [
    '' => ['priority' => '1.0', 'changefreq' => 'weekly'],
    'property-management-software-features.php' => ['priority' => '0.9', 'changefreq' => 'weekly'],
    'property-management-software-pricing.php' => ['priority' => '0.9', 'changefreq' => 'weekly'],
    'property-management-software-about.php' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'property-management-software-services.php' => ['priority' => '0.8', 'changefreq' => 'weekly'],
    'property-management-software-contact.php' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'property-management-software-faq.php' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    'property-management-software-download.php' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    'property-management-software-tutorial.php' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    'property-management-software-testimonial.php' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    'property-management-software-use-cases.php' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    'property-management-software-why-choose-us.php' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    'property-management-software-process.php' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    'property-management-software-blog.php' => ['priority' => '0.8', 'changefreq' => 'weekly'],
    'property-management-software-case-study.php' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    'property-management-software-customer.php' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    'property-management-software-documentation.php' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    'property-management-software-glossary.php' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    'property-management-software-security.php' => ['priority' => '0.5', 'changefreq' => 'monthly'],
    'property-management-software-gdpr.php' => ['priority' => '0.5', 'changefreq' => 'monthly'],
    'property-management-software-privacy.php' => ['priority' => '0.5', 'changefreq' => 'monthly'],
    'property-management-software-terms.php' => ['priority' => '0.5', 'changefreq' => 'monthly'],
    'property-management-software-refund.php' => ['priority' => '0.5', 'changefreq' => 'monthly'],
    'property-management-software-career.php' => ['priority' => '0.5', 'changefreq' => 'monthly'],
    'property-management-software-whitepaper.php' => ['priority' => '0.7', 'changefreq' => 'monthly'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $path => $meta): ?>
  <url>
    <loc><?= htmlspecialchars($domain . '/' . $path) ?></loc>
    <lastmod><?= $lastmod ?></lastmod>
    <changefreq><?= $meta['changefreq'] ?></changefreq>
    <priority><?= $meta['priority'] ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
