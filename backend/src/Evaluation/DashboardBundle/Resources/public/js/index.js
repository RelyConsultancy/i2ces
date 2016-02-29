var app = document.getElementById('application');

setTimeout(function () {
    var host = window.location.origin;
    $.ajax( {
        type : "GET",
        url : host + "/api/me",
        async: false,
        success : function(data){
            app.innerHTML = "<p>Success</p>";
            app.innerHTML += "<p>Id: " + data.id + "</p>";
            app.innerHTML += "<p>Username: " + data.username + "</p>";
        },
        error : function(xhr, ajaxOptions, thrownError) {
            app.innerHTML = thrownError;
        }
    });
}, 2000);
