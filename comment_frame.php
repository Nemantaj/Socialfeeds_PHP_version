<?php

            require "php/config.php";
            include "php/classes/Users.php";
            include "php/classes/Post.php";
            include "php/classes/Message.php";
            include "php/classes/Notification.php";

            if(isset($_SESSION['username'])){

                $userLoggedIn = $_SESSION['username'];
                $user_details_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$userLoggedIn'");
                $user = mysqli_fetch_array($user_details_query);
        
            }else{
        
                header("Location: registration.php");
        
            }
        
?>

<html>

    <head>

        <?php
        
        include "php/plugins.php";

        ?>

        <style>

            body{

                background-color: #fbf3f4;

            }

        </style>

    </head>

    <body>

        <script>

            function toggle(){

                var element = document.getElementById("comment_section");

                if(element.style.display = "block"){
                    element.style.display = "none";
                }else{
                    element.style.display = "block";
                }

            }

        </script>

        <?php

            if(isset($_GET['post_id'])){
                $post_id = $_GET['post_id'];
            }

            $user_query = mysqli_query($conn, "SELECT added_by, user_to FROM posts WHERE id = '$post_id'");
            $row = mysqli_fetch_array($user_query);

            $posted_to = $row['added_by'];
            $user_to = $row['user_to'];

            if(isset($_POST['post_comment' . $post_id])){

                $post_body = $_POST['post_body'];
                $post_body = mysqli_real_escape_string($conn, $post_body);
                $date_time_now = date("Y-m-d H:i:s");
                $insert_post = mysqli_query($conn, "INSERT INTO comments VALUES (NULL, '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')");

                if($posted_to != $userLoggedIn){
                    $notification = new Notification($conn, $userLoggedIn);
                    $notification->insertNotification($post_id, $posted_to, "comment");
                }
                if($user_to != 'none' && $user_to != $userLoggedIn){
                    $notification = new Notification($conn, $userLoggedIn);
                    $notification->insertNotification($post_id, $user_to, "profile_comment");
                }

                $get_commentors = mysqli_query($conn, "SELECT * FROM comments WHERE post_id = '$post_id'");
                $notified_users = array();
                while($row = mysqli_fetch_array($get_commentors)){

                    if($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to
                        && $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)){
                            $notification = new Notification($conn, $userLoggedIn);
                            $notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

                            array_push($notified_users, $row['posted_by']);
                    }

                }
            }

        ?>

        <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
    
            <textarea name="post_body" id="commentsPosted" rows="1" class="form-control" placeholder="Type your comments here!"></textarea>
            <input type="submit" name="post_comment<?php echo $post_id; ?>" value="Post">
    
        </form>

        <!-- Load comments -->

        <?php 
        
              $get_comments = mysqli_query($conn, "SELECT * FROM comments WHERE post_id = '$post_id' ORDER BY id ASC");
              $count = mysqli_num_rows($get_comments);
              
              if($count != 0){

                while($comment = mysqli_fetch_array($get_comments)){

                    $comment_body = $comment['post_body'];
                    $posted_to = $comment['posted_to'];
                    $posted_by = $comment['posted_by'];
                    $date_added = $comment['date_added'];
                    $removed = $comment['removed'];

                        $date_time_now = date("Y-m-d H:i:s");
                        $start_date = new DateTime($date_added);
                        $end_date = new DateTime($date_time_now);
                        $interval = $start_date->diff($end_date);

                        if($interval->y >= 1){

                            if($interval == 1){
                                $time_message = $interval->y . " year ago";
                            }else{
                                $time_message = $interval->y . " years ago";
                            }

                        }else if($interval->m >= 1){

                            if($interval->d == 0){
                                $days = " ago";
                            }else if($interval->d == 1){
                                $days = $interval->d . " day ago";
                            }else{
                                $days = $interval->d . " days ago";
                            }

                            if($interval->m ==1){
                                $time_message = $interval->m . " month". $days;
                            }else{
                                $time_message = $interval->m . " months". $days;
                            }

                        }else if($interval->d >= 1){

                            if($interval->d == 1){
                                $time_message = "Yesterday";
                            }else{
                                $time_message = $interval->d . " days ago";
                            }

                        }else if($interval->h >= 1){

                            if($interval->h == 1){
                                $time_message = $interval->h . " hour ago";
                            }else{
                                $time_message = $interval->h . " hours ago";
                            }

                        }else if($interval->i >= 1){

                            if($interval->i == 1){
                                $time_message = $interval->i . " minute ago";
                            }else{
                                $time_message = $interval->i . " minutes ago";
                            }

                        }else{

                            if($interval->s < 30){
                                $time_message = "Just now";
                            }else{
                                $time_message = $interval->s . " seconds ago";
                            }

                        }

                        $user_obj = new User($conn, $posted_by);

                        ?>

                        <div class="comment-section">

                            <div class="comment-info">

                                <a href="<?php echo $posted_by; ?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic(); ?>"></a>
                                <a href="<?php echo $posted_by; ?>" target="_parent"><span id="comment-names"><b><?php echo $user_obj->getFirstAndLastName()?></b></a><span id="comment-timestamp-messages"><?php echo $time_message; ?></span></span>

                            </div>

                            <div class="comment-body">

                                <?php echo $comment_body;?><br>

                            </div>

                        </div>

                        <?php


                }

              }else{

                echo "<center>No comments to show!</center>";

              }

        ?>



    </body>

</html>