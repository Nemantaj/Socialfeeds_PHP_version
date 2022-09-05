<?php 

    include "config.php";
    include "classes/Users.php";

    $query = $_POST['query'];
    $userLoggedIn = $_POST['userLoggedIn'];

    $names = explode(" ", $query);

    if(strpos($query, "_") !== false){
        $usersReturned = mysqli_query($conn, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed = 'no' LIMIT 9");
    }else if(count($names) == 2){
        $usersReturned = mysqli_query($conn, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') AND user_closed = 'no' LIMIT 9");
    }else{
        $usersReturned = mysqli_query($conn, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND user_closed = 'no' LIMIT 9");
    }

    if($query != ""){
        while($row = mysqli_fetch_array($usersReturned)){
            $user = new User($conn, $userLoggedIn);

            if($row['username'] != $userLoggedIn){
                $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common.";
            }else{
                $mutual_friends = "";
            }

            if($user->isFriend($row['username'])){
                echo "<div class='resultDisplay'>
                        <div class='search_img'>
                            <img src='".$row['profile_pic']."'>
                        </div>
                        <div class='search_info'>
                            <a href='messages.php?u=".$row['username']."'>
                                <span id='search_name'>".$row['first_name']. " " . $row['last_name']."</span>
                                <span id='mutual_friends'>".$mutual_friends."</span>
                            </a>
                        </div>
                        
                        </div>
                ";
            }
        }
    }
?>