<?php
        
            include "header.php";

            include "php/plugins.php";

            include "php/classes/Users.php";

            include "php/classes/Post.php";

            include "php/classes/Message.php";

            include "php/classes/Notification.php";

            $message_obj = new Message($conn, $userLoggedIn);

            if(isset($_GET['u'])){
                $user_to = $_GET['u'];
            }else{
                $user_to = $message_obj->getMostRecentUser();
                if($user_to == false){
                    $user_to = "new";
                }
            }

            if($user_to != "new"){
                $user_to_obj = new User($conn, $user_to);
            }

            if(isset($_POST['post_message'])){
                if(isset($_POST['message_body'])){
                    $body = mysqli_real_escape_string($conn, $_POST['message_body']);
                    $date = date("Y-m-d H:i:s");
                    $message_obj->sendMessage($user_to, $body, $date);
                }
            }

            if(isset($_POST['delete_msg'])){

               $msg_id = $_POST['msg_id'];
               $query = mysqli_query($conn, "UPDATE messages SET deleted = 'yes' WHERE id = '$msg_id'");

            }

?>

</head>

<body>

        <?php include "navbar.php"; ?>

        <div class="container">

            <div class="main_msg_column">

                <div class="msg_body row">
                    <div class="col-md-6">
                    <div class="msg_header">

                        <h4><?php 
                        
                            if($user_to != "new"){
                                echo "You and <a href='$user_to'>" . $user_to_obj->getFirstAndLastName()."</a>";
                        
                        ?></h4>

                        <button type='button' class='message_del' id='message_del'><i class='material-icons'>delete</i></button>

                    </div>
                    
                    <div class="msg_load" id="msg_load_id">

                        <?php 
                                
                            }else{
                                echo "New Message";
                            }

                        ?>

                    </div>

                    <div class="msg_post">

                        <form action="" method="POST">

                            <?php
                                if($user_to == "new"){
                                    echo "<p class='search_title'>Select the friend you would like to message.</p>";
                                    ?><input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Enter your friends name here to start searching!' autocomplete='off' id='search_text_input'>
                                    <?php echo "<div class='results'></div>";
                                }else{
                                    echo "<div id='msg_sender'>'<textarea name='message_body' id='message_textarea' placeholder='Write you message...'></textarea>";
                                    echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'></div>";
                                }
                            ?>

                        </form>

                    </div>
                    </div>
                    <div class="col-md-6">
                    <div class="msg_convos">

                        <div class="convos_header">
                            <h5>Conversations</h5>
                        </div>

                        <div class="loaded_conversations">
                            <?php echo $message_obj->getConvos();?>
                        </div>
                        <div class="convos_child">
                            <a href="messages.php?u=new"><i class="material-icons">add</i></a>
                        </div>
                    </div>
                    </div>

                </div>

            </div>

        </div>

        <script>

            var user = "<?php echo $user_to; ?>";
            const chatBox = document.querySelector(".msg_load");
            

            chatBox.onmouseenter = ()=>{
	
                chatBox.classList.add("active");

            }

            function getMessages(){

                $.ajax({
                    type: "GET",
                    url: "php/ajax_get_messages.php",
                    data: "u=" + user,
                    success: function(response){
                        $(".msg_load").html(response);
                        if(!chatBox.classList.contains("active")){
					
                            chatBox.scrollTop = chatBox.scrollHeight;
                            
                        }
                    }
                })

            }

            setInterval(getMessages, 500);


        </script>
    
</body>

</html>