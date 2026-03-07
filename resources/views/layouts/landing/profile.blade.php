@extends('layouts.landing.app')
@section('content')
    <!-- ***** Main Banner Area Start ***** -->
    <section class="section main-banner" id="top" data-section="section1">
        <video autoplay muted loop id="bg-video">
            <source src="assets/images/course-video.mp4" type="video/mp4" />
        </video>

        <div class="video-overlay header-text">
            <div class="caption">
                <h6>Profil Sekolah</h6>
                <h2><em>MI MIFTAHUL ULUM</em> Mojokerto</h2>
                <div class="main-button">
                    <a href={{ route('login') }}>Sistem Rangking</a>
                </div>
            </div>
        </div>
    </section>
    <!-- ***** Main Banner Area End ***** -->


    
    <section class="section why-us" data-section="section2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-heading">
                        <h2>Visi & Misi</h2>
                    </div>
                </div>
                <div class="col-md-12">
                    <div id='tabs'>
                        <ul>
                            <li><a href='#tabs-1'>Visi</a></li>
                            <li style="width: 100px"></li>
                            <li><a href='#tabs-2'>Misi</a></li>
                        </ul>
                        <section class="tabs-content">

                            <article id="tabs-1">
                                <div class="row justify-content-center">
                                    <div class="col-md-8 text-center">
                                        <h4>Visi</h4>
                                        <p>
                                            Iptek berdasi andal dalam pengetahuan ilmu teknologi dan seni
                                            berdasarkan syariat Islam
                                        </p>
                                    </div>
                                </div>
                            </article>

                            <article id="tabs-2">
                                <div class="row justify-content-center">
                                    <div class="col-md-8 text-center">
                                        <h4>Misi</h4>

                                        <div class="col-md-8 text-left offset-2">
                                        <p>• Mendidik insan yang berakhlakul karimah.</p>
                                        <p>• Mengembangkan model pembelajaran yang seimbang dan sinergis antara penguasaan
                                            IPTEK dan syariat Islam.</p>
                                        <p>• Melaksanakan pembelajaran efektif untuk mengembangkan potensi akademik dan
                                            non-akademik siswa sesuai bakat dan minat.</p>
                                        <p>• Menumbuhkembangkan sikap cinta ilmu dan cinta belajar sebagai perwujudan pilar
                                            pendidikan global.</p>
                                        <p>• Menumbuhkembangkan sikap kritis dan analitis di kalangan guru.</p>
                                        <p>• Mengembangkan model administrasi pendidikan modern berbasis komputer.</p>
                                        <p>• Mengembangkan kegiatan ekstrakurikuler berbasis kecakapan hidup (life skill).
                                        </p>
                                        </div>

                                    </div>
                                </div>
                            </article>

                        </section>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section courses" data-section="section4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-heading">
                        <h2>Kegiatan</h2>
                    </div>
                </div>
                <div class="owl-carousel owl-theme">
                    <div class="item">
                        <img src="assets/images/courses-01.jpg" alt="Course #1">
                        <div class="down-content">
                            <h4>Pramuka</h4>
                        </div>
                    </div>
                    <div class="item">
                        <img src="assets/images/courses-02.jpg" alt="Course #2">
                        <div class="down-content">
                            <h4>Praktik Membatik</h4>
                        </div>
                    </div>
                    <div class="item">
                        <img src="assets/images/courses-03.jpg" alt="Course #3">
                        <div class="down-content">
                            <h4>OTC</h4>
                        </div>
                    </div>
                    <div class="item">
                        <img src="assets/images/courses-04.jpg" alt="Course #4">
                        <div class="down-content">
                            <h4>Tahfidz</h4>
                        </div>
                    </div>
                    <div class="item">
                        <img src="assets/images/courses-05.jpg" alt="Course #5">
                        <div class="down-content">
                            <h4>Tilawah</h4>
                        </div>
                    </div>
                    

                </div>
            </div>
        </div>
    </section>


    <section class="section video" data-section="section5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 align-self-center">
                    <div class="left-content">
                        <span>Sekolah ini untukmu</span>
                        <h4>Tonton videonya untuk mengetahui lebih lanjut <em>tentang MI Miftahul Ulum</em></h4>
                    </div>
                </div>
                <div class="col-md-6">
                    <article class="video-item">
                        <div class="video-caption">
                            <h4>Vidio Profil MI Miftahul Ulum</h4>
                        </div>
                        <figure>
                            <a href="https://www.youtube.com/watch?v=H_9m9Fx_WfM" class="play"><img
                                    src="assets/images/yt.png"></a>
                        </figure>
                    </article>
                </div>
            </div>
        </div>
    </section>

<section class="section contact" data-section="section6">
    <div class="container">
        <div class="row">

            <!-- JUDUL -->
            <div class="col-md-12">
                <div class="section-heading">
                    <h2 style="color:#fff;">Mari Berkunjung</h2>
                </div>
            </div>

            <!-- CONTACT INFO (KIRI) -->
            <div class="col-md-6">
                <div class="contact-info" style="color:#fff;">
                    <h2 style="color:#fff;">Kontak</h2>

                    <div class="info-block">
                        <h5 style="color:#fff;">ALAMAT</h5>
                        <p style="color:#fff;">
                            Dusun Lengkong RT.05, Lengkong, Kecamatan Mojoanyar Kabupaten Mojokerto, Jawa Timur, 61364
                        </p>
                    </div>

                    <div class="info-block">
                        <h5 style="color:#fff;">EMAIL</h5>
                        <p style="color:#fff;">mimiftahululum.lkg13@gmail.com</p>
                    </div>

                    <div class="info-block">
                        <h5 style="color:#fff;">CONTACT</h5>
                        <p style="color:#fff;">(0321) 395131</p>
                    </div>
                </div>
            </div>

            <!-- MAPS (KANAN) -->
            <div class="col-md-6">
                <div id="map">
                    <iframe
                        src="https://www.google.com/maps?q=MI+Miftahul+Ulum+Lengkong&z=15&output=embed"
                        width="100%"
                        height="422"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy">
                    </iframe>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
