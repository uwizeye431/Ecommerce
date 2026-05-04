<?php
require_once __DIR__ . '/includes/auth.php';
logout_user();
redirect(APP_URL . '/index.php');

