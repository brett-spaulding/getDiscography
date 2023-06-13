$('#download_btn').on('click', () => {
    console.log('Clicked Download!');
    let artist = $('#search_bar').val();
    // Prevent
    $('#search_bar').val('');
    console.log(artist);
    if (artist) {
        $("#loader-wrapper").fadeIn(300);
        $.ajax({
            url: `/api/v1/get/${artist}`,
        }).done(function () {
            $('#search_bar').val('');
            $("#loader-wrapper").fadeOut(700);
        });
    } else {
        console.log('No artist');
    }

})