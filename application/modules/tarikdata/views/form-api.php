<div class="modal fade" id="modal-form-tarikdata-api" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl">
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
                <form id="form-tarikdata-api">
                    <!-- CSRF -->
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

                    <div class="row">
                        <div class="col-xs-10 col-md-10">
                            <h4 class="card-title">Tarik Data API</h4>
                            <h6 class="card-subtitle"><?php echo (isset($card_subTitle)) ? $card_subTitle : '' ?></h6>
                            <div class="clear-card"></div>
                        </div>
                    </div>
                    <div class="clear-card"></div>
                    <div class="row">
                        <div class="form-group">
                            <label required>Token</label>
                            <input type="text" name="token" class="form-control token" placeholder="Token" required />
                            <i class="form-group__bar"></i>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label required>Host</label>
                                <input type="text" name="host" class="form-control host" placeholder="Host" required/>
                                <i class="form-group__bar"></i>
                            </div>
                            <div class="form-group">
                                <label required>Port</label>
                                <input type="text" name="port" class="form-control port" placeholder="Port" required/>
                                <i class="form-group__bar"></i>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label required>Username</label>
                                <input type="text" name="username" class="form-control username" placeholder="Username" required/>
                                <i class="form-group__bar"></i>
                            </div>
                            <div class="form-group">
                                <label required>Password</label>
                                <input type="password" name="password" class="form-control password" />
                                <i class="form-group__bar"></i>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label required>Database</label>
                                <input type="text" name="database" class="form-control database" placeholder="Database" required/>
                                <i class="form-group__bar"></i>
                            </div>     
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label required>Table</label>
                                <input type="text" name="table" class="form-control table" placeholder="Table" required/>
                                <i class="form-group__bar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label required>Tarik semua data?</label>
                            <div class="form-control" style="height: 44.22px;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input alldata-0" type="radio" name="alldata" id="alldata-0" value="true">
                                    <label class="form-check-label" for="alldata-1">Ya.</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input alldata-1" type="radio" name="alldata" id="alldata-1" value="false">
                                    <label class="form-check-label" for="alldata-0">Tidak.</label>     
                                </div>
                                <div class="form-check form-check-inline">
                                    (Catatan : Jika memilih "Tidak." dan tidak memilih tanggal, secara otomatis data yang ditarik adalah data absen kemarin.)
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label required>Machine</label>
                                <select name="ip" class="form-control select2 ip" data-placeholder="Select &#8595;" required>
                                    <?= $list_mesin_api ?>
                                </select>
                                <i class="form-group__bar"></i>
                            </div>
                            <div class="form-group date-group-start">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control start_date" value="<?php echo $date=date('Y-m-d') ?>"/>
                                <i class="form-group__bar"></i>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label required>Comm Key</label>
                                <input type="text" name="key" class="form-control key" placeholder="Comm Key" value="0" required />
                                <i class="form-group__bar"></i>
                            </div>
                            <div class="form-group date-group-end">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control end_date" value="<?php echo $date=date('Y-m-d') ?>"/>
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
                <button type="button" class="btn btn-success btn--icon-text tarikdata-action-fetch-api">
                <i class="zmdi zmdi-upload"></i> Fetch Data
                </button>
                <button type="button" class="btn btn-light btn--icon-text tarikdata-action-cancel" data-dismiss="modal">
                Batal
                </button>
            </div>
        </div>
    </div>
</div>
