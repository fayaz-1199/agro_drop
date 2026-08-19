<?php

require_once '../includes/app.php';
$_SESSION = [];
session_destroy();
session_start();
$_SESSION['flash'] = ['success','You have been logged out.'];
redirect('index.php');
