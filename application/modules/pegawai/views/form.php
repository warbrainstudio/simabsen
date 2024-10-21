<div class="modal fade" id="modal-form-pegawai" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title pull-left">
          <?= (isset($card_title)) ? $card_title : 'Form' ?>
        </h5>
      </div>
      <div class="spinner">
        <div class="lds-hourglass"></div>
      </div>
      <div class="modal-body">
        <form id="form-pegawai" autocomplete="off">
          <!-- CSRF -->
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

          <div class="form-group">
            <label required>User ID</label>
            <input type="text" name="absen_pegawai_id" class="form-control pegawai-absen_pegawai_id" maxlength="255" placeholder="User ID" required/>
            <i class="form-group__bar"></i>
          </div>
          <div class="form-group">
            <label required>Nama Pegawai</label>
            <input type="text" name="nama_lengkap" class="form-control pegawai-nama_lengkap" maxlength="30" placeholder="Nama" required />
            <i class="form-group__bar"></i>
          </div>
          <div class="form-group">
            <label required>Departemen</label>
              <select name="dept" class="form-control select2 pegawai-dept" data-placeholder="Select &#8595;" required>
                <?= $list_unit ?>
              </select>
            <i class="form-group__bar"></i>
          </div>
          <div class="form-group">
            <label>Nomor PIN</label>
            <input type="text" name="nopin" class="form-control pegawai-nopin" maxlength="100" placeholder="Nomor PIN" />
            <i class="form-group__bar"></i>
          </div>

          <small class="form-text text-muted">
            Fields with red stars (<label required></label>) are required.
          </small>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn--icon-text pegawai-action-save">
          <i class="zmdi zmdi-save"></i> Simpan
        </button>
        <button type="button" class="btn btn-light btn--icon-text pegawai-action-cancel" data-dismiss="modal">
          Batal
        </button>
      </div>
    </div>
  </div>
</div>