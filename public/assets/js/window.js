$(function() {

    var logged = getCookie("logged");

    if (logged != "yes") {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/api/logout",
            success: function(jResult) {
                location.reload();
            },
            error: function(request, status, error) {

            }
        });
    }

});