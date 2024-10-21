<script type="text/javascript">
  $(document).ready(function() {

    var _key = "";
    var _section = "pegawai";
    var _section_cuti = "cuti";
    var _table = "table-pegawai";
    var _table_null = "table-null-pegawai";
    var _table_histori = "table-histori-attendance";
    var _modal = "modal-form-pegawai";
    var _modal_import = "modal-form-import-pegawai";
    var _form = "form-pegawai";
    var _form_import = "form-import-pegawai";

    // Initialize DataTables: Index
    if ($("#" + _table)[0]) {
      var table_pegawai = $("#" + _table).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "<?php echo base_url('pegawai/ajax_get_all/') ?>",
          type: "get"
        },
        columns: [{
            data: null,
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
            }
          },
          {
            data: "absen_pegawai_id",
          },
          {
            data: "nama_lengkap"
          },
          {
            data: "dept"
          },
          {
            data: "nopin"
          },
          {
            data: "datetime_count"
          },
          {
            data: null,
            render: function(data, type, row, meta) {
              return `
                  <div class="action" style="display: flex; flex-direction: row;">
                      <a href="<?= base_url('pegawai/detail?ref=') ?>${row.absen_pegawai_id}" class="btn btn-sm btn-success x-load-partial" title="Rincian"><i class="zmdi zmdi-eye"></i> Lihat</a>&nbsp;
                      <a href="javascript:;" class="btn btn-sm btn-light btn-table-action action-edit" data-toggle="modal" data-target="#${_modal}"><i class="zmdi zmdi-edit"></i> Ubah</a>&nbsp;
                      <a href="javascript:;" class="btn btn-sm btn-danger action-delete" title="Hapus"><i class="zmdi zmdi-delete"></i> Hapus</a>
                  </div>
              `;
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
          targets: [0, 1, 2, 3, 4, 5]
        }, {
          className: 'tablet',
          targets: [0, 1, 2, 5]
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

    // Handle data add
    $("#" + _section).on("click", "button." + _section + "-action-add", function(e) {
      e.preventDefault();
      resetForm();
    });

    // Handle data edit
    $("#" + _table).on("click", "a.action-edit", function(e) {
      e.preventDefault();
      resetForm();
      var temp = table_pegawai.row($(this).closest('tr')).data();
      _key = temp.id;
      $.each(temp, function(key, item) {
        $(`#${_form} .${_section}-${key}`).val(item).trigger("input").trigger("change");
      });
    });

    // Handle data submit
    $("#" + _modal + " ." + _section + "-action-save").on("click", function(e) {
      e.preventDefault();
      $.ajax({
        type: "post",
        url: "<?php echo base_url('pegawai/ajax_save/') ?>" + _key,
        data: $("#" + _form).serialize(),
        success: function(response) {
          var response = JSON.parse(response);
          if (response.status === true) {
            resetForm();
            $("#" + _modal).modal("hide");
            $("#" + _table).DataTable().order([1, 'desc']).draw();
            $("#" + _table_null).DataTable().ajax.reload(null, false);
            notify(response.data, "success");
          } else {
            notify(response.data, "danger");
          };
        }
      });
    });

    $("#" + _table).on("click", "a.action-delete", function(e) {
      e.preventDefault();
      var temp = table_pegawai.row($(this).closest('tr')).data();

      swal({
        title: "Anda akan menghapus data, lanjutkan?",
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
            url: "<?php echo base_url('pegawai/ajax_delete/') ?>" + temp.id,
            dataType: "json",
            success: function(response) {
              if (response.status) {
                $("#" + _table).DataTable().ajax.reload(null, false);
                $("#" + _table_null).DataTable().ajax.reload(null, false);
                notify(response.data, "success");
              } else {
                notify(response.data, "danger");
              };
            }
          });
        };
      });
    });

    if ($("#" + _table_null)[0]) {
      var table_null = $("#" + _table_null).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "<?php echo base_url('pegawai/ajax_get_null_all/') ?>",
          type: "get"
        },
        columns: [{
            data: null,
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
            }
          },
          {
            data: "absen_id",
          },
          {
            data: "datetime_count",
          },
          {
            data: null,
            render: function(data, type, row, meta) {
              return `
                  <div class="action" style="display: flex; flex-direction: row;">
                      <a href="<?= base_url('pegawai/detailnull?ref=') ?>${row.absen_id}" class="btn btn-sm btn-success x-load-partial" title="Rincian"><i class="zmdi zmdi-eye"></i> Lihat</a>&nbsp;
                      <a href="javascript:;" class="btn btn-sm btn-primary btn-table-action action-add-null" data-toggle="modal" data-target="#${_modal}"><i class="zmdi zmdi-plus"></i> Tambah</a>&nbsp;
                      <a href="javascript:;" class="btn btn-sm btn-danger action-delete-null" title="Hapus"><i class="zmdi zmdi-delete"></i> Hapus</a>
                  </div>
              `;
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
          targets: [0, 1, 2, 3]
        }, {
          className: 'tablet',
          targets: [0, 1, 2]
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
          $("#" + _table_null).DataTable().ajax.reload(null, false);
        };
      });
    };

    $("#" + _modal_import + " ." + _section + "-action-import").on("click", function(e) {
      e.preventDefault();
      var formData = new FormData($("#"+_form_import)[0]);

      $.ajax({
          type: "post",
          url: "<?php echo site_url('pegawai/ajax_import_data'); ?>",
          data: formData,
          processData: false,
          contentType: false,
          success: function(parsedResponse) {
              if (parsedResponse.status === true) {
                  resetForm();
                  $("#" + _modal_import).modal("hide");
                  //$("#" + _table).DataTable().ajax.reload(null, false);
                  var dataCount = parsedResponse.data.dataCount;
                  if(dataCount > 0){
                    var message = dataCount + " Data Pegawai telah berhasil dimasukan ke dalam database";
                    if (parsedResponse.data.existingRecordsCount > 0) {
                        message += " dan, " + parsedResponse.data.existingRecordsCount + " data pegawai sudah ada.";
                    }
                  }else{
                    var message = "Semua data pegawai dari file ini sudah ada.";
                  }
                  swal({
                      title: "Success",
                      text: message,
                      icon: "success",
                      button: "OK",
                  }).then(() => {
                    $("#" + _table).DataTable().order([1, 'desc']).draw();
                  });
              } else {
                  swal({
                      type: parsedResponse.notify,
                      text: parsedResponse.message,
                      button: "OK",
                  });
              }
          },
          error: function(xhr, status, error) {
              console.error("AJAX Error:", status, error);
              console.error("Response:", xhr.responseText);
              notify("An error occurred while fetching data.", "danger");
          }
      });
    });

    $("#" + _table_null).on("click", "a.action-add-null", function(e) {
      e.preventDefault();
      resetForm();
      var temp = table_null.row($(this).closest('tr')).data();
      $(`#${_form} .${_section}-absen_id`).val(temp.absen_id);
    });

    // Handle data delete
    $("#" + _table_null).on("click", "a.action-delete-null", function(e) {
      e.preventDefault();
      var temp = table_null.row($(this).closest('tr')).data();

      swal({
        title: "Anda akan menghapus data ID "+temp.absen_id+", lanjutkan?",
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
            url: "<?php echo base_url('absen/ajax_delete_pegawai/') ?>" + temp.absen_id,
            dataType: "json",
            success: function(response) {
              if (response.status) {
                $("#" + _table_null).DataTable().ajax.reload(null, false);
                notify(response.data, "success");
              } else {
                notify(response.data, "danger");
              };
            }
          });
        };
      });
    });

    if ($("#" + _table_histori)[0]) {
      var table_histori = $("#" + _table_histori).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "<?php echo base_url('absen/ajax_get_all/') ?>",
          type: "get",
            data: {
              filter: "<?= "AND absen_id='$pegawai_idfinger'" ?>",
            },
        },
        columns: [{
            data: null,
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
            }
          },
          {
              data: "tanggal_absen",
              render: function(data, type, row, meta) {
                return moment(data).format('DD-MM-YYYY');
              }
            },
            {
              data: "tanggal_absen",
              render: function(data, type, row, meta) {
                return moment(data).format('HH:mm:ss');
              }
            },
            {
              data: "nama_status",
              render: function(data, type, row, meta) {
                var statusColor = (data == 'Masuk') ? 'warning' : 'primary';
                return `<span class="badge badge-${statusColor}">${data}</span>`;
              }
            },
            {
              data: "verifikasi",
              render: function(data, type, row, meta) {
                var verifiedColor = (data == 'Finger') ? 'success' : 'danger';
                return `<span class="badge badge-${verifiedColor}">${data}</span>`;
              }
            },
            {
              data: "ipadress",
              render: function(data, type, row, meta) {
                return row.namamesin;
              }
            },
          {
            data: null,
            className: "center",
            defaultContent: '<div class="action">' +
              '<a href="javascript:;" class="btn btn-sm btn-danger btn-table-action action-delete-histori"><i class="zmdi zmdi-delete"></i> Hapus</a>' +
              '</div>'
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
          targets: [0, 1, 2, 3, 4, 5]
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
          $("#" + _table_histori).DataTable().ajax.reload(null, false);
        };
      });
    };

    $("#" + _table_histori).on("click", "a.action-delete-histori", function(e) {
      e.preventDefault();
      var temp = table_histori.row($(this).closest('tr')).data();

      swal({
        title: "Anda akan menghapus data Absen Tanggal : "+temp.tanggal_absen+", Status : "+temp.status+", lanjutkan?",
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
            url: "<?php echo base_url('absen/ajax_delete/') ?>" + temp.id,
            dataType: "json",
            success: function(response) {
              if (response.status) {
                $("#" + _table_histori).DataTable().ajax.reload(null, false);
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
      $(`#${_form_import}`).trigger("reset");
    };

  });
</script>