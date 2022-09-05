<?php

    class Post{

        private $user_obj;
        private $conn;

        public function __construct($conn, $user){

            $this->conn = $conn;
            $this->user_obj = new User($conn, $user);

        }

        public function submitPost($body, $user_to, $imagePath){

            $body = strip_tags($body);
            $body = mysqli_real_escape_string($this->conn, $body);
            $check_empty = preg_replace('/\s+/', '', $body);

            if($check_empty != ""){

                $body_array = preg_split("/\s+/", $body);

                foreach($body_array as $key => $value) {

                    if(strpos($value, "www.youtube.com/watch?v=") !== false) {

                        $link = preg_split("!&!", $value);
                        $value = preg_replace("!watch\?v=!", "embed/", $link[0]);
                        $value = "<br><iframe width=\'420\' height=\'315\' src=\'" . $value ."\'></iframe><br>";
                        $body_array[$key] = $value;

                    }

                }
                
                $body = implode(" ", $body_array);

                $date_added = date("Y-m-d H:i:s");
                $added_by = $this->user_obj->getUsername();

                if($user_to == $added_by){

                    $user_to = "none";

                }

                $query = mysqli_query($this->conn, "INSERT INTO posts VALUES(NULL, '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0', '$imagePath')");

                $returned_id = mysqli_insert_id($this->conn);

                if($user_to != "none"){
                    $notification = new Notification($this->conn, $added_by);
                    $notification->insertNotification($returned_id, $user_to, "profile_post");
                }

                $num_posts = $this->user_obj->getNumPosts();
                $num_posts++;
                $update_query = mysqli_query($this->conn, "UPDATE users SET num_posts = '$num_posts' WHERE username = '$added_by'");

                $stopWords = "a about above across after again against all almost alone along already
                also although always among am an and another any anybody anyone anything anywhere are 
                area areas around as ask asked asking asks at away b back backed backing backs be became
                because become becomes been before began behind being beings best better between big 
                both but by c came can cannot case cases certain certainly clear clearly come could
                d did differ different differently do does done down down downed downing downs during
                e each early either end ended ending ends enough even evenly ever every everybody
                everyone everything everywhere f face faces fact facts far felt few find finds first
                for four from full fully further furthered furthering furthers g gave general generally
                get gets give given gives go going good goods got great greater greatest group grouped
                grouping groups h had has have having he her here herself high high high higher
                highest him himself his how however i im if important in interest interested interesting
                interests into is it its itself j just k keep keeps kind knew know known knows
                large largely last later latest least less let lets like likely long longer
                longest m made make making man many may me member members men might more most
                mostly mr mrs much must my myself n necessary need needed needing needs never
                new new newer newest next no nobody non noone not nothing now nowhere number
                numbers o of off often old older oldest on once one only open opened opening
                opens or order ordered ordering orders other others our out over p part parted
                parting parts per perhaps place places point pointed pointing points possible
                present presented presenting presents problem problems put puts q quite r
                rather really right right room rooms s said same saw say says second seconds
                see seem seemed seeming seems sees several shall she should show showed
                showing shows side sides since small smaller smallest so some somebody
                someone something somewhere state states still still such sure t take
                taken than that the their them then there therefore these they thing
                things think thinks this those though thought thoughts three through
                thus to today together too took toward turn turned turning turns two
                u under until up upon us use used uses v very w want wanted wanting
                wants was way ways we well wells went were what when where whether
                which while who whole whose why will with within without work
                worked working works would x y year years yet you young younger
                youngest your yours z lol haha omg hey ill iframe wonder else like 
                hate sleepy reason for some little yes bye choose";

                //Convert stop words into array - split at white space
                $stopWords = preg_split("/[\s,]+/", $stopWords);

                //Remove all punctionation
                $no_punctuation = preg_replace("/[^a-zA-Z 0-9]+/", "", $body);

                //Predict whether user is posting a url. If so, do not check for trending words
                if(strpos($no_punctuation, "height") === false && strpos($no_punctuation, "width") === false
                    && strpos($no_punctuation, "http") === false && strpos($no_punctuation, "youtube") === false){
                    //Convert users post (with punctuation removed) into array - split at white space
                    $keywords = preg_split("/[\s,]+/", $no_punctuation);

                    foreach($stopWords as $value) {
                        foreach($keywords as $key => $value2){
                            if(strtolower($value) == strtolower($value2))
                                $keywords[$key] = "";
                        }
                    }

                    foreach ($keywords as $value) {
                        $this->calculateTrend(ucfirst($value));
                    }

                }

            }

        }

        public function calculateTrend($term){

            if($term != ''){
                $query = mysqli_query($this->conn, "SELECT * FROM trends WHERE title = '$term'");

                if(mysqli_num_rows($query) == 0){
                    $insert_query = mysqli_query($this->conn, "INSERT INTO trends (title, hits) VALUES ('$term', '1')");
                }else{
                    $insert_query = mysqli_query($this->conn, "UPDATE trends SET hits = hits+1 WHERE title = '$term'");
                }
            }

        }

        public function loadPostFriends($data, $limit){

            $page = $data['page'];
            $userLoggedIn = $this->user_obj->getUsername();

            if($page == 1){
                $start = 0;
            }else{
                $start = ($page - 1) * $limit;
            }


            $str = "";
            $data_query = mysqli_query($this->conn, "SELECT * FROM posts WHERE deleted = 'no' ORDER BY id DESC");

            if(mysqli_num_rows($data_query) > 0){

                $num_iterations = 0;
                $count = 1;    

                while($row = mysqli_fetch_array($data_query)){

                    $id = $row['id'];
                    $body = $row['body'];
                    $image = $row['image'];
                    $added_by = $row['added_by'];
                    $date_time = $row['date_added'];

                    if($row['user_to'] == "none"){

                        $user_to = "";

                    }else{

                        $user_to_obj = new User($this->conn, $row['user_to']);
                        $user_to_name = $user_to_obj->getFirstAndLastName();
                        $user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";

                    }

                    $added_by_obj = new User($this->conn, $added_by);
                    if($added_by_obj->isClosed()){

                        continue;

                    }

                    $user_logged_obj = new User($this->conn, $userLoggedIn);
                    if($user_logged_obj->isFriend($added_by)){

                        if($num_iterations++ < $start)
                            continue; 


                        //Once 10 posts have been loaded, break
                        if($count > $limit) {
                            break;
                        }
                        else {
                            $count++;
                        }

                        if($userLoggedIn == $added_by){
                            $delete_button = "<button class='delete_button' id='post$id'><i class='material-icons'>close</i></button>";
                        }else{
                            $delete_button = "";
                        }

                        $user_details_query = mysqli_query($this->conn, "SELECT first_name, last_name, profile_pic FROM users WHERE username = '$added_by'");
                        $user_row = mysqli_fetch_array($user_details_query);
                        $first_name = $user_row['first_name'];
                        $last_name = $user_row['last_name'];
                        $profile_pic = $user_row['profile_pic'];

                        ?>

                        <script>

                            function toggle<?php echo $id; ?>(){

                                var element = document.getElementById("toggleComment<?php echo $id; ?>");

                                if(element.style.display == "block"){
                                    element.style.display = "none";
                                }else{
                                    element.style.display = "block";
                                }

                            }

                        </script>

                        <?php

                        $comments_check = mysqli_query($this->conn, "SELECT * FROM comments WHERE post_id = '$id'");
                        $comments_check_num = mysqli_num_rows($comments_check);

                        $date_time_now = date("Y-m-d H:i:s");
                        $start_date = new DateTime($date_time);
                        $end_date = new DateTime($date_time_now);
                        $interval = $start_date->diff($end_date);

                        if($interval->y >= 1){

                            if($interval == 1){
                                $time_message = $interval->y . " year ago";
                            }else{
                                $time_message = $interval->y . " years ago";
                            }

                        }else if($interval->m >= 1){

                            if($interval->d == 0){
                                $days = " ago";
                            }else if($interval->d == 1){
                                $days = $interval->d . " day ago";
                            }else{
                                $days = $interval->d . " days ago";
                            }

                            if($interval->m ==1){
                                $time_message = $interval->m . " month". $days;
                            }else{
                                $time_message = $interval->m . " months". $days;
                            }

                        }else if($interval->d >= 1){

                            if($interval->d == 1){
                                $time_message = "Yesterday";
                            }else{
                                $time_message = $interval->d . " days ago";
                            }

                        }else if($interval->h >= 1){

                            if($interval->h == 1){
                                $time_message = $interval->h . " hour ago";
                            }else{
                                $time_message = $interval->h . " hours ago";
                            }

                        }else if($interval->i >= 1){

                            if($interval->i == 1){
                                $time_message = $interval->i . " minute ago";
                            }else{
                                $time_message = $interval->i . " minutes ago";
                            }

                        }else{

                            if($interval->s < 30){
                                $time_message = "Just now";
                            }else{
                                $time_message = $interval->s . " seconds ago";
                            }

                        }

                        if($image != ""){
                            $imageDivisonDirectory = "<img src='". $image ."'>";
                        }else{
                            $imageDivisonDirectory = "";
                        }

                        // $str .= '<div class="post-body">

                        //             <div class="post-info">

                        //                 <img id="img_div" src="'.$profile_pic.'" alt="">
                        //                 <p><span id="span1"><a href="'.$added_by.'">'.$first_name.' '.$last_name.'</a> '.$user_to.'</span>&nbsp &nbsp &nbsp<span id="span2">'.$time_message.'</span></p>
                        //                 <a href="post.php?id='.$id.'" class="right_arrow"><i class="material-icons">arrow_forward</i></a>

                        //             </div>

                        //             <div class="post-content">

                        //                 '.$body.'
                        //                 <br>
                        //                 '.$imageDivisonDirectory.'

                        //             </div>

                        //             <div class="newsfeedpostoptions">
                                    
                        //                 <button id="comment-button" onclick="javascript:toggle'.$id.'()">Comments ('.$comments_check_num.')</button>
                        //                 <iframe src="like.php?post_id='.$id.'" scrolling="no"></iframe>
                        //                 '.$delete_button.'

                        //             </div>

                        //             <div class="post_comment" id="toggleComment'.$id.'" style="display:none;">
                        //                 <iframe src="comment_frame.php?post_id='.$id.'" id="comment_iframe"></iframe>
                        //             </div>

                        //         </div>';

                        $str .= '<div class="card post_card">
                                <div class="card-header post_card_info">
                                        <img id="img_div" src="'.$profile_pic.'" alt="">
                                        <span id="span1"><a href="'.$added_by.'">'.$first_name.' '.$last_name.'</a> '.$user_to.'</span>&nbsp &nbsp<span id="span2">'.$time_message.'</span>
                                        <a href="post.php?id='.$id.'" class="right_arrow"><i class="material-icons">arrow_forward</i></a>
                                </div>
                                <div class="card-body">
                                    <div class="post-content">

                                        '.$body.'
                                        <br>
                                        '.$imageDivisonDirectory.'
        
                                    </div>
                                </div>
                                <div class="card-footer text-muted">
                                    <div class="newsfeedpostoptions">
                                    
                                        <button id="comment-button" onclick="javascript:toggle'.$id.'()">Comments ('.$comments_check_num.')</button>
                                        <iframe src="like.php?post_id='.$id.'" scrolling="no"></iframe>
                                        '.$delete_button.'
        
                                     </div>

                                    <div class="post_comment" id="toggleComment'.$id.'" style="display:none;">
                                         <iframe src="comment_frame.php?post_id='.$id.'" id="comment_iframe"></iframe>
                                    </div>
                                </div>
                            </div>';

                    }
                    ?>
                    
                    <script>

                        $(document).ready(function(){

                            $('#post<?php echo $id; ?>').on("click", function(){
                                bootbox.confirm("Are you sure you want to delete this post?", function(result){

                                    $.post("php/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

                                    if(result){
                                        location.reload();
                                    }

                                });
                            })

                        })

                    </script>

                    <?php

                }

                if($count > $limit){ 
				    $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			    }else{ 
				    $str .= "<input type='hidden' class='noMorePosts' value='true'><center> No more posts to show! </center>";
                }

            }    

            echo $str;

        }

        
        public function loadProfilePost($data, $limit){

            $page = $data['page'];
            $profileUser = $data['profileUsername'];
            $userLoggedIn = $this->user_obj->getUsername();

            if($page == 1){
                $start = 0;
            }else{
                $start = ($page - 1) * $limit;
            }


            $str = "";
            $data_query = mysqli_query($this->conn, "SELECT * FROM posts WHERE deleted = 'no' AND ((added_by = '$profileUser' AND user_to = 'none') OR user_to = '$profileUser') ORDER BY id DESC");

            if(mysqli_num_rows($data_query) > 0){

                $num_iterations = 0;
                $count = 1;    

                while($row = mysqli_fetch_array($data_query)){

                    $id = $row['id'];
                    $body = $row['body'];
                    $image = $row['image'];
                    $added_by = $row['added_by'];
                    $date_time = $row['date_added'];

                        if($num_iterations++ < $start)
                            continue; 


                        //Once 10 posts have been loaded, break
                        if($count > $limit) {
                            break;
                        }
                        else {
                            $count++;
                        }

                        if($userLoggedIn == $added_by){
                            $delete_button = "<button class='delete_button' id='post$id'><i class='material-icons'>close</i></button>";
                        }else{
                            $delete_button = "";
                        }

                        $user_details_query = mysqli_query($this->conn, "SELECT first_name, last_name, profile_pic FROM users WHERE username = '$added_by'");
                        $user_row = mysqli_fetch_array($user_details_query);
                        $first_name = $user_row['first_name'];
                        $last_name = $user_row['last_name'];
                        $profile_pic = $user_row['profile_pic'];

                        ?>

                        <script>

                            function toggle<?php echo $id; ?>(){

                                var element = document.getElementById("toggleComment<?php echo $id; ?>");

                                if(element.style.display == "block"){
                                    element.style.display = "none";
                                }else{
                                    element.style.display = "block";
                                }

                            }

                        </script>

                        <?php

                        $comments_check = mysqli_query($this->conn, "SELECT * FROM comments WHERE post_id = '$id'");
                        $comments_check_num = mysqli_num_rows($comments_check);

                        $date_time_now = date("Y-m-d H:i:s");
                        $start_date = new DateTime($date_time);
                        $end_date = new DateTime($date_time_now);
                        $interval = $start_date->diff($end_date);

                        if($interval->y >= 1){

                            if($interval == 1){
                                $time_message = $interval->y . " year ago";
                            }else{
                                $time_message = $interval->y . " years ago";
                            }

                        }else if($interval->m >= 1){

                            if($interval->d == 0){
                                $days = " ago";
                            }else if($interval->d == 1){
                                $days = $interval->d . " day ago";
                            }else{
                                $days = $interval->d . " days ago";
                            }

                            if($interval->m ==1){
                                $time_message = $interval->m . " month". $days;
                            }else{
                                $time_message = $interval->m . " months". $days;
                            }

                        }else if($interval->d >= 1){

                            if($interval->d == 1){
                                $time_message = "Yesterday";
                            }else{
                                $time_message = $interval->d . " days ago";
                            }

                        }else if($interval->h >= 1){

                            if($interval->h == 1){
                                $time_message = $interval->h . " hour ago";
                            }else{
                                $time_message = $interval->h . " hours ago";
                            }

                        }else if($interval->i >= 1){

                            if($interval->i == 1){
                                $time_message = $interval->i . " minute ago";
                            }else{
                                $time_message = $interval->i . " minutes ago";
                            }

                        }else{

                            if($interval->s < 30){
                                $time_message = "Just now";
                            }else{
                                $time_message = $interval->s . " seconds ago";
                            }

                        }

                        if($image != ""){
                            $imageDivisonDirectory = "<img src='". $image ."'>";
                        }else{
                            $imageDivisonDirectory = "";
                        }

                        $str .= '<div class="card post_card">
                                <div class="card-header post_card_info">
                                        <img id="img_div" src="'.$profile_pic.'" alt="">
                                        <span id="span1"><a href="'.$added_by.'">'.$first_name.' '.$last_name.'</a></span>&nbsp &nbsp<span id="span2">'.$time_message.'</span>
                                        <a href="post.php?id='.$id.'" class="right_arrow"><i class="material-icons">arrow_forward</i></a>
                                </div>
                                <div class="card-body">
                                    <div class="post-content">

                                        '.$body.'
                                        <br>
                                        '.$imageDivisonDirectory.'
        
                                    </div>
                                </div>
                                <div class="card-footer text-muted">
                                    <div class="newsfeedpostoptions">
                                    
                                        <button id="comment-button" onclick="javascript:toggle'.$id.'()">Comments ('.$comments_check_num.')</button>
                                        <iframe src="like.php?post_id='.$id.'" scrolling="no"></iframe>
                                        '.$delete_button.'
        
                                     </div>

                                    <div class="post_comment" id="toggleComment'.$id.'" style="display:none;">
                                         <iframe src="comment_frame.php?post_id='.$id.'" id="comment_iframe"></iframe>
                                    </div>
                                </div>
                            </div>';

                    
                    ?>
                    
                    <script>

                        $(document).ready(function(){

                            $('#post<?php echo $id; ?>').on("click", function(){
                                bootbox.confirm("Are you sure you want to delete this post?", function(result){

                                    $.post("php/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

                                    if(result){
                                        location.reload();
                                    }

                                });
                            })

                        })

                    </script>

                    <?php

                }

                if($count > $limit){ 
				    $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			    }else{ 
				    $str .= "<input type='hidden' class='noMorePosts' value='true'><center> No more posts to show! </center>";
                }

            }    

            echo $str;

        }

        public function getSinglePost($post_id){
            $userLoggedIn = $this->user_obj->getUsername();

            $opened_query = mysqli_query($this->conn, "UPDATE notifications SET opened = 'yes' WHERE user_to = '$userLoggedIn' AND link LIKE '%=$post_id'");

            $str = "";
            $likes_str = "";
            $data_query = mysqli_query($this->conn, "SELECT * FROM posts WHERE deleted = 'no' AND id = '$post_id'");
            $value = mysqli_num_rows($data_query);

            if(mysqli_num_rows($data_query) > 0){ 

                    $row = mysqli_fetch_array($data_query);
                    $id = $row['id'];
                    $body = $row['body'];
                    $image = $row['image'];
                    $added_by = $row['added_by'];
                    $date_time = $row['date_added'];

                    if($row['user_to'] == "none"){

                        $user_to = "";

                    }else{

                        $user_to_obj = new User($this->conn, $row['user_to']);
                        $user_to_name = $user_to_obj->getFirstAndLastName();
                        $user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";

                    }

                    $added_by_obj = new User($this->conn, $added_by);
                    if($added_by_obj->isClosed()){

                        return;

                    }

                    $user_logged_obj = new User($this->conn, $userLoggedIn);
                    if($user_logged_obj->isFriend($added_by)){

                        if($userLoggedIn == $added_by){
                            $delete_button = "<button class='delete_button' id='post$id'><i class='material-icons'>close</i></button>";
                        }else{
                            $delete_button = "";
                        }

                        $user_details_query = mysqli_query($this->conn, "SELECT first_name, last_name, profile_pic FROM users WHERE username = '$added_by'");
                        $user_row = mysqli_fetch_array($user_details_query);
                        $first_name = $user_row['first_name'];
                        $last_name = $user_row['last_name'];
                        $profile_pic = $user_row['profile_pic'];

                        ?>

                        <script>

                            function toggle<?php echo $id; ?>(){

                                var element = document.getElementById("toggleComment<?php echo $id; ?>");

                                if(element.style.display == "block"){
                                    element.style.display = "none";
                                }else{
                                    element.style.display = "block";
                                }

                            }

                        </script>

                        <?php

                        $comments_check = mysqli_query($this->conn, "SELECT * FROM comments WHERE post_id = '$id'");
                        $comments_check_num = mysqli_num_rows($comments_check);

                        $likes_by = mysqli_query($this->conn, "SELECT username FROM likes WHERE post_id = '$post_id' ORDER BY id DESC");
            
                        if(mysqli_num_rows($likes_by) > 0){
                            while($row = mysqli_fetch_assoc($likes_by)){
                                $username = $row['username'];
                                $query = mysqli_query($this->conn, "SELECT first_name, last_name, profile_pic FROM users WHERE username = '$username'");
                                $user_details1 = mysqli_fetch_array($query);
                                $first_name_likes = $user_details1['first_name'];
                                $last_name_likes = $user_details1['last_name'];
                                $profile_pic_likes = $user_details1['profile_pic'];
    
                                $likes_str .= '<div class="likes_list">
                                                    <img src="'.$profile_pic_likes.'">
                                                    <a href="'.$username.'"><p>'.$first_name_likes.' '.$last_name_likes.'</p></a>
                                                </div>';
                            }
                        }else{
                            $likes_str .= "No likes for this post so far!";
                        }

                        $date_time_now = date("Y-m-d H:i:s");
                        $start_date = new DateTime($date_time);
                        $end_date = new DateTime($date_time_now);
                        $interval = $start_date->diff($end_date);

                        if($interval->y >= 1){

                            if($interval == 1){
                                $time_message = $interval->y . " year ago";
                            }else{
                                $time_message = $interval->y . " years ago";
                            }

                        }else if($interval->m >= 1){

                            if($interval->d == 0){
                                $days = " ago";
                            }else if($interval->d == 1){
                                $days = $interval->d . " day ago";
                            }else{
                                $days = $interval->d . " days ago";
                            }

                            if($interval->m ==1){
                                $time_message = $interval->m . " month". $days;
                            }else{
                                $time_message = $interval->m . " months". $days;
                            }

                        }else if($interval->d >= 1){

                            if($interval->d == 1){
                                $time_message = "Yesterday";
                            }else{
                                $time_message = $interval->d . " days ago";
                            }

                        }else if($interval->h >= 1){

                            if($interval->h == 1){
                                $time_message = $interval->h . " hour ago";
                            }else{
                                $time_message = $interval->h . " hours ago";
                            }

                        }else if($interval->i >= 1){

                            if($interval->i == 1){
                                $time_message = $interval->i . " minute ago";
                            }else{
                                $time_message = $interval->i . " minutes ago";
                            }

                        }else{

                            if($interval->s < 30){
                                $time_message = "Just now";
                            }else{
                                $time_message = $interval->s . " seconds ago";
                            }

                        }

                        if($image != ""){
                            $imageDivisonDirectory = "<img src='". $image ."'>";
                        }else{
                            $imageDivisonDirectory = "";
                        }

                        $str .= '<div class="post-body">

                                    <div class="post-info">

                                        <img src="'.$profile_pic.'" alt="">
                                        <p><span id="span1"><a href="'.$added_by.'">'.$first_name.' '.$last_name.'</a> '.$user_to.'</span>&nbsp &nbsp &nbsp<span id="span2">'.$time_message.'</span></p>

                                    </div>

                                    <div class="post-content">

                                        '.$body.'
                                        <br>
                                        '.$imageDivisonDirectory.'

                                    </div>

                                    <div class="newsfeedpostoptions">
                                    
                                        <button id="comment-button" onclick="javascript:toggle'.$id.'()">Comments ('.$comments_check_num.')</button>
                                        <iframe src="like.php?post_id='.$id.'" scrolling="no"></iframe>
                                        '.$delete_button.'

                                    </div>

                                    <div class="post_comment" id="toggleComment'.$id.'" style="display:none;">
                                        <iframe src="comment_frame.php?post_id='.$id.'" id="comment_iframe"></iframe>
                                    </div>

                                    <div class="post_liked_header">
                                        <h5>Liked by</h5>
                                    </div>

                                    <div class="post_liked_by">
                                    
                                        '.$likes_str.'
                                    </div>

                                </div>';

                    ?>
                    
                    <script>

                        $(document).ready(function(){

                            $('#post<?php echo $id; ?>').on("click", function(){
                                bootbox.confirm("Are you sure you want to delete this post?", function(result){

                                    $.post("php/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

                                    if(result){
                                        location.reload();
                                    }

                                });
                            })

                        })

                    </script>

                    <?php
                    }else{
                        echo "<center>You are not friends with this user!</center>";
                        return;
                    }

            }else{
                echo "<center>No post found by this post id!</center>";
                return;
            }  

            echo $str;
        }

        public function submitStories($username, $body, $image){

            $date_added = date("Y-m-d H:i:s");
            $query = mysqli_query($this->conn, "INSERT INTO story VALUES (NULL, '$username', '$image', '$body', '$date_added', ',')");

        }

        public function loadStories($user){

            $userLoggedIn = $this->user_obj->getUsername();

            $upload = '<div class="stories_body_upload">

                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalDefault"><i class="material-icons">add</i></button>

                        </div>';
            $str = "";
            $data_query = mysqli_query($this->conn, "SELECT * FROM story WHERE `date` >= NOW() - INTERVAL 1 DAY ORDER BY id DESC");

            if(mysqli_num_rows($data_query) > 0){
                
                while($row = mysqli_fetch_array($data_query)){
                    $id = $row['id'];
                    $body = $row['body'];
                    $image = $row['image'];
                    $username = $row['username'];
                    $date = $row['date'];
                    $date_string = strtotime($date);
                    $viewed = $row['viewed'];

                    if(strpos($viewed, $userLoggedIn) !== false){
                        $view = "yes";
                    }else{
                        $view = "no";
                    }

                    $added_by_obj = new User($this->conn, $username);
                    if($added_by_obj->isClosed()){

                        continue;

                    }

                    $user_logged_obj = new User($this->conn, $userLoggedIn);
                    if($user_logged_obj->isFriend($username)){

                        $user_details_query = mysqli_query($this->conn, "SELECT first_name, last_name, profile_pic FROM users WHERE username = '$username'");
                        $user_row = mysqli_fetch_array($user_details_query);
                        $first_name = $user_row['first_name'];
                        $last_name = $user_row['last_name'];
                        $profile_pic = $user_row['profile_pic'];

                        $str .= '<div class="stories_body">

                        <button type="button" id="story_button" data-bs-toggle="modal" data-bs-target="#exampleModalFullscreen'.$id.'"><img class="'.$view.'" src="'.$profile_pic.'"</button>

                    </div><div class="modal model_view fade" id="exampleModalFullscreen'.$id.'" tabindex="-1" aria-labelledby="exampleModalFullscreenLabel" aria-modal="true" role="dialog">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                        <div class="modal-header stories_header_info">
                            <img src="'.$profile_pic.'">
                            <p class="modal-title" id="exampleModalFullscreenLabel"><span>'.$first_name.' '.$last_name.'</span>'.date("m-d H:i", $date_string).'</p>
                            <form method="POST" class="close_btn" id="close_modal'.$id.'" action="">
                            <input type="hidden" name="story" id="story" value="'.$id.'">
                            <input type="hidden" name="userLoggedIn" value="'.$userLoggedIn.'">
                            <button type="submit" onClick="get_id(this.id)" name="close_modal'.$id.'" id="close_modal'.$id.'" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </form>
                        </div>
                        <div class="modal-body stories_body_section">
                            <img src="'.$image.'">
                            <p>'.$body.'</p>
                        </div>
                        </div>
                    </div>
                    </div>';
                    
                    }    
                    
                }

            }

            echo $str.$upload;




        }


    }

?>