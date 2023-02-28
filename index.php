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
    <script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>


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
                <div id="jqGridPager"></div>
                <div id="t_penjualan"></div>
    </form> 
    </div>



    <script>
        // const customerTable = '#customer'
        // const customerPager = '#jqGridPager'
        // const customerDialog = '#t_penjualan'
        // const customerForm = '#customerForm'



        const customerTable = '#grid_id'
        let indexRow = 0
        let sortName = 'no_invoice'
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
     
        $(document).on('click', '#clearFilter', function () {
		currentSearch = undefined
	  $('[id*="gs_"]').val('')
	  $(customerTable).jqGrid('setGridParam', { postData: null })
	  $(customerTable)
	    .jqGrid('setGridParam', {
	      postData: {
	        page: 1,
	        rows: 10,
	        sidx: 'no_invoice',
	        sord: 'asc',
	      },
	    })
	    .trigger('reloadGrid')
	  highlightSearch = 'undefined'
	})
    $('[id*=gs_]').each(function(i, el) {
		$(el).on('focus', function(el) {
			currentSearch = $(this)
		})
	})
      
        $(document).ready(function () {
            $('#t_grid_id').html(`
		<div id="global_search">
			<label> Global search </label>
			<input  id="gs_global_search" class="ui-widget-content ui-corner-all" style="padding: 4px;" globalsearch="true" clearsearch="true">
		</div>
	`)
})


// function addCustomer() {
// 	$(customerDialog).load(baseUrl + 'customer/create', function() {
// 		$($(customerForm + ' input')[0]).focus()
// 		$.ajax({
// 			url: 'add_header.php',
// 			type: 'GET',
// 			dataType: 'JSON',
// 			success: function(res) {
// 				res.forEach(function(el, i) {
// 					$(`input[name=${el.name}`).attr('maxlength', el.max_length)
// 				})
// 			}
// 		})
// 	}).dialog({
// 		modal: true,
// 		title: "Add Customer",
// 		height: '500',
// 		width: '650',
// 		position: [0, 0],
// 		buttons: {
// 			'Save': function() {
// 				storeCustomer()
// 			},
// 			'Cancel': function() {
			
// 				$(this).dialog('close')
// 			}
// 		}
// 	})
// }



function highlightRow(rowId, response) {
  // Menambahkan kelas CSS pada baris yang baru saja dimasukkan
  $("#" + rowId).addClass("highlight");
}

        

        $("#grid_id").jqGrid({
            datatype: 'json',
            url: 'data.php',
            pager: '#jqGridPager',
   
            emptyrecords: "Nothing to display",
            mtype: 'GET',
            // mtype: 'POST',
            sortable: true,
            editurl: 'update.php',
    //         onSelectRow: function(rowid) {
    //     // Set selected row
    //     $("#grid_id").setSelection(rowid);
    // },
    // afterSubmit: function(response, postdata) {
    //     // Reload grid
    //     $("#grid_id").trigger("reloadGrid");

    //     // Get selected row id
    //     var rowid = $("#grid_id").getGridParam("selrow");

    //     // Set selected row
    //     $("#grid_id").setSelection(rowid);
        
    //     // Return true to prevent default error handling
    //     return [true];
    // },
            colModel: [

                {
                    name: 'id',
                    label: 'Id',
                    index: 'id',
                    key: true,
                    search: true,
                    sortable: true,
                    datafield: 'id',
                    index: 'id',
                    hidden: true
                    // searchoptions: {
                    //     sopt: ["eq", "ne", "lt", "le", "gt", "ge"]
                    // }

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
                            index: 'tgl_pembelian',
                            sortable: true,
                            editable: true,
                            editoptions:
                            {
                                dataInit: function(element) 
                                {
                                    $(element).attr('autocomplete', 'off'),
                                    $(element).css('text-transform', 'uppercase'),
                                    $(element).datepicker(
                                        {
                                            dateFormat: 'dd-mm-yy'
                                        }
                                    ),
                                    $(element).inputmask("date",
                                        {
                                            mask: "1-2-y",
                                            separator: "-",
                                            alias: "d-m-y"
                                        }
                                    )
                                }
                            },
                            formatter: 'text',
                            formatoptions: 
                            { 
                                newformat: 'd-m-Y'
                            },
                            sorttype:'date',
                            searchoptions: 
                            {
                                dataInit: function(element)
                                {
                                    $(element).attr('autocomplete', 'off')
                                }
						    }
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
            editoptions: {
        closeAfterEdit: true,
        closeAfterAdd: true,
        modal: true,
        width: 4000,
        formId: "form_id"
    },
            sortname: 'no_invoice',
            autoencode: true,
            sortorder: 'asc',
            height: 'auto',
            // loadonce: false,
            rownumbers: true,
            rownumWidth: 40,
            gridview: true,
            search: true,
            // afterSubmit: highlightRow,
            ignoreCase: true,
            shrinkToFit: true,
            toolbar: [true, 'top'],
       
            onSelectRow: function (id) {

                indexRow = $(this).jqGrid('getCell', id, 'rn') - 1
                page = $(this).jqGrid('getGridParam', 'page') - 1
                rows = $(this).jqGrid('getGridParam', 'postData').rows
                if (indexRow >= rows) indexRow = (indexRow - rows * page)

            },
            loadComplete: function () {
             
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
                            `).click(function() {
          var grid = $("#jqGrid");

          // Clear the filter
          grid.jqGrid('clearGridData');
          grid[0].p.search = false;
          $.extend(grid[0].p.postData, {filters: ""});
        
          // Reload the grid
          grid.trigger("reloadGrid");
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
                        }, 500);
                    })
                    $('input')
                        .css('text-transform', 'uppercase')
                        .attr('autocomplete', 'off')
                }, 50)
            },

            //cara buat parameter sendiri di jqgrid
            // postData: {
            //     searchField: "",
            //     searchOper: "",
            //     searchString: ""
            // },
            // postData: {
            //     global_search: function () {
            //         return JSON.stringify(jQuery("#jqGridPager").jqGrid("getGridParam", "postData")
            //             .global_search);
            //     }
            // },
            // search: "gSearch",
            // postData: {
            //     global_search: ""
            // },

            // postData: {
            //     searchField: "",
            //     searchOper: "",
            //     searchString: ""
            // },
            // postData: {
            //     filters: function () {
            //         return JSON.stringify(jQuery("#jqGridPager").jqGrid("getGridParam", "postData")
            //             .filters);
            //     }
            // },
            // search: true,
            // postData: {
            //     filters: ""
            // }

        });
        var source =
                {
                    beforeprocessing: function(data)
                    {		
                        source.totalrecords = data[0].TotalRows;
                    }
                };
//         $("grid_id").click(function() {
//     // Menambahkan baris baru pada tabel
//     $("#grid_id").jqGrid("editGridRow", "new", {
//       height: 250,
//       addRowParams: {
//         position: "first"
//       },
//       reloadAfterSubmit: true
//     });
//   });
// selected row after crud

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

        
      

        // $("#grid_id").jqGrid('navButtonAdd', '#jqGridPager', {
        //     caption: "",
        //     title: "Clear All Filters",
        //     buttonicon: "ui-icon-refresh",
        //     onClickButton: function () {
        //         $("#grid_id")[0].clearToolbar();
        //         $("#grid_id").trigger("reloadGrid");

        //     }
        // });
       
        
        jQuery("#grid_id").jqGrid('navGrid', '#jqGridPager', null, {

            recreateForm: true, //formulir akan dibuat ulang setiap kali dialog diaktifkan dengan opsi baru dari colModel
            beforeShowForm: function (form) {
                form[0].querySelector('#no_invoice').setAttribute('readonly', 'readonly')
                // var input = document.getElementById("no_invoice").value;
                // var inputBaru = input.replace('<span class="highlight"></span>', ""); // Menghapus kataHapus dari input menggunakan replace()
                //  document.getElementById("no_invoice").value = inputBaru;
                var nilaiAsli = "";
                var no_invoice = document.getElementById("no_invoice"); // Ambil element input no_invoice
                var tgl_pembelian = document.getElementById("tgl_pembelian"); // Ambil element input tgl_pembelian
                var nama_pelanggan = document.getElementById("nama_pelanggan"); // Ambil element input nama_pelanggan
                var jenis_kelamin = document.getElementById("jenis_kelamin"); // Ambil element input jenis_kelamin
                var saldo = document.getElementById("saldo");// Ambil element input saldo

                nilaiAsli = no_invoice.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = no_invoice.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                no_invoice.value = inputBaru; // Tampilkan input yang telah diubah

                nilaiAsli = tgl_pembelian.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = tgl_pembelian.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                tgl_pembelian.value = inputBaru; // Tampilkan input yang telah diubah

                nilaiAsli = nama_pelanggan.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = nama_pelanggan.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                nama_pelanggan.value = inputBaru; // Tampilkan input yang telah diubah

                nilaiAsli = jenis_kelamin.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = jenis_kelamin.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                jenis_kelamin.value = inputBaru; // Tampilkan input yang telah diubah

                nilaiAsli = saldo.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = saldo.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                saldo.value = inputBaru; // Tampilkan input yang telah diubah



             
            },
            // recreateForm: true,
            recreateForm: true,
             afterSubmit:callAfterSubmit,
            // reloadAfterSubmit:true,
            closeAfterEdit: true
        }, {
            recreateForm: true,
             afterSubmit:callAfterSubmit,
             closeAfterAdd: true
            // reloadAfterSubmit:true,
          
             
    }
,);

function callAfterSubmit(response, postData, oper) {
    // get the sort field, sort order, and page size
    var sortfield = $(this).jqGrid('getGridParam', 'postData').sidx;
    var sortorder = $(this).jqGrid('getGridParam', 'postData').sord;
    var pagesize = $(this).jqGrid('getGridParam', 'postData').rows;

    // get the value of the field that uniquely identifies the newly inserted data
    var no_invoice = postData.no_invoice;

    // make an AJAX call to your PHP script to get the position of the data
    $.ajax({
    url: "add_header.php",
    type: "POST",
    dataType: 'json',
    data: {
        no_invoice: no_invoice,
        sidx: sortfield,
        sord: sortorder,
    },
    success: function(data) {
        $('#cData').click();
        var position = data.position;//2
        var page = Math.ceil(position / pagesize);//2 : 10 = 0,2
        var row = position - (page - 1) * pagesize;// 2- (0,2 -1) * 10
        indexRow = row-1;
        $("#grid_id").jqGrid("setGridParam", { page: page }).trigger("reloadGrid");
        $("#grid_id").jqGrid("setSelection", row);
        console.log(position);
        console.log(pagesize);
        console.log(page);
        console.log(row);
        console.log(indexRow);
        console.log(data);
    }
});

//     console.log("no_invoice: " + no_invoice);
// console.log("sortfield: " + sortfield);
// console.log("sortorder: " + sortorder);
// console.log("pagesize: " + pagesize);
} 


  


$('#grid_id').navButtonAdd('#jqGridPager', {
   	    caption: "",
		title: "Report",
		id: "customersReport",
		buttonicon: "ui-icon-document",
		onClickButton:function(){
            $('#jqGridPager')
				.html(`
					<div class="ui-state-default" style="padding: 5px;">
						<h5> Tentukan Baris </h5>
						
						<label> Dari: </label>
						<input type="text" name="start" value="${$(this).getInd($(this).getGridParam('selrow'))}" class="ui-widget-content ui-corner-all autonumeric" style="padding: 5px; text-transform: uppercase;" max="2" required>

						<label> Sampai: </label>
						<input type="text" name="limit" value="${$(this).getGridParam('records')}" class="ui-widget-content ui-corner-all autonumeric" style="padding: 5px; text-transform: uppercase;" max="2" required>
					</div>
				`)
                
                
        }
        })

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