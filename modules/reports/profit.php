<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

// Redirect to revenue report (profit is calculated in revenue report)
header('Location: revenue.php');
exit;
