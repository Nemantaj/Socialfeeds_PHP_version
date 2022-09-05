<?php
        
            include "header.php";

            include "php/plugins.php";

            include "php/classes/Users.php";

            include "php/classes/Post.php";

            include "php/classes/Message.php";

            include "php/classes/Notification.php";

            if(isset($_GET['profile_username'])){

                $username = $_GET['profile_username'];
                $user_details_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
                $user_array = mysqli_fetch_array($user_details_query);

                $num_friends = (substr_count($user_array['friend_array'], ",")) - 1;

            }

            if(isset($_POST['remove_friend'])){

                $user = new User($conn, $userLoggedIn);
                $user->removeFriend($username);

            }

            if(isset($_POST['add_friend'])){

                $user = new User($conn, $userLoggedIn);
                $user->sendRequest($username);

            }

            if(isset($_POST['respond_request'])){

                header("Location: requests.php");

            }

            $bio_submit_result = "";

            if(isset($_POST['bio_submit'])){

                $length = mb_strlen($_POST['bio_text_body']);
                if($length > 100){
                    $bio_submit_result = "Length should be under 100 characters.";
                }else{
                    $bio_text_body = $_POST['bio_text_body'];
                    $query = mysqli_query($conn, "UPDATE users SET bio = '$bio_text_body' WHERE username = '$userLoggedIn'");
                }

            }

            $logged_in_user_obj = new User($conn, $userLoggedIn);

        ?>

        <title>Social</title>

    </head>

    <body>

        <?php include "navbar.php"; ?>
        
        <div class="container">

            <div class="main-container">

                <div class="row" id="profile_info_main">

                    <div class="col-md-5 profile_info_1">

                        <div class="profile_img">

                            <img src="<?php echo $user_array['profile_pic']; ?>" alt="">

                        </div>

                        <div class="profile_info">

                            <p id="info_main"><?php echo $user_array['first_name']. " " .$user_array['last_name']; ?></p>
                            <p id="info_user">  <?php echo $user_array['username']; ?> | <?php 
                                                                if($userLoggedIn != $username){
                                                                    $mutual = $logged_in_user_obj->getMutualFriends($username);
                                                                    $mutual = "Mutual Friends: ".$mutual;
                                                                }else{
                                                                    $mutual = "";
                                                                }
                                                    echo $mutual;?></p>
                            <p id="info_email"><span><i class="material-icons">post_add</i><?php echo $user_array['num_posts']; ?></span>
                                <span><a href="friend_list.php?q=<?php echo $username; ?>"><i class="material-icons">people</i><?php echo $num_friends; ?></a></span>
                                <span><i class="material-icons">favorite_border</i><?php echo $user_array['num_likes']; ?></span></p>

                        </div>

                    </div>

                    <div class="col-md-7 profile_info_2">

                        <div class="about">

                            <div class="about_1 col-6">

                                <?php 
                                    if($user_array['bio'] == "" && $user_array['username'] == $userLoggedIn){
                                        echo "<div class='bio_text'>
                                            <form action='' method='POST'>
                                            <textarea name='bio_text_body' placeholder='Enter something about yourself under 100 characters.'></textarea>
                                        </div>
                                        <div class='bio_submit_btn'>
                                            <button class='bio_submit_button' name='bio_submit'><i class='material-icons'>send</i></button>
                                            </form>
                                        </div>";
                                    }else if($user_array['bio'] == "" && $user_array['username'] != $userLoggedIn){
                                        echo "No information right now!";
                                    }else{
                                        echo $user_array['bio'];
                                    }
                                ?>

                            </div>

                            <div class="about_2 col-6">

                                <form action="<?php echo $username; ?>" method="POST">
            
                                    <?php
                                        $profile_user_obj = new User($conn, $username);
                                        if($profile_user_obj->isClosed()){
                                            header("Location: user_closed.php");
                                        }

                                        if($userLoggedIn != $username){
                                            if($logged_in_user_obj->isFriend($username)){
                                                echo '<button type="submit" name="remove_friend" class="danger req" value="Remove Friend"><i class="material-icons">person_remove</i></button>';
                                            }else if($logged_in_user_obj->didReceiveRequest($username)){
                                                echo '<button type="submit" name="respond_request" class="warning req" value="Respond to request"><i class="material-icons">compare_arrows</i></button>';
                                            }else if($logged_in_user_obj->didSendRequest($username)){
                                                echo '<button type="submit" name="" class="default req" value="Request Send"><i class="material-icons">send</i></button>';
                                            }else{
                                                echo '<button type="submit" name="add_friend" class="success req" value="Add Friend"><i class="material-icons">person_add</i></button>';
                                            }
                                        }

                                        
                                    ?>

                                </form>

                                <button id="profile_message"><a href="messages.php?u=<?php echo $user_array['username']; ?>"><i class="material-icons">chat</i></a></button>

                                <!-- Button trigger modal -->
                                <button id="post_something" type="button" data-bs-toggle="modal" data-bs-target="#post_form">
                                    <i class="material-icons">post_add</i>
                                </button>

                                <p><?php echo $bio_submit_result; ?></p>

                            <!-- Modal -->
                                <div class="modal modal1 fade" id="post_form" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Post something!</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="" class="profile_post" method="POST">

                                            <div class="form-group">
                                                <textarea name="post_body" rows="3"></textarea>
                                                <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
                                                <input type="hidden" name="user_to" value="<?php echo $username; ?>">
                                            </div>

                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" name="post_button" id="submit_profile_post" class="btn btn-primary">Post</button>
                                    </div>
                                    </div>
                                </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="posts_area"></div>
                
                    <div class="img-loading">

                        <img id="loading" src="assets/images/icons/loading.gif" alt="">

                    </div>

            </div>

        </div>

        <script>
            
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';
            var profileUsername = '<?php echo $username; ?>';

            $(document).ready(function() {

                $('#loading').show();

                //Original ajax request for loading first posts 
                $.ajax({
                    url: "php/ajax_load_profile_posts.php",
                    type: "POST",
                    data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                    cache:false,

                    success: function(data) {
                        $('#loading').hide();
                        $('.posts_area').html(data);
                    }
                });

                $(window).scroll(function() {
                    var height = $('.posts_area').height(); //Div containing posts
                    var scroll_top = $(this).scrollTop();
                    var page = $('.posts_area').find('.nextPage').val();
                    var noMorePosts = $('.posts_area').find('.noMorePosts').val();

                    if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                        $('#loading').show();

                        var ajaxReq = $.ajax({
                            url: "php/ajax_load_profile_posts.php",
                            type: "POST",
                            data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                            cache:false,

                            success: function(response) {
                                $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
                                $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

                                
                                $('.posts_area').append(response);
                                $('#loading').hide();
                                $('.img-loading').hide();
                            }
                        });

                    } //End if 

                    return false;

                }); //End (window).scroll(function())


            });

        </script>

    </body>    

</html>    