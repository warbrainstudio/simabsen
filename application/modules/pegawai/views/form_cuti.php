<div class="modal fade" id="modal-form-cuti" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title pull-left">
          Pengajuan Cuti
        </h5>
      </div>
      <div class="spinner">
        <div class="lds-hourglass"></div>
      </div>
      <div class="modal-body">
        <form id="form-cuti">
          <!-- CSRF -->
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
          
          <div class="row">
            <div class="col-xs-10 col-md-10">
                <h6 class="card-subtitle"><?php echo (isset($card_subTitle)) ? $card_subTitle : '' ?></h6>
                <div class="clear-card"></div>
            </div>
          </div>
          <div class="clear-card"></div>
          <div class="row">
            <div class="form-group">
              <label>Tanggal Pengajuan</label>
              <input type="date" name="tanggal_pengajuan" class="form-control cuti-tanggal_pengajuan" maxlength="100" value="<?php echo $date=date('Y-m-d') ?>" required/>
              <i class="form-group__bar"></i>
            </div>
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label required>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control pegawai-nama_lengkap" maxlength="100" placeholder="Nama Pegawai" value="<?= @$pegawai->nama_lengkap ?>" readonly/>
                <input type="hidden" name="absen_pegawai_id" value="<?= @$pegawai->absen_id ?>" />
                <i class="form-group__bar"></i>
              </div>
              <div class="form-group">
                <label required>NRP</label>
                <input type="text" name="nrp" class="form-control cuti-nrp" maxlength="100" placeholder="NRP" />
                <i class="form-group__bar"></i>
              </div>
              <div class="form-group">
                <label required>Unit Kerja</label>
                <input type="text" name="sub_unit" class="form-control cuti-dept" maxlength="100" placeholder="Unit Kerja" value="<?= @$pegawai->departemen ?>" readonly/>
                <i class="form-group__bar"></i>
              </div>
              <div class="form-group">
                <label required>Jabatan</label>
                <input type="text" name="jabatan" class="form-control cuti-jabatan" maxlength="100" placeholder="Jabatan"/>
                <i class="form-group__bar"></i>
              </div>
            </div>
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label required>Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control select2 cuti-jenis_kelamin" data-placeholder="Select &#8595;" required>
                    <option value="0" >Pria</option>
                    <option value="1" >Wanita</option>
                </select>
                <i class="form-group__bar"></i>
              </div>
              <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control cuti-alamat" rows="3" placeholder="Alamat"></textarea>
                <i class="form-group__bar"></i>
              </div>
              <div class="form-group">
                <label>No.Handphone</label>
                <input type="number" name="handphone" class="form-control cuti-handphone" maxlength="20" placeholder="No.Handphone" />
                <i class="form-group__bar"></i>
              </div>
              <div class="form-group">
                <label>Jumlah Persetujuan</label>
                <input type="number" name="jumlahpersetujuan" class="form-control cuti-jumlahper_setujuan" maxlength="20" placeholder="jumlah persetujuan" />
                <i class="form-group__bar"></i>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <label required>Dengan ini mengajukan permohonan : 'Pilih salah-satu' </label>
              <div class="form-control" style="height: 44.22px;">
                <div class="form-check form-check-inline">
                    <input class="form-check-input cuti-jenis_cuti-0" type="radio" name="jeniscuti" id="jenis_cuti-0" value="Cuti Tahunan">
                    <label class="form-check-label" for="jenis_cuti-0">Cuti Tahunan</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input cuti-jenis_cuti-1" type="radio" name="jeniscuti" id="jenis_cuti-1" value="Cuti Besar">
                    <label class="form-check-label" for="jenis_cuti-1">Cuti Besar</label> 
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input cuti-jenis_cuti-2" type="radio" name="jeniscuti" id="jenis_cuti-2" value="Cuti Melahirkan">
                    <label class="form-check-label" for="jenis_cuti-2">Cuti Melahirkan</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input cuti-jenis_cuti-3" type="radio" name="jeniscuti" id="jenis_cuti-3" value="Cuti Menikah">
                    <label class="form-check-label" for="jenis_cuti-3">Cuti Menikah</label>      
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input cuti-jenis_cuti-4" type="radio" name="jeniscuti" id="jenis_cuti-4">
                    <label class="form-check-label" for="jenis_cuti-4">Ijin
                    </label>  
                </div>
                <div class="form-check form-check-inline">
                    </label>
                    <input class="form-check-input cuti-jenis_cuti-5" type="radio" name="jeniscuti" id="jenis_cuti-5">
                    <label class="form-check-label" for="jenis_cuti-5">....... 
                    </label>      
                </div>                
              </div>
            </div>
          </div>
          <div class="cuti-pengajuan-cuti">
            <div class="row">
              <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Awal Cuti</label>
                    <input type="date" name="awalcuti" class="form-control cuti-awal_cuti" maxlength="100" value="<?php echo $date=date('Y-m-d') ?>" required/>
                    <i class="form-group__bar"></i>
                </div>
                <div class="form-group">
                    <label>Akhir Cuti</label>
                    <input type="date" name="akhircuti" class="form-control cuti-akhir_cuti" maxlength="100" value="<?php echo $date=date('Y-m-d') ?>" required/>
                    <i class="form-group__bar"></i>
                </div>
                <div class="form-group">
                    <label>Tanggal Bekerja</label>
                    <input type="date" name="tanggalbekerja" class="form-control cuti-tanggal_bekerja" maxlength="100" value="<?php echo $date=date('Y-m-d') ?>" required/>
                    <i class="form-group__bar"></i>
                </div>
              </div>
              <div class="col-xs-12 col-sm-6">
                <H6>Alamat dan Telepon yang bisa dihubungi saat cuti/izin : </H6>
                <div class="form-group">
                  <label>Alamat</label>
                  <textarea name="alamatCuti" class="form-control cuti-alamat_cuti" rows="3" placeholder="Alamat"></textarea>
                  <i class="form-group__bar"></i>
                </div>
                <div class="form-group">
                  <label>No.Handphone</label>
                  <input type="number" name="handphoneCuti" class="form-control cuti-telepon_cuti" maxlength="20" placeholder="No.Handphone" />
                  <i class="form-group__bar"></i>
                </div>
                <div class="form-group cuti-keterangan-cuti">
                  <label>Keterangan Cuti</label>
                  <input type="text" name="jeniscutiDetail" class="form-control cuti-jenis_cuti" maxlength="50" placeholder="Keterangan...." />
                  <i class="form-group__bar"></i>
                </div>
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
          <i class="zmdi zmdi-save"></i> Ajukan Cuti
        </button>
        <button type="button" class="btn btn-light btn--icon-text pegawai-action-cancel" data-dismiss="modal">
          Batal
        </button>
      </div>
    </div>
  </div>
</div>