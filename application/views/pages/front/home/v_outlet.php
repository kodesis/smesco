<!-- Page Header Start -->
<div class="container-fluid page-service py-5">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3 animated slideInDown">Outlet Kami</h1>
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="<?= base_url() ?>">Beranda</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">Layanan</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->
<div class="container-xxl py-5">
    <div class="container py-5">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="text-secondary text-uppercase">Outlet Kami</h6>
            <h1 class="mb-5">Kunjungi Outlet Kami</h1>
        </div>
        <div class="row g-4">
            <?php
            if ($outlets) {
                foreach ($outlets as $o) :  ?>
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="service-item p-4">
                            <div class="overflow-hidden mb-4">
                                <img class="img-fluid w-10" src="<?= base_url() ?>assets/img/profile/default.png" alt="Air Freight">
                            </div>
                            <h4 class="mb-3"><?= $o->nama_pendaftar ?></h4>
                            <p><?= $o->alamat_lengkap ?></p>
                            <a class="btn-slide mt-2" href="<?= $o->google_maps ?>" target="_blank"><i class="fa fa-map-marker-alt"></i><span>Lihat di maps</span></a>
                        </div>
                    </div>
            <?php
                endforeach;
            } ?>
        </div>
    </div>
</div>
