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

        <!-- === ALUR 5 MENU SPK (DIPERBARUI) === -->

        <!-- 1. Hitung Manual (SAW) - SEBELUM ROC -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.manual.index') }}">
                <i class="icon-paper menu-icon"></i>
                <span class="menu-title">1. Hitung Manual (SAW)</span>
            </a>
        </li>

        <!-- 2. Bobot ROC -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kriteria.index') }}">
                <i class="icon-paper menu-icon"></i>
                <span class="menu-title">2. Bobot Kriteria (ROC)</span>
            </a>
        </li>

        <!-- 3. Hitung Borda -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.borda.index') }}">
                <i class="icon-paper menu-icon"></i>
                <span class="menu-title">3. Hitung Borda</span>
            </a>
        </li>

        <!-- 4. Hitung WP -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.wp.index') }}">
                <i class="icon-paper menu-icon"></i>
                <span class="menu-title">4. Hitung WP</span>
            </a>
        </li>

        <!-- 5. Laporan Analisis -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#laporan" aria-expanded="false" aria-controls="laporan">
                <i class="icon-paper menu-icon"></i>
                <span class="menu-title">5. Laporan Analisis</span>
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
