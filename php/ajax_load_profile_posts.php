<?php 

    include "../header.php";
    include "plugins.php";
    include "classes/Users.php";
    include "classes/Post.php";

    $limit = 10;

    $posts = new Post($conn, $_REQUEST['userLoggedIn']);
    $posts->loadProfilePost($_REQUEST, $limit);

?>

</head>
<body>
    
    

</body>

</html>

