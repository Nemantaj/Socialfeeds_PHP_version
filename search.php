<?php
        
            include "header.php";

            include "php/plugins.php";

            include "php/classes/Users.php";

            include "php/classes/Post.php";

            include "php/classes/Message.php";

            include "php/classes/Notification.php";

            if(isset($_GET['q'])){
                $query = $_GET['q'];
            }else{
                $query = "";
            }

            if(isset($_GET['type'])){
                $type = $_GET['type'];
            }else{
                $type = "name";
            }

?>
            <title>Search</title>

</head>

<body>

    <?php include "navbar.php"; ?>

    <div class="container">
    <div class="main_column">

        <div class="card">
                    <div class="card-header">
                        Deep Search
                    </div>
                    <div class="card-body">
                        <div class="search" id="search">

                            <form action="search.php" id="search_form" method="GET" name="search_form">
                                <input type="text" class="form-control" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">
                                <button class="button_holder"><i class="material-icons">search</i></button>
                            </form>

                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        
                    <div class="search_all_results">

                <?php 
                    if($query == ""){
                        echo "You must Enter something in the search box.";
                    }else{
                        if($type = "username"){
                            $usersReturnedQuery = mysqli_query($conn, "SELECT * FROM users WHERE username LIKE '%$query%' AND user_closed = 'no' LIMIT 6");
                        }else{

                        $names = explode(" ", $query);

                        if(count($names) == 3){
                            $usersReturnedQuery = mysqli_query($conn, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed = 'no'");
                        }else if(count($names) == 2){
                            $usersReturnedQuery = mysqli_query($conn, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed = 'no'");
                        }else{
                            $usersReturnedQuery = mysqli_query($conn, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed = 'no'");
                        }

                    }

                    if(mysqli_num_rows($usersReturnedQuery) == "0"){
                        echo "We can't find anyone with a ".$type." like: ".$query;
                    }else{
                        echo mysqli_num_rows($usersReturnedQuery). " results found.";
                    }

                    while($row = mysqli_fetch_array($usersReturnedQuery)){
                        $user_obj = new User($conn, $user['username']);
                        $mutual_friends = "";

                        if($user['username'] != $row['username']){
                            $mutual_friends = $user_obj->getMutualFriends($row['username']). " mutual friends";
                        }

                        echo "<div class='resultDisplay'>
                    <div class='search_img'>
                        <img src='".$row['profile_pic']."'>
                    </div>
                    <div class='search_info'>
                        <a href='".$row['username']."'>
                            <span id='search_name'>".$row['first_name']. " " . $row['last_name']."</span>
                            <span id='mutual_friends'>".$mutual_friends."</span>
                        </a>
                    </div>
                    
                    </div>
                    ";

                            }
                        }
                    ?>

                    </div>

                    </div>
        </div>

        <!-- <div class="search_body">

            
            <div class="search" id="search">

                <form action="search.php" id="search_form" method="GET" name="search_form">
                    <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">
                    <button class="button_holder"><i class="material-icons">search</i></button>
                </form>

            </div>

            
            
        </div> -->



    </div>
    </div>

</body>

</html>