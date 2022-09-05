<?php
        
            include "header.php";

            include "php/plugins.php";

            include "php/classes/Users.php";

            include "php/classes/Post.php";

            include "php/classes/Message.php";

            include "php/classes/Notification.php";
?>
            <title>Search</title>

</head>

<body>

    <?php include "navbar.php"; ?>
            
    <div class="container">
    <div class="main_column">

        <!-- <div class="search_body">

            <div class="search_header">
                
            </div>
            <div class="search" id="search">

                <form action="search.php" id="search_form" method="GET" name="search_form">
                    <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">
                    <button class="button_holder"><i class="material-icons">search</i></button>
                </form>

            </div>

            <div class="search_results"></div>

            <div class="search_results_footer_empty"></div>
            
        </div> -->

        <div class="card">
                    <div class="card-header">
                        Search
                    </div>
                    <div class="card-body">
                        <div class="search" id="search">

                            <form action="search.php" id="search_form" method="GET" name="search_form">
                                <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" class="form-control" placeholder="Search..." autocomplete="off" id="search_text_input">
                                <button class="button_holder"><i class="material-icons">search</i></button>
                            </form>

                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <div class="search_results"></div>

                        <div class="search_results_footer_empty"></div>
                    </div>
                </div>

    </div>
    </div>

</body>

</html>