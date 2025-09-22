<nav class="navbar navbar-expand-lg navbar-light bg-light d-flex justify-content-between">
    <div class="m-2">
        <button type="button" id="sidebarCollapse" class="em-btn btn-dark">
            <i class="fa fa-bars"></i>
            <span class="sr-only">Toggle Menu</span>
        </button>
    </div>

    <div class="m-2" id="navbarSupportedContent">
        <ul class="nav navbar-nav ml-auto">

            {{ $slot }}

        </ul>
    </div>
</nav>