<script type="text/javascript">
  $(document).ready(function() {

    var _key = "";
    var _section = "tarikdata";
    var _table = "table-tarikdata";
    var _modal = "modal-form-tarikdata";
    var _modal_api = "modal-form-tarikdata-api";
    var _form = "form-tarikdata";
    var _form_api = "form-tarikdata-api";
    var token = document.querySelector('.token');
    /*var dateGroupStart = document.querySelector('.form-group.date-group-start');
    var dateGroupEnd = document.querySelector('.form-group.date-group-end');
    dateGroupStart.style.display = 'none';
    dateGroupEnd.style.display = 'none';

    $("#" + _form_api + "-alldata-1").change(function(e) {
      if (this.checked) {
        dateGroupStart.style.display = 'none';
        dateGroupEnd.style.display = 'none';
      } else {
          dateGroupStart.style.display = 'block';
          dateGroupEnd.style.display = 'block';
      }
    });*/

    // Initialize DataTables: Index
    if ($("#" + _table)[0]) {
      var table_tarikdata = $("#" + _table).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "<?php echo base_url('tarikdata/ajax_get_all/') ?>",
          type: "get"
        },
        columns: [{
            data: null,
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
            }
          },
          {
            data: "host"
          },
          {
            data: "namamesin"
          },
          {
            data: "jumlahdata"
          },
          {
            data: "existsdata"
          },
          {
            data: "tanggaldata"
          },
          {
            data: "created_date",
            render: function(data, type, row, meta) {
              return moment(data).format('DD-MM-YYYY H:mm:ss');
            }
          },
          {
            data: null,
            className: "center",
            defaultContent: '<div class="action">' +
              '<a href="javascript:;" class="btn btn-sm btn-danger btn-table-action action-delete"><i class="zmdi zmdi-delete"></i> Hapus</a>' +
              '</div>'
          }
        ],
        order: [[6, 'desc']],
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
          targets: [0, 1, 2, 3, 4, 5, 6]
        }, {
          className: 'tablet',
          targets: [0, 1, 2, 3]
        }, {
          className: 'mobile',
          targets: [0, 2]
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

    // Handle data submit
    $("#" + _modal + " ." + _section + "-action-fetch").on("click", function(e) {
        e.preventDefault();
        const startTime = performance.now();
        $.ajax({
            type: "get",
            url: "<?php echo site_url('tarikdata/ajax_fetch_data'); ?>" + _key,
            data: $("#" + _form).serialize(),
            dataType: "json", // Expect JSON response
            success: function(parsedResponse) {
                const endTime = performance.now();
                const duration = ((endTime - startTime) / 1000).toFixed(1);
                if (parsedResponse.status === true) {
                    resetForm();
                    $("#" + _modal).modal("hide");
                    $("#" + _table).DataTable().ajax.reload(null, false);
                    
                    var message = "Tarik " + parsedResponse.data.dataCount + " Data" +
                                  " Dari Mesin dengan IP " + parsedResponse.data.datamesin + 
                                  " Sukses.";
                    
                    if (parsedResponse.data.existingRecordsCount > 0) {
                        message += " Namun " + parsedResponse.data.existingRecordsCount + 
                                  " data sudah ada.";
                    }

                    message +=" Waktu Proses : " + duration + " detik.";

                    swal({
                        title: "Success",
                        text: message,
                        icon: "success",
                        button: "OK",
                    });
                } else {
                    swal({
                        title: "Error",
                        text: parsedResponse.message,
                        icon: "error",
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

    // Handle data submit
    $("#" + _modal_api + " ." + _section + "-action-fetch-api").on("click", function(e) {
        e.preventDefault();
        const startTime = performance.now();
        $.ajax({
            type: "get",
            url: "<?php echo site_url('api/fetchData'); ?>" + _key,
            data: $("#" + _form_api).serialize(),
            dataType: "json", 
            success: function(parsedResponse) {
                const endTime = performance.now();
                const duration = ((endTime - startTime) / 1000).toFixed(1);
                if (parsedResponse.status === true) {
                    resetForm();
                    $("#" + _modal_api).modal("hide");
                    $("#" + _table).DataTable().ajax.reload(null, false);
                    
                    var message = "host " + parsedResponse.data.arrayDB['host'] +
                                  " dengan port " + parsedResponse.data.arrayDB['port'] +
                                  " berhasil menarik " + parsedResponse.data.dataCount + " Data" +
                                  " ke dalam database " + parsedResponse.data.arrayDB['database'] +
                                  " dengan table " + parsedResponse.data.arrayDB['table'] +
                                  " Dari Mesin dengan IP " + parsedResponse.data.arrayDB['ip'] + 
                                  " Sukses.";
                    
                    if (parsedResponse.data.existingRecordsCount > 0) {
                        message += " Namun " + parsedResponse.data.existingRecordsCount + 
                                  " data sudah ada.";
                    }

                    message +=" Waktu Proses : " + duration + " detik.";

                    swal({
                        title: "Success",
                        text: message,
                        icon: "success",
                        button: "OK",
                    });
                } else {
                    swal({
                        title: "Error",
                        text: parsedResponse.message,
                        icon: "error",
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

    // Handle data delete
    $("#" + _table).on("click", "a.action-delete", function(e) {
      e.preventDefault();
      var temp = table_tarikdata.row($(this).closest('tr')).data();

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
            url: "<?php echo base_url('tarikdata/ajax_delete/') ?>" + temp.id,
            dataType: "json",
            success: function(response) {
              if (response.status) {
                $("#" + _table).DataTable().ajax.reload(null, false);
                notify(response.data, "success");
              } else {
                notify(response.data, "danger");
              };
            }
          });
        };
      });
    });

    // Handle form reset
    resetForm = () => {
      _key = "";
      $(`#${_form}`).trigger("reset");
      $(`#${_form_api}`).trigger("reset");
    };

  });
</script>