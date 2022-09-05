$(document).ready(function(){

    $('.button_holder').on('click', function(){
        document.search_form.submit();
    })

})

    $('#submit_profile_post').click(function(){

        $.ajax({
            type: "POST",
            url: "php/ajax_submit_profile_post.php",
            data: $('form.profile_post').serialize(),
            success: function(msg){

                $("#post_form").modal('hide');
                location.reload();

            },
            error: function(){
                alert('Failure');
            }
        });

    })

$(document).click(function(e){

    if(e.target.class != "dropdown_data_window"){
        $(".dropdown_data_window").css({"padding" : "0px"});
        $(".dropdown_data_window").animate({"height" : "0px"}, 300);
    }

});    


function getUsers(value, user){

    $.post("php/ajax_friend_search.php", {query:value, userLoggedIn:user}, function(data){
        $(".results").html(data);
    })

}

function getDropdownData(user, type){

    if($(".dropdown_data_window").css("height") == "0px"){
        var pageName;
        if(type == 'notification'){
            pageName = "ajax_load_notification.php";
            $("span").remove("#unread_notification");
        }else if(type == 'message'){
            pageName = "ajax_load_messages.php";
            $("span").remove("#unread_message");
        }

        var ajaxreq = $.ajax({
            url: "php/" + pageName,
            type: "POST",
            data: "page=1&userLoggedIn=" + user,
            cache: false,

            success: function(response){
                $(".dropdown_data_window").html(response);
                $(".dropdown_data_window").css({"padding" : "0px"});
                $(".dropdown_data_window").animate({"height" : "300px"}, 300);
                $("#dropdown_data_type").val(type);
            }
        })
    }else{
        $(".dropdown_data_window").html("");
        $(".dropdown_data_window").css({"padding" : "0px"});
        $(".dropdown_data_window").animate({"height" : "0px"}, 300);
    }

}

function getLiveSearchUsers(value, user){

    $.post("php/ajax_search.php", {query:value, userLoggedIn:user}, function(data){
        if($(".search_results_footer_empty")[0]){
            $(".search_results_footer_empty").toggleClass("search_results_footer");
            $(".search_results_footer_empty").toggleClass("search_results_footer_empty");
        }

        $('.search_results').html(data);
        $('.search_results_footer').html("<span id='more_results'><a href='search.php?q=" + value +"'>See All Results</a></span>");

        if(data == ""){
            $('.search_results_footer').html("");
            $('.search_results_footer').toggleClass("search_results_footer_empty");
            $('.search_results_footer').toggleClass("search_results_footer");
        }
    })

}