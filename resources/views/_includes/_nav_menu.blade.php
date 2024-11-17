<!-- [ navigation menu ] start -->
<nav class="pc-sidebar ">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('home') }}" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <!-- <img src="{{ asset('images/logo.svg') }}" alt="" class="logo logo-sm"> -->
                <img src="{{ asset('images/bbt_logo_medium2.svg') }}" style="height:50px;" alt="" class="logo logo-lg">
                <!-- <img src="{{ asset('images/logo-sm.svg') }}" alt="" class="logo logo-sm"> -->
                <img src="{{ asset('images/bbt_logo_medium2.svg') }}" style="height:50px;" alt="" class="logo logo-sm">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                    <label>Rekap</label>
                </li>
                <li class="pc-item">
                    <a href="{{ route('home') }}" class="pc-link "><span class="pc-micon"><i class="material-icons-two-tone">home</i></span><span class="pc-mtext">Dashboard</span></a>
                </li>
                
                <li class="pc-item pc-caption">
                    <label>Contact Person</label>
                    <span></span>
                </li>
                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link "><span class="pc-micon"><i class="material-icons-two-tone">business_center</i></span><span class="pc-mtext">View Data</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link" href="{{ url('/contact-person/list-by-hpl') }}">By Lokasi HPL</a></li>
                        <li class="pc-item"><a class="pc-link" href="{{ url('/contact-person/list-by-instansi') }}">By Instansi</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ url('/contact-person/input') }}" class="pc-link "><span class="pc-micon"><i class="material-icons-two-tone">business_center</i></span><span class="pc-mtext">Input</span></a>
                </li>
                
                <li class="pc-item pc-caption">
                    <label>Laporan</label>
                    <span></span>
                </li>
                <li>
                    <a href="{{ url('/laporan/list') }}" class="pc-link "><span class="pc-micon"><i class="material-icons-two-tone">business_center</i></span><span class="pc-mtext">View Data</span></a>
                </li>
                <li>
                    <a href="{{ url('/laporan/input') }}" class="pc-link "><span class="pc-micon"><i class="material-icons-two-tone">business_center</i></span><span class="pc-mtext">Upload</span></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- [ navigation menu ] end -->