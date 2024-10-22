<div class="modal fade" id="modal-view-pegawai" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pull-left">
                    <?= (isset($card_title)) ? $card_title : 'Rincian' ?>
                </h5>
            </div>
            <div class="spinner">
                <div class="lds-hourglass"></div>
            </div>
            <div class="modal-body">
                <h4 class="card-title"><?php echo (isset($card_title)) ? $card_title : '' ?></h4>
                <h6 class="card-subtitle"><?php echo (isset($card_subTitle)) ? $card_subTitle : '' ?></h6>
            <?php if($isnull=='true') : ?>
                <div class="col-xs-12 col-sm-9">
                    <div class="row">
                        <div class="table-action">
                            <div class="buttons">
                                <button class="btn btn-sm btn-dark pegawai-backButton">
                                <i class="zmdi zmdi-long-arrow-return"></i> Kembali
                                </button> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane active fade show" id="tab-histori_attendance" role="tabpanel">
                    <div class="pt-4">
                        <?php require_once(APPPATH . 'modules/pegawai/views/histori_attendance.php') ?>
                    </div>
                </div>
            <?php else : ?>
                <?php if (!is_null(@$pegawai->updated_date) && !empty(@$pegawai->updated_date)) : ?>
                    <div class="alert alert-light border p-3 mt-3 mb-2">
                        <i class="zmdi zmdi-info"></i>
                        Terakhir diubah pada <?= @$pegawai->updated_date ?>
                    </div>
                <?php endif ?>

                <div class="col-xs-12 col-sm-9">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group form-group-auto">
                                <label>Nama Lengkap</label>
                                <div class="form-control auto-filled-text-nama"><?= @$pegawai->nama_lengkap ?>&nbsp;</div>
                            </div>
                            <div class="form-group form-group-auto">
                                <label>Departemen</label>
                                <div class="form-control auto-filled-text-departemen">
                                    <?php if (!is_null(@$pegawai->departemen) && !empty(@$pegawai->departemen)) : ?>
                                        <?=@$pegawai->departemen?>
                                    <?php else : ?>
                                        -
                                    <?php endif?>&nbsp;</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group form-group-auto">
                                <label>Nomor PIN</label>
                                <div class="form-control auto-filled-text-nomorpin">
                                    <?php if (!is_null(@$pegawai->nomorpin) && !empty(@$pegawai->nomorpin)) : ?>
                                        <?=@$pegawai->nomorpin?>
                                    <?php else : ?>
                                        -
                                    <?php endif?>&nbsp;</div>
                            </div>
                            <div class="form-group form-group-auto">
                                <label>Jatah Cuti Tahunan</label>
                                <div class="form-control auto-filled-text-jatah_cuti_tahunan">
                                    <?php if (!is_null(@$pegawai->jatah_cuti_tahunan) && !empty(@$pegawai->jatah_cuti_tahunan)) : ?>
                                        <?=@$pegawai->jatah_cuti_tahunan?>
                                    <?php else : ?>
                                        0
                                    <?php endif?>&nbsp;</div>
                            </div>
                        </div>
                        <div class="table-action">
                            <div class="buttons">
                                <button class="btn btn-sm btn-success" onclick="window.location.href='<?= base_url('pegawai/excel/?ref=cxsmi&absen_pegawai_id='.@$pegawai->absen_pegawai_id) ?>'">
                                    <i class="zmdi zmdi-download"></i> Download Data (Excel)
                                </button>
                                <button class="btn btn-sm btn-dark pegawai-backButton">
                                <i class="zmdi zmdi-long-arrow-return"></i> Kembali
                                </button> 
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
                <table id="table-histori-attendance" class="table table-bordered">
                    <thead class="thead-default">
                    <tr>
                        <th width="100">No</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Status</th>
                        <th>Verifikasi</th>
                        <th>Mesin</th>
                        <th width="170" class="text-center">Option</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <?php endif ?>
        </div>
    </div>
</div>