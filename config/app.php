<?php
/**
 * pbg_cn runtime config — values from project-root .env
 */
require_once __DIR__ . '/env.php';
pbg_load_env();

return [
    'db' => [
        'host' => pbg_env('database.default.hostname', 'localhost'),
        'user' => pbg_env('database.default.username', 'root'),
        'pass' => pbg_env('database.default.password', ''),
        'name' => pbg_env('database.default.database', 'pbg_cn'),
        'charset' => pbg_env('database.default.charset', 'utf8mb4'),
    ],
    // Same MySQL server: read live draws from powerball DB
    'draw_db' => [
        'host' => pbg_env('database.draw.hostname', pbg_env('database.default.hostname', 'localhost')),
        'user' => pbg_env('database.draw.username', pbg_env('database.default.username', 'root')),
        'pass' => pbg_env('database.draw.password', pbg_env('database.default.password', '')),
        'name' => pbg_env('database.draw.database', 'powerball'),
        'charset' => pbg_env('database.draw.charset', 'utf8mb4'),
    ],
    // Powerball web root (mini view iframe)
    'powerball_base_url' => pbg_env('powerball.base_url', 'http://powerball.com:82/'),
    // Same-server draw fetch
    'powerball_fetch_url' => pbg_env('powerball.fetch_url', 'http://127.0.0.1:82/lottery/getDrawResult'),
    'powerball_fetch_host' => pbg_env('powerball.fetch_host', 'powerball.com'),
    'timezone' => pbg_env('app.timezone', 'Asia/Seoul'),
    'currency' => pbg_env('app.currency', 'USD'),
    'currency_symbol' => pbg_env('app.currency_symbol', '$'),
    'default_machine' => pbg_env('app.default_machine', 'm01'),
];
