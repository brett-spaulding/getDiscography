<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>getDiscography</title>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <meta name="description" content=""/>
    <link href="https://cdn.jsdelivr.net/npm/modern-normalize@v2.0.0/modern-normalize.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href=" {{ asset('/css/line-awesome.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/style.css') }}"/>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" type="image/png" sizes="180x180" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="theme-color" content="#1db8d7">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/htmx.org@2.0.1" integrity="sha384-QWGpdj554B4ETpJJC9z+ZHJcA/i59TyjxEPXiiUgN2WmTyV5OEZWCD6gQhgkdpB/" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>

<div id="loader-wrapper" class="bg-white position-fixed z-3 w-100 h-100 text-center">
    <div class="centered text-center" role="status" style="position: fixed; top: 50%;">
        <i class="la la-4x la-compact-disc icn-spinner"></i>
        <h3>Loading..</h3>
    </div>
</div>

<div id="content">
    <div class="wrapper">
        <div class="page-wrapper">
            <div class="page-content">

                <div id="action_list">
                    <span>
                        <a href="#download-queue" class="pl-2">
                            <i class="las la-2x la-list queue_btn"></i>
                        </a>
                        <a href="#settings" class="pl-2">
                            <i class="las la-2x la-cog settings_btn"></i>
                        </a>
                    </span>
                </div>

                <div class="container">
                    <div class="row">
                        <div id="disk" class="card centered spin">

                            <div class="disk-content centered">
                                <!-- LOGO -->
                                <div class="logo-home mx-auto">
                                    <a id="logo" class="text-black btn-modal" href="#" data-action="main_menu">
                                        <h1 class="logo-text" style="margin-left: 6px;">
                                            getDiscography
                                        </h1>
                                    </a>
                                </div>

                                <div class="text-center mb-2">
                                    <p>Enter an Artist</p>
                                </div>

                                <!-- Search bar -->
                                <div id="search-container">
                                    <div class="input-group mb-3">
                                        <input id="search_bar" type="text" class="form-control" placeholder=""
                                               aria-label="Search" aria-describedby="Search"/>
                                    </div>
                                </div>

                                <a id="download_btn" class="btn btn-outline-secondary action-item-home btn-modal"
                                   href="#download"
                                   data-action="download">
                                    <i class="las la-download"></i>
                                    Download
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Download Queue Modal -->
                <div class="modal fade"
                     id="modalDownloadQueue" tabindex="-1" aria-labelledby="modalDownloadQueue" aria-hidden="true">
                    <div class="modal-dialog  modal-dialog-centered modal-lg modal-dialog-scrollable modal-fullscreen-md-down modal-fullscreen-sm-down">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Download Queue</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="modal_content"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Modal -->
                <div class="modal fade"
                     id="modalSettings" tabindex="-1" aria-labelledby="modalSettings" aria-hidden="true">
                    <div class="modal-dialog  modal-dialog-centered modal-lg modal-dialog-scrollable modal-fullscreen-md-down modal-fullscreen-sm-down">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Settings</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>No settings yet, check back later. A configurable output path is on the todo list!</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!--[if IE]>
                <div class="position-fixed bottom-0 w-100 p-2 text-bg-dark d-flex justify-content-between"><p>
                    Outdated
                    browser
                    dectected. Please use a modern browser for a better browsing experience.</p>
                    <button type="button" class="btn-close btn-close-white" onClick="parentNode.remove()"
                            aria-label="Close"></button>
                </div><![endif]-->


<!--    #         ⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣀⣀⣤⣴⣶⣶⣦⡀            -->
<!--    #         ⠀⣤⡀⢀⣤⣶⣶⣶⣶⣶⣶⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿            -->
<!--    #         ⢀⣈⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿            -->
<!--    #         ⠈⢩⣿⣿⣿⣿⣿⠋⠉⠉⠉⠁⠀⠀⠀⠀⠀⠀⠉⠙⣿⡋⠀            -->
<!--    #         ⠀⣿⣿⣿⣿⣿⡿⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸⣧⠀            -->
<!--    #         ⠀⣿⣿⣿⣿⣿⣇⠀⠀⠀⠀⠀⠀⠀⢀⣤⠀⣤⡄⠀⠈⠻⣷            -->
<!--    #         ⠀⢿⣿⣿⣿⣿⣿⠀⢴⣾⠿⢿⠿⠿⣿⠏⠀⠸⣿⡿⠿⢿⡟            -->
<!--    #         ⠀⠘⣿⡿⠿⢿⣏⠀⠀⠻⢷⣤⣤⡾⠟⠀⠀⠀⢻⣷⠶⢿⡇            -->
<!--    #         ⠀⢰⣿⠀⣤⠀⠉⠁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⣿⠄⢸⡇            -->
<!--    #         ⠀⠀⢿⣦⣤⡄⠀⠀⠀⠀⠀⠀⠀⠀⠀⠻⢶⣶⠾⠛⠀⠘⣿            -->
<!--    #         ⠀⠀⠀⢸⣯⠀⠀⠀⠀⣾⠛⢿⡷⣶⣤⣤⣤⣤⣤⣤⣶⠾⠋            -->
<!--    #         ⠀⠀⠀⠘⣿⠀⠀⠀⠀⠻⢷⣾⣇⣀⣿⡇⠈⣿⣇⣸⣇⠀⠀            -->
<!--    #         ⠀⠀⠀⠀⣿⠀⠀⠀⠀⠀⠀⠉⠙⠛⠛⠛⠛⠛⠛⢛⣿⠇⠀            -->
<!--    #         ⠀⠀⠀⠀⣿⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣿⡏⠀⠀            -->
<!--    #         ⠀⠀⠀⠀⠻⠃⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠻⠃⠀⠀            -->

<!-- "She’s built like a steakhouse but handles like a bistro." -->



                <script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
                        crossorigin="anonymous"></script>
                <script type="text/javascript">
                    $(window).on('load', function () {
                        $("#loader-wrapper").fadeOut(900);
                    });
                </script>
                <script src="{{ asset('js/app.js') }}"></script>
            </div>
        </div>
    </div>
</div>

</body>
</html>
