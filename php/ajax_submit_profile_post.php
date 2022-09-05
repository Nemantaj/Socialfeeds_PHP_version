<?php
        
            require "config.php";

            include "classes/Users.php";

            include "classes/Post.php";

            include "php/classes/Message.php";

            include "php/classes/Notification.php";

            if(isset($_POST['post_body'])){

                $imagePath = "";
                $post = new Post($conn, $_POST['user_from']);
                $post->submitPost($_POST['post_body'], $_POST['user_to'], $imagePath);

            }


?>