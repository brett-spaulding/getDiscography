console.log('Version 1:20:2');
const appModal = $('#modalDownloadQueue');
const loader = $("#loader-wrapper");

function construct_artist_result_html(artist_list) {
    let html = '<h3>Found Artist!</h3>';
    let index = 0;
    artist_list.forEach((element) => {
        index += 1;
        html += `
            <div class="card w-100 p-2 mb-2">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-3">
                            <img src="${element.thumbnail}" width="72px" height="72px" style="border-radius: 12px;"/>
                        </div>
                        <div class="col-9 m-auto">
                            <h4>${element.name}</h4>
                        </div>
                    </div>
                </div>
            </div>
        `
        if (index === 1 && artist_list.length > 1) {
            html += '<hr/>';
            html += '<h6>Suggested Artists</h6>'
            html += '<hr/>';
        }
    })
    return html
}

function proc_notification(icon, html, text) {
    Swal.fire({
        html: html,
        icon: icon,
        text: text
    })
}

$('#settings_btn').on('click', () => {
    $('#modalSettings').modal('toggle');
});

$('#catalog_btn').on('click', () => {
    $('#modalCatalog').modal('toggle');
});

$('#queue_btn').on('click', () => {
    appModal.modal('toggle');
});

$('#download_btn').on('click', () => {
    loader.fadeIn(300);

    let artist = $('#search_bar').val();
    let icon = 'error';
    let title = 'What the flip?!';

    // Send request to server
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
                    title = construct_artist_result_html(response);
                    proc_notification(icon, title, 'Artist found');
                    $('#search_bar').val('');
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
            proc_notification(icon, title, 'You need to add an artist, c\'mon man!');
            loader.fadeOut(700);
        }
    }, 10);

});

document.addEventListener('alpine:init', () => {
    console.log('Alpine:init');
    Alpine.store('app', {
        init() {
            // TODO: Poll for artists and queue
            this.Artists = [];
            this.Queue = [];
            this.ArtistResults = [];
        },

        Artists: [],          // Rendered in the 'Artists' modal
        ArtistResults: [],   // Rendered in the SWAL popup
        Queue: [],          // Rendered in the 'Queue' modal

    });

    $("#loader-wrapper").fadeOut(900);

});

$(document).ready(function () {
    let ArtistTable = $('#artistsCatalogDatatable').DataTable({
        ajax: '/api/artists',
        type: 'get',
        dataType: 'json',
        columns: [
            {data: 'thumbnail', render: (data) => { return `<img src="${data}" height=48 width="48" style="border-radius: 6px;"/>`}},
            {data: 'name'},
            {title: 'Channel', data: 'url_remote', render: (data) => {return `<a href="https://music.youtube.com/${data}" class="btn btn-danger" target="_blank"><i class="lab la-youtube"></i></a>`}},
            {data: 'state'},
            {data: 'id', render: (data, row) => {
                let stateDiable = row.state === 'in_progress' ? 'disabled': '';
                let stateClass = row.state === 'in_progress' ? '': 'btn-primary';
                return `<button class="btn ${stateClass}" hx-get="/api/artist/toggle" ${stateDiable}><i class="las la-cloud-download-alt"></i> Download</button>`}
            }
        ],
    });

    // const getArtistTableInterval = setInterval(function() {
    //     table.ajax.reload();
    // }, 5000);

});

