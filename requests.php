<?php
        
            include "header.php";

            include "php/plugins.php";

            include "php/classes/Users.php";

            include "php/classes/Post.php";

            include "php/classes/Message.php";

            include "php/classes/Notification.php";

            $req_res = "";

?>

</head>

<body>

    <div class="req_main">

    <?php include "navbar.php"; ?>

        <div class="container">

            <div class="main_container_req">

            <?php 
            
                $query = mysqli_query($conn, "SELECT * FROM friend_requests WHERE user_to = '$userLoggedIn'");
                if(mysqli_num_rows($query) == 0){

                    echo "<center>You have no friend requests at this time!</center>";

                }else{

                    while($row = mysqli_fetch_array($query)){

                        $user_from = $row['user_from'];
                        $query2 = mysqli_query($conn, "SELECT profile_pic from users WHERE username = '$user_from'");
                        $user_from_obj = new User($conn, $user_from);

                        $req =  $user_from_obj->getFirstAndLastName() . " sent you a friend request.";
                        $profile_from = $user_from_obj->getProfilePic();

                        $user_from_friend_array = $user_from_obj->getFriendArray();

                        if(isset($_POST['accept_request'.$user_from ])){
                            $add_friend_query = mysqli_query($conn, "UPDATE users SET friend_array = CONCAT(friend_array, '$user_from,') WHERE username = '$userLoggedIn'");
                            $add_friend_query = mysqli_query($conn, "UPDATE users SET friend_array = CONCAT(friend_array, '$userLoggedIn,') WHERE username = '$user_from'");

                            $delete_query = mysqli_query($conn, "DELETE FROM friend_requests WHERE user_to = '$userLoggedIn' AND user_from = '$user_from'");
                            $req_res = "You are now friends!";
                            header("Location: requests.php");

                        }

                        if(isset($_POST['ignore_request'. $user_from ])){
                            $delete_query = mysqli_query($conn, "DELETE FROM friend_requests WHERE user_to = '$userLoggedIn' AND user_from = '$user_from'");
                            $req_res = "Requests ignored!";
                            header("Location: requests.php");
                        }

                    ?>

                <div class="req_body">

                    <div class="req_img">

                        <img src="<?php echo $profile_from; ?>" alt="">

                    </div>
                    <div class="req_input_box">
                        <div class="req_cur">

                            <span><?php echo $req; ?></span>
                            <span><?php echo $req_res; ?></span>

                        </div>
                        <div class="req_inputs">
                            <form action="requests.php" id="req_forms" method="POST">

                                <button type="submit" class="accept" name="accept_request<?php echo $user_from; ?>" id="accept_button" value="Accept"><i class="material-icons">done</i></button>
                                <button type="submit" class="ignore" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" value="Ignore"><i class="material-icons">clear</i></button>

                            </form>
                        </div>
                    </div>

                </div>

                    <?php

                    }

                }
            
            ?>

            </div>

        </div>

    </div>
    
</body>

</html>