<?php

    include "../header.php";

    include "plugins.php";

    if(isset($_GET['post_id'])){
        $post_id = $_GET['post_id'];
    }

    if(isset($_POST['result'])){
        if($_POST['result'] == 'true'){
        $query = mysqli_query($conn, "UPDATE posts SET deleted = 'yes' WHERE id = '$post_id'");
        $unlink_query = mysqli_query($conn, "SELECT image from posts WHERE id = '$post_id'");
        $row = mysqli_fetch_array($unlink_query);
        $imagefilename = $row['image'];
        if($row['image'] != ""){
            unlink($imagefilename);
        }
    }
}

?>

</head>

<body>
    
</body>

</html>