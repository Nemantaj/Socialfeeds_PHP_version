<?php

    ob_start();
    session_start();

    $timezone = date_default_timezone_set('Asia/Kolkata');

    $conn = mysqli_connect("localhost", "root", "mini7", "social");

    if(mysqli_connect_errno()){

        echo "Failed to connect: " . mysqli_connect_errno();

    }

?>