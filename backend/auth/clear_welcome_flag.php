<?php
session_start();
unset($_SESSION['show_welcome']);
http_response_code(204); // No content response
?>