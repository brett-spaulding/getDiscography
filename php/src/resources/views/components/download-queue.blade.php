<div class="row">

    <template x-if="$store.app.Queue.length > 0" >
        <template x-for="album in $store.app.Queue">
        <div class="col-12">
            <div class="queue_card">
                <div class="card mb-3 p-0">
                    <div class="row g-0">
                        <div class="col-md-3">

                            <div class="dl_queue_img">

                                <!-- Downloading Spinner -->
                                <template x-if="album.state === 'in_progress'">
                                    <div class="icn-downloading">
                                        <i class="la la-6x la-compact-disc icn-spinner text-white"></i>
                                    </div>
                                </template>
                                <!-- Album Art -->
                                <img :src="album.image" class="img-fluid rounded-start"
                                     :alt="album.name" style="width: 100%; height: 100%; min-height: 180px;">
                            </div>

                        </div>
                        <div class="col-md-9 vinyl-card">
                            <div class="card-body">
                                <a :href="album.url_remote" target="_blank">
                                    <h3 class="card-title" x-text="album.name"></h3>
                                </a>
                                <h5 class="card-text"
                                    style="font-weight: bold; font-family: serif;"><span x-text="album.artist_id.name"/>
                                </h5>

                                <template x-if="album.state === 'in_progress'">
                                    <p>Downloading...</p>
                                </template>

                                <template x-if="album.state === 'pending'">
                                    <p class="text-muted">Waiting to Download</p>
                                </template>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    </template>

    <template x-if="$store.app.Queue.length === 0">
        <p>Queue is empty</p>
    </template>

</div>
