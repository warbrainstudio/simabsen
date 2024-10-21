<div class="modal fade" id="modal-form-tarikdata-harian" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pull-left">Form Tarik data absen</h5>
            </div>
            <div class="spinner">
                <div class="lds-hourglass"></div>
            </div>
            <div class="modal-body">
                <form id="form-tarikdata-harian">
                    <!-- CSRF -->
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

                    <div class="row">
                        <div class="col-xs-10 col-md-10">
                            <h4 class="card-title"><?php echo (isset($card_title)) ? $card_title : '' ?></h4>
                            <h6 class="card-subtitle"><?php echo (isset($card_subTitle)) ? $card_subTitle : '' ?></h6>
                            <div class="clear-card"></div>
                        </div>
                    </div>
                    <div class="clear-card"></div>
                    <div class="row">
                        <div class="col-xs-10 col-md-16">
                            <div class="form-group">
                                <label required>Machine</label>
                                <select name="machine" class="form-control select2 machine" data-placeholder="Select &#8595;" required>
                                    <?= $list_mesin ?>
                                </select>
                                <i class="form-group__bar"></i>
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-10 col-md-16">
                            <div class="form-group">
                                <label required>Comm Key</label>
                                <input type="text" name="key" class="form-control key" placeholder="Comm Key" value="0" required />
                                <i class="form-group__bar"></i>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="start_date" class="form-control dashboard-start_date"/>
                    <input type="hidden" name="end_date" class="form-control dashboard-end_date"/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn--icon-text dashboard-action-fetch">
                <i class="zmdi zmdi-upload"></i> Fetch Data
                </button>
                <button type="button" class="btn btn-light btn--icon-text dashboard-action-cancel" data-dismiss="modal">
                Batal
                </button>
            </div>
        </div>
    </div>
</div>
