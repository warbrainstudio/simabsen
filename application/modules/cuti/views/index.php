<?php require_once(APPPATH . 'modules/_cssInject/main.css.php') ?>
<section id="cuti">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"><?php echo (isset($card_title)) ? $card_title : '' ?></h4>
            <h6 class="card-subtitle"><?php echo (isset($card_subTitle)) ? $card_subTitle : '' ?></h6>

            <div class="table-action">
                <div class="buttons">
                    <a href="<?php echo base_url('cuti/input') ?>" modal-id="modal-form-cuti" class="btn btn--raised btn-primary btn--icon-text x-load-modal-partial cuti-add">
                        <i class="zmdi zmdi-plus"></i> Buat Cuti
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table id="table-cuti" class="table table-bordered">
                    <thead class="thead-default">
                        <tr>
                            <th width="30">No</th>
                            <th>TANGGAL</th>
                            <th>ID</th>
                            <th>JENIS</th>
                            <th>DIMULAI</th>
                            <th>BERAKHIR</th>
                            <th>BEKERJA</th>
                            <?php //if ($this->session->userdata('user')['role'] === 'Administrator') : ?>
                            <th>P1</th>
                            <th>P2</th>
                            <th>P3</th>
                            <?php //else : ?>
                            <!--<th>PERSETUJUAN</th>-->
                            <?php //endif ?>
                            <th>STATUS</th>
                            <th width="170" class="text-center">#</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <br>
</section>