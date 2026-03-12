<div class="d-block d-md-none">
    <div class="row row-cards mt-2">
        <div class="space-y">
            <?php
            if (!empty($bookings)) {
                $page = (is_numeric($this->uri->segment(3))) ? $this->uri->segment(3) : 1;
                $no = (($page - 1) * $per_page) + 1;

                foreach ($bookings as $c) : ?>
                    <div class="card">
                        <div class="row g-0">
                            <div class="col-auto">
                                <div class="card-body">
                                    <div class="avatar avatar-md"><?= $no++; ?></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card-body ps-0">
                                    <div class="row">
                                        <div class="col">
                                            <h3 class="mb-0"><a href="#"><?= htmlspecialchars($c->no_resi) ?></a></h3>
                                        </div>
                                        <div class="col-auto fs-3 text-green">
                                            <?php
                                            $status_bayar_colors = [
                                                '0' => 'yellow',
                                                '1' => 'lime',
                                                // Tambahkan status lainnya jika diperlukan
                                            ];
                                            $color_status_bayar = $status_bayar_colors[$c->status_bayar] ?? 'secondary'; // Default ke 'secondary' jika tidak terdaftar 
                                            ?>
                                            <span class="badge bg-<?= $color_status_bayar ?> text-<?= $color_status_bayar ?>-fg">
                                                <?= ucfirst($c->order_status) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <!-- <div class="row">
                                        <div class="col-md-6">
                                            <div class="mt-3 list-inline list-inline-dots mb-0 text-secondary d-sm-block">
                                                <div class="list-inline-item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                    </svg>
                                                    <?= $c->order_name ?>
                                                </div>
                                                <div class="list-inline-item text-end">
                                                    <h3><?= $c->order_name ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                endforeach;
            } else {
                ?>
                <div class="card">
                    <div class="card-body">
                        <h3>Tidak ada data yang ditampilkan</h3>
                    </div>
                </div>
            <?php
            } ?>
            <div class="card">
                <?php $this->load->view('pages/layouts/_pagination') ?>
            </div>
        </div>
    </div>

</div>