<?php
require_once __DIR__.'/fct.inc.php';
startSecureSession();
if (!isLoggedIn()) {
    redirect('index.php?action=login');
}
