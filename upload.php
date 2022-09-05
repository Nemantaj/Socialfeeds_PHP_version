<?php
        
            include "header.php";

            include "php/plugins.php";

            include "php/classes/Users.php";

            include "php/classes/Post.php";

            include "php/classes/Message.php";

            include "php/classes/Notification.php";

            include "php/compress.php";

            $result = "";
            
            if(isset($_POST['submit'])){
            
                $filename = $_FILES['image']['name'];
                $valid_ext = array('png', 'jpeg', 'jpg');
                $photoExt1 = @end(explode(".", $filename));
                $photoTest1 = strtolower($photoExt1);
                $new_profile_pic = time() . '.' . $photoTest1;
                $location = "assets/images/profile_pics/" . $new_profile_pic;
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);

                if(in_array($file_extension, $valid_ext)){
                    compressedImage($_FILES['image']['tmp_name'], $location, 60);

                    $query = mysqli_query($conn, "UPDATE users SET profile_pic = '$location' WHERE username = '$userLoggedIn'");
                    header("Location: settings.php");

                }else{
                    $result = "File format is not correct.";
                }

            }
            
?>

    </head>

    <body>

            <?php include "navbar.php"; ?>

            <div class="main_column1 column">

            <div id="formExample">

                <div class="upload1">

                    <h5>Update profile picture</h5>

                </div>
                
                <div class="upload2">
                    <form action="upload.php" method="post" enctype="multipart/form-data">
                        
                        <p>Upload something</p>
                        <input type="file" id="image" name="image" /><br /><br />
                        <input type="submit" value="Submit" name="submit" style="width:85px; height:25px;"/>
                        <p><br><?php echo $result; ?></p>
                    </form><br /><br />
                </div>

            </div> 
            
    </body>

</html>
