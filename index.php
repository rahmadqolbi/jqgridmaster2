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
        $(window).resize(function () {
            $(customerTable).setGridWidth($(window).width() - 15)

        })
        $(document).ready(function () {
                $(document).on('click', '#clearFilter', function () {
                        currentSearch = undefined
                        $('[id*="gs_"]').val('')
                        $(customerTable).jqGrid('setGridParam', {
                            postData: null
                        })
                        $(customerTable)
                            .jqGrid('setGridParam', {
                                postData: {
                                    page: 1,
                                    rows: 10,
                                    sidx: 'customer_name',
                                    sord: 'asc',
                                },
                            })
                            .trigger('reloadGrid')
                        highlightSearch = 'undefined'
                    }),


                    $('#t_grid_id').html(`
		<div id="global_search">
			<label> Global search </label>
			<input id="gs_global_search" class="ui-widget-content ui-corner-all" style="padding: 4px;" globalsearch="true" clearsearch="true">
		</div>
	`)
            }),


            $('#t_grid_id input').on('input', function () {
                clearTimeout(timeout)

                timeout = setTimeout(function () {
                    indexRow = 0
                    $(customerTable).jqGrid('setGridParam', {
                        postData: {
                            'global_search': highlightSearch
                        }
                    }).trigger('reloadGrid')
                }, 500);
            })


        // stop

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
                    datafield: 'nama_pelanggan',
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
                    datafield: 'jenis_kelamin',
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
            // autoencode: true,
            sortorder: 'asc',
            height: 'auto',
            // loadonce: false,
            rownumbers: true,
            rownumWidth: 40,
            gridview: true,
            search: true,
            toolbar: [true, 'top'],
            // afterSearch: null,
            // beforeClear: null,
            // afterClear: null,
            // onClearSearchValue: null,
            //         autoResizing: {
            //   compact: true
            // },
            onSelectRow: function(id) {
                // activeGrid = '#grid_id'
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
                
                setTimeout(function() { 
                    
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
                <button id="clearFilter" title="Clear Filter" style="width: 100%; height: 100%;"> X </button>  
            `).click(function (e) {

                    var grid = $("#grid_id");
                    // Clear the filter
                    grid.jqGrid('clearGridData');
                    grid[0].p.search = false; //pencarian tidak sedang dilakukan
                    $.extend(grid[0].p.postData, {
                        filters: ""
                    });
                    // Reload the grid
                    grid.trigger("reloadGrid", [{
                        page: 1
                    }]);
                    e.preventDefault(); //agar tidak mereload ulang
                })

                
                })
               
//                 setCustomBindKeys($(this))
// postData = $(this).jqGrid('getGridParam', 'postData')
// var rowsPerPage = $(this).jqGrid('getGridParam', 'rowNum');
// var selectedRowId = $(this).jqGrid('getGridParam', 'selrow');
// var indexRow = $(this).jqGrid('getInd', selectedRowId);
// if (triggerClick) {
//     $('#' + $(this).getDataIDs()[indexRow]).click()
//     triggerClick = false
// } else {
//     var nextIndexRow = indexRow + rowsPerPage;
//     if (nextIndexRow < $(this).getGridParam('records')) {
//         $(this).jqGrid('setSelection', $(this).getDataIDs()[nextIndexRow], false);
//     }
// }










                //     $('#grid_id tbody tr td:not([aria-describedby=grid_id_rn])').highlight(highlightSearch)

                // if (indexRow > $('#grid_id').getDataIDs().length - 1) {
                // 	indexRow = $('#grid_id').getDataIDs().length - 1
                // }

            

               

                //////////////////////////


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

        // function defaultBindKeys() {
        //     $(document).keydown(function (e) {
        //         if (
        //             e.keyCode == 38 ||
        //             e.keyCode == 40 ||
        //             e.keyCode == 33 ||
        //             e.keyCode == 34 ||
        //             e.keyCode == 35 ||
        //             e.keyCode == 36
        //         ) {
        //             e.preventDefault();

        //             if (activeGrid !== undefined) {
        //                 var gridArr = $(activeGrid).getDataIDs();
        //                 var selrow = $(activeGrid).getGridParam("selrow");
        //                 var curr_index = 0;
        //                 var currentPage = $(activeGrid).getGridParam('page')
        //                 var lastPage = $(activeGrid).getGridParam('lastpage')
        //                 var row = $(activeGrid).jqGrid('getGridParam', 'postData').rows

        //                 for (var i = 0; i < gridArr.length; i++) {
        //                     if (gridArr[i] == selrow) curr_index = i;
        //                 }

        //                 switch (e.keyCode) {
        //                     case 33:
        //                         if (currentPage > 1) {
        //                             $(activeGrid).jqGrid('setGridParam', {
        //                                 "page": currentPage - 1
        //                             }).trigger('reloadGrid')
        //                         }
        //                         break
        //                     case 34:
        //                         if (currentPage !== lastPage) {
        //                             $(activeGrid).jqGrid('setGridParam', {
        //                                 "page": currentPage + 1
        //                             }).trigger('reloadGrid')
        //                         }
        //                         case 38:
        //                             if (curr_index - 1 >= 0)
        //                                 $(activeGrid)
        //                                 .resetSelection()
        //                                 .setSelection(gridArr[curr_index - 1])
        //                             break
        //                         case 40:
        //                             if (curr_index + 1 < gridArr.length)
        //                                 $(activeGrid)
        //                                 .resetSelection()
        //                                 .setSelection(gridArr[curr_index + 1])
        //                             break
        //                 }
        //             }
        //         }
        //     })
        // }
        // function defaultBindKeys() {
        //   $(document).keydown(function(e) {
        //     if (
        //       e.keyCode == 38 ||
        //       e.keyCode == 40 ||
        //       e.keyCode == 33 ||
        //       e.keyCode == 34 ||
        //       e.keyCode == 35 ||
        //       e.keyCode == 36
        //     ) {
        //       e.preventDefault();

        //       if (activeGrid !== undefined) {
        //         var gridArr = $(activeGrid).getDataIDs();
        //         var selrow = $(activeGrid).getGridParam("selrow");
        //         var curr_index = 0;
        //         var currentPage = $(activeGrid).getGridParam('page')
        //         var lastPage = $(activeGrid).getGridParam('lastpage')
        //         var row = $(activeGrid).jqGrid('getGridParam', 'postData').rows

        //         for (var i = 0; i < gridArr.length; i++) {
        //           if (gridArr[i] == selrow) curr_index = i;
        //         }

        //         switch (e.keyCode) {
        //           case 33:
        //             if (currentPage > 1) {
        //               $(activeGrid).jqGrid('setGridParam', { "page": currentPage - 1 }).trigger('reloadGrid')
        //             }
        //             break
        //           case 34:
        //             if (currentPage !== lastPage) {
        //               $(activeGrid).jqGrid('setGridParam', { "page": currentPage + 1 }).trigger('reloadGrid')
        //             }
        //             break
        //         case 38:
        // 			  		if (curr_index - 1 >= 0)
        // 			      $(activeGrid)
        // 			        .resetSelection()
        // 			        .setSelection(gridArr[curr_index - 1])
        // 			      break
        // 			    case 40:
        // 			    	if (curr_index + 1 < gridArr.length)
        // 			      $(activeGrid)
        // 			        .resetSelection()
        // 			        .setSelection(gridArr[curr_index + 1])
        // 			        break
        //         }
        //       }
        //     }
        //   })
        // }
    </script>
</body>

</html>