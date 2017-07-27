var table;

var validations = (function() {

    var init = function() {
        _applyValidation();
        _applyDatepicker();
    };

    var _applyDatepicker = function() {
        $('.datepicker').datepicker({
            weekStart: 1,
            todayHighlight: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    };

    var _applyValidation = function() {

        $('#frmData').formValidation({
            framework: "bootstrap",
            button: {
                selector: '#btnSubmit',
                disabled: 'disabled'
            },
            icon: null,
            fields: {
                status_karyawan_id: {
                    validators: {
                        notEmpty: {
                            message: 'Status Karyawan harus diisi'
                        }
                    }
                },
                nik: {
                    validators: {
                        notEmpty: {
                            message: 'NIK harus diisi'
                        }
                    }
                },
                nama: {
                    validators: {
                        notEmpty: {
                            message: 'Nama harus diisi'
                        }
                    }
                },
                alamat: {
                    validators: {
                        notEmpty: {
                            message: 'Alamat harus diisi'
                        }
                    }
                },
                phone: {
                    validators: {
                        notEmpty: {
                            message: 'Alamat harus diisi'
                        }
                    }
                },
                lulusan: {
                    validators: {
                        notEmpty: {
                            message: 'Lulusan harus diisi'
                        }
                    }
                },
                tgl_masuk: {
                    validators: {
                        notEmpty: {
                            message: 'Tanggal Masuk harus diisi'
                        }
                    }
                },
                nilai_upah: {
                    validators: {
                        notEmpty: {
                            message: 'Nilai Upah harus diisi'
                        }
                    }
                },
                uang_makan: {
                    validators: {
                        notEmpty: {
                            message: 'Alamat harus diisi'
                        }
                    }
                },
                uang_lembur: {
                    validators: {
                        notEmpty: {
                            message: 'Uang lembur harus diisi'
                        }
                    }
                },
                norek: {
                    validators: {
                        notEmpty: {
                            message: 'Nomor Rekening harus diisi'
                        }
                    }
                }
            }
        });

    };

    return {
        init: init
    };

})();

var confirmDelete = function(event, id, nama) {
    event.preventDefault();

    swal({
            title: "Apakah anda yakin?",
            text: "Data angkutan dengan nama " + nama + " akan dihapus!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, lanjutkan!",
            cancelButtonText: "Tidak, batalkan!",
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        },
        function() {
            $.ajax({
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader("X-CSRF-Token", $("meta[name='csrf-token']").attr("content"));
                    },
                    type: "POST",
                    data: {
                        _method: 'DELETE'
                    },
                    url: "/angkutan/" + id
                })
                .done(function(data) {
                    if (data === "success") {
                        // Redraw table
                        table.draw();
                        swal("", "Data berhasil dihapus.", "success");
                    } else {
                        swal("", data, "error");
                    }
                });
        });

};

var datatables = (function() {

    var datatablesURL = '/angkutan/list';

    var init = function() {
        _applyDatatable();
    };

    var _applyDatatable = function() {

        table = $('#list').DataTable({

            'processing': true,
            'serverSide': true,
            'paging': true,
            //'lengthChange': false,
            //'searching': false,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'ajax': {
                "url": datatablesURL,
                //"type": "POST"
            },
            'columns': [{
                data: 'nama',
                name: 'nama'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        });

    };

    return {
        init: init
    };

})();