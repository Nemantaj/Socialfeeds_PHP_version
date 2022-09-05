<?php 

    include "config.php";
    include "classes/Users.php";
    include "classes/Post.php";

    $posts = new Post($conn, $_REQUEST['userLoggedIn']);
    $posts->loadStories($_REQUEST);

?>