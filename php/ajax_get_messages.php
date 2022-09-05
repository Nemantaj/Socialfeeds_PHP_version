<?php

    include "../header.php";
    include "classes/Users.php";
    include "classes/Message.php";

    $message_obj = new Message($conn, $userLoggedIn);;
    echo $message_obj->getMessages($_REQUEST['u']);

?>