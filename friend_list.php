<?php
        
            include "header.php";

            include "php/plugins.php";

            include "php/classes/Users.php";

            include "php/classes/Post.php";

            include "php/classes/Message.php";

            include "php/classes/Notification.php";

            if(isset($_GET['q'])){
                $username = $_GET['q'];
            }

?>
            <title>Search</title>

</head>

<body>

    <?php include "navbar.php"; ?>

    <div class="container">

        <div class="main_column1 column">

            <div class="friend_list_body">

                <div class="friend_list_header">

                    <h5>Friends</h5>

                </div>

                <div class="friend_list">

                    <?php 
                        $user = new User($conn, $userLoggedIn);
                        $user->getFriendList($username);            
                    ?>

                </div>

            </div>

        </div>

    </div>

</body>

</html>