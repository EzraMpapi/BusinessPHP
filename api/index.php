<?php
// BusinessSphere ERP — Vercel Entry Point
// All paths go one level up with __DIR__.'/../'

define('ROOT', dirname(__DIR__));

require_once ROOT . '/config.php';

// Override asset path for Vercel
define('ASSET_PATH', '/assets');

require_once ROOT . '/includes/layout.php';
