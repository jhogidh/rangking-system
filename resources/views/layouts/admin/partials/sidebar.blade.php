<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        <!-- Menu 1: Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <!-- Menu 2: Master Data -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#master-data" aria-expanded="false"
                aria-controls="master-data">
                <i class="icon-folder menu-icon"></i>
                <span class="menu-title">Master Data</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="master-data">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('admin.siswa.index') }}">Siswa</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('admin.kelas.index') }}">Kelas</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('admin.tahun-ajaran.index') }}">Tahun
                            Ajaran</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('admin.semester.index') }}">Semester</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('admin.akademik.index') }}">Mapel
                            Akademik</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('admin.nonakademik.index') }}">Mapel
                            Non-Akademik</a></li>
                </ul>
            </div>
        </li>

        <!-- Menu 3: Proses Input -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#proses-input" aria-expanded="false"
                aria-controls="proses-input">
                <i class="icon-upload menu-icon"></i>
                <span class="menu-title">Proses Input Data</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="proses-input">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('admin.penempatan.index') }}">Penempatan
                            Kelas</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('admin.input-nilai.index') }}">Import Nilai
                            (Excel)</a></li>
                </ul>
            </div>
        </li>

        <!-- === ALUR 4 MENU SPK KAMU === -->

        <!-- Menu 1 (dari 4): Bobot ROC -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kriteria.index') }}">
                <i class="icon-calculator menu-icon"></i>
                <span class="menu-title">1. Bobot Kriteria (ROC)</span>
            </a>
        </li>

        <!-- Menu 2 (dari 4): Hitung Borda -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.borda.index') }}">
                <i class="icon-graph menu-icon"></i>
                <span class="menu-title">2. Hitung Borda</span>
            </a>
        </li>

        <!-- Menu 3 (dari 4): Hitung WP -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.wp.index') }}">
                <i class="icon-graph menu-icon"></i>
                <span class="menu-title">3. Hitung WP</span>
            </a>
        </li>

        <!-- Menu 4 (dari 4): Laporan Analisis -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#laporan" aria-expanded="false" aria-controls="laporan">
                <i class="icon-pie-chart menu-icon"></i>
                <span class="menu-title">4. Laporan Analisis</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="laporan">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('admin.analisis.pemeringkatan') }}">Hasil
                            Pemeringkatan</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('admin.analisis.pengujian') }}">Hasil
                            Pengujian</a></li>
                </ul>
            </div>
        </li>

    </ul>
</nav>
<!-- partial -->
