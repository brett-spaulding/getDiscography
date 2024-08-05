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
    @include('assets.scripts-header');

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

                <!-- Navigation -->
                @include('components.action-menu');

                <main class="container">
                    @yield('content')
                </main>

                <!-- Modals -->
                @include('modals.modal-download-queue');
                @include('modals.modal-settings');

                <!-- Utilities -->
                @include('utils.outdated-browser');

            </div>
        </div>
    </div>
</div>

<!-- Assets -->
@include('assets.zapp');
@include('assets.scripts-footer');
</body>
</html>
