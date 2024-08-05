const appModal = $('#modalDownloadQueue');
const loader = $("#loader-wrapper");

function proc_notification(icon, title, text) {
    Swal.fire({
        title: title,
        icon: icon,
        text: text
    })
}

$('#settings_btn').on('click', () => {
    $('#modalSettings').modal('toggle');
})

$('#queue_btn').on('click', () => {
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
            async: false,
        }).done(function (res) {
            text = res.message;
            if (res.status === 200) {
                icon = 'success';
                title = 'Shazam!';
            }
        });
    }
    loader().fadeOut(700);
    proc_notification(icon, title, text);
})

document.addEventListener('alpine:init', () => {
    console.log('Alpine:init');
    Alpine.store('app', {
        init() {
            this.Artists = [];
            this.Queue = [];
        },

        Artists: [],
        Queue: false,

    });

    $("#loader-wrapper").fadeOut(900);

})
