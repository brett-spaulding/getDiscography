console.log('Version 1:20:2');
const appModal = $('#modalDownloadQueue');
const loader = $("#loader-wrapper");

function template_artist_result(element) {
    return `
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
    console.log(self);
    console.log(self.data('artist_id'));
    let artist_name = self.data('artist_name');
    self.prop('disabled', true)
    $.ajax({
        url: `/api/queue/artist/${self.data('artist_id')}`,
        success: () => {
            proc_notification('success', 'Queued Download', `Artist ${artist_name} Queued for Download!`);
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
        loader.fadeIn(300);
        let artist = $('#search_bar').val();

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
                        let html = construct_artist_result_html(response);
                        proc_notification(icon, 'Shazam!', html);
                        $('#search_bar').val('');
                        loader.fadeOut(700);
                    },
                    error: (response) => {
                        console.log('Receiving response...');
                        console.log(response);
                        console.log('===========');
                        proc_notification(icon, 'What the flip?!', response.statusText);
                        loader.fadeOut(700);
                    }
                });

            } else {
                proc_notification(icon, 'Whoopsie!', 'You need to add an artist, c\'mon man!');
                loader.fadeOut(700);
            }
        }, 10);

    });
}

document.addEventListener('alpine:init', () => {
    console.log('Alpine:init');
    Alpine.store('app', {
        init() {
            // TODO: Poll for artists and queue
            this.Queue = [];
        },

        Queue: [],          // Rendered in the 'Queue' modal

    });

    $("#loader-wrapper").fadeOut(900);

});

$(document).ready(function () {

    bind_action_buttons();

    //Datatable for 'Catalog' menu
    let ArtistTable = $('#artistsCatalogDatatable').DataTable({
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
                    let stateClass = row.state === 'in_progress' ? '' : 'btn-primary';
                    let artist_name = row.name;
                    return `<button class="btn ${stateClass}" style="float: right;" data-artist_name="${artist_name}" data-artist_id="${data}" onclick="artist_queue_toggle(this)" ${stateDiable}><i class="las la-cloud-download-alt"></i> Download</button>`
                }
            }
        ],
    });
    // Polling for table update
    const getArtistTableInterval = setInterval(function () {
        ArtistTable.ajax.reload();
    }, 5000);
});

