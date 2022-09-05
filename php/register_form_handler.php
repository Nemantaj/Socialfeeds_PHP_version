<?php 

    //Variables
    
    $fname = "";
    $lname = "";
    $em = "";
    $em2 = "";
    $password = "";
    $password2 = "";
    $date = "";
    $error_array = array();

    if(isset($_POST['register_button'])){

        $fname = strip_tags($_POST['reg_fname']);
        $fname = str_replace(' ', '', $fname);
        $fname = ucfirst(strtolower($fname));
        $_SESSION['reg_fname'] = $fname;

        $lname = strip_tags($_POST['reg_lname']);
        $lname = str_replace(' ', '', $lname);
        $lname = ucfirst(strtolower($lname));
        $_SESSION['reg_lname'] = $lname;

        $em = strip_tags($_POST['reg_email']);
        $em = str_replace(' ', '', $em);
        $em = ucfirst(strtolower($em));
        $_SESSION['reg_email'] = $em;

        $em2 = strip_tags($_POST['reg_email2']);
        $em2 = str_replace(' ', '', $em2);
        $em2 = ucfirst(strtolower($em2));
        $_SESSION['reg_email2'] = $em2;

        $password = strip_tags($_POST['reg_password']);
        $password2 = strip_tags($_POST['reg_password2']);

        $date = date("Y-m-d");

        if($em == $em2){

            if(filter_var($em, FILTER_VALIDATE_EMAIL)){

                $em = filter_var($em, FILTER_VALIDATE_EMAIL);

                $e_check = mysqli_query($conn, "SELECT email FROM users WHERE email = '$em'");

                $num_rows = mysqli_num_rows($e_check);

                if($num_rows > 0){

                    array_push($error_array, "Email is already in use.<br>");

                }

            }else{

                array_push($error_array, "Invalid email format.<br>");

            }

        }else{

            array_push($error_array, "Emails don't match.<br>");

        }

        if(strlen($fname) > 25 || strlen($fname) < 2){

            array_push($error_array, "Your first name must be between 2 and 25 characters.<br>");

        }

        if(strlen($lname) > 25 || strlen($lname) < 2){

            array_push($error_array, "Your last name must be between 2 and 25 characters.<br>");

        }

        if($password != $password2){

            array_push($error_array, "Your passwords do not match.<br>");

        }else{

            if(preg_match('/[^A-Za-z0-9]/', $password)){

                array_push($error_array, "Your password can only contain English characters or numbers.<br>");

            }

        }

        if(strlen($password) > 30 || strlen($password) < 5){

            array_push($error_array, "Your password must be between 5 and 30 characters.<br>");

        }

        if(empty($error_array)){

            $password = md5($password);

            $username = strtolower($fname . "_" . $lname);
            $check_username_query = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");

            $i = 0;
            while(mysqli_num_rows($check_username_query) != 0){

                $i++;
                $username = $username . "_" . $i;
                $check_username_query = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");

            }

            $rand = rand(1, 16);
            $profile_pic = "assets/images/profile_pics/defaults/".$rand.".png";

            $query = mysqli_query($conn, "INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `pwd`, `signup_date`, `profile_pic`, `num_posts`, `num_likes`, `user_closed`, `friend_array`, `bio`)
             VALUES (NULL, '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',', '')");

            array_push($error_array, "<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>");
            
            $_SESSION['reg_fname'] = "";
            $_SESSION['reg_lname'] = "";
            $_SESSION['reg_email'] = "";
            $_SESSION['reg_email2'] = "";
            
        }

    }

?>