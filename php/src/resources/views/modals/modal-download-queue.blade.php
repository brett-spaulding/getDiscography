<!-- Download Queue Modal -->
<div class="modal fade"
     id="modalDownloadQueue" tabindex="-1" aria-labelledby="modalDownloadQueue" aria-hidden="true">
    <div
        class="modal-dialog  modal-dialog-centered modal-lg modal-dialog-scrollable modal-fullscreen-md-down modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Download Queue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal_content">
                    @include('components.download-queue')
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
