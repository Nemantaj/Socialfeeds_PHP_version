<?php
        
            include "header.php";

            include "php/plugins.php";

            include "php/classes/Users.php";

            include "php/classes/Post.php";

            include "php/classes/Message.php";

            include "php/classes/Notification.php";

            include "php/settings_handler.php";

?>
            <title>Settings</title>

</head>

<body>

    <?php include "navbar.php"; ?>

    <div class="container">

    <div class="main_column">

        <div class="settings_body">

            <div class="settings_header">
                <h5>Settings</h5>
            </div>

            <div class="change_img_settings">

                <div class="settings_img">

                    <img src="<?php echo $user['profile_pic'] ?>" alt="">

                </div>

                <div class="settings_info">

                    <span><?php echo $user['first_name']." ".$user['last_name']; ?></span><br>
                    <a href="upload.php">Change your current profile picture.</a>

                </div>
                

            </div>

            <?php
                $user_data_query = mysqli_query($conn, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
                $row = mysqli_fetch_array($user_data_query);

                $first_name = $row['first_name'];
                $last_name = $row['last_name'];
                $email = $row['email'];
            ?>

            <div class="change_user_settings">

                <div class="change_user_header">
                    <h5>Change User Details</h5>
                </div>

                <div class="user_form_settings">
                    <form class="user_form_set" action="settings.php" method="POST">
                        First Name: <input type="text" name="first_name" value="<?php echo $first_name; ?>" id="settings_input"><br>
                        Last Name: <input type="text" name="last_name" value="<?php echo $last_name; ?>" id="settings_input"><br>
                        Email: <input type="text" name="email" value="<?php echo $email; ?>" id="settings_input"><br>

                        <?php echo $message; ?>

                        <input type="submit" name="update_details" id="save_details" value="Update Details" class="info settings_submit"><br>
                    </form>
                </div>

            </div>

            <div class="change_bio_settings">

                <div class="change_bio_header">
                    <h5>Change Your Bio</h5>
                </div>

                <div class="bio_form_settings">
                                        <div class='bio_text'>
                                            <form action='' method='POST'>
                                            <textarea name='bio_text_body' placeholder='Enter something about yourself under 100 characters.'></textarea>
                                        </div>
                                        <div class='bio_submit_btn'>
                                            <button class='bio_submit_button' name='bio_submit'>Change Bio</button>
                                            </form>
                                        </div>
                </div>

            </div>

            <div class="change_password_settings">

                <div class="change_password_header">
                    <h5>Change Password</h5>
                </div>

                <div class="passowrd_form_settings">
                <form class="user_form_set" action="settings.php" method="POST">
                    Old Password: <input type="password" name="old_password" id="settings_input"><br>
                    New Password: <input type="password" name="new_password_1" id="settings_input"><br>
                    New Password Again: <input type="password" name="new_password_2" id="settings_input"><br>

                    <?php echo $password_message; ?>

                    <input type="submit" name="update_password" id="save_details" value="Update Password" class="info settings_submit"><br>
                </form>
                </div>

            </div>

            <div class="close_user_settings">

                <div class="close_user_header">
                    <h5>Close Your Account</h5>
                </div>

                <div class="close_form_settings">
                <form action="settings.php" method="POST">
                    <input type="submit" name="close_account" id="close_account" value="Close Account" class="danger settings_submit">
                </form>
                </div>

            </div>

        </div>

    </div>

    </div>

</body>

</html>