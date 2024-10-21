<div class="modal fade" id="modal-form-tarikdata" data-backdrop="static" data-keyboard="false">
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
                <form id="form-tarikdata">
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
                    <div class="row">
                        <div class="col-xs-10 col-md-16">
                            <div class="form-group">
                                <label required>Start Date</label>
                                <input type="date" name="start_date" class="form-control start_date" value="<?php echo $date=date('Y-m-d') ?>" required/>
                                <i class="form-group__bar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-10 col-md-16">
                            <div class="form-group">
                                <label required>End Date</label>
                                <input type="date" name="end_date" class="form-control end_date" value="<?php echo $date=date('Y-m-d') ?>" required/>
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
                <button type="button" class="btn btn-success btn--icon-text tarikdata-action-fetch">
                <i class="zmdi zmdi-upload"></i> Fetch Data
                </button>
                <button type="button" class="btn btn-light btn--icon-text tarikdata-action-cancel" data-dismiss="modal">
                Batal
                </button>
            </div>
        </div>
    </div>
</div>
