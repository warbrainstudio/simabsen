<div class="modal fade" id="modal-form-cuti" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title pull-left">
          <?= (isset($card_title)) ? $card_title : 'Input' ?>
        </h5>
      </div>
      <div class="spinner">
        <div class="lds-hourglass"></div>
      </div>
      <div class="modal-body">
        <form id="form-cuti" autocomplete="off">
          <!-- CSRF -->
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
          <input type="hidden" name="ref" value="<?= $key ?>" readonly />

          <div class="row">
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label>Tanggal Pengajuan</label>
                <input type="date" name="tanggal_pengajuan" class="form-control cuti-tanggal_pengajuan" maxlength="100" value="<?= (@$cuti->tanggal_pengajuan == '') ? $date=date('Y-m-d') : @$cuti->tanggal_pengajuan ?>" required/>
                
              </div>
            </div>
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label required>Pegawai</label>
                  <div class="select">
                    <select name="absen_pegawai_id" class="form-control select2-partial cuti-absen_pegawai_id" required></select>
                  </div>
                  <input type="hidden" value="3" name="jumlahpersetujuan" class="form-control cuti-jumlah_persetujuan" maxlength="20" placeholder="jumlah persetujuan" />
              </div>
            </div>
          </div>
          <div class="row">
            <!--<div class="col-xs-12 col-sm-6">-->
              <div class="form-group">
                <label required>Dengan ini mengajukan permohonan : 'Pilih salah-satu' </label>
                <div class="form-control" style="height: 44.22px;">
                  <div class="form-check form-check-inline">
                      <input class="form-check-input cuti-jenis_cuti-0" type="radio" name="jeniscuti" id="jenis_cuti-0" value="Cuti Tahunan" <?= (@$cuti->jenis_cuti == 'Cuti Tahunan') ? 'checked' : '' ?>>
                      <label class="form-check-label" for="jenis_cuti-0">Cuti Tahunan</label>
                  </div>
                  <div class="form-check form-check-inline">
                      <input class="form-check-input cuti-jenis_cuti-1" type="radio" name="jeniscuti" id="jenis_cuti-1" value="Cuti Besar" <?= (@$cuti->jenis_cuti == 'Cuti Besar') ? 'checked' : '' ?>>
                      <label class="form-check-label" for="jenis_cuti-1">Cuti Besar</label> 
                  </div>
                  <div class="form-check form-check-inline">
                      <input class="form-check-input cuti-jenis_cuti-2" type="radio" name="jeniscuti" id="jenis_cuti-2" value="Cuti Melahirkan" <?= (@$cuti->jenis_cuti == 'Cuti Melahirkan') ? 'checked' : '' ?>>
                      <label class="form-check-label" for="jenis_cuti-2">Cuti Melahirkan</label>
                  </div>
                  <div class="form-check form-check-inline">
                      <input class="form-check-input cuti-jenis_cuti-3" type="radio" name="jeniscuti" id="jenis_cuti-3" value="Cuti Menikah" <?= (@$cuti->jenis_cuti == 'Cuti Menikah') ? 'checked' : '' ?>>
                      <label class="form-check-label" for="jenis_cuti-3">Cuti Menikah</label>      
                  </div>
                  <div class="form-check form-check-inline">
                      <input class="form-check-input cuti-jenis_cuti-4" type="radio" name="jeniscuti" id="jenis_cuti-4">
                      <label class="form-check-label" for="jenis_cuti-4">Ijin .......</label>  
                  </div>
                  <div class="form-check form-check-inline">
                      <input class="form-check-input cuti-jenis_cuti-5" type="radio" name="jeniscuti" id="jenis_cuti-5">
                      <label class="form-check-label" for="jenis_cuti-5">(Lain-lain)</label>      
                  </div>                
                </div>
              </div>
              <div class="form-group cuti-keterangan-cuti">
                <label>Keterangan Cuti</label>
                <input type="text" name="jeniscutiDetail" class="form-control cuti-jenis_cuti" maxlength="50" placeholder="Keterangan...." value="<?= @$cuti->jenis_cuti ?>" />
                <i class="form-group__bar"></i>
              </div>
            <!--</div>-->
          </div>
          <div class="cuti-pengajuan-cuti">
            <div class="row">
              <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Awal Cuti</label>
                    <input type="date" name="awalcuti" class="form-control cuti-awal_cuti" maxlength="100" value="<?= (@$cuti->awal_cuti == '') ? '' : @$cuti->awal_cuti ?>" required/>
                    <i class="form-group__bar"></i>
                </div>
              </div>
              <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Akhir Cuti</label>
                    <input type="date" name="akhircuti" class="form-control cuti-akhir_cuti" maxlength="100" value="<?= (@$cuti->akhir_cuti == '') ? '' : @$cuti->akhir_cuti ?>" required/>
                    <i class="form-group__bar"></i>
                </div>
              </div>
                <div class="form-group">
                    <label>Tanggal Bekerja</label>
                    <input type="date" name="tanggalbekerja" class="form-control cuti-tanggal_bekerja" maxlength="100" value="<?= (@$cuti->tanggal_bekerja == '') ? '' : @$cuti->tanggal_bekerja ?>" required/>
                    <i class="form-group__bar"></i>
                </div>
                <H6>Alamat dan Telepon yang bisa dihubungi saat cuti/izin : </H6>
                <div class="form-group">
                  <label>Alamat</label>
                  <textarea name="alamatCuti" class="form-control cuti-alamat_cuti" rows="3" placeholder="Alamat"><?= @$cuti->alamat_cuti ?></textarea>
                  <i class="form-group__bar"></i>
                </div>
                <div class="form-group">
                  <label>Telepon</label>
                  <input type="number" name="teleponCuti" class="form-control cuti-telepon_cuti" maxlength="20" placeholder="No.Handphone" value="<?= @$cuti->telepon_cuti ?>" />
                  <i class="form-group__bar"></i>
                </div>
            </div>
          </div>
          <small class="form-text text-muted">
            Fields with red stars (<label required></label>) are required.
          </small>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn--icon-text cuti-action-save">
          <i class="zmdi zmdi-save"></i> Simpan
        </button>
        <button type="button" class="btn btn-light btn--icon-text cuti-action-cancel" data-dismiss="modal">
          Batal
        </button>
      </div>
    </div>
  </div>
</div>