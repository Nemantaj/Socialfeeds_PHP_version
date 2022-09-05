<?php

    include "config.php";
    include "classes/Users.php";
    include "classes/Message.php";

    $limit = 7;
    $message = new Message($conn, $_REQUEST['userLoggedIn']);
    echo $message->getConvosDropdown($_REQUEST, $limit);

?>