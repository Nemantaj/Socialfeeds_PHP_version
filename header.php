<?php 

    require "php/config.php";

    if(isset($_SESSION['username'])){

        $userLoggedIn = $_SESSION['username'];
        $user_details_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$userLoggedIn'");
        $user = mysqli_fetch_array($user_details_query);
        $user1 = $user['first_name'];

    }else{

        header("Location: registration.php");

    }

?>

<html>

    <head>

    

