<section id="unit">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"><?php echo (isset($card_title)) ? $card_title : '' ?></h4>
            <h6 class="card-subtitle"><?php echo (isset($card_subTitle)) ? $card_subTitle : '' ?></h6>
        <?php if($isnull=='true') : ?>
            <div class="col-xs-12 col-sm-9">
                <div class="row">
                    <div class="table-action">
                        <div class="buttons">
                            <button class="btn btn-sm btn-success" onclick="window.location.href='<?php echo site_url('unit'); ?>'">
                            <i class="zmdi zmdi-long-arrow-return"></i> Kembali
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane active fade show" id="tab-histori_attendance" role="tabpanel">
                <div class="pt-4">
                    <?php require_once(APPPATH . 'modules/unit/views/daftar_pegawai.php') ?>
                </div>
            </div>
        <?php else : ?>
            <?php if (!is_null(@$unit->updated_date) && !empty(@$unit->updated_date)) : ?>
                <div class="alert alert-light border p-3 mt-3 mb-2">
                    <i class="zmdi zmdi-info"></i>
                    Terakhir diubah pada <?= @$unit->updated_date ?>
                </div>
            <?php endif ?>

            <div class="col-xs-12 col-sm-9">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group form-group-auto">
                            <label>ID Unit</label>
                            <div class="form-control auto-filled-text-idunit"><?= @$unit->id ?>&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group form-group-auto">
                            <label>Nama Unit</label>
                            <div class="form-control auto-filled-text-departemen"><?= @$unit->nama_unit ?>&nbsp;</div>
                        </div>
                    </div>
                    <div class="table-action">
                        <div class="buttons">
                            <button class="btn btn-sm btn-success" onclick="window.location.href='<?php echo site_url('unit'); ?>'">
                            <i class="zmdi zmdi-long-arrow-return"></i> Kembali
                            </button> 
                        </div>
                    </div>
                    <br>
                </div>
            </div>
            <div class="tab-pane active fade show" id="tab-daftar_pegawai" role="tabpanel">
                <div class="pt-4">
                    <?php require_once(APPPATH . 'modules/unit/views/daftar_pegawai.php') ?>
                </div>
            </div>
        </div>
        <?php endif ?>
    </div>
</section>