<?php 

    include "config.php";
    include "classes/Users.php";
    include "classes/Post.php";

    $limit = 10;

    $posts = new Post($conn, $_REQUEST['userLoggedIn']);
    $posts->loadPostFriends($_REQUEST, $limit);

?>