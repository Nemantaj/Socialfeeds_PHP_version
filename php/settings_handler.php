<?php  
if(isset($_POST['update_details'])) {

	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$email = $_POST['email'];

	$email_check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
	$row = mysqli_fetch_array($email_check);
	$matched_user = $row['username'];

	if($matched_user == "" || $matched_user == $userLoggedIn) {
		$message = "Details updated!<br>";

		$query = mysqli_query($conn, "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE username='$userLoggedIn'");
	}
	else 
		$message = "<center>That email is already in use!</center>";
}
else 
	$message = "";


//**************************************************

if(isset($_POST['update_password'])) {

	$old_password = strip_tags($_POST['old_password']);
	$new_password_1 = strip_tags($_POST['new_password_1']);
	$new_password_2 = strip_tags($_POST['new_password_2']);

	$password_query = mysqli_query($conn, "SELECT pwd FROM users WHERE username='$userLoggedIn'");
	$row = mysqli_fetch_array($password_query);
	$db_password = $row['pwd'];

	if(md5($old_password) == $db_password) {

		if($new_password_1 == $new_password_2) {


			if(strlen($new_password_1) <= 4) {
				$password_message = "<center>Sorry, your password must be greater than 4 characters</center>";
			}	
			else {
				$new_password_md5 = md5($new_password_1);
				$password_query = mysqli_query($conn, "UPDATE users SET pwd='$new_password_md5' WHERE username='$userLoggedIn'");
				$password_message = "<center>Password has been changed!</center>";
			}


		}
		else {
			$password_message = "<center>Your two new passwords need to match!</center>";
		}

	}
	else {
			$password_message = "<center>The old password is incorrect! </center>";
	}

}
else {
	$password_message = "";
}


if(isset($_POST['close_account'])) {
	header("Location: close_account.php");
}

if(isset($_POST['bio_submit'])){

    $length = mb_strlen($_POST['bio_text_body']);
    if($length > 100){
        $bio_submit_result = "<center>Length should be under 100 characters.</center>";
    }else{
        $bio_text_body = $_POST['bio_text_body'];
        $query = mysqli_query($conn, "UPDATE users SET bio = '$bio_text_body' WHERE username = '$userLoggedIn'");
    }

}


?>