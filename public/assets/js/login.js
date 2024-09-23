$(document).ready(function() {
    $("#signUp").on('click', function(){
        console.log("call to signUP");
        
        $("#container").addClass("right-panel-active");
    })

    $("#signIn").on('click', function(){
        console.log("call to signIn");

        $("#container").removeClass("right-panel-active");
    });
});