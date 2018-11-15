var timer = 12;
// Timer to redirect user to previous page. Only for this page.
setInterval(function() {
    if (timer === 0) {
       location.href = $('#back-url').val();
    } else {
        timer--;
    }

    $('#timer').html(timer);

}, 1000);