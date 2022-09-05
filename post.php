<?php
        
            include "header.php";

            include "php/plugins.php";

            include "php/classes/Users.php";

            include "php/classes/Post.php";

            include "php/classes/Message.php";

            include "php/classes/Notification.php";

            if(isset($_GET['id'])){
                $id = $_GET['id'];
            }else{
                $id = 0;
            }
?>

    </head>

    <body>

            <?php include "navbar.php"; ?>

            <div class="container">

                <div class="main_column1 column">

                    <div class="posts_area">

                        <?php 
                            $post = new Post($conn, $userLoggedIn);
                            $post->getSinglePost($id);
                        ?>

                    </div>

                </div>

            </div> 
            
    </body>

</html>
