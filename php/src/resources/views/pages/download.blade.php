@extends('layouts.public')

@section('content')
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
@show
