<nav class="fixed-top">

        <div class="row">

            <div class="col-md-6 d-none d-md-block">

                <div class="navbar-section-1">

                    <a href="#" class="navbar-logo"><h3>Social Feed!</h3></a>

                </div>

            </div>

            <div class="col-md-6">

                <div class="navbar-section-2">

                <?php 
                    $messages = new Message($conn, $userLoggedIn);
                    $num_messages = $messages->getUnreadNumber();

                    $notifications = new Notification($conn, $userLoggedIn);
                    $num_notifications = $notifications->getUnreadNumber();

                    $user_obj = new User($conn, $userLoggedIn);
                    $num_requests = $user_obj->getNumberOfFriendRequests();
                ?>

                <a href="<?php echo $userLoggedIn; ?>">
				<img id="nav_img" src="<?php echo $user['profile_pic']; ?>">
			    </a>
                <a href="search_corner.php" id="searchBtn">
                <i class="material-icons">search</i>
                </a>
                <a href="index.php">
                    <i class="material-icons">home</i>
                </a>
                <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
                    <i class="material-icons">chat</i><?php if($num_messages > 0){
                        echo "<span>".$num_messages."</span>";
                    }else{
                        echo "";
                    }
                    ?>
                </a>
                <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
                    <i class="material-icons">event_note</i><?php if($num_notifications > 0){
                        echo "<span>".$num_notifications."</span>";
                    }else{
                        echo "";
                    }
                     ?>
                </a>
                <a href="requests.php">
                    <i class="material-icons">people</i><?php if($num_requests > 0){
                        echo "<span>".$num_requests."</span>";
                    }else{
                        echo "";
                    }
                    ?>
                </a>
                <a href="settings.php">
                    <i class="material-icons">settings</i>
                </a>
                <a href="php/logout.php">
                    <i class="material-icons">logout</i>
                </a>

                </div>

                <div class="container overflows"><div class="dropdown_data_window"></div></div>
                <input type="hidden" id="dropdown_data_type" value="">

            </div>

        </div>

    </nav>

    <script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';

	$(document).ready(function() {

		$('.dropdown_data_window').scroll(function() {
			var inner_height = $('.dropdown_data_window').innerHeight(); //Div containing data
			var scroll_top = $('.dropdown_data_window').scrollTop();
			var page = $('.dropdown_data_window').find('.nextPageDropdownData').val();
			var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

			if ((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false') {

				var pageName; //Holds name of page to send ajax request to
				var type = $('#dropdown_data_type').val();


				if(type == 'notification')
					pageName = "ajax_load_notifications.php";
				else if(type = 'message')
					pageName = "ajax_load_messages.php"


				var ajaxReq = $.ajax({
					url: "php/" + pageName,
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
					cache:false,

					success: function(response) {
						$('.dropdown_data_window').find('.nextPageDropdownData').remove(); //Removes current .nextpage 
						$('.dropdown_data_window').find('.noMoreDropdownData').remove(); //Removes current .nextpage 


						$('.dropdown_data_window').append(response);
					}
				});

			} //End if 

			return false;

		}); //End (window).scroll(function())


	});

	</script>

    