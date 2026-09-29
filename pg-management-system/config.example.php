<?php
/**
 * StayFlow PG SaaS — Production Configuration Template
 * 
 * Instructions:
 * 1. Copy this file to `config/config.php` or set corresponding variables in `.env`.
 * 2. Fill in your InfinityFree database credentials (from InfinityFree vPanel -> MySQL Databases).
 * 3. Update APP_URL with your production domain (e.g. https://yourname.infinityfreeapp.com).
 */

return [
    // --- Application Environment ---
    'APP_NAME'     => 'StayFlow — PG Management SaaS',
    'APP_ENV'      => 'production', // 'development' or 'production'
    'APP_DEBUG'    => false,        // Set to false in production to prevent exposing internal errors
    'APP_URL'      => 'https://YOUR-INFINITYFREE-DOMAIN.com',
    'TIMEZONE'     => 'Asia/Kolkata',

    // --- InfinityFree MySQL Database Connection ---
    // (Obtained from InfinityFree vPanel -> MySQL Databases)
    'DB_HOST'      => 'sqlXXX.infinityfree.com', // e.g. sql305.infinityfree.com (NOT localhost)
    'DB_NAME'      => 'if0_XXXXXXXX_stayflow',   // e.g. if0_42764776_stayflow
    'DB_USER'      => 'if0_XXXXXXXX',            // e.g. if0_42764776
    'DB_PASS'      => 'YOUR_INFINITYFREE_CPANEL_PASSWORD',
    'DB_PORT'      => 3306,
    'DB_CHARSET'   => 'utf8mb4',

    // --- Security & Encryption ---
    'JWT_SECRET'   => 'CHANGE_THIS_TO_A_RANDOM_SECURE_STRING_' . bin2hex(random_bytes(16)),
    'CRON_KEY'     => 'STAYFLOW_CRON_SECRET_' . bin2hex(random_bytes(8)),

    // --- Optional External Integrations ---
    'MAIL_HOST'    => 'smtp.gmail.com',
    'MAIL_PORT'    => 587,
    'MAIL_USERNAME'=> '',
    'MAIL_PASSWORD'=> '',
    'MAIL_FROM'    => 'no-reply@yourdomain.com',
    'MAIL_FROM_NAME'=> 'StayFlow Notifications',

    // WhatsApp Meta Cloud API (Optional)
    'WHATSAPP_ENABLED'     => false,
    'WHATSAPP_PHONE_ID'    => '',
    'WHATSAPP_ACCESS_TOKEN'=> '',
    'WHATSAPP_WABA_ID'     => '',
];
