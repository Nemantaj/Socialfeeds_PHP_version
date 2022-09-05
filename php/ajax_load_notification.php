<?php

    include "config.php";
    include "classes/Users.php";
    include "classes/Notification.php";

    $limit = 7;
    $notification = new Notification($conn, $_REQUEST['userLoggedIn']);
    echo $notification->getNotification($_REQUEST, $limit);

?>