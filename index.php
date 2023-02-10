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
        // menampilkan data ke browser dari data json

        //         function myelem (value, options) {
        //   var el = document.createElement("input");
        //   el.setAttribute = "datepicker";
        //   el.type="date";
        //   el.value = value;
        //   return el;
        // }


        // function myvalue(elem, operation, value) {
        //     if(operation === 'get') {
        //        return $(elem).val();
        //     } else if(operation === 'set') {
        //        $(elem).val(value);
        //     }
        // }
            
        $("#grid_id").jqGrid({
            datatype: 'json',
            url: 'data.php',
            pager: '#jqGridPager',
            emptyrecords: "Nothing to display",
            // colNames: ['sa','nama_pelanggan','tgl_pembelian'],
            mtype: 'GET',
            editurl: 'update.php',
            colModel: [

                {
                    name: 'id',
                    label: 'Id',
                    key: true,
                    search:true,
                    sortable:true,
                    datafield: 'id',
                    index: 'id',
                    searchoptions: {sopt: ["eq", "ne", "lt", "le", "gt", "ge"]}

                },
                {
                    name: 'no_invoice',
                    index: 'no_invoice',
                    label: 'No Invoice',
                    editable: true,
                    search:true,
                    searchoptions: {sopt: ["eq", "ne", "lt", "le", "gt", "ge"]},
                    sortable:true,
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
                    index:'tgl_pembelian',
                    searchoptions: {sopt: ["eq", "ne", "lt", "le", "gt", "ge"]},
                    datafield: 'tgl_pembelian',
                    label: 'Tanggal Pembelian',
                    editable: true,
                    sorttype: 'date',
                    formatter: 'date',
                    search: true,
                    sortable:true,
                    loadonce: true,
                    formatoptions: {
                        srcformat: "Y-m-d",
                        newformat: "d-m-Y"

                    },
                    editoptions: {

                        dataInit: function (tgl) {
                            // $(tgl).inputmask("99/99/9999");
                            $(tgl).datepicker({
                                dateFormat: 'dd-mm-yy',
                            }),$(tgl).inputmask("date",{
                                mask:"1-2-y",
                                separator: "-",
                                alias: "D-M-Y",
                                
                            })
                                
                               
                            
                        },
                        // defaultValue: function () {
                        //     var currentTime = new Date();
                        //     var month = parseInt(currentTime.getMonth() + 1);
                        //     month = month <= 9 ? "0" + month : month;
                        //     var day = currentTime.getDate();
                        //     day = day <= 9 ? "0" + day : day;
                        //     var year = currentTime.getFullYear();
                        //     return day + "-" + month + "-" + year;
                        // }
                    },

                },
                {
                    name: 'nama_pelanggan',
                    label: 'Nama Pelanggan',
                    searchoptions: {sopt: ["eq", "ne", "lt", "le", "gt", "ge"]},
                    width: 200,
                    editable: true,
                    datafield: 'nama_pelanggan',
                    search:true,
                    sortable:true,
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
                    searchoptions: {sopt: ["eq", "ne", "lt", "le", "gt", "ge"]},
                    search:true,
                    formatter: "select",
                    sortable:true,
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
                    // stype: "select",
                    // searchoptions: {
                    //     value: "LAKI-LAKI:LAKI-LAKI;PEREMPUAN:PEREMPUAN",
                    //     defaultValue: "Search",
                    //     dataInit: function(element) {
                    //         $(element).width(122).select2({
                    //             dropdownCssClass: "ui-widget ui-jqdialog"
                    //         });
                    //     }
                    // }


                },
                {
                    name: 'saldo',
                    idName: 'number',
                    label: 'Saldo',
                    editable: true,
                    sorttype: "float",
                    formatter: 'number',
                    searchoptions: {sopt: ["eq", "ne", "lt", "le", "gt", "ge"]},
                    datafield: 'saldo',
                    sortable:true,
                    search:true,
                    align: 'right',
                    editrules: {
                        edithidden: true,
                        required: true
                    },
                    formatoptions: {
                        thousandsSeparator: ".",
                        decimalSeparator: ".",
                        decimalPlaces: 0,

                        // prefix : "Rp. ",
                        // deaultValue: "Rp. 0.00"
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
                    // sorttype: function (cell, rowData) {
                    //         return (parseInt(rowData.Price) * parseInt(rowData.Quantity));
                    //     }


                },

                //   defaultValue: function(){ 
                //     var currentTime = new Date(); 
                //     var month = parseInt(currentTime.getMonth() + 1); 
                //     month = month <= 9 ? "0"+month : month; 
                //     var day = currentTime.getDate(); 
                //     day = day <= 9 ? "0"+day : day; 
                //     var year = currentTime.getFullYear(); 
                //     return day+"/"+month + "/"+year; 
                //   } 

                // },

                // {
                // 	"name":"parent_id",
                // 	"hidden":true
                // }
            ],
				viewrecords: true,
                width: 1200,
                height: 200,
                rowNum: 10,//jumlah baris data yang akan ditampilkan pada setiap halaman
                rowList: [10,20,30], //rowList adalah daftar opsi jumlah baris yang dapat dipilih oleh pengguna untuk ditampilkan pada setiap halaman
                pager: "#jqGridPager",
                caption: "Master Penjualan",
                sortname: 'id',
                autoencode: true,
                sortorder: 'asc',
                height: 'auto',
                // loadonce: false,
                rownumbers: true,
	            rownumWidth: 40,
                viewrecords: true,
                gridview: true,
                search: true,
postData: {
searchField: "",
searchOper: "",
searchString: ""
},
                postData: {
        filters: function() {
            return JSON.stringify(jQuery("#jqGridPager").jqGrid("getGridParam", "postData").filters);
        }
    },
    
    search: true,
    postData: {
        filters: ""
    }
                
        });
        
        // '#jqGridPager', null,
        jQuery("#grid_id").jqGrid('filterToolbar',{
            // groupOp: 'AND',
    defaultSearch: "cn",
    searchOnEnter: true,
    searchOperators: true,
    stringResult: true,
            gridComplete: function(){ 
        $("#grid_id").setGridParam({datatype: 'json'}); 
}
        });
        jQuery("#grid_id").on("change keyup", function() {
    var search_value = jQuery(this).val();
    jQuery("#grid_id").setGridParam({
        postData: {
            filters: JSON.stringify({
                groupOp: "AND",
                rules: [
                    { field: "column_name", op: "cn", data: search_value }
                ]
            }),
            multipleSearch: true,
            
        }
    }).trigger("reloadGrid");
});
        jQuery("#grid_id").jqGrid('navGrid', '#jqGridPager', null, {
          
            recreateForm: true,
            beforeShowForm: function (form) {
                form[0].querySelector('#no_invoice').setAttribute('readonly', 'readonly')
            }

        }, {
            recreateForm: true
        });
    
      
      

    </script>
</body>

</html>