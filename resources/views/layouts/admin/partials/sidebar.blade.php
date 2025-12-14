<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        <!-- Menu 1: Dashboard (Bisa diakses Admin & Guru) -->
        <li class="nav-item">
            <a class="nav-link"
                href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('proses.dashboard') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <!-- === KHUSUS GURU: Master Data === -->
        @if (Auth::user()->role === 'guru')
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#master-data" aria-expanded="false"
                    aria-controls="master-data">
                    <i class="icon-folder menu-icon"></i>
                    <span class="menu-title">Master Data</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="master-data">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"> <a class="nav-link" href="{{ route('proses.siswa.index') }}">Siswa</a>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('proses.kelas.index') }}">Kelas</a>
                        </li>
                        <!-- Tahun Ajaran dihapus -->
                        <li class="nav-item"> <a class="nav-link"
                                href="{{ route('proses.semester.index') }}">Semester</a></li>
                        <li class="nav-item"> <a class="nav-link"
                                href="{{ route('proses.kriteriaguru.index') }}">Kriteria</a></li>
                    </ul>
                </div>
            </li>
        @endif

        <!-- === AKSES PROSES INPUT (Guru & Admin Bisa Akses, atau Khusus Guru) === -->

        @if (Auth::user()->role === 'guru')
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#proses-input" aria-expanded="false"
                    aria-controls="proses-input">
                    <i class="icon-upload menu-icon"></i>
                    <span class="menu-title">Proses Input Data</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="proses-input">
                    <ul class="nav flex-column sub-menu">
                        {{-- <li class="nav-item"> <a class="nav-link"
                                href="{{ route('proses.penempatan.index') }}">Penempatan Kelas</a></li> --}}
                        <li class="nav-item"> <a class="nav-link" href="{{ route('proses.input-nilai.index') }}">Import
                                Nilai (Excel)</a></li>
                    </ul>
                </div>
            </li>
        @endif

        <!-- === KHUSUS ADMIN: SPK (5 Menu) === -->
        @if (Auth::user()->role === 'admin')
            <li class="nav-item nav-category">Perankingan SPK</li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.status-input.index') }}">
                    <i class="icon-eye menu-icon"></i>
                    <span class="menu-title">Cek Kelengkapan Data</span>
                </a>
            </li>

            <!-- 1. Hitung Manual -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.manual.index') }}">
                    <i class="icon-paper menu-icon"></i>
                    <span class="menu-title">1. Hitung Manual (SAW)</span>
                </a>
            </li>

            <!-- 2. Bobot ROC -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.kriteria.index') }}">
                    <i class="icon-command menu-icon"></i>
                    <span class="menu-title">2. Bobot Kriteria (ROC)</span>
                </a>
            </li>

            <!-- 3. Hitung Borda -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.borda.index') }}">
                    <i class="icon-bar-graph menu-icon"></i>
                    <span class="menu-title">3. Hitung Borda</span>
                </a>
            </li>

            <!-- 4. Hitung WP -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.wp.index') }}">
                    <i class="icon-pie-graph menu-icon"></i>
                    <span class="menu-title">4. Hitung WP</span>
                </a>
            </li>

            <!-- 5. Laporan Analisis -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#laporan" aria-expanded="false"
                    aria-controls="laporan">
                    <i class="icon-file menu-icon"></i>
                    <span class="menu-title">5. Laporan Analisis</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="laporan">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"> <a class="nav-link"
                                href="{{ route('admin.analisis.pemeringkatan') }}">Hasil Pemeringkatan</a></li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('admin.analisis.pengujian') }}">Hasil
                                Pengujian</a></li>
                        <li class="nav-item"> <a class="nav-link"
                                href="{{ route('admin.analisis.gabungan') }}">Rekapitulasi (3 Tahun)</a></li>
                    </ul>
                </div>
            </li>
        @endif

    </ul>
</nav>
