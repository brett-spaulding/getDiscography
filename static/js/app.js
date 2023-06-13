$('#download_btn').on('click', () => {
    console.log('Clicked Download!');
    let artist = $('#search_bar').val();
    $("#loader-wrapper").fadeIn(300);
    $.ajax({
        url: `/api/v1/get/${artist}`,
    }).done(function () {
        artist.val('');
        $("#loader-wrapper").fadeOut(700);
    });
})