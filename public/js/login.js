$(document).ready(function() {
    var checkbox = $("#checkbox");
    var password = $("#password" );
    var registration_password =  $("#registration_password")
    checkbox.click(function() {
        if(checkbox.prop("checked")) {
            password.attr("type", "text");
            registration_password.attr("type", "text");
        } else {
            password.attr("type", "password");
            registration_password.attr("type", "text");
        }
    });
});




