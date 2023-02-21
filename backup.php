<?php 
require "db.php";

?>

<!DOCTYPE html>
<html>

<head>
    <meta content="text/html; charset=utf-8" />
    <title>CRUD</title>
    <link rel="stylesheet" type="text/css" media="screen" href="css/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/trirand/ui.jqgrid.css" />
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="js/trirand/i18n/grid.locale-en.js" type="text/javascript"></script>

    <script src="js/trirand/jquery.jqGrid.min.js" type="text/javascript"></script>
    <!-- from a cdn -->
    <script src="//unpkg.com/autonumeric"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
    <script src="highlight.js" type="text/javascript"></script>
    <!-- <script src="jquery.js"></script>
<script src="dist/jquery.inputmask.js"></script>
<script src="dist/inputmask.js"></script>
<script src="dist/bindings/inputmask.binding.js"></script> -->
</head>
<style>
    body {
        font-size: 10px;
    }

    * {
        font-size: 12px;
        text-transform: uppercase;
    }
    * 
            .highlight {
                background-color: #fbec88;
            }
</style>

<body>

    <div class="container">
        <form action="" method="post">
            <table id="grid_id"></table>
            <div id="jqGridPager" class="sasa"></div>

        </form>
    </div>



    <script>
        const customerTable = '#grid_id'
        let indexRow = 0
        let sortName = 'customer_name'
        let timeout = null
        let highlightSearch
        let noInvoice
        let currentSearch
        let postData
        let ordersPostData
        let triggerClick = true
        let activeGrid = '#grid_id'
        let socket
        const form = document.querySelector('form');
const table = document.querySelector('table');

// menambahkan event listener pada form untuk menangkap event submit
form.addEventListener('submit', function(event) {
  // mencegah perilaku default dari event submit
  event.preventDefault();

  // menetapkan fokus pada elemen pertama pada tabel
  const firstCell = table.querySelector('td');
  if (firstCell) {
    firstCell.focus();
  }
});
        $(window).resize(function () {
            $(customerTable).setGridWidth($(window).width() - 15)

        })
        $(document).on('click','#clearFilter',function()
                {
                    currentSearch = undefined
                    $('[id*="gs_"]').val('')
                    $('#grid_id').jqGrid('setGridParam', {postData: null})
                    $('#grid_id').jqGrid('setGridParam',
                    {
                        postData: 
                        {
                            page: 1,
                            rows: 10,
                            sidx: 'Invoice',
                            sord: 'asc',
                        },
                    })
                    .trigger('reloadGrid')
                    highlightSearch = 'undefined'
                })


      
        $(document).ready(function () {
            $('#t_grid_id').html(`
		<div id="global_search">
			<label> Global search </label>
			<input  id="gs_global_search" class="ui-widget-content ui-corner-all" style="padding: 4px;" globalsearch="true" clearsearch="true">
		</div>
	`)
})
        

        $("#grid_id").jqGrid({
            datatype: 'json',
            url: 'data.php',
            pager: '#jqGridPager',
            emptyrecords: "Nothing to display",
            mtype: 'GET',
            editurl: 'update.php',
            colModel: [

                {
                    name: 'id',
                    label: 'Id',
                    key: true,
                    search: true,
                    sortable: true,
                    datafield: 'id',
                    index: 'id',
                    searchoptions: {
                        sopt: ["eq", "ne", "lt", "le", "gt", "ge"]
                    }

                },
                {
                    name: 'no_invoice',
                    index: 'no_invoice',
                    label: 'No Invoice',
                    editable: true,
                    search: true,
                    searchoptions: {
                        sopt: ["eq", "ne", "lt", "le", "gt", "ge"]
                    },
                    sortable: true,
                    datafield: 'no_invoice',
                    numberOfColumns: 2,
                    editrules: {
                        edithidden: true,
                        required: true
                    },
                    editoptions: {
                        style: "text-transform: uppercase",
                        dataInit: function (inv) {
                            $(inv).height(8);

                        }
                    },

                },
                {
                    name: 'tgl_pembelian',
                    width: 200,
                    index: 'tgl_pembelian',
                    searchoptions: {
                        sopt: ["eq", "ne", "lt", "le", "gt", "ge"]
                    },
                    datafield: 'tgl_pembelian',
                    label: 'Tanggal Pembelian',
                    editable: true,
                    sorttype: 'date',
                    formatter: 'date',
                    search: true,
                    sortable: true,
                    loadonce: false,
                    formatoptions: {
                        srcformat: "Y-m-d",
                        newformat: "d-m-Y"

                    },
                    editoptions: {

                        dataInit: function (tgl) {
                            $(tgl).datepicker({
                                dateFormat: 'dd-mm-yy',
                            }), $(tgl).inputmask("date", {
                                mask: "1-2-y",
                                separator: "-",
                                alias: "D-M-Y",

                            })



                        },
                    },

                },
                {
                    name: 'nama_pelanggan',
                    label: 'Nama Pelanggan',
                    
                    searchoptions: {
                        sopt: ["eq", "ne", "lt", "le", "gt", "ge"]
                    },
                    width: 200,
                    editable: true,
                    index: 'nama_pelanggan',
                    search: true,
                    sortable: true,
                    editrules: {
                        edithidden: true,
                        required: true
                    },
                    editoptions: {
                        style: "text-transform: uppercase",
                        dataInit: function (inv) {
                            $(inv).height(8);
                        }
                    },
                },
                {
                    name: 'jenis_kelamin',
                    label: 'Jenis Kelamin',
                    index: 'jenis_kelamin',
                    editable: true,
                    searchoptions: {
                        sopt: ["eq", "ne", "lt", "le", "gt", "ge"]
                    },
                    search: true,
                    formatter: "select",
                    sortable: true,
                    editrules: {
                        edithidden: true,
                        required: true
                    },
                    edittype: "select",
                    editoptions: {
                        value: "LAKI-LAKI:LAKI-LAKI;PEREMPUAN:PEREMPUAN",
                        // defaultValue: "Search",
                        dataInit: function (element) {
                            $(element).width(150).select2();
                            $(element).height(5).select2();
                        }
                    },
                },
                {
                    name: 'saldo',
                    idName: 'number',
                    label: 'Saldo',
                    index: 'saldo',
                    editable: true,
                    sorttype: "float",
                    formatter: 'number',
                    searchoptions: {
                        sopt: ["eq", "ne", "lt", "le", "gt", "ge"]
                    },
                    datafield: 'saldo',
                    sortable: true,
                    search: true,
                    align: 'right',
                    editrules: {
                        edithidden: true,
                        required: true
                    },
                    formatoptions: {
                        thousandsSeparator: ".",
                        decimalSeparator: ".",
                        decimalPlaces: 0,
                    },
                    editoptions: {
                        dataInit: function (elem) {
                            $(elem).attr('autocomplete', 'off');
                            $(elem).height(8);
                            $(elem).css('text-align', 'right');
                            const autoNumericOptionsEuro = {
                                digitGroupSeparator: '.',
                                decimalCharacter: ',',
                                decimalCharacterAlternative: '.',
                                thousandsSeparator: ".",
                                decimalSeparator: ".",
                                decimalPlaces: 0,
                                currencySymbolPlacement: AutoNumeric.options.currencySymbolPlacement
                                    .suffix,
                                roundingMethod: AutoNumeric.options.roundingMethod.halfUpSymmetric,
                            };
                            new AutoNumeric(elem, autoNumericOptionsEuro);

                        }
                    },

                },

            ],
            viewrecords: true,
            width: 1200,
            height: 200,
            rowNum: 10, //jumlah baris data yang akan ditampilkan pada setiap halaman
            rowList: [10, 20,
                30
            ], //rowList adalah daftar opsi jumlah baris yang dapat dipilih oleh pengguna untuk ditampilkan pada setiap halaman
            pager: "#jqGridPager",
            caption: "Master Penjualan",
            sortname: 'id',
            autoencode: true,
            sortorder: 'asc',
            height: 'auto',
            // loadonce: false,
            rownumbers: true,
            rownumWidth: 40,
            gridview: true,
            search: true,
            ignoreCase: true,
            toolbar: [true, 'top'],
         
            onSelectRow: function (id) {

                indexRow = $(this).jqGrid('getCell', id, 'rn') - 1
                page = $(this).jqGrid('getGridParam', 'page') - 1
                rows = $(this).jqGrid('getGridParam', 'postData').rows
                if (indexRow >= rows) indexRow = (indexRow - rows * page)

            },
            loadComplete: function () {
                // bindkeys

                $(document).unbind('keydown')
                setCustomBindKeys($(this))
                postData = $(this).jqGrid('getGridParam', 'postData')

                setTimeout(function () {
                    $('#grid_id tbody tr td:not([aria-describedby=grid_id_rn])').highlight(highlightSearch)
                    if (indexRow > $('#grid_id').getDataIDs().length - 1) {
                        indexRow = $('#grid_id').getDataIDs().length - 1
                    }

                    if (triggerClick) {
                        $('#' + $('#grid_id').getDataIDs()[indexRow]).click()
                        triggerClick = false
                    } else {
                        $('#grid_id').setSelection($('#grid_id').getDataIDs()[indexRow])
                    }


                    $('#gsh_grid_id_rn').html(`
                                <button type="button" id="clearFilter" title="Clear Filter" style="width: 100%; height: 100%;"> X </button>
                            `).click(function(){})
                            $('[id*=gs_]').on('input', function() 
                            {
                                highlightSearch = $(this).val()
                            })
                    $('[id*=gs_]').on('input', function () {
                        highlightSearch = $(this).val()
                        clearTimeout(timeout)

                        timeout = setTimeout(function () {
                            $('#grid_id').trigger('reloadGrid')
                        }, 500);
                    })

                    $('#t_grid_id input').on('input', function () {

                        clearTimeout(timeout)

                        timeout = setTimeout(function () {
                            indexRow = 0
                            $(customerTable).jqGrid('setGridParam', {
                                postData: {
                                    'global_search': highlightSearch
                                }
                            }).trigger('reloadGrid')
                        }, 400);
                    })
                    $('input')
                        .css('text-transform', 'uppercase')
                        .attr('autocomplete', 'off')
                }, 50)
            },
            //cara buat parameter sendiri di jqgrid
            postData: {
                searchField: "",
                searchOper: "",
                searchString: ""
            },
            postData: {
                global_search: function () {
                    return JSON.stringify(jQuery("#jqGridPager").jqGrid("getGridParam", "postData")
                        .global_search);
                }
            },
            search: "gSearch",
            postData: {
                global_search: ""
            },

            postData: {
                searchField: "",
                searchOper: "",
                searchString: ""
            },
            postData: {
                filters: function () {
                    return JSON.stringify(jQuery("#jqGridPager").jqGrid("getGridParam", "postData")
                        .filters);
                }
            },
            search: true,
            postData: {
                filters: ""
            }

        });

        // '#jqGridPager', null,
        jQuery("#grid_id").jqGrid('filterToolbar', {
            defaultSearch: "cn",
            searchOnEnter: false, //MENCARI SAAT DI KLIK ENTER FALSE
            searchOperators: true,
            stringResult: true,
            afterSearch: function () {
                indexRow = 0
            },
            gridComplete: function () {
                $("#grid_id").setGridParam({
                    datatype: 'json'
                });

            }
        });

        jQuery("#grid_id").on("change keyup", function () {
            var search_value = jQuery(this).val();
            jQuery("#grid_id").setGridParam({
                postData: {
                    filters: JSON.stringify({
                        groupOp: "AND",
                        rules: [{
                            field: "column_name",
                            op: "cn",
                            data: search_value
                        }]
                    }),
                    multipleSearch: true,

                }
            }).trigger("reloadGrid");
        });
        $("#grid_id").jqGrid('navButtonAdd', '#jqGridPager', {
            caption: "",
            title: "Clear All Filters",
            buttonicon: "ui-icon-refresh",
            onClickButton: function () {
                $("#grid_id")[0].clearToolbar();
                $("#grid_id").trigger("reloadGrid");

            }
        });
        // bagaimana membuat input data dapat terlihat dibaris pertama ketika disubmit
        // konfigurasi jqgrid untuk membuat kolom pencarian
        $("#grid_id").jqGrid({
  // konfigurasi jQGrid lainnya
  pager: "#jqGridPager",
  inlineNav: {
    add: true, // menampilkan tombol Tambah
    edit: false, // tidak menampilkan tombol Edit
    save: true, // menampilkan tombol Simpan
    cancel: true, // menampilkan tombol Batal
    addParams: {
      rowID: "new_row", // memberikan ID pada baris yang akan ditambahkan
      useFormatter: false, // menggunakan form input bawaan jQGrid
      addRowParams: {
        // konfigurasi form input
        position: "first", // menampilkan form di baris pertama
        addRow: "top", // menambahkan baris di bagian atas grid
        keys: true, // memungkinkan pengguna untuk menekan tombol Enter untuk menyimpan
        closeOnEscape: true,
         // menutup form ketika pengguna menekan tombol Escape
      }
    }
  }
});

        // onUpKey: function() {
        //     var selrow = $("#grid_id").jqGrid('getGridParam', 'selrow');
        //     var prevRow = $("#grid_id").jqGrid('getRowData', selrow - 0);
        //     if (prevRow !== null) {
        //         $("#grid_id").jqGrid('setSelection', selrow + 0, true);
        //     }
        // },
        // onDownKey: function() {
        //     var selrow = $("#grid_id").jqGrid('getGridParam', 'selrow');
        //     var nextRow = $("#grid_id").jqGrid('getRowData', selrow + 0);
        //     if (nextRow !== null) {
        //         $("#grid_id").jqGrid('setSelection', selrow - 0, true);
        //     }
        // }


        jQuery("#grid_id").jqGrid('navGrid', '#jqGridPager', null, {

            recreateForm: true,
            beforeShowForm: function (form) {
                form[0].querySelector('#no_invoice').setAttribute('readonly', 'readonly')

                // let id = form.find('#id').val();
                // let id = form.find('#no_invoice').val();
                // let id = form.find('#tgl_pembelian').val();
                // let id = form.find('#nama_pelanggan').val();
                // console.log(form.find('#id').val(id.replace('<span class="highlight">', '').replace('</span>', '')));
            }

        }, {
            recreateForm: true

        });

        /**
         * Set Home, End, PgUp, PgDn
         * to move grid page
         */
        function setCustomBindKeys(grid) {
            $(document).on("keydown", function (e) {
                if (activeGrid) {
                    if (
                        e.keyCode == 33 ||
                        e.keyCode == 34 ||
                        e.keyCode == 35 ||
                        e.keyCode == 36 ||
                        e.keyCode == 38 ||
                        e.keyCode == 40 ||
                        e.keyCode == 13
                    ) {
                        e.preventDefault();

                        var gridIds = $(activeGrid).getDataIDs();
                        var selectedRow = $(activeGrid).getGridParam("selrow");
                        var currentPage = $(activeGrid).getGridParam("page");
                        var lastPage = $(activeGrid).getGridParam("lastpage");
                        var currentIndex = 0;
                        var row = $(activeGrid).jqGrid("getGridParam", "postData").rows;

                        for (var i = 0; i < gridIds.length; i++) {
                            if (gridIds[i] == selectedRow) currentIndex = i;
                        }

                        if (triggerClick == false) {
                            if (33 === e.keyCode) {
                                if (currentPage > 1) {
                                    $(activeGrid)
                                        .jqGrid("setGridParam", {
                                            page: parseInt(currentPage) - 1,
                                        })
                                        .trigger("reloadGrid");

                                    triggerClick = true;
                                }
                                $(activeGrid).triggerHandler("jqGridKeyUp"), e.preventDefault();
                            }
                            if (34 === e.keyCode) {
                                if (currentPage !== lastPage) {
                                    $(activeGrid)
                                        .jqGrid("setGridParam", {
                                            page: parseInt(currentPage) + 1,
                                        })
                                        .trigger("reloadGrid");

                                    triggerClick = true;
                                }
                                $(activeGrid).triggerHandler("jqGridKeyUp"), e.preventDefault();
                            }
                            if (35 === e.keyCode) {
                                if (currentPage !== lastPage) {
                                    $(activeGrid)
                                        .jqGrid("setGridParam", {
                                            page: lastPage,
                                        })
                                        .trigger("reloadGrid");
                                    if (e.ctrlKey) {
                                        if (
                                            $(activeGrid).jqGrid("getGridParam", "selrow") !==
                                            $("#customer")
                                            .find(">tbody>tr.jqgrow")
                                            .filter(":last")
                                            .attr("id")
                                        ) {
                                            $(activeGrid)
                                                .jqGrid(
                                                    "setSelection",
                                                    $(activeGrid)
                                                    .find(">tbody>tr.jqgrow")
                                                    .filter(":last")
                                                    .attr("id")
                                                )
                                                .trigger("reloadGrid");
                                        }
                                    }

                                    triggerClick = true;
                                }
                                if (e.ctrlKey) {
                                    if (
                                        $(activeGrid).jqGrid("getGridParam", "selrow") !==
                                        $("#customer")
                                        .find(">tbody>tr.jqgrow")
                                        .filter(":last")
                                        .attr("id")
                                    ) {
                                        $(activeGrid)
                                            .jqGrid(
                                                "setSelection",
                                                $(activeGrid)
                                                .find(">tbody>tr.jqgrow")
                                                .filter(":last")
                                                .attr("id")
                                            )
                                            .trigger("reloadGrid");
                                    }
                                }
                                $(activeGrid).triggerHandler("jqGridKeyUp"), e.preventDefault();
                            }
                            if (36 === e.keyCode) {
                                if (currentPage > 1) {
                                    if (e.ctrlKey) {
                                        if (
                                            $(activeGrid).jqGrid("getGridParam", "selrow") !==
                                            $("#customer")
                                            .find(">tbody>tr.jqgrow")
                                            .filter(":first")
                                            .attr("id")
                                        ) {
                                            $(activeGrid).jqGrid(
                                                "setSelection",
                                                $(activeGrid)
                                                .find(">tbody>tr.jqgrow")
                                                .filter(":first")
                                                .attr("id")
                                            );
                                        }
                                    }
                                    $(activeGrid)
                                        .jqGrid("setGridParam", {
                                            page: 1,
                                        })
                                        .trigger("reloadGrid");

                                    triggerClick = true;
                                }
                                $(activeGrid).triggerHandler("jqGridKeyUp"), e.preventDefault();
                            }
                            if (38 === e.keyCode) {
                                if (currentIndex - 1 >= 0) {
                                    $(activeGrid)
                                        .resetSelection()
                                        .setSelection(gridIds[currentIndex - 1]);
                                }
                            }
                            if (40 === e.keyCode) {
                                if (currentIndex + 1 < gridIds.length) {
                                    $(activeGrid)
                                        .resetSelection()
                                        .setSelection(gridIds[currentIndex + 1]);
                                }
                            }
                            if (13 === e.keyCode) {
                                let rowId = $(activeGrid).getGridParam("selrow");
                                let ondblClickRowHandler = $(activeGrid).jqGrid(
                                    "getGridParam",
                                    "ondblClickRow"
                                );

                                if (ondblClickRowHandler) {
                                    ondblClickRowHandler.call($(activeGrid)[0], rowId);
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>

</html>