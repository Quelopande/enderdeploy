<?php
function deleteCookie($name) {
    setcookie($name, '', time() - 3600, '/', '', true, true);
}

deleteCookie('id');
session_unset();
session_destroy();

header('Location: ../auth/signin');
exit;
?>