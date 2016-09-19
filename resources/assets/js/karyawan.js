var karyawanModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'karyawans';
    var select2BaseURLBrand = commonModule.select2BaseURL + 'brands';
    var select2BaseURLItem = commonModule.select2BaseURL + 'items';
    var select2BaseURLColor = commonModule.select2BaseURL + 'colors';

    var existing_model = null;

    var init = function() {
        _applyDatatable();
        _applySelect2Brand();
        _switchModel();
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
            todayHighlight: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    };

    var _applySelect2Brand = function() {

        $('select.brand_ajax').select2({
            minimumInputLength: 2,
            ajax: {
                url: select2BaseURLBrand,
                dataType: "json",
                type: "POST",
                data: function(params) {
                    var queryParameters = {
                        term: params.term
                    };
                    return queryParameters;
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name + ' (' + item.code + ')',
                                id: item.id
                            };
                        })
                    };
                }
            }
        });
    };

    var _applySelect2Model = function() {

        $('select.item_ajax').select2({
            minimumInputLength: 2,
            placeholder: "Search existing item",
            ajax: {
                url: select2BaseURLItem,
                dataType: "json",
                type: "POST",
                data: function(params) {
                    var queryParameters = {
                        term: params.term
                    };
                    return queryParameters;
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.model,
                                id: item.model
                            };
                        })
                    };
                }
            }
        });
    };

    var _switchModel = function() {
        $('#switch_model').on('click', function() {
            var model_input = $('#model_input');
            var switch_model_button = $(this);

            if (existing_model == null) {
                existing_model = $("input#model").val();
            }

            if (switch_model_button.html() == '<i class="fa fa-search fa-fw"></i> Search') {
                model_input.html('<select name="model" class="item_ajax form-control"></select>');
                _applySelect2Model();
                switch_model_button.html('<i class="fa fa-keyboard-o fa-fw"></i> Type');
            } else {
                model_input.html('<input type="text" id="model" placeholder="Enter your new item" class="form-control" value="' + existing_model + '" name="model">');
                switch_model_button.html('<i class="fa fa-search fa-fw"></i> Search');
            }
        });
    }

    

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
                data: 'uang_lembur',
                name: 'karyawans.uang_lembur'
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

    return {
        init: init
    };

})(commonModule);