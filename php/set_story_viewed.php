<?php 

    include "config.php";
    include "classes/Users.php";
    include "classes/Post.php";

    $id = $_POST['story'];
    $user = $_POST['userLoggedIn'];

   $query = mysqli_query($conn, "UPDATE story SET viewed = CONCAT(viewed, '$user,') WHERE id = '$id'");

?>