<?php 

    require "php/config.php";

    require "php/register_form_handler.php";

    require "php/login_form_handler.php";

?>

<html>

    <head>

        <?php include "php/plugins.php"; ?>

        <title>Social -  Accounts Page</title>

        <link rel="stylesheet" href="register-page-style.css">

    </head>

    <body>

        <?php 
        
            if(isset($_POST['register_button'])){

                echo '
                
                    <script>
                    
                        $(document).ready(function() {
                         
                            $(".first").hide();
                            $(".second").show();
                            
                        });
                    
                    </script>
                
                ';

            }
        
        ?>

        <div class="container">

            <div class="row">

                <div class="col-md-3"></div>

                <div class="col-md-6" id="wrapper-accounts-page">

                    <div class="header">

                        <h1>Social Feeds!</h1>

                    </div>

                        <div class="first">

                        <div class="titlebar-acc">

                            <h5>Log in to your account</h5>

                        </div>

                        <form action="registration.php" method="POST">

                            <input type="email" name="log_email" placeholder="Email address" value="<?php 
                            
                            if(isset($_SESSION['log_email'])){

                                echo $_SESSION['log_email'];

                            }
                        
                            ?>" required><br>


                            <input type="password" name="log_password" placeholder="Enter your password" required><br>

                            <?php
                            
                            if(in_array("Email or password is incorrect.<br>", $error_array)){

                                echo '<div class="error-info" id="error">

                                <p>Email or password is incorrect.<br></p>

                            </div>';

                            }
                        
                            ?>


                            <input type="submit" name="login_button" value="Log In"><br>


                            <span id="switch"><a href="#" id="signup">Create new account</a></span>

                        </form>


                    </div>    

                    <div class="second">

                        <div class="titlebar-acc">

                            <h5>Create a new Social Feeds! account</h5>

                        </div>
                            
                        <form action="registration.php" method="POST">

                            <input type="text" name="reg_fname" placeholder="First Name" value="<?php 
                            
                                if(isset($_SESSION['reg_fname'])){

                                    echo $_SESSION['reg_fname'];

                                }
                            
                            ?>" required>

                            <br>

                            <?php 
                            
                                if(in_array("Your first name must be between 2 and 25 characters.<br>", $error_array)){

                                    echo '<div class="error-info" id="error">

                                    <p>Your first name must be between 2 and 25 characters.<br></p>
    
                                </div>';

                                }    

                            ?>

                            <input type="text" name="reg_lname" placeholder="Last Name" value="<?php
                            
                                if(isset($_SESSION['reg_lname'])){

                                    echo $_SESSION['reg_lname'];

                                }
                            
                            ?>" required>

                            <br>

                            <?php 
                            
                                if(in_array("Your last name must be between 2 and 25 characters.<br>", $error_array)){

                                    echo '<div class="error-info" id="error">

                                    <p>Your last name must be between 2 and 25 characters.<br></p>
    
                                </div>';

                                }    

                            ?>

                            <input type="email" name="reg_email" placeholder="Email"  value="<?php
                            
                                if(isset($_SESSION['reg_email'])){

                                    echo $_SESSION['reg_email'];

                                }
                        
                            ?>" required>

                            <br>

                            <input type="email" name="reg_email2" placeholder="Confirm Email"  value="<?php
                            
                                if(isset($_SESSION['reg_email2'])){

                                    echo $_SESSION['reg_email2'];

                                }
                        
                            ?>" required>

                            <br>

                            <?php 
                            
                                if(in_array("Email is already in use.<br>", $error_array)){

                                    echo '<div class="error-info" id="error">

                                    <p>Your last name must be between 2 and 25 characters.<br></p>
    
                                </div>';

                                }elseif(in_array("Invalid email format.<br>", $error_array)){

                                    echo '<div class="error-info" id="error">

                                    <p>Your last name must be between 2 and 25 characters.<br></p>
    
                                </div>';

                                }elseif(in_array("Emails don't match.<br>", $error_array)){

                                    echo '<div class="error-info" id="error">

                                    <p>Emails do not match.<br></p>
    
                                </div>';

                                }

                            ?>

                            <input type="password" name="reg_password" placeholder="Password" required>

                            <br>

                            <input type="password" name="reg_password2" placeholder="Confirm Password" required>

                            <br>

                            <?php 
                            
                                if(in_array("Your passwords do not match.<br>", $error_array)){

                                    echo '<div class="error-info" id="error">

                                    <p>Your passwords do not match.<br></p>
    
                                </div>';

                                }elseif(in_array("Your password can only contain English characters or numbers.<br>", $error_array)){

                                    echo '<div class="error-info" id="error">

                                    <p>Your password can only contain English characters or numbers.<br></p>
    
                                </div>';

                                }elseif(in_array("Your password must be between 5 and 30 characters.<br>", $error_array)){

                                    echo '<div class="error-info" id="error">

                                    <p>Your password can only contain English characters or numbers.<br></p>
    
                                </div>';

                                }    

                            ?>

                            <input type="submit" id="register_button" value="Register" name="register_button">

                            <br>

                            <?php 
                            
                                if(in_array("<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>", $error_array)){

                                    echo "<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>";

                                }
                                
                            ?>
                            
                            <span id="switch"><a href="#" id="login">Log In</a></span>
    
                        </form>

                    </div>          
                        
                </div> 

                <div class="col-md-3"></div>
                
            </div>        

    </div>
    
    <script src="js/registration.js"></script>


    </body>

</html>