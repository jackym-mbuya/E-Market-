<?php
require_once '../config/functions.php';

$action = $_GET['action'] ?? '';

switch($action) {
    case 'logout':
        session_destroy();
        redirect('../../index.php');
        break;
        
    default:
        redirect('../../index.php');
}
