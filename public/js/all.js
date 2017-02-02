var commonModule = (function() {

    var datatableBaseURL = 'http://adm.trimitrakemasindo.com/datatable/';

    var select2BaseURL = '/select2/';

    /* Auto Log Out Setelah Sekian Detik */
    var autoLogOut = function() {
        var delay = 1000; // 10 menit (10 * 60 detik * 1000 untuk miliseconds)
        window.setTimeout(function() {
            window.location.href = '/logout';
        }, delay);
    };

    return {
        autoLogOut: autoLogOut,
        datatableBaseURL: datatableBaseURL,
        select2BaseURL: select2BaseURL
    };

    autoLogOut();

})();
var karyawanModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'karyawans';

    var existing_model = null;

    var init = function() {
        _applyDatatable();
        _applyDatepicker();
        _applyAutoNumeric();


    };

    var _applyAutoNumeric = function() {
        $("#uang_makan").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#uang_makan"));
            });
        $("#nilai_upah").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#nilai_upah"));
            });
        $("#uang_lembur").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#uang_lembur"));
            });

    };


    var _applyThousandSeperator = function() {
        $("input.number").each(function() {
            var input_name = $(this).data('input');

            $(this).autoNumeric('init', {
                aSep: ',',
                aDec: '.',
                aSign: 'Rp ',
                mDec: '0'
            });

            $(this).on('change keyup', function() {
                var value = $(this).val().replace('Rp ', '');
                $("input[name='" + input_name + "']").val(value);
            });
        });
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


    var _applyDatatable = function() {
        /* Tambah Input Field di TFOOT */
        $('#datatable tfoot th').each(function() {
            var title = $(this).text();
            if (title != '') {
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
            if (title == 'Created Date' || title == 'Updated Date') {
                $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": datatableBaseURL,
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'status_karyawan_id',
                name: 'status_karyawans.keterangan'
            }, {
                data: 'nik',
                name: 'karyawans.nik'
            }, {
                data: 'nama',
                name: 'karyawans.nama'
            }, {
                data: 'alamat',
                name: 'karyawans.alamat'
            }, {
                data: 'phone',
                name: 'karyawans.phone'
            }, {
                data: 'lulusan',
                name: 'karyawans.lulusan'
            }, {
                data: 'tgl_masuk',
                name: 'karyawans.tgl_masuk'
            }, {
                data: 'nilai_upah',
                name: 'karyawans.nilai_upah'
            }, {
                data: 'uang_makan',
                name: 'karyawans.uang_makan'
            }, {
                data: 'tunjangan',
                name: 'karyawans.tunjangan'
            }, {
                data: 'pot_koperasi',
                name: 'karyawans.pot_koperasi'
            }, {
                data: 'pot_bpjs',
                name: 'karyawans.pot_bpjs'
            }, {
                data: 'norek',
                name: 'karyawans.norek'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        });

        /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
        table.columns().every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change', function() {

                var keyword = this.value;

                if (this.placeholder == 'Search Model' || this.placeholder == 'Search Brand') {
                    keyword = keyword.toUpperCase();
                }

                if (that.search() !== keyword) {
                    that
                        .search(keyword)
                        .draw();
                }
            });
        });

    };

    var confirmDelete = function(event, id) {

        event.preventDefault();

        swal({
                title: "Apakah anda yakin?",
                text: "Data Karyawan akan dihapus!",
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
                        url: "/karyawan-tetap/" + id
                    })
                    .done(function(data) {
                        if (data === "success") {
                            // Redraw table
                            $('#datatable').DataTable().draw();
                            swal("", "Data berhasil dihapus.", "success");
                        } else {
                            swal("", data, "error");
                        }
                    });
            });

    };

    var showPrint = function(id) {

        $.ajax({
            method: "GET",
            url: "/karyawan-tetap/" + id,
            dataType: "json",
        }).done(function(response) {

            if (response.status == 1) {

                /* Clear Modal Body */
                $('#print_modal').find(".modal-title").html("");
                $('#print_modal').find(".modal-body").html("");

                /* Insert Data to Modal Body */
                $('#print_modal').find(".modal-body").append('<table class="table table-bordered table-striped"><thead><tr><th>NIK</th><th>Nama</th><th>Status</th><th>Norek</th></thead><tbody>');

                $.each(response.records, function(i, record) {
                    $('#print_modal').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id + "' />" + record.nik + "</td><td>" + record.nama + "</td><td>" + record.keterangan + "</td><td>" + record.norek + "</td></tr>");

                });

                $('#print_modal').find(".modal-body").append("</table>");


                /* Finally show */
                $('#print_modal').modal();
            } else {
                alert('Data Pegawai salah');
            }

        }).fail(function(response) {

        });
    };

    return {
        init: init,
        showPrint: showPrint,
        confirmDelete: confirmDelete,
    };

})(commonModule);
var karyawanHarianModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'karyawan-harians';

    var existing_model = null;

    var init = function() {
        _applyDatatable();
        _applyDatepicker();
        _applyAutoNumeric();
    };



    var confirmDelete = function(event, id) {

        event.preventDefault();

        swal({
                title: "Apakah anda yakin?",
                text: "Data Karyawan akan dihapus!",
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
                        url: "/karyawan-harian/" + id
                    })
                    .done(function(data) {
                        if (data === "success") {
                            // Redraw table
                            $('#datatable').DataTable().draw();
                            swal("", "Data berhasil dihapus.", "success");
                        } else {
                            swal("", data, "error");
                        }
                    });
            });

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

    var _applyAutoNumeric = function() {
        $("#uang_makan").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#uang_makan"));
            });
        $("#nilai_upah").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#nilai_upah"));
            });
        $("#uang_lembur").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#uang_lembur"));
            });

    };

    var _applyDatatable = function() {
        /* Tambah Input Field di TFOOT */
        $('#datatable tfoot th').each(function() {
            var title = $(this).text();
            if (title != '') {
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
            if (title == 'Created Date' || title == 'Updated Date') {
                $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": datatableBaseURL,
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'status_karyawan_id',
                name: 'status_karyawans.keterangan'
            }, {
                data: 'nik',
                name: 'karyawan_harians.nik'
            }, {
                data: 'nama',
                name: 'karyawan_harians.nama'
            }, {
                data: 'alamat',
                name: 'karyawan_harians.alamat'
            }, {
                data: 'phone',
                name: 'karyawan_harians.phone'
            }, {
                data: 'lulusan',
                name: 'karyawan_harians.lulusan'
            }, {
                data: 'tgl_masuk',
                name: 'karyawan_harians.tgl_masuk'
            }, {
                data: 'nilai_upah',
                name: 'karyawan_harians.nilai_upah'
            }, {
                data: 'uang_makan',
                name: 'karyawans.uang_makan'
            }, {
                data: 'tunjangan',
                name: 'karyawans.tunjangan'
            }, {
                data: 'pot_koperasi',
                name: 'karyawans.pot_koperasi'
            }, {
                data: 'pot_bpjs',
                name: 'karyawans.pot_bpjs'
            }, {
                data: 'norek',
                name: 'karyawans.norek'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        });

        /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
        table.columns().every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change', function() {

                var keyword = this.value;

                if (this.placeholder == 'Search Model' || this.placeholder == 'Search Brand') {
                    keyword = keyword.toUpperCase();
                }

                if (that.search() !== keyword) {
                    that
                        .search(keyword)
                        .draw();
                }
            });
        });

    };
    var showPrint = function(id) {

        $.ajax({
            method: "GET",
            url: "/karyawan-harian/" + id,
            dataType: "json",
        }).done(function(response) {

            if (response.status == 1) {

                /* Clear Modal Body */
                $('#print_modal').find(".modal-title").html("");
                $('#print_modal').find(".modal-body").html("");

                /* Insert Data to Modal Body */
                $('#print_modal').find(".modal-body").append('<table class="table table-bordered table-striped"><thead><tr><th>NIK</th><th>Nama</th><th>Status</th><th>Norek</th></thead><tbody>');

                $.each(response.records, function(i, record) {
                    $('#print_modal').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id + "' />" + record.nik + "</td><td>" + record.nama + "</td><td>" + record.keterangan + "</td><td>" + record.norek + "</td></tr>");

                });

                $('#print_modal').find(".modal-body").append("</table>");


                /* Finally show */
                $('#print_modal').modal();
            } else {
                alert('Data Pegawai salah');
            }

        }).fail(function(response) {

        });
    };

    return {
        init: init,
        showPrint: showPrint,
        confirmDelete: confirmDelete,
    };

})(commonModule);
var karyawanStaffModule = (function(commonModule) {
    var datatableBaseURL = commonModule.datatableBaseURL + 'karyawan-staff';

    var existing_model = null;

    var init = function() {
        _applyDatatable();
        _applyDatepicker();
        _applyAutoNumeric();
    };



    var confirmDelete = function(event, id) {

        event.preventDefault();

        swal({
                title: "Apakah anda yakin?",
                text: "Data Karyawan dengan ID " + id + " akan dihapus!",
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
                        url: "/karyawan-staff/" + id
                    })
                    .done(function(data) {
                        if (data === "success") {
                            // Redraw table
                            $('#datatable').DataTable().draw();
                            swal("", "Data berhasil dihapus.", "success");
                        } else {
                            swal("", data, "error");
                        }
                    });
            });

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

    var _applyAutoNumeric = function() {
        $("#uang_makan").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#uang_makan"));
            });
        $("#nilai_upah").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#nilai_upah"));
            });
        $("#uang_lembur").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#uang_lembur"));
            });

    };

    var _applyDatatable = function() {
        /* Tambah Input Field di TFOOT */
        $('#datatable tfoot th').each(function() {
            var title = $(this).text();
            if (title != '') {
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
            if (title == 'Created Date' || title == 'Updated Date') {
                $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": datatableBaseURL,
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'status_karyawan_id',
                name: 'status_karyawans.keterangan'
            }, {
                data: 'nik',
                name: 'karyawan_harians.nik'
            }, {
                data: 'nama',
                name: 'karyawan_harians.nama'
            }, {
                data: 'alamat',
                name: 'karyawan_harians.alamat'
            }, {
                data: 'phone',
                name: 'karyawan_harians.phone'
            }, {
                data: 'lulusan',
                name: 'karyawan_harians.lulusan'
            }, {
                data: 'tgl_masuk',
                name: 'karyawan_harians.tgl_masuk'
            }, {
                data: 'nilai_upah',
                name: 'karyawan_harians.nilai_upah'
            }, {
                data: 'uang_makan',
                name: 'karyawans.uang_makan'
            }, {
                data: 'tunjangan',
                name: 'karyawans.tunjangan'
            }, {
                data: 'pot_koperasi',
                name: 'karyawans.pot_koperasi'
            }, {
                data: 'pot_bpjs',
                name: 'karyawans.pot_bpjs'
            }, {
                data: 'norek',
                name: 'karyawans.norek'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        });

        /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
        table.columns().every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change', function() {

                var keyword = this.value;

                if (this.placeholder == 'Search Model' || this.placeholder == 'Search Brand') {
                    keyword = keyword.toUpperCase();
                }

                if (that.search() !== keyword) {
                    that
                        .search(keyword)
                        .draw();
                }
            });
        });

    };
    var showPrint = function(id) {

        $.ajax({
            method: "GET",
            url: "/karyawan-staff/" + id,
            dataType: "json",
        }).done(function(response) {

            if (response.status == 1) {

                /* Clear Modal Body */
                $('#print_modal').find(".modal-title").html("");
                $('#print_modal').find(".modal-body").html("");

                /* Insert Data to Modal Body */
                $('#print_modal').find(".modal-body").append('<table class="table table-bordered table-striped"><thead><tr><th>NIK</th><th>Nama</th><th>Status</th><th>Norek</th></thead><tbody>');

                $.each(response.records, function(i, record) {
                    $('#print_modal').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id + "' />" + record.nik + "</td><td>" + record.nama + "</td><td>" + record.keterangan + "</td><td>" + record.norek + "</td></tr>");

                });

                $('#print_modal').find(".modal-body").append("</table>");


                /* Finally show */
                $('#print_modal').modal();
            } else {
                alert('Data Pegawai salah');
            }

        }).fail(function(response) {

        });
    };

    return {
        init: init,
        showPrint: showPrint,
        confirmDelete: confirmDelete
    };

})(commonModule);
var absensiHarianModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'absensi-harians';

    var existing_model = null;

    var init = function() {
        _applyDatatable();
        _applyDatepicker();
        _applyThousandSeperator();

    };

    var _applyAutoNumeric = function() {
        $("#harga").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#harga"));
            });
    };

    var _applyValidation = function() {
        alert('hello');
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

    var _applyThousandSeperator = function() {
        $("input.number").each(function() {
            var input_name = $(this).data('input');

            $(this).autoNumeric('init', {
                aSep: ',',
                aDec: '.',
                aSign: 'Rp ',
                mDec: '0'
            });

            $(this).on('change keyup', function() {
                var value = $(this).val().replace('Rp ', '');
                $("input[name='" + input_name + "']").val(value);
            });
        });
    };

    var _applyDatepicker = function() {
        $('.datepicker').datepicker({
            weekStart: 1,
            todayHighlight: false,
            clearBtn: true,
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    };


    var _applyDatatable = function() {
        /* Tambah Input Field di TFOOT */
        $('#datatable tfoot th').each(function() {
            var title = $(this).text();
            if (title != '') {
                $(this).html('<input type="text" class="form-control" placeholder="' + title + '" style="width: 100%;" />');
            }
            if (title == 'Created Date' || title == 'Updated Date' || title == 'Tanggal') {
                $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": datatableBaseURL,
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'tanggal',
                name: 'absensi_harians.tanggal'
            }, {
                data: 'nama',
                name: 'karyawans.nama'
            }, {
                data: 'jam_kerja',
                name: 'absensi_harians.jam_kerja'
            }, {
                data: 'jam_masuk',
                name: 'absensi_harians.jam_masuk'
            }, {
                data: 'jam_pulang',
                name: 'absensi_harians.jam_pulang'
            }, {
                data: 'scan_masuk',
                name: 'absensi_harians.scan_masuk'
            }, {
                data: 'scan_pulang',
                name: 'absensi_harians.scan_pulang'
            }, {
                data: 'terlambat',
                name: 'absensi_harians.terlambat'
            }, {
                data: 'plg_cepat',
                name: 'absensi_harians.plg_cepat'
            }, {
                data: 'jenis_lembur',
                name: 'absensi_harians.jenis_lembur'
            }, {
                data: 'jam_lembur',
                name: 'absensi_harians.jam_lembur'
            }, {
                data: 'konfirmasi_lembur',
                name: 'absensi_harians.konfirmasi_lembur'
            }, {
                data: 'jml_kehadiran',
                name: 'absensi_harians.jml_kehadiran'
            }, {
                data: 'status',
                name: 'absensi_harians.status'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        });

        /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
        table.columns().every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change', function() {

                var keyword = this.value;

                if (this.placeholder == 'Search Model' || this.placeholder == 'Search Brand') {
                    keyword = keyword.toUpperCase();
                }

                if (that.search() !== keyword) {
                    that
                        .search(keyword)
                        .draw();
                }
            });
        });

    };

    var showDetail = function(id) {

        $.ajax({
            method: "GET",
            url: "/upload-absen/" + id,
            dataType: "json",
        }).done(function(response) {

            if (response.status == 1) {

                /* Clear Modal Body */
                $('#detail_modal').find(".modal-title").html("");
                $('#detail_modal').find(".modal-body").html("");

                /* Insert Data to Modal Body */




                $('#detail_modal').find(".modal-body").append('<table class="table table-bordered table-striped"><thead><tr><th>NIK</th><th>Nama</th><th>Departemen</th><th>Scan Masuk</th><th>Scan Keluar</th><th>Lembur</th></tr></thead><tbody>');

                $.each(response.records, function(i, record) {
                    $('#detail_modal').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id + "' /><input name ='tanggal' type='hidden' value='" + record.tanggal + "' />" + record.id + "</td><td>" + record.nama + "</td><td>" + record.departemen + "</td><td>" + record.scan_masuk + "</td><td>" + record.scan_pulang + "</td><td>" + record.jam_lembur + "</td></tr><tr><td colspan='5' align='right'>Jenis Lembur</td><td> <select name='jenis_lembur' class='form-control selectpicker' title='-- Pilih Jenis Lembur --'><option value='0'>Tidak Ada</option><option value='1'>Rutin</option><option value='2'>Biasa</option><option value='3'>Off</option></select></td></tr><tr><td colspan='5' align='right'>Jam Lembur</td><td><input name ='lembur' type='text' /></td></tr>");

                });

                $('#detail_modal').find(".modal-body").append("</table>");


                /* Finally show */
                $('#detail_modal').modal();
            } else {
                alert('Data Pegawai belum ada');
            }

        }).fail(function(response) {

        });
    };

    var showPrint = function(id) {

        $.ajax({
            method: "GET",
            url: "/absensi-harian/" + id,
            dataType: "json",
        }).done(function(response) {

            if (response.status == 1) {

                /* Clear Modal Body */
                $('#print_modal').find(".modal-title").html("");
                $('#print_modal').find(".modal-body").html("");

                /* Insert Data to Modal Body */
                $('#print_modal').find(".modal-body").append('<table class="table table-bordered table-striped"><thead><tr><th>NIK</th><th>Nama</th><th>Departemen</th></thead><tbody>');

                $.each(response.records, function(i, record) {
                    $('#print_modal').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id + "' /><input name ='tanggal' type='hidden' value='" + record.tanggal + "' />" + record.id + "</td><td>" + record.nama + "</td><td>" + record.departemen + "</td></tr>");

                });

                $('#print_modal').find(".modal-body").append("</table>");


                /* Finally show */
                $('#print_modal').modal();
            } else {
                alert('Data Pegawai belum ada');
            }

        }).fail(function(response) {

        });
    };

    return {
        init: init,
        showDetail: showDetail,
        showPrint: showPrint,
    };

})(commonModule);
var absensiApprovalModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'absensi-approvals';

    var existing_model = null;

    var init = function() {
        _applyDatatable();
        _applyDatepicker();
        _applyThousandSeperator();

    };

    var _applyAutoNumeric = function() {
        $("#harga").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#harga"));
            });
    };

    var _applyValidation = function() {
        alert('hello');
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

    var _applyThousandSeperator = function() {
        $("input.number").each(function() {
            var input_name = $(this).data('input');

            $(this).autoNumeric('init', {
                aSep: ',',
                aDec: '.',
                aSign: 'Rp ',
                mDec: '0'
            });

            $(this).on('change keyup', function() {
                var value = $(this).val().replace('Rp ', '');
                $("input[name='" + input_name + "']").val(value);
            });
        });
    };

    var _applyDatepicker = function() {
        $('.datepicker').datepicker({
            weekStart: 1,
            todayHighlight: false,
            clearBtn: true,
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    };


    var _applyDatatable = function() {
        /* Tambah Input Field di TFOOT */
        $('#datatable tfoot th').each(function() {
            var title = $(this).text();
            if (title != '') {
                $(this).html('<input type="text" class="form-control" placeholder="' + title + '" style="width: 100%;" />');
            }
            if (title == 'Created Date' || title == 'Updated Date' || title == 'Tanggal') {
                $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": datatableBaseURL,
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'check',
                name: 'check',
                orderable: false,
                searchable: false
            }, {
                data: 'tanggal',
                name: 'absensi_harians.tanggal'
            }, {
                data: 'nama',
                name: 'karyawans.nama'
            }, {
                data: 'jam_kerja',
                name: 'absensi_harians.jam_kerja'
            }, {
                data: 'jam_masuk',
                name: 'absensi_harians.jam_masuk'
            }, {
                data: 'jam_pulang',
                name: 'absensi_harians.jam_pulang'
            }, {
                data: 'scan_masuk',
                name: 'absensi_harians.scan_masuk'
            }, {
                data: 'scan_pulang',
                name: 'absensi_harians.scan_pulang'
            }, {
                data: 'terlambat',
                name: 'absensi_harians.terlambat'
            }, {
                data: 'plg_cepat',
                name: 'absensi_harians.plg_cepat'
            }, {

                data: 'jenis_lembur',
                name: 'absensi_harians.jenis_lembur'
            }, {
                data: 'jam_lembur',
                name: 'absensi_harians.jam_lembur'
            }, {
                data: 'konfirmasi_lembur',
                name: 'absensi_harians.konfirmasi_lembur'
            }, {
                data: 'jml_kehadiran',
                name: 'absensi_harians.jml_kehadiran'
            }, {
                data: 'pot_absensi',
                name: 'absensi_harians.pot_absensi'
            }, {
                data: 'status',
                name: 'absensi_harians.status'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        });

        /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
        table.columns().every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change', function() {

                var keyword = this.value;

                if (this.placeholder == 'Search Model' || this.placeholder == 'Search Brand') {
                    keyword = keyword.toUpperCase();
                }

                if (that.search() !== keyword) {
                    that
                        .search(keyword)
                        .draw();
                }
            });
        });

    };

    var showDetail = function(id) {

        $.ajax({
            method: "GET",
            url: "/upload-absen/" + id,
            dataType: "json",
        }).done(function(response) {

            if (response.status == 1) {




                /* Clear Modal Body */
                $('#detail_modal').find(".modal-title").html("");
                $('#detail_modal').find(".modal-body").html("");

                /* Insert Data to Modal Body */
                $('#detail_modal').find(".modal-body").append('<table class="table table-bordered table-striped"><thead><tr><th>NIK</th><th>Nama</th><th>Departemen</th><th>Scan Masuk</th><th>Scans Keluar</th><th>Jam Lembur</th><th>Konfirmasi Lembur</th><th>Jenis Lembur</th></tr></thead><tbody>');

                $.each(response.records, function(i, record) {

                    var jenis_lembur = 0;

                    if (record.jenis_lembur == 1) {

                        jenis_lembur = 'lembur rutin';

                    } else if (record.jenis_lembur == 2) {
                        jenis_lembur = 'lembur biasa';
                    }

                    $('#detail_modal').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id + "' /><input name ='tanggal' type='hidden' value='" + record.tanggal + "' /><input name ='jenis_lembur' type='hidden' value='" + record.jenis_lembur + "' /><input name ='konfirmasi_lembur' type='hidden' value='" + record.konfirmasi_lembur + "' />" + record.nik + "</td><td>" + record.nama + "</td><td>" + record.departemen + "</td><td>" + record.scan_masuk + "</td><td>" + record.scan_pulang + "</td><td>" + record.jam_lembur + "</td><td>" + record.konfirmasi_lembur + "</td><td>" + jenis_lembur + "</td></tr>");

                });

                $('#detail_modal').find(".modal-body").append("</table>");



                /* Clear Modal Body */
                $('#detail_modal2').find(".modal-body2").html("");


                /* Insert Data to Modal Body */
                $('#detail_modal2').find(".modal-body2").append('<table class="table table-bordered table-striped"><thead><tr><th>Upah</th><th>Uang Makan</th><th>Pot. Koperasi</th><th>Pot. BPJS</th><th>Tunjangan</th></tr></thead><tbody>');

                $.each(response.records, function(i, record) {
                    $('#detail_modal2').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id + "' /><input name ='tanggal' type='hidden' value='" + record.tanggal + "' /><input name ='uang_makan' type='hidden' value='" + record.uang_makan + "' />" + record.nilai_upah + "</td><td>" + record.uang_makan + "</td><td>" + record.pot_koperasi + "</td><td>" + record.pot_bpjs + "</td><td>" + record.tunjangan + "</td></tr>");

                });

                $('#detail_modal2').find(".modal-body2").append("</table>");


                /* Finally show */
                $('#detail_modal').modal();




            } else {
                alert('Data Pegawai belum ada');
            }

        }).fail(function(response) {

        });
    };

    var confirmLembur = function() {
        var query = $('form#approve input[name="selected_karyawans[]"]').serialize();
        window.location = '/absensi-approval/confirmAbsen?' + query;



    };

    return {
        init: init,
        showDetail: showDetail,
        confirmLembur: confirmLembur,
    };

})(commonModule);
var absensiPackingModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'absensi-packings';

    var existing_model = null;

    var init = function() {
        $("#overlayForm").hide();
        _applyDatatable();
        _applyDatepicker();
        _applyThousandSeperator();

    };

    var _applyAutoNumeric = function() {
        $("#harga").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#harga"));
            });
    };

    var _applyValidation = function() {
        alert('hello');
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

    var createAbsensi = function() {
        /* Show Overlay */
        $("#overlayForm").show();

        /* Send Data */
        _createAbsensi(commonModule.maxRetries, commonModule.retryInterval);
    };

    var _createAbsensi = function(maxRetries, retryInterval) {
        axios.post("/absensi-packing", $('#main_form').serialize())
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success"

                    }, function() {
                        /* Reset and Focus */
                        $('form#main_form')[0].reset();

                        $("form#main_form:not(.filter) :input:visible:enabled:first").focus();
                    });
                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error"

                    });
                }
                /* Hide Overlay */
                $("#overlayForm").hide();
            })
            .catch(function(error) {
                switch (error.response.status) {
                    case 422:
                        swal({
                            title: "Oops!",
                            text: 'Failed form validation. Please check your input.',
                            type: "error"
                        });
                        break;
                    case 500:
                        swal({
                            title: "Oops!",
                            text: 'Something went wrong.',
                            type: "error"
                        });
                        break;
                }

                // Try again if we haven't reached maxRetries yet
                retryInterval = Math.min(commonModule.maxRetryInterval, retryInterval * commonModule.exponentMultiplication);
                if (maxRetries > 0) {
                    setTimeout(function() {
                        _createReduction(maxRetries - 1, retryInterval);
                    }, retryInterval);
                } else {
                    swal({
                        title: "Oops!",
                        text: "Please check your network connection.",
                        timer: 500,
                        type: "error"
                    });
                    /* Hide Overlay */
                    $("#overlayForm").hide();
                }
            });
    };


    var _applyThousandSeperator = function() {
        $("input.number").each(function() {
            var input_name = $(this).data('input');

            $(this).autoNumeric('init', {
                aSep: ',',
                aDec: '.',
                aSign: 'Rp ',
                mDec: '0'
            });

            $(this).on('change keyup', function() {
                var value = $(this).val().replace('Rp ', '');
                $("input[name='" + input_name + "']").val(value);
            });
        });
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


    var _applyDatatable = function() {
        /* Tambah Input Field di TFOOT */
        $('#datatable tfoot th').each(function() {
            var title = $(this).text();
            if (title != '') {
                $(this).html('<input type="text" class="form-control" placeholder="' + title + '" style="width: 100%;" />');
            }
            if (title == 'Created Date' || title == 'Updated Date' || title == 'Tanggal') {
                $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": datatableBaseURL,
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'tanggal',
                name: 'absensi_packings.tanggal'
            }, {
                data: 'bagian',
                name: 'absensi_packings.bagian'
            }, {
                data: 'jenis',
                name: 'absensi_packings.jenis'
            }, {
                data: 'jumlah',
                name: 'absensi_packings.jumlah'

            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        });

        /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
        table.columns().every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change', function() {

                var keyword = this.value;

                if (this.placeholder == 'Search Model' || this.placeholder == 'Search Brand') {
                    keyword = keyword.toUpperCase();
                }

                if (that.search() !== keyword) {
                    that
                        .search(keyword)
                        .draw();
                }
            });
        });

    };

    var showDetail = function(id) {

        $.ajax({
            method: "GET",
            url: "/upload-absen/" + id,
            dataType: "json",
        }).done(function(response) {

            if (response.status == 1) {

                /* Clear Modal Body */
                $('#detail_modal').find(".modal-title").html("");
                $('#detail_modal').find(".modal-body").html("");

                /* Insert Data to Modal Body */




                $('#detail_modal').find(".modal-body").append('<table class="table table-bordered table-striped"><thead><tr><th>NIK</th><th>Nama</th><th>Departemen</th><th>Scan Masuk</th><th>Scan Keluar</th><th>Lembur</th></tr></thead><tbody>');

                $.each(response.records, function(i, record) {
                    $('#detail_modal').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id + "' /><input name ='tanggal' type='hidden' value='" + record.tanggal + "' />" + record.id + "</td><td>" + record.nama + "</td><td>" + record.departemen + "</td><td>" + record.scan_masuk + "</td><td>" + record.scan_pulang + "</td><td>" + record.jam_lembur + "</td></tr><tr><td colspan='5' align='right'>Koreksi Lembur</td><td><input name ='lembur' type='text' value='" + record.jam_lembur + "' /></td></tr>");

                });

                $('#detail_modal').find(".modal-body").append("</table>");


                /* Finally show */
                $('#detail_modal').modal();
            } else {
                alert('Data Pegawai belum ada');
            }

        }).fail(function(response) {

        });
    };

    var showPrint = function(id) {

        $.ajax({
            method: "GET",
            url: "/absensi-harian/" + id,
            dataType: "json",
        }).done(function(response) {

            if (response.status == 1) {

                /* Clear Modal Body */
                $('#print_modal').find(".modal-title").html("");
                $('#print_modal').find(".modal-body").html("");

                /* Insert Data to Modal Body */
                $('#print_modal').find(".modal-body").append('<table class="table table-bordered table-striped"><thead><tr><th>NIK</th><th>Nama</th><th>Departemen</th></thead><tbody>');

                $.each(response.records, function(i, record) {
                    $('#print_modal').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id + "' /><input name ='tanggal' type='hidden' value='" + record.tanggal + "' />" + record.id + "</td><td>" + record.nama + "</td><td>" + record.departemen + "</td></tr>");

                });

                $('#print_modal').find(".modal-body").append("</table>");


                /* Finally show */
                $('#print_modal').modal();
            } else {
                alert('Data Pegawai belum ada');
            }

        }).fail(function(response) {

        });
    };

    return {
        init: init,
        showDetail: showDetail,
        showPrint: showPrint,
        createAbsensi: createAbsensi
    };

})(commonModule);
var karyawanValidation = (function(commonModule) {


    var existing_model = null;

    var init = function() {
        _applyDatepicker();
        _applyAutoNumeric();
        _applyValidation();

    };

    var _applyAutoNumeric = function() {
        $("#uang_makan").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#uang_makan"));
            });
        $("#nilai_upah").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#nilai_upah"));
            });
        $("#uang_lembur").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#uang_lembur"));
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


    var _applyDatepicker = function() {
        $('.datepicker').datepicker({
            weekStart: 1,
            todayHighlight: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    };


    return {
        init: init
    };

})(commonModule);
var reportAbsensiKaryawanTetapModule = (function(commonModule) {

    var existing_model = null;

    var init = function() {
        _applyValidation();
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
                    bulan: {
                        validators: {
                            notEmpty: {
                                message: 'Bulan harus diisi'
                            }
                        }
                    }
                }
            })
            // submit button always enable
            .on('err.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.form.fv', function(e) {
                // Prevent form submission
                e.preventDefault();


                var bulan = $("#bulan").val();

                var url = "/report/absensi-karyawan-tetap/preview/" + bulan;


                window.open(url, "_blank");
            });

    };


    return {
        init: init
    };

})(commonModule);
var reportAbsensiKaryawanHarianModule = (function(commonModule) {

    var existing_model = null;

    var init = function() {
        _applyValidation();
        _applyDatePicker();
    };

    var _applyDatePicker = function() {

        $("#tanggal").datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        }).on("change", function() {
            // Revalidate form
            $('#frmData').formValidation('revalidateField', 'tanggal');
        });

        $("#hingga").datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        }).on("change", function() {
            // Revalidate form
            $('#frmData').formValidation('revalidateField', 'tanggal');
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
                bulan: {
                        validators: {
                            notEmpty: {
                                message: 'Bulan harus diisi'
                            }
                        }
                    }
                }
            })
            // submit button always enable
            .on('err.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.form.fv', function(e) {
                // Prevent form submission
                e.preventDefault();

                var tanggal = $("#tanggal").val();
                var hingga = $("#hingga").val();

                var url = "/report/absensi-karyawan-harian/preview/" + tanggal + "/" + hingga;


                window.open(url, "_blank");
            });

    };


    return {
        init: init
    };

})(commonModule);
var reportAbsensiKaryawanPackingModule = (function(commonModule) {

    var existing_model = null;

    var init = function() {
        _applyValidation();
        _applyDatePicker();
    };

    var _applyDatePicker = function() {

        $("#tanggal").datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        }).on("change", function() {
            // Revalidate form
            $('#frmData').formValidation('revalidateField', 'tanggal');
        });

        $("#hingga").datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        }).on("change", function() {
            // Revalidate form
            $('#frmData').formValidation('revalidateField', 'tanggal');
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
                    bulan: {
                        validators: {
                            notEmpty: {
                                message: 'Bulan harus diisi'
                            }
                        }
                    }
                }
            })
            // submit button always enable
            .on('err.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.form.fv', function(e) {
                // Prevent form submission
                e.preventDefault();

                var tanggal = $("#tanggal").val();
                var hingga = $("#hingga").val();

                var url = "/report/absensi-karyawan-packing/preview/" + tanggal + "/" + hingga;


                window.open(url, "_blank");
            });

    };


    return {
        init: init
    };

})(commonModule);
var reportAbsensiKaryawanStaffModule = (function(commonModule) {

    var existing_model = null;

    var init = function() {
        _applyValidation();
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
                    bulan: {
                        validators: {
                            notEmpty: {
                                message: 'Bulan harus diisi'
                            }
                        }
                    }
                }
            })
            // submit button always enable
            .on('err.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.form.fv', function(e) {
                // Prevent form submission
                e.preventDefault();


                var bulan = $("#bulan").val();

                var url = "/report/absensi-karyawan-staff/preview/" + bulan;


                window.open(url, "_blank");
            });

    };


    return {
        init: init
    };

})(commonModule);
var upahJenisBarangModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'upah-jenis-barangs';

    var existing_model = null;

    var init = function() {
        _applyDatatable();
        _applyDatepicker();
        _applyAutoNumeric();
    };



    var confirmDelete = function(event, id) {

        event.preventDefault();

        swal({
                title: "Apakah anda yakin?",
                text: "Data Karyawan akan dihapus!",
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
                        url: "/karyawan-harian/" + id
                    })
                    .done(function(data) {
                        if (data === "success") {
                            // Redraw table
                            $('#datatable').DataTable().draw();
                            swal("", "Data berhasil dihapus.", "success");
                        } else {
                            swal("", data, "error");
                        }
                    });
            });

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

    var _applyAutoNumeric = function() {
        $("#uang_makan").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#uang_makan"));
            });
        $("#nilai_upah").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#nilai_upah"));
            });
        $("#uang_lembur").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#uang_lembur"));
            });

    };

    var _applyDatatable = function() {
        /* Tambah Input Field di TFOOT */
        $('#datatable tfoot th').each(function() {
            var title = $(this).text();
            if (title != '') {
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
            if (title == 'Created Date' || title == 'Updated Date') {
                $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": datatableBaseURL,
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'nama',
                name: 'nama'
            }, {
                data: 'upah',
                name: 'upah'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        });

        /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
        table.columns().every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change', function() {

                var keyword = this.value;

                if (this.placeholder == 'Search Model' || this.placeholder == 'Search Brand') {
                    keyword = keyword.toUpperCase();
                }

                if (that.search() !== keyword) {
                    that
                        .search(keyword)
                        .draw();
                }
            });
        });

    };


    return {
        init: init,
        confirmDelete: confirmDelete,
    };

})(commonModule);
//# sourceMappingURL=all.js.map
