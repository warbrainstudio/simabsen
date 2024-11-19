<section id="tarikdata">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"><?php echo (isset($card_title)) ? $card_title : '' ?></h4>
            <h6 class="card-subtitle"><?php echo (isset($card_subTitle)) ? $card_subTitle : '' ?></h6>
            
            <div class="table-action">
                <div class="buttons">
                    <button class="btn btn--raised btn-primary btn--icon-text tarikdata-action-add" data-toggle="modal" data-target="#modal-form-tarikdata">
                        <i class="zmdi zmdi-cloud-download"></i> Tarik Data Manual
                    </button>
                    <!--<button class="btn btn--raised btn-primary btn--icon-text tarikdata-api-action-add" data-toggle="modal" data-target="#modal-form-tarikdata-api">
                        <i class="zmdi zmdi-cloud-download"></i> Tarik Data API
                    </button>-->
                </div>
            </div>
            <?php include_once('form.php') ?>
            <?php include_once('form-api.php') ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="table-tarikdata" class="table table-bordered">
                    <thead class="thead-default">
                        <tr>
                            <th width="100">No</th>
                            <th>Host</th>
                            <th>Mesin</th>
                            <th>Jumlah Data</th>
                            <th>Data Sama</th>
                            <th>Tanggal Data</th>
                            <th>Tanggal Tarik</th>
                            <th width="170" class="text-center">#</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>