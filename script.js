<!DOCTYPE html>
  <html>

  <head>
    <meta content="text/html; charset=utf-8" />
    <title>My First Grid</title>
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="css/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/trirand/ui.jqgrid.css" /> -->
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="themes/ui.datepicker.css" /> -->
    <link rel="stylesheet" href="assets/jqgrid/550/css/trirand/ui.jqgrid.css">
    <link rel="stylesheet" href="assets/jqgrid/themes/redmond/jquery-ui.css">
    <link rel="stylesheet" href="assets/jqgrid/themes/redmond/jquery-ui.theme.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style type="text/css">
      * {
        font-size: 12px;
      }

      .text-validation {
        font-size: 10px;
        color: red;
      }

      .detail-validation {
        font-size: 10px;
        color: #fff;
      }

      /*.form-input{
    margin-top: 5px;
  }*/
    </style>
    


    <!-- <script src="assets/autoNumeric.js"></script> -->
  </head>

  <body>
    <table id="listHeader">
      <div id="headDialog"></div>
    </table>
    <div id="pager"></div>
    <br><br>
    <!-- TBL Detail -->
    <table id="listDetail"></table>



    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="assets/jqgrid/550/js/jquery-ui.min.js"></script>
    <script src="assets/jqgrid/550/js/trirand/i18n/grid.locale-en.js"></script>
    <script src="assets/jqgrid/550/js/trirand/src/jquery.jqGrid.js"></script>
    <!-- <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/jquery-ui.min.js" type="text/javascript"></script>
    <script src="js/ui.datepicker.js" type="text/javascript"></script> 
    <script src="js/trirand/i18n/grid.locale-en.js" type="text/javascript"></script>
    <script src="js/trirand/jquery.jqGrid.min.js" type="text/javascript"></script>-->
    <script src="inputmask/dist/jquery.inputmask.js"></script>
    <script src="inputmask/dist/bindings/inputmask.binding.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.6.0/autoNumeric.min.js"></script>
    <script>

      let selectId = null;
      // let firstId = $("#listHeader").getDataIDs()[0];
      let cellDelete;
      let idDel
      let activeGrid = '#listHeader'

    $(document).ready(function() { 

       
        $("#listHeader").jqGrid({
              url: 'show_header.php',
              datatype: "json",
              mtype: "GET",
              colNames: ["No Faktur", "Tgl Faktur", "Nama Pelanggan", "Jenis Kelamin", "Keterangan"],
              colModel: [
                {
                  name: 'no_faktur',
                  label: 'no_faktur',
                  index: 'no_faktur',
                  editable: true,
                  required: true
                },
                {
                  name: 'tgl_faktur',
                  label: 'tgl_faktur',
                  index: 'tgl_faktur',
                  editable: true,
                  sorttype: "date",
                  required: true

                },
                {
                  name: 'nama_pelanggan',
                  label: 'nama_pelanggan',
                  index: 'nama_pelanggan',
                  editable: true,
                  required: true
                },
                {
                  name: 'jenis_kelamin',
                  label: 'jenis_kelamin',
                  index: 'jenis_kelamin',
                  editable: true,
                  required: true
                },
                {
                  name: 'keterangan',
                  label: 'keterangan',
                  index: 'keterangan',
                  editable: true,
                  required: true
                },
              ],

              caption: 'Data Header',
              height: 'auto',
              viewrecords: true,
              gridview: true,
              rownumbers: true,
              rowList: [5, 10],
              rowNum: 10,
              sortname: "no_faktur",
              sortorder: "asc",
              width: 1310,
              pager: '#pager',
              onSelectRow: function() {
                
                    var rowid = $('#listHeader').jqGrid('getGridParam', 'selrow')
                    $("#listDetail").jqGrid('setGridParam',{url:"show_detail.php?id="+rowid}).trigger('reloadGrid')
                   tampil(rowid)
              },
              loadComplete: function(data) {
                $(document).unbind('keydown')
                customBindKeys()
                if(selectId == null){
                  var firstRow = $('#listHeader').getDataIDs()[0]
                    // $('#'+firstRow).click();
                      $('#listHeader').jqGrid('setSelection', firstRow);
                     
                      
                      console.log(selectId)
                }else{

                  if(cellDelete == 'hapus')
                  {   
                      $('#listHeader').jqGrid('getCell',idDel,'rn')
                      
                      console.log($('#listHeader').jqGrid('getCell',idDel,'rn'))
                      $('#'+idDel).click();

                      cellDelete = 'no'

                  }else{
                      $('#listHeader').trigger('reloadGrid');
                      $('#listHeader').jqGrid('setSelection', selectId);
                      // $('#'+selectId).click();
                      // console.log(selectId)
                  }
                }
              }
              // 
            })


            //SET DEFAULT
            .jqGrid('navGrid', '#pager', {
              search: false,
              refresh: false,
              add: false,
              edit: false,
              del: false
            })
            
          // $('#listHeader').jqGrid('bindKeys');

            

          
          .jqGrid('filterToolbar', {
              // autosearch: true,
              stringResult: true,
              searchOnEnter: false,
              defaultSearch: "cn",
              multipleSearch: true,
              disabledKeys: [33, 34, 35, 36, 37, 38, 39, 40],
            });

          //SET BUTTON ADD
          $("#listHeader").jqGrid('navButtonAdd', "#pager", {
              caption: "Add",
              buttonicon: "ui-icon-plus",
              onClickButton: function() {
                // activeGrid = undefined
                addNew();
              },
              position: "last",
              title: "Add",
              id: "addHeader",
              cursor: "pointer"
          });

          //SET BUTTON EDIT
          $("#listHeader").jqGrid('navButtonAdd', "#pager", {
            caption: "Edit",
            buttonicon: "ui-icon-pencil",
            onClickButton: function() {
              var rowid = $('#listHeader').jqGrid('getGridParam', 'selrow');
              // activeGrid = undefined
              edit(rowid);
            },
            position: "last",
            title: "Edit",
            id: "editHeader",
            cursor: "pointer"
          });


          //SET BUTTON DELETE
          $("#listHeader").jqGrid('navButtonAdd', "#pager", {
            caption: "Delete",
            buttonicon: "ui-icon-trash",
            onClickButton: function() {
              var rowid = $('#listHeader').jqGrid('getGridParam', 'selrow');
              hapus(rowid);
            },
            position: "last",
            title: "Delete",
            id: "deleteHeader",
            cursor: "pointer"
          });

          //SET INPUT GLOBAL SEARCH
          $(".ui-jqgrid-titlebar").after("<div class='ui-jqgrid-titlebar ui-widget-header'>Global Search : <input type='text' name='gsearch' id='gsearch' class='ui-widget-content ui-corner-all'></div>");

          $(document).on('input', '#gsearch', function() {
            let text = $(this).val()

            //ada banyak parameter grid, salah satunya postData. untuk nngeliat bisa bikin getGridParam
            //untuk nambahin isi dari parameternya bisa dibuat pake setGridParam
            //jadi untuk search, set dulu data baru untuk param postData. lalu di trigger dengan reloadGrid
            //maka setelah itu, isi param postData bisa bertambah sesuai yg diinginkan
            $('#listHeader').jqGrid('setGridParam', {
              search: 'gSearch',
              postData: {
                global_search: text
              }
            }).trigger('reloadGrid')

          })

          $("#gsh_listHeader_rn").append("<button id='reset' style='width:100%; height:100%'>X</button>");


          //SET BUTTON EXPORT
          $("#listHeader").jqGrid('navButtonAdd', "#pager", {
            caption: "Export",
            buttonicon: "ui-icon-document",
            onClickButton: function() {
              var gSearch = $('#listHeader').jqGrid('getGridParam', 'postData').global_search;

              if($('#listHeader').jqGrid('getGridParam', 'postData')._search == false && !gSearch)
              {
                ekspor()
              }
              if(gSearch != null)
              {
                ekspor('undefined','undefined',gSearch)
              }
              if($('#listHeader').jqGrid('getGridParam', 'postData')._search == true){

                let dataFilter = JSON.parse($('#listHeader').jqGrid('getGridParam', 'postData').filters)['rules'][0].data;
                let fieldFilter = JSON.parse($('#listHeader').jqGrid('getGridParam', 'postData').filters)['rules'][0].field;
                ekspor(dataFilter, fieldFilter);
              }
              // ekspor()
            },
            position: "last",
            title: "Export",
            id: "ekspor",
            cursor: "pointer"
          });

          //SET BUTTON REPORT
          $("#listHeader").jqGrid('navButtonAdd', "#pager", {
            caption: "Report",
            buttonicon: "ui-icon-disk",
            onClickButton: function() {
              var gSearch = $('#listHeader').jqGrid('getGridParam', 'postData').global_search;

              if($('#listHeader').jqGrid('getGridParam', 'postData')._search == false && !gSearch)
              {
                report()
              }
              if(gSearch != null)
              {
                report('undefined','undefined',gSearch)
              }
              if($('#listHeader').jqGrid('getGridParam', 'postData')._search == true){

                let dataFilter = JSON.parse($('#listHeader').jqGrid('getGridParam', 'postData').filters)['rules'][0].data;
                let fieldFilter = JSON.parse($('#listHeader').jqGrid('getGridParam', 'postData').filters)['rules'][0].field;
                report(dataFilter, fieldFilter);
              }
              // report()
            },
            position: "last",
            title: "Report",
            id: "report",
            cursor: "pointer"
          });


    })
      

      



      function checkFields() {
          var found = false;
          $('.cek_detail').each(function(){
              if($(this).val()){
                  found = true;
                  return false;
              }else{
                return true
              }

          });
           if (found == false)  {
              
              alert("all fields are empty");
              }else{
                return true;
              }


      }
      //TAMPIL FORM DIALOG ADD
      function addNew() {

        $('#headDialog').load('add_form.php').dialog({
          modal: true,
          height: 500,
          width: 900,
          position: [0, 0],
          title: 'Add Header',

          buttons: {
            'Save': function() {
              let sortname = $('#listHeader').jqGrid('getGridParam', 'sortname')
              let sortorder = $('#listHeader').jqGrid('getGridParam', 'sortorder')
              let rowNum = parseInt($('#listHeader').jqGrid('getGridParam', 'rowNum'))

                  $.ajax({
                      url: 'add_header.php?sortname=' + sortname + '&sortorder=' + sortorder,
                      method: 'POST',
                      dataType: 'JSON',
                      data: $('#add_header').serializeArray(),
                      success: function(data) {

                        let id = data['id']
                        let countRow = parseInt(data['count'])
                        let page = Math.ceil(countRow / rowNum)
                        let gridPage = $('#listHeader').getGridParam('page')

                        selectId = id


                        $('#headDialog').dialog('close')

                        if (page >= gridPage) {
                          $('#listHeader').trigger('reloadGrid')
                        }

                        setTimeout(() => {
                          $('#listHeader').trigger('reloadGrid', {
                            page: page,
                            id: id,
                            position: id
                          })
                        }, 100);
                      },
                      error: function(error) {
                        alert(error.responseJSON.error)
                      }
                  });
              
            },
            'Cancel': function() {
              $('#headDialog').dialog("close");
            }
          }
        })
      }

      function edit(rowid) {
        var id = rowid;
        $('#headDialog').load('add_form.php?id=' + id).dialog({
          modal: true,
          height: 500,
          width: 900,
          position: [0, 0],
          title: 'Edit Header',
          buttons: {
            'Save': function() {
              let sortname = $('#listHeader').jqGrid('getGridParam', 'sortname')
              let sortorder = $('#listHeader').jqGrid('getGridParam', 'sortorder')
              let rowNum = parseInt($('#listHeader').jqGrid('getGridParam', 'rowNum'))
              
              
                $.ajax({
                  url: 'add_header.php?id=' + id + '&sortname=' + sortname + '&sortorder=' + sortorder,
                  method: 'POST',
                  dataType: 'JSON',
                  data: $('#edit_header').serialize(),
                  success: function(data) {
                      let id = data['id']
                      let countRow = data['count']
                      let page = Math.ceil(countRow / rowNum)

                      selectId = id
                      console.log(selectId)
                      $('#headDialog').dialog('close')
                      $('#listHeader').trigger('reloadGrid', {
                        page: page,
                        id: id,
                        position: id
                      })
                  },
                  error: function(error) {
                    alert(error.responseJSON.error)
                  }
                });
              


            },
            // alert('no faktur harus diisi')

            'Cancel': function() {
              $('#headDialog').dialog("close");
            }
          }

        })
      }


      function hapus(rowid) {
        var id = rowid;
        $('#headDialog').load('add_form.php?id_del=' + id).dialog({
          modal: true,
          height: 500,
          width: 900,
          position: [0, 0],
          title: 'Delete Header',
          buttons: {
            'Delete': function() {
              let sortname = $('#listHeader').jqGrid('getGridParam', 'sortname')
              let sortorder = $('#listHeader').jqGrid('getGridParam', 'sortorder')
              let rowNum = parseInt($('#listHeader').jqGrid('getGridParam', 'rowNum'))

              $.ajax({
                url: 'delete.php?id=' + id + '&sortname=' + sortname + '&sortorder=' + sortorder,
                method: 'POST',
                dataType: 'JSON',
                data: $('#delete_header').serialize(),
                success: function(data) {
                  
                  $('#headDialog').dialog('close')
                  $('#listHeader').trigger('reloadGrid')
                  cellDelete = 'hapus'
                  selectId = data['id']
                  idDel = data['id']
                },
                error: function(data) {
                  alert('Terjadi Kesalahan')
                }
              });
            },
            'Cancel': function() {
              $('#headDialog').dialog("close");
            }
          }
        });
      }

      // SHOW DETAIL
      function tampil(rowid){
        

        $("#listDetail").jqGrid({
           url: 'show_detail.php?id='+rowid,
           datatype: "json",
           mtype: "GET",
           colNames: ["No Faktur", "Kode Barang", "Nama Barang", "QTY", "Harga", "Total"],
           colModel: [
                     {name:'no_faktur', label: 'no_faktur', index:'no_faktur', editable:true, required: true},
                     {name:'kode_barang', label: 'kode_barang', index:'kode_barang', editable:true, required: true},
                     {name:'nama_barang', label: 'nama_barang', index:'nama_barang', editable:true, required: true},
                     {name:'qty', label: 'qty', index:'qty', editable:true, required: true, formatter: 'number', formatoptions:{thousandsSeparator: ",", decimalPlaces: 0}, align: "right"
                     },
                     {name:'harga', label: 'harga', index:'harga', editable:true, editoptions: {size:10}, required: true, formatter: 'number', formatoptions:{thousandsSeparator: ",", decimalPlaces: 0}, align: "right"},
                     // {name:'total', label: 'total', index:'total', editable:true, required: true}, 
                     
                     {label: 'total', name: 'total', index: 'total', formatter: 'number', formatoptions:{thousandsSeparator: ",", decimalPlaces: 0}, align: "right"}
           ],    

           caption : 'Data Detail',
           height: 'auto',
           viewrecords: true,
           gridview: true,
           rownumbers:true,
           rowList: [5,10],
           rowNum: 10,
           sortname: "id_detail",
           sortorder: "asc",
           width:1310,
           footerrow:true,
           userDataOnFooter: true,
            loadComplete : function(){
              
            var $grid = $("#listDetail");
            var colSum = $grid.jqGrid('getCol','total');
            // var colSum = $grid.jqGrid('getCol','total',false,'sum');
         
           function untukTotal(colSum) {
                    var totalSum = 0;

                     for(i=0; i<colSum.length; i++){
                        var ambil = parseInt(colSum[i])
                        totalSum += ambil
                     }
                     return totalSum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }

            $grid.jqGrid('footerData','set',{harga: "Total:", total : untukTotal(colSum)}, false);
            }
       });
      }

       
      $(document).on('click', '#reset', function() {
        $('#gs_no_faktur').val('')
        $('#gs_tgl_faktur').val('')
        $('#gs_nama_pelanggan').val('')
        $('#gs_jenis_kelamin').val('')
        $('#gs_keterangan').val('')
        $('#gsearch').val('')

        $('#listHeader').jqGrid('setGridParam', {
          postData: null
        })

        $('#listHeader').jqGrid('setGridParam', {
          search: false,
          postData: {
            _search: false,
            rows: 10,
            page: 1,
            sidx: 'no_faktur',
            sord: 'asc',
          }
        }).trigger('reloadGrid')

      });

      

      

      function ekspor(dataFilter, fieldFilter, gSearch) {
        console.log(dataFilter)
        console.log(fieldFilter)
        console.log(gSearch)
        $('#headDialog').load('ekspor_form.php').dialog({
          modal: true,
          height: 200,
          width: 400,
          position: [0, 0],
          title: 'Ekspor Data',

          buttons: {
            'Ekspor': function() {
              let dari = $('#dari').val()
              let sampai = $('#sampai').val()

              let sortname = $('#listHeader').jqGrid('getGridParam', 'sortname')
              let sortorder = $('#listHeader').jqGrid('getGridParam', 'sortorder')
              let xhr = new XMLHttpRequest()
              xhr.open('GET', 'export.php?sn='+sortname+'&sd='+sortorder+'&data='+dataFilter+'&field='+fieldFilter+'&gSearch='+gSearch + '&dari=' + dari + '&sampai=' + sampai, true)
              xhr.responseType = 'arraybuffer'

              xhr.onload = function(e) {
                if (this.status === 200) {
                  if (this.response !== undefined) {
                    let blob = new Blob([this.response], {
                      type: "application/vnd.ms-excel"
                    })
                    let link = document.createElement('a')

                    link.href = window.URL.createObjectURL(blob)
                    link.download = 'laporan.xlsx'
                    link.click()

                    submitButton.removeAttr('disabled')
                  }
                }

                if(this.status === 400)
                {
                  alert('Nilai dari tidak boleh lebih besar daripada nilai sampai')
                }
              }

              xhr.onerror = () => {
                submitButton.removeAttr('disabled')
              }

              xhr.send()
             
            },
            'Cancel': function() {
              $('#headDialog').dialog("close");
            }
          }
        });
      }

      
      function report(dataFilter, fieldFilter, gSearch){
        

         $('#headDialog').load('report_form.php').dialog({
          modal: true,
          height: 200,
          width: 400,
          position: [0, 0],
          title: 'Download Report',
          buttons: {
            'Report': function() {
              let sortname = $('#listHeader').jqGrid('getGridParam', 'sortname')
              let sortorder = $('#listHeader').jqGrid('getGridParam', 'sortorder')
              let dari = $('#dari').val()
              let sampai = $('#sampai').val()

              if(sampai < dari)
              {
                alert('Nilai dari tidak boleh lebih besar daripada nilai sampai')
              }else{
                window.open('proses_report_final.php?sn='+sortname+'&sd='+sortorder+'&dFilter='+dataFilter+'&field='+fieldFilter+'&gSearch='+gSearch + '&dari=' + dari + '&sampai=' + sampai, "_blank")
              }
            },
            'Cancel': function() {
              $('#headDialog').dialog("close");
            }
          }
        });
      }

      $.fn.keyControl = function (e) {
        var l = $.extend(
          {
            onEnter: null,
            onSpace: null,
            onLeftKey: null,
            onRightKey: null,
            scrollingRows: !0,
          },
          e || {}
        )
        return this.each(function () {
          var s = this

          $("body").is("[role]") || $("body").attr("role", "application"),
            (s.p.scrollrows = l.scrollingRows),
            $(s)
              .on("keydown", function (e) 
              {
                var t,
                  i,
                  r = $(s).find("tr[tabindex=0]")[0],
                  o = s.p.treeReader.expanded_field

                if (r) 
                {
                  var n = s.p.selrow,
                    a = s.p._index[$.jgrid.stripPref(s.p.idPrefix, r.id)]
                  var currentPage = $(s).getGridParam('page')
                  var lastPage = $(s).getGridParam('lastpage')
                  var row = $(this).jqGrid('getGridParam', 'postData').rows

                  if (
                    33 === e.keyCode ||
                    34 === e.keyCode ||
                    35 === e.keyCode ||
                    36 === e.keyCode ||
                    37 === e.keyCode ||
                    38 === e.keyCode ||
                    39 === e.keyCode ||
                    40 === e.keyCode
                  ) {
                    if (33 === e.keyCode) {
                      triggerClick = true
                      if (currentPage > 1) {
                        $(s).jqGrid('setGridParam', { "page": currentPage - 1 }).trigger('reloadGrid')
                      }
                      $(s).triggerHandler("jqGridKeyUp", [t, n, e]),
                        $(this).isFunction(l.onUpKey) && l.onUpKey.call(s, t, n, e),
                        e.preventDefault()
                    }
                    if (34 === e.keyCode) {
                      triggerClick = true
                      if (currentPage !== lastPage) {
                        $(s).jqGrid('setGridParam', { "page": currentPage + 1 }).trigger('reloadGrid')
                      }
                      $(s).triggerHandler("jqGridKeyUp", [t, n, e]),
                        $(this).isFunction(l.onUpKey) && l.onUpKey.call(s, t, n, e),
                        e.preventDefault()
                    }
                    if (35 === e.keyCode) {
                      triggerClick = true
                      if (currentPage !== lastPage) {
                        $(s).jqGrid('setGridParam', { "page": lastPage}).trigger('reloadGrid')
                        if (e.ctrlKey) {
                          if ($(s).jqGrid('getGridParam', 'selrow') !== $('#listHeader').find(">tbody>tr.jqgrow").filter(":last").attr('id')) {
                            $(s).jqGrid('setSelection', $(s).find(">tbody>tr.jqgrow").filter(":last").attr('id')).trigger('reloadGrid')
                          }
                        }
                      }
                      if (e.ctrlKey) {
                        if ($(s).jqGrid('getGridParam', 'selrow') !== $('#listHeader').find(">tbody>tr.jqgrow").filter(":last").attr('id')) {
                          $(s).jqGrid('setSelection', $(s).find(">tbody>tr.jqgrow").filter(":last").attr('id')).trigger('reloadGrid')
                        }
                      }
                      $(s).triggerHandler("jqGridKeyUp", [t, n, e]),
                        $(this).isFunction(l.onUpKey) && l.onUpKey.call(s, t, n, e),
                        e.preventDefault()
                    }
                    if (36 === e.keyCode) {
                      triggerClick = true
                      if (e.ctrlKey) {
                        if ($(s).jqGrid('getGridParam', 'selrow') !== $('#listHeader').find(">tbody>tr.jqgrow").filter(":first").attr('id')) {
                          $(s).jqGrid('setSelection', $(s).find(">tbody>tr.jqgrow").filter(":first").attr('id'))
                        }
                      }
                      $(s).jqGrid('setGridParam', { "page": 1}).trigger('reloadGrid')
                      $(s).triggerHandler("jqGridKeyUp", [t, n, e]),
                        $(this).isFunction(l.onUpKey) && l.onUpKey.call(s, t, n, e),
                        e.preventDefault()
                    }
                    if (38 === e.keyCode) {
                      if (e.ctrlKey) {
                       if ($(s).jqGrid('getGridParam', 'selrow') !== $('#listHeader').find(">tbody>tr.jqgrow").filter(":first").attr('id')) {
                         $(s).jqGrid('setSelection', $(s).find(">tbody>tr.jqgrow").filter(":first").attr('id')).trigger('reloadGrid')
                       } else {
                         return false
                       }
                      } else {
                        if (
                          ((t = ""), (i = r.previousSibling) && $(i).hasClass("jqgrow"))
                        ) {
                          if ($(i).is(":hidden")) {
                            for (; i; )
                              if (
                                ((i = i.previousSibling),
                                !$(i).is(":hidden") && $(i).hasClass("jqgrow"))
                              ) {
                                t = i.id
                                break
                              }
                          } else t = i.id
                          $(s).jqGrid("setSelection", t, !0, e)
                       }
                      }
                      $(s).triggerHandler("jqGridKeyUp", [t, n, e]),
                        $(this).isFunction(l.onUpKey) && l.onUpKey.call(s, t, n, e),
                        e.preventDefault()
                    }
                    if (40 === e.keyCode) {
                      console.log('test')
                      if (e.ctrlKey) {
                       if ($(s).jqGrid('getGridParam', 'selrow') !== $('#listHeader').find(">tbody>tr.jqgrow").filter(":last").attr('id')) {
                         $(s).jqGrid('setSelection', $(s).find(">tbody>tr.jqgrow").filter(":last").attr('id')).trigger('reloadGrid')
                       } else {
                         return false
                       }
                      } else {
                        if (
                          ((t = ""), (i = r.nextSibling) && $(i).hasClass("jqgrow"))
                        ) {
                          if ($(i).is(":hidden")) {
                            for (; i; )
                              if (
                                ((i = i.nextSibling),
                                !$(i).is(":hidden") && $(i).hasClass("jqgrow"))
                              ) {
                                t = i.id
                                break
                              }
                          } else t = i.id
                          $(s).jqGrid("setSelection", t, !0, e)
                       }
                      }
                      $(s).triggerHandler("jqGridKeyDown", [t, n, e]),
                        $(this).isFunction(l.onDownKey) &&
                          l.onDownKey.call(s, t, n, e),
                        e.preventDefault()
                    }
                    37 === e.keyCode &&
                      (s.p.treeGrid &&
                        s.p.data[a][o] &&
                        $(r).find("div.treeclick").trigger("click"),
                      $(s).triggerHandler("jqGridKeyLeft", [s.p.selrow, e]),
                      $(this).isFunction(l.onLeftKey) &&
                        l.onLeftKey.call(s, s.p.selrow, e)),
                      39 === e.keyCode &&
                        (s.p.treeGrid &&
                          !s.p.data[a][o] &&
                          $(r).find("div.treeclick").trigger("click"),
                        $(s).triggerHandler("jqGridKeyRight", [s.p.selrow, e]),
                        $(this).isFunction(l.onRightKey) &&
                          l.onRightKey.call(s, s.p.selrow, e))
                  } else
                   13 === e.keyCode
                      ? ($(s).triggerHandler("jqGridKeyEnter", [s.p.selrow, e]),
                        $(this).isFunction(l.onEnter) &&
                          l.onEnter.call(s, s.p.selrow, e))
                      : 32 === e.keyCode &&
                        ($(s).triggerHandler("jqGridKeySpace", [s.p.selrow, e]),
                        $(this).isFunction(l.onSpace) &&
                          l.onSpace.call(s, s.p.selrow, e))
                  
                }
              })
              .on("click", function (e) {
                $(e.target).is("input, textarea, select") ||
                  $(e.target, s.rows).closest("tr.jqgrow").focus()
              })
        })
      }

      $.fn.isFunction = function (e) {
        return "function" == typeof e
      }

function customBindKeys() {
  $(document).keydown(function(e) {
    if (
      e.keyCode == 38 ||
      e.keyCode == 40 ||
      e.keyCode == 33 ||
      e.keyCode == 34 ||
      e.keyCode == 35 ||
      e.keyCode == 36
    ) {
      e.preventDefault();

      if (activeGrid !== undefined) {
        var gridArr = $(activeGrid).getDataIDs();
        var selrow = $(activeGrid).getGridParam("selrow");
        var curr_index = 0;
        var currentPage = $(activeGrid).getGridParam('page')
        var lastPage = $(activeGrid).getGridParam('lastpage')
        var row = $(activeGrid).jqGrid('getGridParam', 'postData').rows

        for (var i = 0; i < gridArr.length; i++) {
          if (gridArr[i] == selrow) curr_index = i;
        }

        switch (e.keyCode) {
          case 33:
            if (currentPage > 1) {
              $(activeGrid).jqGrid('setGridParam', { "page": currentPage - 1 }).trigger('reloadGrid')
            }
            break
          case 34:
            if (currentPage !== lastPage) {
              $(activeGrid).jqGrid('setGridParam', { "page": currentPage + 1 }).trigger('reloadGrid')
            }
          case 38:
            if (curr_index - 1 >= 0)
            $(activeGrid)
              .resetSelection()
              .setSelection(gridArr[curr_index - 1])
            break
          case 40:
            if (curr_index + 1 < gridArr.length)
            $(activeGrid)
              .resetSelection()
              .setSelection(gridArr[curr_index + 1])
              break
        }
      }
    }
  })
}

/**
 * Set Home, End, PgUp, PgDn
 * to move grid page
 */
// function setCustomBindKeys(grid) {
//   $(document).on("keydown", function (e) {
//     if (!sidebarIsOpen) {
//       if (
//         e.keyCode == 33 ||
//         e.keyCode == 34 ||
//         e.keyCode == 35 ||
//         e.keyCode == 36 ||
//         e.keyCode == 38 ||
//         e.keyCode == 40
//       ) {
//         e.preventDefault();

//         var gridIds = $("#jqGrid").getDataIDs();
//         var selectedRow = $("#jqGrid").getGridParam("selrow");
//         var currentPage = $(grid).getGridParam("page");
//         var lastPage = $(grid).getGridParam("lastpage");
//         var currentIndex = 0;
//         var row = $(grid).jqGrid("getGridParam", "postData").rows;

//         for (var i = 0; i < gridIds.length; i++) {
//           if (gridIds[i] == selectedRow) currentIndex = i;
//         }

//         if (triggerClick == false) {
//           if (33 === e.keyCode) {
//             if (currentPage > 1) {
//               $(grid)
//                 .jqGrid("setGridParam", {
//                   page: parseInt(currentPage) - 1,
//                 })
//                 .trigger("reloadGrid");

//               triggerClick = true;
//             }
//             $(grid).triggerHandler("jqGridKeyUp"), e.preventDefault();
//           }
//           if (34 === e.keyCode) {
//             if (currentPage !== lastPage) {
//               $(grid)
//                 .jqGrid("setGridParam", {
//                   page: parseInt(currentPage) + 1,
//                 })
//                 .trigger("reloadGrid");

//               triggerClick = true;
//             }
//             $(grid).triggerHandler("jqGridKeyUp"), e.preventDefault();
//           }
//           if (35 === e.keyCode) {
//             if (currentPage !== lastPage) {
//               $(grid)
//                 .jqGrid("setGridParam", {
//                   page: lastPage,
//                 })
//                 .trigger("reloadGrid");
//               if (e.ctrlKey) {
//                 if (
//                   $(grid).jqGrid("getGridParam", "selrow") !==
//                   $("#customer")
//                     .find(">tbody>tr.jqgrow")
//                     .filter(":last")
//                     .attr("id")
//                 ) {
//                   $(grid)
//                     .jqGrid(
//                       "setSelection",
//                       $(grid)
//                         .find(">tbody>tr.jqgrow")
//                         .filter(":last")
//                         .attr("id")
//                     )
//                     .trigger("reloadGrid");
//                 }
//               }

//               triggerClick = true;
//             }
//             if (e.ctrlKey) {
//               if (
//                 $(grid).jqGrid("getGridParam", "selrow") !==
//                 $("#customer")
//                   .find(">tbody>tr.jqgrow")
//                   .filter(":last")
//                   .attr("id")
//               ) {
//                 $(grid)
//                   .jqGrid(
//                     "setSelection",
//                     $(grid).find(">tbody>tr.jqgrow").filter(":last").attr("id")
//                   )
//                   .trigger("reloadGrid");
//               }
//             }
//             $(grid).triggerHandler("jqGridKeyUp"), e.preventDefault();
//           }
//           if (36 === e.keyCode) {
//             if (currentPage > 1) {
//               if (e.ctrlKey) {
//                 if (
//                   $(grid).jqGrid("getGridParam", "selrow") !==
//                   $("#customer")
//                     .find(">tbody>tr.jqgrow")
//                     .filter(":first")
//                     .attr("id")
//                 ) {
//                   $(grid).jqGrid(
//                     "setSelection",
//                     $(grid).find(">tbody>tr.jqgrow").filter(":first").attr("id")
//                   );
//                 }
//               }
//               $(grid)
//                 .jqGrid("setGridParam", {
//                   page: 1,
//                 })
//                 .trigger("reloadGrid");

//               triggerClick = true;
//             }
//             $(grid).triggerHandler("jqGridKeyUp"), e.preventDefault();
//           }
//           if (38 === e.keyCode) {
//             if (currentIndex - 1 >= 0) {
//               $(grid)
//                 .resetSelection()
//                 .setSelection(gridIds[currentIndex - 1]);
//             }
//           }
//           if (40 === e.keyCode) {
//             if (currentIndex + 1 < gridIds.length) {
//               $(grid)
//                 .resetSelection()
//                 .setSelection(gridIds[currentIndex + 1]);
//             }
//           }
//         }

//         $(".ui-jqgrid-bdiv").find("tbody").animate({
//           scrollTop: 200,
//         });
//         $(".table-success").position().top > 300;
//       }
//     }
//   });
// }


      
    </script>
  </body>

  </html>