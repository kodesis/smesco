<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

<!-- Page Header Start -->
<div class="container-fluid page-service py-5">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3 animated slideInDown">Daftar Harga</h1>
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="<?= base_url() ?>">Beranda</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">Daftar Harga</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->
<div class="container-xxl py-5">
    <div class="container py-5">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="text-secondary text-uppercase">Daftar Harga Kami</h6>
            <h1 class="mb-5">Jelajahi Daftar Harga Kami</h1>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-12 wow fadeInUp justify-content-center" data-wow-delay="0.3s">
                <div id="pdf-container" class="text-center"></div>
            </div>
        </div>
    </div>
</div>
<script>
    const url = "<?php echo base_url('assets/files/kribo_express_pricelist_2025.pdf'); ?>";

    pdfjsLib.getDocument(url).promise.then(function(pdf) {
        for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
            pdf.getPage(pageNumber).then(function(page) {
                const scale = 1.5;
                const viewport = page.getViewport({
                    scale: scale
                });

                const canvas = document.createElement("canvas");
                const context = canvas.getContext("2d");
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                document.getElementById("pdf-container").appendChild(canvas);

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                page.render(renderContext);
            });
        }
    });
</script>