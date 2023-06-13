$('#download_btn').on('click', () => {
    let artist = $('#search_bar').val();
    // Prevent
    $('#search_bar').val('');

    if (artist) {
        $("#loader-wrapper").fadeIn(300);
        $.ajax({
            url: `/api/v1/get/${artist}`,
        }).done(function (res) {
            console.log('---');
            console.log(res);
            console.log('---');
            $("#loader-wrapper").fadeOut(700);
        });
    } else {
        console.log('No artist');
    }

})