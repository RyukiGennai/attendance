<?php
session_start();
session_destroy();
header('Location: 001_index.php');
exit;