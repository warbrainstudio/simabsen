<script type="text/javascript">
  $(document).ready(function() {

    var _key = "<?= $key ?>";
    var _section = "dashboard";
    var _form = "form-tarikdata-periode";
    var _modal = "modal-form-tarikdata-periode";
    var _table = "table-absen-periode";
    const calendar = ".calendar";
    const days = document.querySelectorAll(calendar + ' .day');

    if ($("#" + _table)[0]) {
      var daily = "<?= $isDaily ?>";
      var table = $("#" + _table).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "<?php echo base_url('absen/ajax_get_all/') ?>",
          type: "get",
            data: {
              filter: "<?= $searchFilter ?>",
            },
        },
        columns: [{
            data: null,
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
            }
          },
            {
              data: "absen_id",
              render: function(data, type, row, meta) {
                if(row.nama=='-'){
                  return `<a href="<?= base_url('pegawai/detailnull?ref=') ?>${row.absen_id}" modal-id="modal-view-pegawai" class="x-load-modal-partial">${data}</a>`;
                }else{
                  return `<a href="<?= base_url('pegawai/detail?ref=') ?>${row.absen_id}" modal-id="modal-view-pegawai" class="x-load-modal-partial">${data}</a>`;
                }
              }
            },
            {
              data: "nama"
            },
            {
              data: "dept"
            },
            {
              data: "nopin"
            },
            {
              data: "tanggal_absen",
              render: function(data, type, row, meta) {
                if(daily=='true'){
                  return moment(data).format('HH:mm:ss');
                }else{
                  return moment(data).format('DD-MM-YYYY HH:mm:ss');
                }
              }
            },
            {
              data: "nama_status",
              render: function(data, type, row, meta) {
                var statusColor = (data == 'Masuk') ? 'warning' : (data == 'Pulang') ? 'primary' : 'danger';
                return `<span class="badge badge-${statusColor}">${data}</span>`;
              }
            },
            {
              data: "verifikasi",
              render: function(data, type, row, meta) {
                var verifiedColor = (data == 'Finger') ? 'success' : (data == 'Input') ? 'danger' : 'secondary';
                return `<span class="badge badge-${verifiedColor}">${data}</span>`;
              }
            },
            {
              data: "ipadress",
              render: function(data, type, row, meta) {
                return row.namamesin;
              }
            }/*,
          {
            data: null,
            className: "center",
            defaultContent: '<div class="action">' +
              '<a href="javascript:;" class="btn btn-sm btn-danger btn-table-action action-delete-histori"><i class="zmdi zmdi-delete"></i> Hapus</a>' +
              '</div>'
          }*/
        ],
        order: [[5, 'asc']],
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
          targets: [0, 1, 2, 3, 4, 5, 6, 7, 8]
        }, {
          className: 'tablet',
          targets: [0, 1, 2, 3, 4, 5, 6]
        }, {
          className: 'mobile',
          targets: [0, 1, 2, 3, 4]
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

    // Adding mouseover and mouseout event listeners to each day
    days.forEach(day => {
        day.addEventListener('mouseover', function() {
            this.style.backgroundColor = '#e0e0e0'; 
        });
        day.addEventListener('mouseout', function() {
            this.style.backgroundColor = ''; 
        });
    });

    $("#" + _section).on("click", "button." + _section + "-export", function(e) {
      swal({
        title: "Download Data",
        text: "Unduh data absen tanggal "+_key+" ?",
        type: "info",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        closeOnConfirm: false
      }).then((result) => {
          if (result.value) {
            var downloadUrl = "<?= base_url('absen/excel?ref=cxsmi&date=') ?>" + _key;
            window.location.href = downloadUrl;
          }
      });
    });

    $(`${calendar} .header_month .month_name .month_content`).on("click", function(e) {
        const currentUrl = window.location.pathname;
        const regex = /(\d{4})\/(\d{2})$/;
        const now = new Date();
        let date = '';
        const match = currentUrl.match(regex);
        if (match) {
            const year = match[1];
            const month = match[2];
            date = `${year}-${String(month).padStart(2, '0')}`;
        } else {
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            date = `${year}-${month}`;
        }

        var detailUrl = "<?php echo base_url('dashboard/detail?date=') ?>" + date;
        window.location.href = detailUrl;
    });


    // Click event for no content days
    $(`${calendar} .day .no_content_fill_day`).on("click", function(e) {
        e.preventDefault();
        const currentUrl = window.location.pathname;
        const regex = /(\d{4})\/(\d{2})$/;
        const match = currentUrl.match(regex);
        const now = new Date();
        let year = "";
        let month = "";

        if (match) {
            year = match[1];
            month = match[2];
        } else {
            year = now.getFullYear();
            month = String(now.getMonth() + 1).padStart(2, '0');
        }

        const day = $(this).text().padStart(2, '0');
        const clickedDate = new Date(year, month - 1, day);
        const limit = new Date('2023-07-03');

        if (clickedDate < now && clickedDate > limit) {
            const dateShow = `${day}-${String(month).padStart(2, '0')}-${year}`;
            const tanggal = `${year}-${String(month).padStart(2, '0')}-${day}`;
            
            $(`#${_form} .${_section}-start_date`).val(tanggal);
            $(`#${_form} .${_section}-end_date`).val(tanggal);
            
            swal({
                title: "Tarik Data",
                text: `Data tanggal ${dateShow} belum ada. Ingin tarik data?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                closeOnConfirm: false
            }).then((result) => {
              if (result.value) {
                  fetchData(tanggal);
              }
            });
        }
    });


    $(`${calendar} .today .no_content_fill_today`).on("click", function(e) {
        e.preventDefault();
        resetForm();
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = $(this).find('strong').text().padStart(2, '0');
        const tanggal = `${year}-${month}-${day}`;
        $(`#${_form} .${_section}-start_date`).val(tanggal);
        $(`#${_form} .${_section}-end_date`).val(tanggal);
        swal({
                title: "Tarik Data",
                text: "Data hari ini belum ada. Ingin tarik data?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                closeOnConfirm: false
            }).then((result) => {
                if (result.value) {
                  fetchData(tanggal);
                }
            });
        
    });

    $("#" + _section).on("click", "button." + _section + "-action-add", function(e) {
      e.preventDefault();
      resetForm();
      const currentUrl = window.location.href;
      const url = new URL(currentUrl);
      const tanggal = url.searchParams.get("date");
      swal({
                title: "Update Data Absen tanggal "+tanggal+"?",
                text: "",
                type: "warning",
                showCancelButton: true,
                buttons: {
                    cancel: {
                        text: "Tidak",
                        value: null,
                        visible: true,
                        closeModal: true,
                    },
                    confirm: {
                        text: "Ya",
                        value: true,
                        visible: true,
                        closeModal: true,
                    }
                }
            }).then((result) => {
                if (result.value) {
                  fetchData(tanggal);
                }
            });
    });

    $("#" + _modal + " ." + _section + "-action-fetch").on("click", function(e) {
        e.preventDefault();
        const startTime = performance.now();
        $.ajax({
            type: "get",
            url: "<?php echo site_url('tarikdata/ajax_fetch_data'); ?>",
            data: $("#" + _form).serialize(),
            dataType: "json", // Expect JSON response
            success: function(parsedResponse) {
                const endTime = performance.now();
                const duration = ((endTime - startTime) / 1000).toFixed(1);
                if (parsedResponse.status === true) {
                    resetForm();
                    $("#" + _modal).modal("hide");
                    $("#" + _table).DataTable().ajax.reload(null, false);
                    //$("#" + calendar).DataTable().ajax.reload(null, false);

                    var message = "Tarik " + parsedResponse.data.dataCount + " Data" +
                                  " Dari Mesin dengan IP " + parsedResponse.data.datamesin + 
                                  " Sukses.";

                    if(parsedResponse.data.dataCount = parsedResponse.data.existingRecordsCount) {
                        message = "Tidak ada data absen baru dari mesin dengan IP " + parsedResponse.data.datamesin +
                        " . Tidak perlu menarik data lagi untuk sekarang";
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
    
    $(".img-home").each(function() {
      $(this)[0].oncontextmenu = function() {
        return false;
      };
    });

    function fetchData(tanggal) {
        const startTime = performance.now();

        swal.fire({
            title: "Loading...",
            text: "Sedang menarik data, harap tunggu.",
            icon: "info",
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                swal.showLoading();
            }
        });

        $.ajax({
            type: "get",
            url: "<?php echo site_url('dashboard/ajax_fetch_data'); ?>",
            data: { tanggal: tanggal },
            dataType: "json",
            success: function(parsedResponse) {
                const endTime = performance.now();
                const duration = ((endTime - startTime) / 1000).toFixed(1);
                swal.close();

                if (parsedResponse.status === true) {

                    var message = parsedResponse.dataCount+" Data tanggal "+tanggal+" Sukses ditarik.";

                    if (parsedResponse.dataCount != parsedResponse.existRecord) {
                        message += " Namun " + parsedResponse.existRecord + 
                                  " data sudah ada.";
                    }

                    if(parsedResponse.dataCount == parsedResponse.existRecord) {
                        message = "Jumlah data yang ditarik : "+parsedResponse.dataCount+", Jumlah data yang sudah ada : "+parsedResponse.existRecord+
                        ". Tidak ada data absen baru. Tidak perlu menarik data lagi untuk sekarang.";
                    }

                    if(parsedResponse.dataCount==0){
                      message = "Tidak ada data absen di tanggal "+tanggal+".";
                    }

                    message += " Waktu Proses: "+duration+" detik.";
                    
                    swal({
                        title: "Success",
                        text: message,
                        icon: "success",
                        button: "OK",
                    }).then(() => {
                        location.reload(); // Reload after showing the success message
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
    }

    $("#" + _section).on("click", "button." + _section + "-backButton", function(e) {
        window.history.back();
    });

    resetForm = () => {
      _key = "";
      $(`#${_form}`).trigger("reset");
    };

  });
</script>