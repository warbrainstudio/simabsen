<script type="text/javascript">
  $(document).ready(function() {

    var _key = "<?= $key ?>";
    var _section = "cuti";
    var _table = "table-cuti";
    var _modal = "modal-form-cuti";
    var _form = "form-cuti";
    var _p_search = "<?= (isset($_GET['q'])) ? $_GET['q'] : '' ?>";
    var _is_load_partial = "<?= (isset($is_load_partial)) ? $is_load_partial : '0' ?>";
    var _is_first_load = (_key != null && _key != "") ? true : false;
    var _pegawai_id = "<?= @$pegawai_id ?>";
    var _pegawai_namaLengkap = "<?= @$pegawai_nama_lengkap ?>";
    var _pengajuan_cuti = document.querySelector("."+_section+"-pengajuan-cuti");
    var _keterangan_cuti = document.querySelector("."+_section+"-keterangan-cuti");
    _pengajuan_cuti.style.display = 'none';
    _keterangan_cuti.style.display = 'none';

    // Init on load
    initSelect2_enter(".cuti-absen_pegawai_id", "Cari dengan ID Absen / Nama Lengkap...", "<?= base_url('ref/ajax_search_pegawai') ?>", formatSelect2Result_pegawai);
    load_select2DefaultValue();

    if (_is_load_partial === '0' && $(`#${_table}`)[0]) {
      if ($.fn.DataTable.isDataTable(`#${_table}`) === false) {
        var table = $("#" + _table).DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "<?php echo base_url('cuti/ajax_get_all/') ?>",
            type: "get"
          },
          columns: [{
              data: null,
              render: function(data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
              }
            },
              {
                data: "tanggal_pengajuan",
                render: function(data, type, row, meta) {
                  return moment(data).format('DD-MM-YYYY');
                }
              },
              {
                data: "absen_pegawai_id"/*,
              render: function(data, type, row, meta) {
                if(row.nama=='-'){
                  return `<a href=" ${row.absen_pegawai_id}">${data}</a>`;
                }else{
                  return `<a href=" ${row.absen_pegawai_id}">${data}</a>`;
                }
              }*/
              },
              {
                data: "jenis_cuti"
              },
              {
                data: "awal_cuti",
                render: function(data, type, row, meta) {
                  return moment(data).format('DD-MM-YYYY');
                }
              },
              {
                data: "akhir_cuti",
                render: function(data, type, row, meta) {
                  return moment(data).format('DD-MM-YYYY');
                }
              },
              {
                data: "tanggal_bekerja",
                render: function(data, type, row, meta) {
                  return moment(data).format('DD-MM-YYYY');
                }
              },
              {
                data: "persetujuan_pertama",
                render: function(data, type, row, meta) {
                  let status;
                  let verifiedColor;
                  if (data === null) {
                    status = '-';
                    verifiedColor = 'secondary'; // Use 'secondary' for null status
                  } else if (data === 'Ditolak') {
                    status = 'x';
                    verifiedColor = 'danger';
                  } else {
                    status = '<i class="zmdi zmdi-check"></i>';
                    verifiedColor = 'success';
                  }
                  return `<a class="status_single_detail1"><span class="badge badge-${verifiedColor}">${status}</span></a>`;
                }
              },
              {
                data: "persetujuan_kedua",
                render: function(data, type, row, meta) {
                  let status;
                  let verifiedColor;
                  if (data === null) {
                    status = '-';
                    verifiedColor = 'secondary'; // Use 'secondary' for null status
                  } else if (data === 'Ditolak') {
                    status = 'x';
                    verifiedColor = 'danger';
                  } else {
                    status = '<i class="zmdi zmdi-check"></i>';
                    verifiedColor = 'success';
                  }
                  return `<a class="status_single_detail2"><span class="badge badge-${verifiedColor}">${status}</span></a>`;
                }
              },
              {
                data: "persetujuan_ketiga",
                render: function(data, type, row, meta) {
                  let status;
                  let verifiedColor;
                  if (data === null) {
                    status = '-';
                    verifiedColor = 'secondary'; // Use 'secondary' for null status
                  } else if (data === 'Ditolak') {
                    status = 'x';
                    verifiedColor = 'danger';
                  } else {
                    status = '<i class="zmdi zmdi-check"></i>';
                    verifiedColor = 'success';
                  }
                  return `<a class="status_single_detail3"><span class="badge badge-${verifiedColor}">${status}</span></a>`;
                }
              },
              {
                data: "status_persetujuan",
                render: function(data, type, row, meta) {
                  var status = (data === null) ? 'Menunggu persetujuan' : data;
                  var verifiedColor = (data === null) ? 'warning' : 'success';
                  return `<a class="status_detail"><span class="badge badge-${verifiedColor}">${status}</span></a>`;
                }
              },
            {
              data: null,
              render: function(data, type, row, meta) {
                var _jumlah_persetujuan = row.persetujuan_ketiga;
                if(_jumlah_persetujuan !== null){
                  return `
                      <div class="action" style="display: flex; flex-direction: row;">
                          <a href="javascript:;" class="btn btn-sm btn-danger action-delete" title="Hapus"><i class="zmdi zmdi-delete"></i> Hapus</a>
                      </div>
                  `;
                }else{
                  return `
                      <div class="action" style="display: flex; flex-direction: row;">
                        <a href="<?= base_url('cuti/detail?ref=') ?>${row.id}" modal-id="modal-view-cuti" class="btn btn-sm btn-success x-load-modal-partial" title="Rincian"><i class="zmdi zmdi-eye"></i></a>&nbsp;
                        <a href="<?= base_url('cuti/input?ref=') ?>${row.id}" modal-id="modal-form-cuti" class="btn btn-sm btn-light x-load-modal-partial" title="Ubah"><i class="zmdi zmdi-edit"></i> Ubah</a>&nbsp;
                        <a href="<?= base_url('cuti/delete?ref=') ?>${row.id}" class="btn btn-sm btn-danger action-delete" title="Hapus"><i class="zmdi zmdi-delete"></i> Hapus</a>
                      </div>
                  `;
                }
              }
            }
          ],
          order: [[1, 'asc']],
          autoWidth: !1,
          responsive: {
            details: {
              renderer: function(api, rowIdx, columns) {
                var hideColumn = [];
                var data = $.map(columns, function(col, i) {
                  return ($.inArray(col.columnIndex, hideColumn)) ?
                    '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                    '<td class="dt-details-td">' + col.title + ':' + '</td> ' +
                    '<td class="dt-details-td">' + col.data + '</td>' +
                    '</tr>' :
                    '';
                }).join('');

                return data ? $('<table/>').append(data) : false;
              },
              type: "inline",
              target: 'tr',
            }
          },
          columnDefs: [{
            className: 'desktop',
            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
          }, {
            className: 'tablet',
            targets: [0, 1, 2, 3]
          }, {
            className: 'mobile',
            targets: [0, 1]
          }, {
            responsivePriority: 2,
            targets: -1
          }],
          pageLength: 15,
          language: {
            searchPlaceholder: "Cari...",
            sProcessing: '<div style="text-align: center;"><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>'
          },
          sDom: '<"dataTables_ct"><"dataTables__top"fb>rt<"dataTables__bottom"ip><"clear">',
          buttons: [{
            extend: "excelHtml5",
            title: "Export Result"
          }, {
            extend: "print",
            title: "Export Result"
          }],
          oSearch: {
            sSearch: _p_search
          },
          initComplete: function(a, b) {
            $(this).closest(".dataTables_wrapper").find(".dataTables__top").prepend(
              '<div class="dataTables_buttons hidden-sm-down actions">' +
              '<span class="actions__item zmdi zmdi-refresh" data-table-action="reload" title="Reload" />' +
              '</div>'
            );
          },
        });

        $(".dataTables_filter input[type=search]").focus(function() {
          $(this).closest(".dataTables_filter").addClass("dataTables_filter--toggled")
        });

        $(".dataTables_filter input[type=search]").blur(function() {
          $(this).closest(".dataTables_filter").removeClass("dataTables_filter--toggled")
        });

        $("body").on("click", "[data-table-action]", function(a) {
          a.preventDefault();
          var b = $(this).data("table-action");
          if ("reload" === b) {
            $("#" + _table).DataTable().ajax.reload(null, false);
          };
        });
      };
    };

    $("#" + _section).on("click", "button." + _section + "-add", function(e) {
      e.preventDefault();
      resetForm();
      
    });

    $("#" + _table).on("click", "a.action-edit", function(e) {
      e.preventDefault();
      resetForm();
      var temp = table.row($(this).closest('tr')).data();

      // Set key for update params, important!
      _key = temp.id;

      $.each(temp, function(key, item) {
        $(`#${_form} .${_section}-${key}`).val(item).trigger("input").trigger("change");
      });
    });

    $("#" + _modal + " ." + _section + "-jenis_cuti-0").on("change", function(e) {
      var jatahCuti = '<?= $pegawai->jatah_cuti_tahunan ?>';
      var _pengajuan_cuti = document.querySelector("."+_section+"-pengajuan-cuti");
      var _keterangan_cuti = document.querySelector("."+_section+"-keterangan-cuti");
      if (jatahCuti=='' || jatahCuti=='0'){
        notify("Jatah cuti tahunan sudah habis. tidak bisa mengajukan cuti tahunan");
        _pengajuan_cuti.style.display = 'none';
        _keterangan_cuti.style.display = 'none';
      }else{
        notify("Sisa cuti tahunan : "+jatahCuti);
        _pengajuan_cuti.style.display = 'block';
        _keterangan_cuti.style.display = 'none';
      }
      _keterangan_cuti.style.display = 'none';
    });

    $("#" + _modal + " ." + _section + "-jenis_cuti-1").on("change", function(e) {
      var _pengajuan_cuti = document.querySelector("."+_section+"-pengajuan-cuti");
      var _keterangan_cuti = document.querySelector("."+_section+"-keterangan-cuti");
      _pengajuan_cuti.style.display = 'block';
      _keterangan_cuti.style.display = 'none';
    });

    $("#" + _modal + " ." + _section + "-jenis_cuti-2").on("change", function(e) {
      var _pengajuan_cuti = document.querySelector("."+_section+"-pengajuan-cuti");
      var _keterangan_cuti = document.querySelector("."+_section+"-keterangan-cuti");
      _pengajuan_cuti.style.display = 'block';
      _keterangan_cuti.style.display = 'none';
    });

    $("#" + _modal + " ." + _section + "-jenis_cuti-3").on("change", function(e) {
      var _pengajuan_cuti = document.querySelector("."+_section+"-pengajuan-cuti");
      var _keterangan_cuti = document.querySelector("."+_section+"-keterangan-cuti");
      var _cuti = document.querySelector("."+_section+"-jenis_cuti");
      _pengajuan_cuti.style.display = 'block';
      _keterangan_cuti.style.display = 'none';
      _cuti.value = "";
    });

    $("#" + _modal + " ." + _section + "-jenis_cuti-4").on("change", function(e) {
      var _pengajuan_cuti = document.querySelector("."+_section+"-pengajuan-cuti");
      var _keterangan_cuti = document.querySelector("."+_section+"-keterangan-cuti");
      _pengajuan_cuti.style.display = 'block';
      _keterangan_cuti.style.display = 'block';
    });

    $("#" + _modal + " ." + _section + "-jenis_cuti-5").on("change", function(e) {
      var _pengajuan_cuti = document.querySelector("."+_section+"-pengajuan-cuti");
      var _keterangan_cuti = document.querySelector("."+_section+"-keterangan-cuti");
      _pengajuan_cuti.style.display = 'block';
      _keterangan_cuti.style.display = 'block';
    });

    // Handle submit
    $(document).on("click", `#${_modal} .cuti-action-save`, function(e) {
            e.preventDefault();
            tinyMCE.triggerSave();
            if (table) {
                swal({
                    title: "Anda akan menyimpan data, lanjutkan?",
                    text: "Sebelum disimpan, pastikan data sudah benar.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: "Ya",
                    cancelButtonText: "Tidak",
                    closeOnConfirm: false
                }).then((result) => {
                    if (result.value) {
                        var formData = new FormData($(`#${_form}`)[0]);
                        $.ajax({
                            type: "post",
                            url: "<?php echo base_url('cuti/ajax_save/') ?>",
                            data: formData,
                            dataType: "json",
                            enctype: "multipart/form-data",
                            processData: false,
                            contentType: false,
                            cache: false,
                            success: function(response) {
                                if (response.status === true) {
                                    $(`#${_modal}`).modal("hide");
                                    table.ajax.reload(null, false);
                                    notify(response.data, "success");
                                } else {
                                    notify(response.data, "danger");
                                };
                            }
                        });
                    };
                });
            };
        });

    $("#" + _table).on("click", "a.action-aprove", function(e) {
      e.preventDefault();
      var temp = table.row($(this).closest('tr')).data();

      swal.fire({
        title: "Persetujuan "+temp.jenis_cuti+" "+temp.nama_lengkap+"?",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        closeOnConfirm: false
      }).then((result) => {
        if (result.value) {
          $.ajax({
            type: "post",
            url: "<?php echo base_url('cuti/ajax_approve/') ?>" + temp.id,
            data: { statuspersetujuan: 'Disetujui' },
            dataType: "json",
            success: function(response) {
              if (response.status) {
                table.ajax.reload(null, false);
                notify(response.data, "success");
              } else {
                notify(response.data, "danger");
              };
            }
          });
        };
      });
    });

    $("#" + _table).on("click", "a.status_single_detail1", function(e) {
      e.preventDefault();
      var temp = table.row($(this).closest('tr')).data();
      var p = temp.persetujuan_pertama;
      if(p===null){
        p = "menunggu persetujuan";
      }
      swal({
          title: "Status Pengajuan",
          text: "Pengajuan cuti "+p,
          icon: "info",
          buttons: {
              confirm: {
                  text: "OK",
                  value: true,
                  visible: true,
                  className: "",
                  closeModal: true,
              }
          }
      });
    });

    $("#" + _table).on("click", "a.status_single_detail2", function(e) {
      e.preventDefault();
      var temp = table.row($(this).closest('tr')).data();
      var p = temp.persetujuan_kedua;
      if(p===null){
        p = "menunggu persetujuan";
      }
      swal({
          title: "Status Pengajuan",
          text: "Pengajuan cuti "+p,
          icon: "info",
          buttons: {
              confirm: {
                  text: "OK",
                  value: true,
                  visible: true,
                  className: "",
                  closeModal: true,
              }
          }
      });
    });

    $("#" + _table).on("click", "a.status_single_detail3", function(e) {
      e.preventDefault();
      var temp = table.row($(this).closest('tr')).data();
      var p = temp.persetujuan_ketiga;
      if(p===null){
        p = "menunggu persetujuan";
      }
      swal({
          title: "Status Pengajuan",
          text: "Pengajuan cuti "+p,
          icon: "info",
          buttons: {
              confirm: {
                  text: "OK",
                  value: true,
                  visible: true,
                  className: "",
                  closeModal: true,
              }
          }
      });
    });

    $("#" + _table).on("click", "a.status_detail", function(e) {
      e.preventDefault();
      var temp = table.row($(this).closest('tr')).data();
      var p1 = temp.persetujuan_pertama;
      var p2 = temp.persetujuan_kedua;
      var p3 = temp.persetujuan_ketiga;
      var p = temp.jumlah_persetujuan;
      if(p1===null){
        p1 = "Menunggu proses";
      }
      if(p2===null){
        p2 = "Menunggu proses";
      }
      if(p3===null){
        p3 = "Menunggu proses";
      }
      swal({
          title: "Detail",
          text: "Persetujuan pertama "+p1
                +", Persetujuan kedua "+p2
                +", Persetujuan ketiga "+p3,
          icon: "info",
          buttons: {
              confirm: {
                  text: "OK",
                  value: true,
                  visible: true,
                  className: "",
                  closeModal: true,
              }
          }
      });
    });

    $("#" + _table).on("click", "a.action-delete", function(e) {
      e.preventDefault();
      var temp = table.row($(this).closest('tr')).data();

      swal({
        title: "Anda akan menghapus data lanjutkan?",
        text: "Setelah dihapus, data tidak dapat dikembalikan lagi!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        closeOnConfirm: false
      }).then((result) => {
        if (result.value) {
          $.ajax({
            type: "delete",
            url: "<?php echo base_url('cuti/ajax_delete/') ?>" + temp.id,
            dataType: "json",
            success: function(response) {
              if (response.status) {
                table.ajax.reload(null, false);
                notify(response.data, "success");
              } else {
                notify(response.data, "danger");
              };
            }
          });
        };
      });
    });

    $("#" + _section).on("click", "button." + _section + "-backButton", function(e) {
        window.history.back();
    });

    // Handle form reset
    resetForm = () => {
      _key = "";
      $(`#${_form}`).trigger("reset");
    };

    // Handle pegawai list format
    function formatSelect2Result_pegawai(item) {
      var $container = $(
        `<div class="select2-result-repository clearfix">
          <div class="select2-result-repository__title" style="font-weight: 600;"></div>
          <div class="select2-result-repository__description"></div>
        </div>`
      );

      $container.find(".select2-result-repository__title").text(item.text);
      $container.find(".select2-result-repository__description").html(item.absen_pegawai_id);

      return $container;
    };

        // Handle select2 default value for edit
    function load_select2DefaultValue() {
      setTimeout(() => {
        if (_is_first_load === true && (_pegawai_id != "" && _pegawai_namaLengkap != "")) {
            var optionPegawai = new Option(_pegawai_namaLengkap, _pegawai_id, true, true);
            $(".cuti-absen_pegawai_id").append(optionPegawai).trigger("change");
        };
      }, 300);
    };

  });
</script>