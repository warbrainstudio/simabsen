<div class="modal fade" id="modal-view-cuti" data-backdrop="static" data-keyboard="false">
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
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label>Tanggal Pengajuan</label>
                            <div class="form-control"><?= @$cuti->tanggal_pengajuan ?>&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label>Pegawai</label>
                            <div class="form-control"><?= @$cuti->absen_pegawai_id . ' / ' . @$cuti->nama_lengkap ?>&nbsp;</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <div class="form-group">
                            <label>Keterangan</label>
                            <div class="form-control"><?= @$cuti->jenis_cuti ?>&nbsp;</div>
                        </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label>Dimulai dari</label>
                            <div class="form-control"><?= @$cuti->awal_cuti ?>&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label>Sampai dengan</label>
                            <div class="form-control"><?= @$cuti->akhir_cuti ?>&nbsp;</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label>Bekerja kembali</label>
                            <div class="form-control"><?= @$cuti->tanggal_bekerja ?>&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label>No. Telepon</label>
                            <div class="form-control"><?= @$cuti->telepon_cuti ?>&nbsp;</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <div class="form-group">
                            <label>Alamat saat cuti</label>
                            <div class="form-control"><?= @$cuti->alamat_cuti ?>&nbsp;</div>
                        </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group">
                            <label>Persetujuan Pertama</label>
                            <div class="form-control"><?= (@$cuti->persetujuan_pertama == '') ? 'Menunggu persetujuan' : @$cuti->persetujuan_pertama ?>&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group">
                            <label>Persetujuan Kedua</label>
                            <div class="form-control"><?= (@$cuti->persetujuan_kedua == '') ? 'Menunggu persetujuan' : @$cuti->persetujuan_kedua ?>&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group">
                            <label>Persetujuan Ketiga</label>
                            <div class="form-control"><?= (@$cuti->persetujuan_ketiga == '') ? 'Menunggu persetujuan' : @$cuti->persetujuan_ketiga ?>&nbsp;</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <div class="form-group">
                            <label>Status Persetujuan</label>
                            <div class="form-control"><?= (@$cuti->status_persetujuan == '') ? '-Menunggu semua persetujuan-' : @$cuti->status_persetujuan ?>&nbsp;</div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if (@$cuti->status_persetujuan != '') : ?>
                    <button type="button" class="btn btn-warning btn--icon-text skspk-action-download">
                        <i class="zmdi zmdi-download"></i> Cetak Form Cuti
                    </button>
                <?php endif ?>
                    <button type="button" class="btn btn-light btn--icon-text skspk-action-cancel" data-dismiss="modal">
                        Tutup
                    </button>
            </div>
        </div>
    </div>
</div>