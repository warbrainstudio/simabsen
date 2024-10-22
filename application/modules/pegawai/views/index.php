<?php require_once(APPPATH . 'modules/_cssInject/main.css.php') ?>
<section id="pegawai">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"><?php echo (isset($card_title)) ? $card_title : '' ?></h4>
            <h6 class="card-subtitle"><?php echo (isset($card_subTitle)) ? $card_subTitle : '' ?></h6>

            <div class="table-action">
                <div class="buttons">
                    <button class="btn btn--raised btn-primary btn--icon-text pegawai-action-add" data-toggle="modal" data-target="#modal-form-pegawai">
                        <i class="zmdi zmdi-plus-circle"></i> Buat Baru
                    </button>
                </div>
            </div>

            <?php include_once('form.php') ?>
            <?php include_once('form_Import.php') ?>

            <div class="table-responsive">
                <table id="table-pegawai" class="table table-bordered">
                    <thead class="thead-default">
                        <tr>
                            <th width="100">No</th>
                            <th>ID ABSEN</th>
                            <th>NAMA</th>
                            <th>DEPARTEMEN</th>
                            <th>PIN</th>
                            <th>JUMLAH ABSENSI</th>
                            <th width="170" class="text-center">#</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <br>
    <?php if($countNull>0) : ?>
    <div class="card">
        <div class = "card-body">
            <h4 class="card-title">Data Pegawai Tanpa Nama</h4>
            <div class="table-action">
                <div class="buttons">
                    <button class="btn btn--raised btn-success btn--icon-text pegawai-import-action-add" data-toggle="modal" data-target="#modal-form-import-pegawai">
                        <i class="zmdi zmdi-upload"></i> Import Data Pegawai
                    </button>
                </div>
                <small class="form-text text-muted">
                (<label required></label>) Tabel ini tidak akan muncul jika semua data pegawai lengkap.
                </small>
            </div>

            <div class="table-responsive">
                <table id="table-null-pegawai" class="table table-bordered">
                    <thead class="thead-default">
                        <tr>
                            <th width="100">No</th>
                            <th>ID ABSEN</th>
                            <th>Jumlah Absensi</th>
                            <th width="170" class="text-center">#</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <?php endif ?>
</section>