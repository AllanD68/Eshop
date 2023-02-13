$(document).ready(function() {
    var checkbox = $("#checkbox");
    var registration_password =  $("#registration_password")
    var registration_confirm_password = $("#registration_confirm_password")
    checkbox.click(function() {
        if(checkbox.prop("checked")) {
            registration_password.attr("type", "text");
            registration_confirm_password.attr("type", "text");
        } else {
            registration_password.attr("type", "password");
            registration_confirm_password.attr("type", "password");
        }
    });
});
