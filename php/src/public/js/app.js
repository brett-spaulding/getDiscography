console.log('Version 1:20:2');
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
    console.log('Blocking UI');
    loader.fadeIn(300);

    let artist = $('#search_bar').val();
    // Prevent
    $('#search_bar').val('');
    let icon = 'error';
    let title = 'What the flip?!';
    let text = 'You need to add an artist bro..';

    setTimeout(() => {
        if (artist) {
            console.log('Sending search request...');
            $.ajax({
                url: `/artist/${artist}`,
                success: (response) => {
                    console.log('Receiving response...');
                    console.log(response);
                    console.log('===========');
                    icon = 'success';
                    title = 'Shazam!';
                    proc_notification(icon, title, text);
                    loader.fadeOut(700);
                },
                error: (response) => {
                    console.log('Receiving response...');
                    console.log(response);
                    console.log('===========');
                    proc_notification(icon, title, response.statusText);
                    loader.fadeOut(700);
                }
            });

        } else {
            proc_notification(icon, title, text);
            loader.fadeOut(700);
        }
    }, 100);

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
