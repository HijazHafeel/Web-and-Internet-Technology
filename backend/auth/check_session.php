<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

send_success(['user' => current_user_payload()]);
