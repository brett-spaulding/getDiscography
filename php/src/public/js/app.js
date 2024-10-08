console.log('Version 1:20:2');
const appModal = $('#modalDownloadQueue');
const loader = $("#loader-wrapper");
let ArtistTable = {}; // Initialized for ajax reload

function requestQueue() {
    $.ajax({
        url: '/api/queue/albums',
        success: (response) => {
            Alpine.store('app').Queue = JSON.parse(response);
        }
    })
}

function template_artist_result(element) {
    let image_src = element.image.replace('/var/www/html/public', '');
    console.log(image_src);
    return `
        <div class="card w-100 p-2 mb-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-3">
                        <img src="${image_src}" width="72px" height="72px" style="border-radius: 12px;"/>
                    </div>
                    <div class="col-9 m-auto">
                        <h4>${element.name}</h4>
                    </div>
                </div>
            </div>
        </div>
    `
}

function construct_artist_result_html(artist_list) {
    let html = '<h3>Found Artist</h3>';
    let index = 0;
    if (artist_list.length > 1) {
        artist_list.forEach((element) => {
            index += 1;
            html += template_artist_result(element);
            if (index === 1 && artist_list.length > 1) {
                html += '<hr/>';
                html += '<h6>Suggested Artists</h6>'
                html += '<hr/>';
            }
        })
    } else {
        html += template_artist_result(artist_list);
    }
    return html
}

function proc_notification(icon, title, html) {
    Swal.fire({
        icon: icon,
        title: title,
        html: html
    })
}

function artist_queue_toggle(element) {
    let self = $(element);
    let artist_name = self.data('artist_name');
    self.prop('disabled', true)
    $.ajax({
        url: `/api/queue/artist/${self.data('artist_id')}`,
        success: () => {
            proc_notification('success', 'Queued Download', `Artist ${artist_name} Queued for Download!`);
            // ArtistTable.ajax.reload();
        },
        error: (response) => {
            console.log(response);
            proc_notification('error', 'What the flip?!', `Failed to queue artist ${artist_name} <br/><br/> <strong>${response.status}: ${response.statusText}</strong>`);
            self.prop('disabled', false);
        }
    })
}

function bind_action_buttons() {
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
        let artist = $('#search_bar').val();

        // Send request to server
        setTimeout(() => {

            if (artist == '') {
                return proc_notification('error', 'Whoopsie!', 'You need to add an artist, c\'mon man!');;
            }

            loader.fadeIn(300);

            $.ajax({
                url: `/artist/${artist}`,
                success: (response) => {
                    let html = construct_artist_result_html(response);
                    proc_notification('success', 'Shazam!', html);
                    ArtistTable.ajax.reload();
                    $('#search_bar').val('');
                    loader.fadeOut(700);
                },
                error: (response) => {
                    proc_notification('error', 'What the flip?!', response.statusText);
                    loader.fadeOut(700);
                }
            });

        }, 10);

    });
}

document.addEventListener('alpine:init', () => {
    console.log('Alpine:init');
    Alpine.store('app', {
        Queue: [],          // Rendered in the 'Queue' modal
    });
    requestQueue();
    setInterval(requestQueue, 5000);
    $("#loader-wrapper").fadeOut(900);

});

$(document).ready(function () {
    bind_action_buttons();
    //Datatable for 'Catalog' menu
    ArtistTable = $('#artistsCatalogDatatable').DataTable({
        ajax: '/api/artists',
        type: 'get',
        dataType: 'json',
        columns: [
            {
                data: 'thumbnail', orderable: false, render: (data) => {
                    return `<img src="${data}" height=48 width="48" style="border-radius: 6px;"/>`
                }
            },
            {data: 'name'},
            {
                title: 'Channel', data: 'url_remote', render: (data) => {
                    return `<a href="https://music.youtube.com/${data}" class="btn btn-danger" target="_blank"><i class="lab la-youtube"></i></a>`
                }
            },
            {data: 'state'},
            {
                data: 'id', orderable: false, render: (data, type, row) => {
                    let stateDiable = row.state === 'in_progress' ? 'disabled' : '';
                    let stateClass = row.state === 'done' ? 'btn-success' : 'btn-primary';
                    let artist_name = row.name;
                    let button_icon = row.state === 'done' ? '<i class="las la-redo-alt"></i>' : '<i class="las la-cloud-download-alt"></i>';
                    return `<button class="btn ${stateClass}" style="float: right;" data-artist_name="${artist_name}" data-artist_id="${data}" onclick="artist_queue_toggle(this)" ${stateDiable}>${button_icon} Download</button>`
                }
            }
        ],
    });
});
