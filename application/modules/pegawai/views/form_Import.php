<div class="modal fade" id="modal-form-import-pegawai" data-backdrop="static" data-keyboard="false">
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
        <form id="form-import-pegawai" enctype="multipart/form-data">
          <!-- CSRF -->
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

          <div class="form-group">
            <label>Upload File Pegawai</label>
            <input type="file" name="file" class="form-control import-pegawai-file" required accept=".xls" required/>
            <i class="form-group__bar"></i>
            <br>
            <small class="form-text text-muted">
            Contoh kolom yang benar.
            </small>
            <img src="<?= base_url() ?>/directory/img/import_pegawai_example.png" style="max-width: 100%; height: auto;" alt="Import Pegawai Example">
            <br>
            <small class="form-text text-muted">
            (<label required></label>) Pastikan nama kolom berada di baris paling atas.
            </small>
            <small class="form-text text-muted">
            (<label required></label>) Jika file excel tidak terbaca, direkomendasikan untuk memindahkan semua data dari file excel yang lama ke file excel yang baru.
            </small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn--icon-text pegawai-action-import">
          <i class="zmdi zmdi-save"></i> Import
        </button>
        <button type="button" class="btn btn-light btn--icon-text pegawai-action-cancel" data-dismiss="modal">
          Batal
        </button>
      </div>
    </div>
  </div>
</div>