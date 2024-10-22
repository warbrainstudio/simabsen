<table id="table-absen-periode" class="table table-bordered">
    <thead class="thead-default">
        <tr>
            <th width="100">No</th>
            <th>ID Absen</th>
            <th>Nama Pegawai</th>
            <th>Departemen</th>
            <th>Nomor PIN</th>
            <?php if($isDaily=='true') : ?>
            <th>Jam</th>
            <?php else : ?>
            <th>Tanggal</th>
            <?php endif ?>
            <th>Status</th>
            <th>Verifikasi</th>
            <th>Mesin</th>
                        <!--<th width="170" class="text-center">Option</th>-->
        </tr>
    </thead>
</table>