<?php
session_start();
session_unset();
session_destroy();
header("Location: /user/login.php?logout=1");
exit;
?>