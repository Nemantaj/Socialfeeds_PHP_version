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

    </head>

    <body>
        
            <?php

                if(isset($_GET['post_id'])){
                    $post_id = $_GET['post_id'];
                }

                $get_likes = mysqli_query($conn, "SELECT likes, added_by FROM posts WHERE id = '$post_id'");
                $row = mysqli_fetch_array($get_likes);
                $total_likes = $row['likes'];
                $user_liked = $row['added_by'];

                $user_details_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$user_liked'");
                $row = mysqli_fetch_array($user_details_query);
                $total_user_likes = $row['num_likes'];

                //Like button
                if(isset($_POST['like_button'])){
                    $total_likes++;
                    $query = mysqli_query($conn, "UPDATE posts SET likes = '$total_likes' WHERE id = '$post_id'");
                    $total_user_likes++;
                    $user_likes = mysqli_query($conn, "UPDATE users SET num_likes = '$total_user_likes' WHERE username = '$user_liked'");
                    $insert_user = mysqli_query($conn, "INSERT INTO likes VALUES (NULL, '$userLoggedIn', '$post_id')");

                    if($user_liked != $userLoggedIn){
                        $notification = new Notification($conn, $userLoggedIn);
                        $notification->insertNotification($post_id, $user_liked, "like");
                    }
                }


                //Unlike button
                if(isset($_POST['unlike_button'])){
                    $total_likes--;
                    $query = mysqli_query($conn, "UPDATE posts SET likes = '$total_likes' WHERE id = '$post_id'");
                    $total_user_likes--;
                    $user_likes = mysqli_query($conn, "UPDATE users SET num_likes = '$total_user_likes' WHERE username = '$user_liked'");
                    $insert_user = mysqli_query($conn, "DELETE FROM likes WHERE username = '$userLoggedIn' AND post_id = '$post_id'");
                }


                //Check for the previous likes
                $check_query = mysqli_query($conn, "SELECT * FROM likes WHERE username = '$userLoggedIn' AND post_id = '$post_id'");
                $num_rows = mysqli_num_rows($check_query);

                if($num_rows > 0){
                    echo '
                    <div class="likes_frame">
                    <form action="like.php?post_id='.$post_id.'" id="likes" method="POST">
                            <button type="submit" class="comment_like" name="unlike_button">

                                <i class="material-icons">favorite</i>
                            
                            </button>
                            
                            <div class="like_value">
                                '.$total_likes.' Likes
                            </div>

                            </form>
                    </div>
                    ';
                }else{
                    echo '
                    <div class="likes_frame">
                    <form action="like.php?post_id='.$post_id.'" id="likes" method="POST">
                            <button type="submit" class="comment_like" name="like_button">

                                <i class="material-icons">favorite_border</i>

                            </button>
                            <div class="like_value">
                                '.$total_likes.' Likes
                            </div>

                            </form>
                    </div>
                    ';
                }

            ?>

    </body>

</html>    