
        <?php
        
            include "header.php";

            include "php/plugins.php";

            include "php/classes/Users.php";

            include "php/classes/Post.php";

            include "php/classes/Message.php";

            include "php/classes/Notification.php";

            include "php/compress.php";

            $result = "";

            if(isset($_POST['post'])){

                $filename = $_FILES['fileToUpload']['name'];

                if($filename != ""){
   
                    $valid_ext = array('png', 'jpeg', 'jpg');
                    $photoExt1 = @end(explode(".", $filename));
                    $photoTest1 = strtolower($photoExt1);
                    $post_pic = time() . '.' . $photoTest1;
                    $location = "assets/images/posts/" . $post_pic;
                    $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                    $file_extension = strtolower($file_extension);

                    if(in_array($file_extension, $valid_ext)){
                        compressedImage($_FILES['fileToUpload']['tmp_name'], $location, 50);

                        $imagePath = "assets/images/posts/" . $post_pic;
                        $post = new Post($conn, $userLoggedIn);
                        $post->submitPost($_POST['post_text'], 'none', $imagePath);

                    }else{
                        $result = "File format is not correct.";
                    }

                }else{
                    $imagePath = "";
                    $post = new Post($conn, $userLoggedIn);
                    $post->submitPost($_POST['post_text'], 'none', $imagePath);
                }

                
            }

            if(isset($_POST['submit_stories'])){

                if($_POST['story_body'] == ""){
                    $body = "";
                }else{
                    $body = $_POST['story_body'];
                }

                $filename = $_FILES['story_image']['name'];

                if($filename != ""){
   
                    $valid_ext = array('png', 'jpeg', 'jpg');
                    $photoExt1 = @end(explode(".", $filename));
                    $photoTest1 = strtolower($photoExt1);
                    $post_pic = time() . '.' . $photoTest1;
                    $location = "assets/images/story/" . $post_pic;
                    $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                    $file_extension = strtolower($file_extension);

                    if(in_array($file_extension, $valid_ext)){
                        compressedImage($_FILES['story_image']['tmp_name'], $location, 50);

                        $imagePath = "assets/images/story/" . $post_pic;
                        $post = new Post($conn, $userLoggedIn);
                        $post->submitStories($userLoggedIn, $body, $imagePath);

                    }else{
                        $result = "File format is not correct.";
                    }

                }else{
                    $result = "Select an image for the story.";
                }

            }else{
                $result = "no";
            }

        ?>

        <title>Social</title>

    </head>

    <body>

        <?php include "navbar.php"; ?>
        
        <div class="container">

            <div class="main_column">

                <div class="stories">


                </div>

                <!-- <form action="index.php" method="POST" class="post_form" enctype="multipart/form-data">

                    <textarea name="post_text" id="post_text" placeholder="Got something to say!"></textarea>
                    <label for="fileToUpload" id="uploadImageButton">
                        <input type="file" name="fileToUpload" id="fileToUpload">
                        Attach Image File
                    </label>
                    <input type="submit" name="post" id="post_button" value="Post">

                </form> -->

                <div class="card">
                    <div class="card-header">
                        Create a new Post.
                    </div>
                    <div class="card-body">
                        <form action="index.php" method="POST" enctype="multipart/form-data">
                            <textarea name="post_text" class="form-control" id="post_text" placeholder="Got something to say!"></textarea>
                            <input type="file" class="form-control" name="fileToUpload" id="fileUpload">
                    </div>
                    <div class="card-footer text-muted">
                        <input type="submit" name="post" id="post_button_home" value="Post">
                        </form>
                    </div>
                </div>

                <!-- <div class="trends_body">

                    <div class="trends_header">
                        <h5><?php echo $result; ?></h5>
                    </div>

                    <div class="trending_words">

                        <?php 
                            $query = mysqli_query($conn, "SELECT * FROM trends ORDER BY hits DESC LIMIT 6");

                            foreach($query as $row){

                                $word = $row['title'];
                                $word_dot = strlen($word) >= 14 ? "..." : "";

                                $trimmed_word = str_split($word, 14);
                                $trimmed_word = $trimmed_word[0];

                                echo "<div class='trending_box'>
                                        <span>".$trimmed_word. $word_dot ."</span>
                                    </div>";

                            }
                        ?>
                        
                    </div>

                </div> -->

                <div class="card trend">
                    <div class="card-header">
                        Trending
                    </div>
                    <div class="card-body">
                        <div class="trending_words">

                            <?php 
                                $query = mysqli_query($conn, "SELECT * FROM trends ORDER BY hits DESC LIMIT 6");

                                foreach($query as $row){

                                    $word = $row['title'];
                                    $word_dot = strlen($word) >= 14 ? "..." : "";

                                    $trimmed_word = str_split($word, 14);
                                    $trimmed_word = $trimmed_word[0];

                                    echo "<div class='trending_box'>
                                            <span>".$trimmed_word. $word_dot ."</span>
                                        </div>";

                                }
                            ?>

                        </div>
                    </div>
                </div>



                <div class="posts_area"></div>
                <div class="img-loading">

                    <img id="loading" src="assets/images/icons/loading.gif" alt="">

                </div>

            </div>
            
            <!--modal -->

            <div class="modal fade" id="exampleModalDefault" tabindex="-1" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload a story.</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">

                            <textarea name="story_body" class="form-control" placeholder="Got something to say!"></textarea>
                            <input type="file" name="story_image" class="form-control" id="story_image">
                            <input type="hidden" name='story_username' value="<?php echo $userLoggedIn; ?> ">
                            <div class="modal-footer">
                            <button type="submit" name="submit_stories" id="submit_stories"><i class="material-icons">add</i></button>

                    </form>
                    </div>
                </div>
                </div>
            </div>
            </div>

            

        </div>

        <script>
            
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';

            function getStories(){

            $.ajax({
                url: "php/ajax_load_stories.php",
                type: "POST",
                data: "userLoggedIn=" + userLoggedIn,
                cache:false,

                success: function(data) {
                    $('.stories').html(data);
                }
            });

            }

            getStories();

            function get_id(clicked_id){
                id = "#"+clicked_id;

                $(document).on("submit", id, function(){
                
                $.ajax({
                    url: "php/set_story_viewed.php",
                    type: "POST",
                    data: $(id).serialize(),
                    cache: false,

                    success: function(data){
                        getStories();
                    }
                })
                return false;
                });
            }

            $(document).ready(function() {

                $('#loading').show();

                //Original ajax request for loading first posts 
                $.ajax({
                    url: "php/ajax_load_posts.php",
                    type: "POST",
                    data: "page=1&userLoggedIn=" + userLoggedIn,
                    cache:false,

                    success: function(data) {
                        $('#loading').hide();
                        $('.posts_area').html(data);
                    }
                });

                $(window).scroll(function() {
                    var height = $('.posts_area').height(); //Div containing posts
                    var scroll_top = $(this).scrollTop();
                    var page = $('.posts_area').find('.nextPage').val();
                    var noMorePosts = $('.posts_area').find('.noMorePosts').val();

                    if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                        $('#loading').show();

                        var ajaxReq = $.ajax({
                            url: "php/ajax_load_posts.php",
                            type: "POST",
                            data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                            cache:false,

                            success: function(response) {
                                $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
                                $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

                                
                                $('.posts_area').append(response);
                                $('#loading').hide();
                                $('.img-loading').hide();
                            }
                        });

                    } //End if 

                    return false;

                }); //End (window).scroll(function())

            });



                

        </script>

    </body>

</html>