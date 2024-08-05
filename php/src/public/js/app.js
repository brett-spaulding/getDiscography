const appModal = $('#modalDownloadQueue');

function proc_notification(icon, title, text) {
    Swal.fire({
        title: title,
        icon: icon,
        text: text
    })
}

$('.settings_btn').on('click', () => {
    $('#modalSettings').modal('toggle');
})

$('.queue_btn').on('click', () => {
    console.log('Get Queue!');
    appModal.modal('toggle');
})

$('#download_btn').on('click', () => {
    let artist = $('#search_bar').val();
    // Prevent
    $('#search_bar').val('');
    let icon = 'error';
    let title = 'What the flip?!';
    let text = 'You need to add an artist bro..';

    if (artist) {
        $("#loader-wrapper").fadeIn(300);
        $.ajax({
            url: `/api/v1/get/artist/${artist}`,
        }).done(function (res) {
            text = res.message;
            if (res.status === 200) {
                icon = 'success';
                title = 'Shazam!';
            }
            $("#loader-wrapper").fadeOut(700);
            proc_notification(icon, title, text);
        });
    } else {
        proc_notification(icon, title, text);
    }
})

document.addEventListener('alpine:init', () => {
    console.log('Alpine:init');
    Alpine.store('app', {
        init() {
            this.Artists = [];
        },

        Artists: [],
        Queue: false,

    });
    
    $("#loader-wrapper").fadeOut(900);

})
