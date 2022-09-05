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

        <div class="user_closed_body">

            <div class="user_header">

                <h5>Close Account</h5>

            </div>

            <div class="user_close_info">

                <p>Are you sure you want to close your account?<br>
                Closing your account will hide your profile and all your activity from other users.<br>
                You can re-open your account at any time by simply logging in.<br></p>

            </div>

            <div class="user_close_form">

                <form action="close_account.php" method="POST">
                    <input type="submit" name="close_account" id="close_account" value="Yes! Close it!" class="settings_submit_cancel">
                    <input type="submit" name="cancel" id="update_details" value="No way!" class="settings_submit_noway">
                </form>

            </div>

        </div>

    </div>

    </div>

</body>

</html>