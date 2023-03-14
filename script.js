const customerTable = '#customer'
const customerPager = '#customerPager'
const customerDialog = '#customerDialog'
const customerForm = '#customerForm'

const orderTable = '#order'
const orderPager = '#orderPager'
const orderDialog = '#orderDialog'
const orderForm = '#orderForm'

let indexRow = 0
let sortName = 'customer_name'
let timeout = null
let highlightSearch
let noInvoice
let currentSearch
let postData
let ordersPostData
let triggerClick = true
let activeGrid = '#customer'
let socket

$(window).resize(function() {
	$(customerTable).setGridWidth($(window).width() - 15)
	$(orderTable).setGridWidth($(window).width() - 15)
})

$(document).ready(function() {
	loadCustomerTable()
	loadOrderTable()
  loadSocket()

	$('#t_customer').html(`
		<div id="global_search">
			<label> Global search </label>
			<input id="gs_global_search" class="ui-widget-content ui-corner-all" style="padding: 5px;" globalsearch="true" clearsearch="true">
		</div>
	`)

	$(document).on('click', '#clearFilter', function () {
		currentSearch = undefined
	  $('[id*="gs_"]').val('')
	  $(customerTable).jqGrid('setGridParam', { postData: null })
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
	})

	$('[id*=gs_]').each(function(i, el) {
		$(el).on('focus', function(el) {
			currentSearch = $(this)
		})
	})

  $('#pg_customerPager table tr').first().attr('align', 'center')
})

function loadSocket() {
  socket = io('http://' + document.domain + ':3000')
  
  console.log(socket)
}

function convertHanzi() {
	let td = $(document).find('td')

	td.map(function(i, td) {
		$(td).text().match(/[\u4E00-\u9FCC\u3400-\u4DB5\uFA0E\uFA0F\uFA11\uFA13\uFA14\uFA1F\uFA21\uFA23\uFA24\uFA27-\uFA29]|[\ud840-\ud868][\udc00-\udfff]|\ud869[\udc00-\uded6\udf00-\udfff]|[\ud86a-\ud86c][\udc00-\udfff]|\ud86d[\udc00-\udf34\udf40-\udfff]|\ud86e[\udc00-\udc1d]/) !== null
			? $(td).text(`${$(td).text()} (${PinyinHelper.convertToPinyinString($(td).text(), ' ', PinyinFormat.WITH_TONE_MARK)})`)
			: ''
	})
}

function loadCustomerTable() {
	$(customerTable).jqGrid({
		url: baseUrl + 'customer/get',
		datatype: 'JSON',
		caption: 'Customers Datassss',
		rownumbers: true,
		height: 277,
		autowidth: true,
		shrinkToFit: false,
		viewrecords: true,
		sortable: true,
		sortname: sortName,
		pager: $(customerPager),
   	rowNum:10,
   	rowList:[10,20,30],
		toolbar: [true, 'top'],
		colModel: [
			{
				index: 'no_invoice',
				name: 'no_invoice',
				label: 'No Invoice',
			},
			{
				index: 'tgl_invoice',
				name: 'tgl_invoice',
				label: 'Tanggal Invoice',
				formatter: 'date',
		    formatoptions: {
		    	newformat:'d-m-Y'
		    },
			},
			{
				index: 'customer_name',
				name: 'customer_name',
				label: 'Customer Name',
			},
			{
				index: 'first_name',
				name: 'first_name',
				label: 'First Name',
			},
			{
				index: 'last_name',
				name: 'last_name',
				label: 'Last Name',
			},
			{
				index: 'gender_id',
				name: 'gender',
				label: 'Gender',
				stype: 'select',
				searchoptions: {
					value: ':ALL;1:LAKI - LAKI;2:PEREMPUAN',
				},
			},
			{
				index: 'phone',
				name: 'phone',
				label: 'Phone',
			},
			{
				index: 'address',
				name: 'address',
				label: 'Address',
			},
			{
				index: 'city',
				name: 'city',
				label: 'City',
			},
			{
				index: 'country',
				name: 'country',
				label: 'Country',

			},
		],
		onSelectRow: function(id) {
			activeGrid = '#customer'
			indexRow = $(this).jqGrid('getCell', id, 'rn') - 1
    	page = $(this).jqGrid('getGridParam', 'page') - 1
    	rows = $(this).jqGrid('getGridParam', 'postData').rows
    	if (indexRow >= rows) indexRow = (indexRow - rows * page)

			noInvoice = $(this)
				.jqGrid('getRowData', id).no_invoice
				.replace(/(<([^>]+)>)/ig,"")

			if (noInvoice) showOrder(noInvoice)
		},
		loadComplete: function() {
			convertHanzi()
			$(document).unbind('keydown')
			customBindKeys()
			postData = $(this).jqGrid('getGridParam', 'postData')

			setTimeout(function() {
				$('#customer tbody tr td:not([aria-describedby=customer_rn])').highlight(highlightSearch)

				if (indexRow > $('#customer').getDataIDs().length - 1) {
					indexRow = $('#customer').getDataIDs().length - 1
				}

				if (triggerClick) {
					$('#' + $('#customer').getDataIDs()[indexRow]).click()
					triggerClick = false
				} else {
					$('#customer').setSelection($('#customer').getDataIDs()[indexRow])
				}

				$('#gsh_customer_rn').html(`
	    		<button id="clearFilter" title="Clear Filter" style="width: 100%; height: 100%;"> X </button>	
	  		`).click(function() {
          var grid = $("#jqGrid");

          // Clear the filter
          grid.jqGrid('clearGridData');
          grid[0].p.search = false;
          $.extend(grid[0].p.postData, {filters: ""});
        
          // Reload the grid
          grid.trigger("reloadGrid");
	  		})


        
				$('[id*=gs_]').on('input', function() {
					highlightSearch = $(this).val()
					clearTimeout(timeout)

					timeout = setTimeout(function() {
		    		$('#customer').trigger('reloadGrid')
					}, 500);
				})

				$('#t_customer input').on('input', function() {
					clearTimeout(timeout)

					timeout = setTimeout(function() {
						indexRow = 0
		    		$(customerTable).jqGrid('setGridParam', {postData: {'global_search': highlightSearch}}).trigger('reloadGrid')
					}, 500);
				})

				$('input')
					.css('text-transform', 'uppercase')
					.attr('autocomplete', 'off')
			}, 50)
		}
	})

	.jqGrid('filterToolbar',
		{
			searchOnEnter: false,
			defaultSearch: 'cn',
			afterSearch: function() {
				indexRow = 0
			},
		}
	)

	.jqGrid('navGrid', customerPager,
		{
			search: false,
			refresh: false,
			add: false,
			edit: false,
			del: false,
		}
	)

	$(customerTable).navButtonAdd(customerPager, {
   	caption: "Add",
		title: "Add",
		id: "addCustomer",
		buttonicon: "ui-icon-plus",
		onClickButton:function(){
			activeGrid = undefined
			addCustomer()
		},
	})

	$(customerTable).navButtonAdd(customerPager, {
   	caption: "Edit",
		title: "Edit",
		id: "editCustomer",
		buttonicon: "ui-icon-pencil",
		onClickButton:function(){
			if ($(customerTable).jqGrid('getGridParam','selrow') !== null) {
				activeGrid = undefined
				editCustomer(noInvoice)
			} else {
				alert('Please, select row')
			}
		},
	})

	$(customerTable).navButtonAdd(customerPager, {
   	caption: "Delete",
		title: "Delete",
		id: "deleteCustomer",
		buttonicon: "ui-icon-trash",
		onClickButton:function(){
			if ($(customerTable).jqGrid('getGridParam','selrow') !== null) {
		
				activeGrid = undefined
				noInvoice = $(customerTable).jqGrid('getGridParam','selrow')
				confirmDeleteCustomer(noInvoice)
			} else {
				alert('Please, select row')
			}
		},
	})

	$(customerTable).navButtonAdd(customerPager, {
   	caption: "Report",
		title: "Report",
		id: "customersReport",
		buttonicon: "ui-icon-document",
		onClickButton:function()
    {
			$(customerDialog)
				.html(`
					<div class="ui-state-default" style="padding: 5px;">
						<h5> Tentukan Baris </h5>
						
						<label> Dari: </label>
						<input type="text" name="start" value="${$(this).getInd($(this).getGridParam('selrow'))}" class="ui-widget-content ui-corner-all autonumeric" style="padding: 5px; text-transform: uppercase;" max="2" required>

						<label> Sampai: </label>
						<input type="text" name="limit" value="${$(this).getGridParam('records')}" class="ui-widget-content ui-corner-all autonumeric" style="padding: 5px; text-transform: uppercase;" max="2" required>
					</div>
				`)
				.dialog({
					modal: true,
					title: "Customer Report",
					height: '200',
					width: '500',
					position: [0, 0],
					buttons: {
						'Report': function() {
							let start = $(this).find('input[name=start]').val()
							let limit = $(this).find('input[name=limit]').val()
							let params

							if (parseInt(start) > parseInt(limit)) {
								return alert('Sampai harus lebih besar')
							}

							for (var key in postData) {
						    if (params != "") {
						        params += "&";
						    }
						    params += key + "=" + encodeURIComponent(postData[key]);
							}

							window.open( baseUrl + `customer/report?${params}&start=${start}&limit=${limit}&orders_sidx=${ordersPostData.sidx}&orders_sord=${ordersPostData.sord}`)
						},
						'Cancel': function() {
							activeGrid = '#customer'
							$(this).dialog('close')
						}
					}
				})

				AutoNumeric.multiple('.autonumeric', {
					digitGroupSeparator: '',
					decimalPlace: 0,
					minimumValue: 1,
					maximumValue: $(this).getGridParam('records'),
					emptyInputBehavior: '1',
					allowDecimalPadding: false,
				})

			// window.open('customer/report/' + noInvoice)
		},
	})

	/*
	$(customerTable).navButtonAdd(customerPager, {
   	caption: "All Reports",
		title: "All Reports",
		id: "allReports",
		buttonicon: "ui-icon-document",
		onClickButton:function(){
			let params
			for (var key in postData) {
		    if (params != "") {
		        params += "&";
		    }
		    params += key + "=" + encodeURIComponent(postData[key]);
			}

			window.open('customer/report?' + params)
		},
	})
	*/

	.keyControl()
}

function addCustomer() {
	$(customerDialog).load(baseUrl + 'customer/create', function() {
		$($(customerForm + ' input')[0]).focus()
		$.ajax({
			url: baseUrl + '/customer/get_structure',
			type: 'GET',
			dataType: 'JSON',
			success: function(res) {
				res.forEach(function(el, i) {
					$(`input[name=${el.name}`).attr('maxlength', el.max_length)
				})
			}
		})
	}).dialog({
		modal: true,
		title: "Add Customer",
		height: '500',
		width: '650',
		position: [0, 0],
		buttons: {
			'Save': function() {
				storeCustomer()
			},
			'Cancel': function() {
				activeGrid = '#customer'
				$(this).dialog('close')
			}
		}
	})
}

function storeCustomer() {
	$.ajax({
		url: baseUrl + 'customer/store',
		type: 'POST',
		dataType: 'JSON',
		data: $(customerForm).serializeArray(),
		beforeSend: function() {
			$('#errorBox').remove()
		},
		success: function(res) {
			$('#errorBox').remove()
			if (res.status == 'submitted') {
				$(customerDialog).dialog('close')

				$.ajax({
					url: baseUrl + '/customer/show/' + res.postData.no_invoice + '/' + res.postData[$(customerTable).jqGrid('getGridParam', 'sortname')] + '/' + $(customerTable).jqGrid('getGridParam', 'sortname') + '/' + $(customerTable).jqGrid('getGridParam', 'sortorder') + '/' + $(customerTable).jqGrid('getGridParam', 'postData').rows,
					type: 'GET',
					dataType: 'JSON',
					success: function(res) {
						if (res.data) {
							indexRow = res.row - 1
						}
						selectedPage = res.page
						setTimeout(function() {
							$('#customer').trigger('reloadGrid', [{page: selectedPage}])
						}, 50)
					}
				})
			} else {
				console.log(res)
				$('#customerData').before(`
					<div id="errorBox" class="ui-state-error" style="padding: 5px;">
						${res.messages}
					</div>
				`)
			}
		} 
	})
}

/*
function storeCustomer() {
	$.ajax({
		url: baseUrl + 'customer/store',
		type: 'POST',
		dataType: 'JSON',
		data: $(customerForm).serializeArray(),
		success: function(res) {
			$('#errorBox').remove()
			if (res.status == 'submitted') {
				$(customerDialog).dialog('close')

				$.ajax({
					url: baseUrl + '/customer/show/' + res.postData.no_invoice + '/' + res.postData[$(customerTable).jqGrid('getGridParam', 'sortname')] + '/' + $(customerTable).jqGrid('getGridParam', 'sortname') + '/' + $(customerTable).jqGrid('getGridParam', 'sortorder') + '/' + $(customerTable).jqGrid('getGridParam', 'postData').rows,
					type: 'GET',
					dataType: 'JSON',
					success: function(res) {
						if (res.data) {
							indexRow = res.row - 1
						}
						selectedPage = res.page
						setTimeout(function() {
							$('#customer').trigger('reloadGrid', [{page: selectedPage}])
						}, 50)
					}
				})
			} else {
				$('#customerData').before(`
					<div id="errorBox" class="ui-state-error" style="padding: 5px;">
						${res}
					</div>
				`)
			}
		}
	})
}
*/

function editCustomer(noInvoice) {
	$(customerDialog).load(baseUrl + 'customer/edit/' + noInvoice, function() {
		$($(customerForm + ' input')[0]).focus()
		$.ajax({
			url: baseUrl + '/customer/get_structure',
			type: 'GET',
			dataType: 'JSON',
			success: function(res) {
				res.forEach(function(el, i) {
					$(`input[name=${el.name}`).attr('maxlength', el.max_length)
				})
			}
		})
	}).dialog({
		modal: true,
		title: "Edit customer",
		height: '500',
		width: '650',
		position: [0, 0],
		buttons: {
			'Save': function() {
				updateCustomer(noInvoice)
			},
			'Cancel': function() {
				activeGrid = '#customer'
				$(this).dialog('close')
			}
		}
	})
}

function updateCustomer(noInvoice) {
	$.ajax({
		url: baseUrl + 'customer/update/' + noInvoice,
		type: 'POST',
		dataType: 'JSON',
		data: $(customerForm).serializeArray(),
		success: function(res) {
			$('#errorBox').remove()
			if (res.status == 'submitted') {
				$(customerDialog).dialog('close')

				$.ajax({
					url: baseUrl + '/customer/show/' + res.postData.no_invoice + '/' + res.postData[$(customerTable).jqGrid('getGridParam', 'sortname')] + '/' + $(customerTable).jqGrid('getGridParam', 'sortname') + '/' + $(customerTable).jqGrid('getGridParam', 'sortorder') + '/' + $(customerTable).jqGrid('getGridParam', 'postData').rows,
					type: 'GET',
					dataType: 'JSON',
					success: function(res) {
						if (res.data) {
							indexRow = res.row - 1
						}
						selectedPage = res.page
						setTimeout(function() {
							$('#customer').trigger('reloadGrid', [{page: selectedPage}])
						}, 50)
					}
				})
			} else {
				$('#customerData').before(`
					<div id="errorBox" class="ui-state-error" style="padding: 5px;">
						${res}
					</div>
				`)
			}
		}
	})
}

function confirmDeleteCustomer(noInvoice) {
	$(customerDialog)
		.load('customer/confirm_delete/' + noInvoice)
		.dialog({
		modal: true,
		title: "Delete Customer",
		height: '500',
		width: '650',
		position: [0, 0],
		buttons: {
			'Delete': function() {
				deleteCustomer(noInvoice)
			},
			'Cancel': function() {
				activeGrid = '#customer'
				$(this).dialog('close')
			}
		}
	})

	function deleteCustomer(noInvoice) {
		$.ajax({
			url: baseUrl + 'customer/delete/' + noInvoice,
			type: 'POST',
			dataType: 'JSON',
			success: function(res) {
				if (res == 'deleted') {
					$(customerDialog).dialog('close')
					$('#customer').trigger('reloadGrid')
				}
			}
		})
	}
}

function loadOrderTable() {
	$(orderTable).jqGrid({
		datatype: 'JSON',
		caption: 'Orders Data',
		rownumbers: true,
		autowidth: true,
		shrinkToFit: false,
		viewrecords: true,		
		pager: $(orderPager),
   	rowNum:10,
   	rowList:[10,20,30],
		footerrow: true,
		userDataOnFooter: true,
		height: 'auto',
		sortable: true,
		colModel: [
			{
				index: 'item_name',
				name: 'item_name',
				label: 'Item Name',
			},
			{
				index: 'item_price',
				name: 'item_price',
				label: 'Item Price',
				formatter: 'currency',
				align: 'right',
				formatoptions: {
					decimalPlaces: 0,
					decimalSeparator: ',',
					thousandsSeparator: '.',
				},
			},
			{
				index: 'qty',
				name: 'qty',
				label: 'Qty',
				align: 'right',
			},
			{
				index: 'total_price',
				name: 'total_price',
				label: 'Total Price',
				formatter: 'currency',
				align: 'right',
				formatoptions: {
					decimalPlaces: 0,
					decimalSeparator: ',',
					thousandsSeparator: '.',
				},
			},
		],
		loadComplete: function(data) {
			ordersPostData = $(this).jqGrid('getGridParam', 'postData')

			sum = $('#order').jqGrid("getCol", "total_price", false, "sum")

			$('#order').jqGrid('footerData', 'set', {
				qty: 'Total',
				total_price: sum,
			}, true)
		},
		onSelectRow: function(id) {
			activeGrid = '#order'
			noInvoice = $(this).jqGrid('getRowData', id).no_invoice
		},
		keys: true,
	})

	.jqGrid('navGrid', orderPager,
		{
			search: false,
			refresh: false,
			add: false,
			edit: false,
			del: false,
		}
	)

	.keyControl()
}

function showOrder(noInvoice) {
	$('#order').jqGrid('setGridParam', {
		url: baseUrl + 'order/show/' + noInvoice,
	}).trigger('reloadGrid')
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
        .on("keydown", function (e) {
          var t,
            i,
            r = $(s).find("tr[tabindex=0]")[0],
            o = s.p.treeReader.expanded_field

          if (r) {
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
	              		if ($(s).jqGrid('getGridParam', 'selrow') !== $('#customer').find(">tbody>tr.jqgrow").filter(":last").attr('id')) {
		              		$(s).jqGrid('setSelection', $(s).find(">tbody>tr.jqgrow").filter(":last").attr('id')).trigger('reloadGrid')
	              		}
	              	}
              	}
              	if (e.ctrlKey) {
              		if ($(s).jqGrid('getGridParam', 'selrow') !== $('#customer').find(">tbody>tr.jqgrow").filter(":last").attr('id')) {
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
              		if ($(s).jqGrid('getGridParam', 'selrow') !== $('#customer').find(">tbody>tr.jqgrow").filter(":first").attr('id')) {
	              		$(s).jqGrid('setSelection', $(s).find(">tbody>tr.jqgrow").filter(":first").attr('id'))
              		}
              	}
            		$(s).jqGrid('setGridParam', { "page": 1}).trigger('reloadGrid')
                $(s).triggerHandler("jqGridKeyUp", [t, n, e]),
                  $(this).isFunction(l.onUpKey) && l.onUpKey.call(s, t, n, e),
                  e.preventDefault()
              }
              if (38 === e.keyCode) {
              	// if (e.ctrlKey) {
              	// 	if ($(s).jqGrid('getGridParam', 'selrow') !== $('#customer').find(">tbody>tr.jqgrow").filter(":first").attr('id')) {
	              // 		$(s).jqGrid('setSelection', $(s).find(">tbody>tr.jqgrow").filter(":first").attr('id')).trigger('reloadGrid')
              	// 	} else {
              	// 		return false
              	// 	}
              	// } else {
	              //   if (
	              //     ((t = ""), (i = r.previousSibling) && $(i).hasClass("jqgrow"))
	              //   ) {
	              //     if ($(i).is(":hidden")) {
	              //       for (; i; )
	              //         if (
	              //           ((i = i.previousSibling),
	              //           !$(i).is(":hidden") && $(i).hasClass("jqgrow"))
	              //         ) {
	              //           t = i.id
	              //           break
	              //         }
	              //     } else t = i.id
	              //     $(s).jqGrid("setSelection", t, !0, e)
	              // 	}
               //  }
                $(s).triggerHandler("jqGridKeyUp", [t, n, e]),
                  $(this).isFunction(l.onUpKey) && l.onUpKey.call(s, t, n, e),
                  e.preventDefault()
              }
              if (40 === e.keyCode) {
              	// if (e.ctrlKey) {
              	// 	if ($(s).jqGrid('getGridParam', 'selrow') !== $('#customer').find(">tbody>tr.jqgrow").filter(":last").attr('id')) {
	              // 		$(s).jqGrid('setSelection', $(s).find(">tbody>tr.jqgrow").filter(":last").attr('id')).trigger('reloadGrid')
              	// 	} else {
              	// 		return false
              	// 	}
              	// } else {
	              //   if (
	              //     ((t = ""), (i = r.nextSibling) && $(i).hasClass("jqgrow"))
	              //   ) {
	              //     if ($(i).is(":hidden")) {
	              //       for (; i; )
	              //         if (
	              //           ((i = i.nextSibling),
	              //           !$(i).is(":hidden") && $(i).hasClass("jqgrow"))
	              //         ) {
	              //           t = i.id
	              //           break
	              //         }
	              //     } else t = i.id
	              //     $(s).jqGrid("setSelection", t, !0, e)
	              // 	}
               //  }
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

$.jgrid.extend({
  filterToolbar: function (p) {
    var regional = $.jgrid.getRegional(this[0], "search")
    p = $.extend(
      {
        autosearch: true,
        autosearchDelay: 500,
        searchOnEnter: true,
        beforeSearch: null,
        afterSearch: null,
        beforeClear: null,
        afterClear: null,
        onClearSearchValue: null,
        url: "",
        stringResult: false,
        groupOp: "AND",
        defaultSearch: "bw",
        searchOperators: false,
        resetIcon: "x",
        splitSelect: ",",
        groupOpSelect: "OR",
        errorcheck: true,
        operands: {
          eq: "==",
          ne: "!",
          lt: "<",
          le: "<=",
          gt: ">",
          ge: ">=",
          bw: "^",
          bn: "!^",
          in: "=",
          ni: "!=",
          ew: "|",
          en: "!@",
          cn: "~",
          nc: "!~",
          nu: "#",
          nn: "!#",
          bt: "...",
        },
      },
      regional,
      p || {}
    )
    return this.each(function () {
      var $t = this
      if ($t.p.filterToolbar) {
        return
      }
      if (!$($t).data("filterToolbar")) {
        $($t).data("filterToolbar", p)
      }
      if ($t.p.force_regional) {
        p = $.extend(p, regional)
      }
      var classes = $.jgrid.styleUI[$t.p.styleUI || "jQueryUI"].filter,
        common = $.jgrid.styleUI[$t.p.styleUI || "jQueryUI"].common,
        base = $.jgrid.styleUI[$t.p.styleUI || "jQueryUI"].base,
        triggerToolbar = function () {
          var sdata = {},
            j = 0,
            v,
            nm,
            sopt = {},
            so,
            ms = false,
            ssfield = [],
            bbt = false,
            sop,
            ret = [true, "", ""],
            err = false
          $.each($t.p.colModel, function () {
            var $elem = $(
              "#gs_" + $t.p.idPrefix + $.jgrid.jqID(this.name),
              this.frozen === true && $t.p.frozenColumns === true
                ? $t.grid.fhDiv
                : $t.grid.hDiv
            )
            nm = this.index || this.name
            sop = this.searchoptions || {}
            if (p.searchOperators && sop.searchOperMenu) {
              so =
                $elem.parent().prev().children("a").attr("soper") ||
                p.defaultSearch
            } else {
              so = sop.sopt
                ? sop.sopt[0]
                : this.stype === "select"
                ? "eq"
                : p.defaultSearch
            }
            v =
              this.stype === "custom" &&
              $.isFunction(sop.custom_value) &&
              $elem.length > 0
                ? sop.custom_value.call($t, $elem, "get")
                : $elem.val()
            // detect multiselect
            if (
              this.stype === "select" &&
              sop.multiple &&
              $.isArray(v) &&
              v.length
            ) {
              ms = true
              ssfield.push(nm)
              v = v.length === 1 ? v[0] : v
            }
            if (this.searchrules && p.errorcheck) {
              if ($.isFunction(this.searchrules)) {
                ret = this.searchrules.call($t, v, this)
              } else if ($.jgrid && $.jgrid.checkValues) {
                ret = $.jgrid.checkValues.call(
                  $t,
                  v,
                  -1,
                  this.searchrules,
                  this.label || this.name
                )
              }
              if (ret && ret.length && ret[0] === false) {
                if (this.searchrules.hasOwnProperty("validationError")) {
                  err = this.searchrules.validationError
                }
                return false
              }
            }
            if (so === "bt") {
              bbt = true
            }
            if (v || so === "nu" || so === "nn") {
              sdata[nm] = v
              sopt[nm] = so
              j++
            } else {
              try {
                delete $t.p.postData[nm]
              } catch (z) {}
            }
          })
          if (ret[0] === false) {
            if ($.isFunction(err)) {
              err.call($t, ret[1])
            } else {
              var errors = $.jgrid.getRegional($t, "errors")
              $.jgrid.info_dialog(errors.errcap, ret[1], "", {
                styleUI: $t.p.styleUI,
              })
            }
            return
          }
          var sd = j > 0 ? true : false
          if (
            p.stringResult === true ||
            $t.p.datatype === "local" ||
            p.searchOperators === true
          ) {
            var ruleGroup = '{"groupOp":"' + p.groupOp + '","rules":['
            var gi = 0
            $.each(sdata, function (i, n) {
              if (gi > 0) {
                ruleGroup += ","
              }
              ruleGroup += '{"field":"' + i + '",'
              ruleGroup += '"op":"' + sopt[i] + '",'
              n += ""
              ruleGroup +=
                '"data":"' +
                n.replace(/\\/g, "\\\\").replace(/\"/g, '\\"') +
                '"}'
              gi++
            })
            ruleGroup += "]}"
            // multiselect
            var filters, rules, k, str, rule, ssdata, group
            if (ms) {
              $.jgrid.filterRefactor({
                ruleGroup: ruleGroup,
                ssfield: ssfield,
                splitSelect: p.splitSelect,
                groupOpSelect: p.groupOpSelect,
              })
              //ruleGroup = JSON.stringify( filters );
            }
            if (bbt) {
              if (!$.isPlainObject(filters)) {
                filters = $.jgrid.parse(ruleGroup)
              }
              if (filters.rules && filters.rules.length) {
                rules = filters.rules
                for (k = 0; k < rules.length; k++) {
                  rule = rules[k]
                  if (rule.op === "bt") {
                    ssdata = rule.data.split("...")
                    if (ssdata.length > 1) {
                      if (filters.groups === undefined) {
                        filters.groups = []
                      }
                      group = { groupOp: "AND", groups: [], rules: [] }
                      filters.groups.push(group)
                      $.each(ssdata, function (l) {
                        var btop = l === 0 ? "ge" : "le"
                        str = ssdata[l]
                        if (str) {
                          group.rules.push({
                            data: ssdata[l],
                            op: btop,
                            field: rule.field,
                          })
                        }
                      })
                      rules.splice(k, 1)
                      k--
                    }
                  }
                }
              }
            }
            if (bbt || ms) {
              ruleGroup = JSON.stringify(filters)
            }
            $.extend($t.p.postData, { filters: ruleGroup })
            $.each(
              ["searchField", "searchString", "searchOper"],
              function (i, n) {
                if ($t.p.postData.hasOwnProperty(n)) {
                  delete $t.p.postData[n]
                }
              }
            )
          } else {
            $.extend($t.p.postData, sdata)
          }
          var saveurl
          if (p.url) {
            saveurl = $t.p.url
            $($t).jqGrid("setGridParam", { url: p.url })
          }
          var bsr =
            $($t).triggerHandler("jqGridToolbarBeforeSearch") === "stop"
              ? true
              : false
          if (!bsr && $.isFunction(p.beforeSearch)) {
            bsr = p.beforeSearch.call($t)
          }
          if (!bsr) {
            $($t)
              .jqGrid("setGridParam", { search: sd })
              .trigger("reloadGrid", [{ page: 1 }])
          }
          if (saveurl) {
            $($t).jqGrid("setGridParam", { url: saveurl })
          }
          $($t).triggerHandler("jqGridToolbarAfterSearch")
          if ($.isFunction(p.afterSearch)) {
            p.afterSearch.call($t)
          }
        },
        clearToolbar = function (trigger) {
          var sdata = {},
            j = 0,
            nm
          trigger = typeof trigger !== "boolean" ? true : trigger
          $.each($t.p.colModel, function () {
            var v,
              $elem = $(
                "#gs_" + $t.p.idPrefix + $.jgrid.jqID(this.name),
                this.frozen === true && $t.p.frozenColumns === true
                  ? $t.grid.fhDiv
                  : $t.grid.hDiv
              )
            if (
              this.searchoptions &&
              this.searchoptions.defaultValue !== undefined
            ) {
              v = this.searchoptions.defaultValue
            }
            nm = this.index || this.name
            switch (this.stype) {
              case "select":
                $elem.find("option").each(function (i) {
                  if (i === 0) {
                    this.selected = true
                  }
                  if ($(this).val() === v) {
                    this.selected = true
                    return false
                  }
                })
                if (v !== undefined) {
                  // post the key and not the text
                  sdata[nm] = v
                  j++
                } else {
                  try {
                    delete $t.p.postData[nm]
                  } catch (e) {}
                }
                break
              case "text":
                $elem.val(v || "")
                if (v !== undefined) {
                  sdata[nm] = v
                  j++
                } else {
                  try {
                    delete $t.p.postData[nm]
                  } catch (y) {}
                }
                break
              case "custom":
                if (
                  $.isFunction(this.searchoptions.custom_value) &&
                  $elem.length > 0
                ) {
                  this.searchoptions.custom_value.call(
                    $t,
                    $elem,
                    "set",
                    v || ""
                  )
                }
                break
            }
          })
          var sd = j > 0 ? true : false
          $t.p.resetsearch = true
          if (p.stringResult === true || $t.p.datatype === "local") {
            var ruleGroup = '{"groupOp":"' + p.groupOp + '","rules":['
            var gi = 0
            $.each(sdata, function (i, n) {
              if (gi > 0) {
                ruleGroup += ","
              }
              ruleGroup += '{"field":"' + i + '",'
              ruleGroup += '"op":"' + "eq" + '",'
              n += ""
              ruleGroup +=
                '"data":"' +
                n.replace(/\\/g, "\\\\").replace(/\"/g, '\\"') +
                '"}'
              gi++
            })
            ruleGroup += "]}"
            $.extend($t.p.postData, { filters: ruleGroup })
            $.each(
              ["searchField", "searchString", "searchOper"],
              function (i, n) {
                if ($t.p.postData.hasOwnProperty(n)) {
                  delete $t.p.postData[n]
                }
              }
            )
          } else {
            $.extend($t.p.postData, sdata)
          }
          var saveurl
          if (p.url) {
            saveurl = $t.p.url
            $($t).jqGrid("setGridParam", { url: p.url })
          }
          var bcv =
            $($t).triggerHandler("jqGridToolbarBeforeClear") === "stop"
              ? true
              : false
          if (!bcv && $.isFunction(p.beforeClear)) {
            bcv = p.beforeClear.call($t)
          }
          if (!bcv) {
            if (trigger) {
              $($t)
                .jqGrid("setGridParam", { search: sd })
                .trigger("reloadGrid", [{ page: 1 }])
            }
          }
          if (saveurl) {
            $($t).jqGrid("setGridParam", { url: saveurl })
          }
          $($t).triggerHandler("jqGridToolbarAfterClear")
          if ($.isFunction(p.afterClear)) {
            p.afterClear()
          }
        },
        toggleToolbar = function () {
          var trow = $("tr.ui-search-toolbar", $t.grid.hDiv)
          if ($t.p.frozenColumns === true) {
            $($t).jqGrid("destroyFrozenColumns")
          }
          if (trow.css("display") === "none") {
            trow.show()
          } else {
            trow.hide()
          }
          if ($t.p.frozenColumns === true) {
            $($t).jqGrid("setFrozenColumns")
          }
        },
        buildRuleMenu = function (elem, left, top) {
          $("#sopt_menu").remove()

          left = parseInt(left, 10)
          top = parseInt(top, 10) + 18

          var fs = $(".ui-jqgrid").css("font-size") || "11px"
          var str =
              '<ul id="sopt_menu" class="ui-search-menu modal-content" role="menu" tabindex="0" style="font-size:' +
              fs +
              ";left:" +
              left +
              "px;top:" +
              top +
              'px;">',
            selected = $(elem).attr("soper"),
            selclass,
            aoprs = [],
            ina
          var i = 0,
            nm = $(elem).attr("colname"),
            len = $t.p.colModel.length
          while (i < len) {
            if ($t.p.colModel[i].name === nm) {
              break
            }
            i++
          }
          var cm = $t.p.colModel[i],
            options = $.extend({}, cm.searchoptions)
          if (!options.sopt) {
            options.sopt = []
            options.sopt[0] = cm.stype === "select" ? "eq" : p.defaultSearch
          }
          $.each(p.odata, function () {
            aoprs.push(this.oper)
          })
          for (i = 0; i < options.sopt.length; i++) {
            ina = $.inArray(options.sopt[i], aoprs)
            if (ina !== -1) {
              selclass = selected === p.odata[ina].oper ? common.highlight : ""
              str +=
                '<li class="ui-menu-item ' +
                selclass +
                '" role="presentation"><a class="' +
                common.cornerall +
                ' g-menu-item" tabindex="0" role="menuitem" value="' +
                p.odata[ina].oper +
                '" oper="' +
                p.operands[p.odata[ina].oper] +
                '"><table class="ui-common-table"><tr><td width="25px">' +
                p.operands[p.odata[ina].oper] +
                "</td><td>" +
                p.odata[ina].text +
                "</td></tr></table></a></li>"
            }
          }
          str += "</ul>"
          $("body").append(str)
          $("#sopt_menu").addClass("ui-menu " + classes.menu_widget)
          $("#sopt_menu > li > a")
            .hover(
              function () {
                $(this).addClass(common.hover)
              },
              function () {
                $(this).removeClass(common.hover)
              }
            )
            .click(function () {
              var v = $(this).attr("value"),
                oper = $(this).attr("oper")
              $($t).triggerHandler("jqGridToolbarSelectOper", [v, oper, elem])
              $("#sopt_menu").hide()
              $(elem).text(oper).attr("soper", v)
              if (p.autosearch === true) {
                var inpelm = $(elem).parent().next().children()[0]
                if ($(inpelm).val() || v === "nu" || v === "nn") {
                  triggerToolbar()
                }
              }
            })
        }
      // create the row
      var tr = $("<tr class='ui-search-toolbar' role='row'></tr>"),
        timeoutHnd,
        rules,
        filterobj
      if (p.restoreFromFilters) {
        filterobj = $t.p.postData.filters
        if (filterobj) {
          if (typeof filterobj === "string") {
            filterobj = $.jgrid.parse(filterobj)
          }
          rules = filterobj.rules.length ? filterobj.rules : false
        }
      }
      $.each($t.p.colModel, function (ci) {
        var cm = this,
          soptions,
          select = "",
          sot = "=",
          so,
          i,
          st,
          csv,
          df,
          elem,
          restores,
          th = $(
            "<th role='columnheader' class='" +
              base.headerBox +
              " ui-th-" +
              $t.p.direction +
              "' id='gsh_" +
              $t.p.id +
              "_" +
              cm.name +
              "' ></th>"
          ),
          thd = $("<div></div>"),
          stbl = $(
            "<table class='ui-search-table' cellspacing='0'><tr><td class='ui-search-oper' headers=''></td><td class='ui-search-input' headers=''></td><td class='ui-search-clear' headers=''></td></tr></table>"
          )
        if (this.hidden === true) {
          $(th).css("display", "none")
        }
        this.search = this.search === false ? false : true
        if (this.stype === undefined) {
          this.stype = "text"
        }
        this.searchoptions = this.searchoptions || {}
        if (this.searchoptions.searchOperMenu === undefined) {
          this.searchoptions.searchOperMenu = true
        }
        soptions = $.extend({}, this.searchoptions, {
          name: cm.index || cm.name,
          id: "gs_" + $t.p.idPrefix + cm.name,
          oper: "search",
        })
        if (this.search) {
          if (p.restoreFromFilters && rules) {
            restores = false
            for (var is = 0; is < rules.length; is++) {
              if (rules[is].field) {
                var snm = cm.index || cm.name
                if (snm === rules[is].field) {
                  restores = rules[is]
                  break
                }
              }
            }
          }
          if (p.searchOperators) {
            so = soptions.sopt
              ? soptions.sopt[0]
              : cm.stype === "select"
              ? "eq"
              : p.defaultSearch
            // overwrite  search operators
            if (p.restoreFromFilters && restores) {
              so = restores.op
            }
            for (i = 0; i < p.odata.length; i++) {
              if (p.odata[i].oper === so) {
                sot = p.operands[so] || ""
                break
              }
            }
            st =
              soptions.searchtitle != null
                ? soptions.searchtitle
                : p.operandTitle
            select = this.searchoptions.searchOperMenu
              ? "<a title='" +
                st +
                "' style='padding-right: 0.5em;' soper='" +
                so +
                "' class='soptclass' colname='" +
                this.name +
                "'>" +
                sot +
                "</a>"
              : ""
          }
          $("td:eq(0)", stbl).attr("colindex", ci).append(select)
          if (soptions.clearSearch === undefined) {
            soptions.clearSearch = true
          }
          if (soptions.clearSearch) {
            csv = p.resetTitle || "Clear Search Value"
            $("td:eq(2)", stbl).append(
              "<a title='" +
                csv +
                "' style='padding-right: 0.3em;padding-left: 0.3em;' class='clearsearchclass'>" +
                p.resetIcon +
                "</a>"
            )
          } else {
            $("td:eq(2)", stbl).hide()
          }
          if (this.surl) {
            soptions.dataUrl = this.surl
          }
          df = ""
          if (soptions.defaultValue) {
            df = $.isFunction(soptions.defaultValue)
              ? soptions.defaultValue.call($t)
              : soptions.defaultValue
          }
          //overwrite default value if restore from filters
          if (p.restoreFromFilters && restores) {
            df = restores.data
          }
          elem = $.jgrid.createEl.call(
            $t,
            this.stype,
            soptions,
            df,
            false,
            $.extend({}, $.jgrid.ajaxOptions, $t.p.ajaxSelectOptions || {})
          )
          $(elem).addClass(classes.srInput)
          $("td:eq(1)", stbl).append(elem)
          $(thd).append(stbl)
          if (soptions.dataEvents == null) {
            soptions.dataEvents = []
          }
          switch (this.stype) {
            case "select":
              if (p.autosearch === true) {
                soptions.dataEvents.push({
                  type: "change",
                  fn: function () {
                    triggerToolbar()
                    return false
                  },
                })
              }
              break
            case "text":
              if (p.autosearch === true) {
                if (p.searchOnEnter) {
                  soptions.dataEvents.push({
                    type: "keypress",
                    fn: function (e) {
                      var key = e.charCode || e.keyCode || 0
                      if (key === 13) {
                        triggerToolbar()
                        return false
                      }
                      return this
                    },
                  })
                } else {
                  soptions.dataEvents.push({
                    type: "keydown",
                    fn: function (e) {
                      var key = e.which
                      switch (key) {
                        case 13:
                          return false
                        case 9:
                        case 16:
                        case 37:
                        case 38:
                        case 39:
                        case 40:
                        case 27:
                          break
                        default:
                          if (timeoutHnd) {
                            clearTimeout(timeoutHnd)
                          }
                          timeoutHnd = setTimeout(function () {
                            triggerToolbar()
                          }, p.autosearchDelay)
                      }
                    },
                  })
                }
              }
              break
          }

          $.jgrid.bindEv.call($t, elem, soptions)
        }
        $(th).append(thd)
        $(tr).append(th)
        if (!p.searchOperators || select === "") {
          $("td:eq(0)", stbl).hide()
        }
      })
      $("table thead", $t.grid.hDiv).append(tr)
      if (p.searchOperators) {
        $(".soptclass", tr).click(function (e) {
          var offset = $(this).offset(),
            left = offset.left,
            top = offset.top
          buildRuleMenu(this, left, top)
          e.stopPropagation()
        })
        $("body").on("click", function (e) {
          if (e.target.className !== "soptclass") {
            $("#sopt_menu").remove()
          }
        })
      }
      $(".clearsearchclass", tr).click(function () {
        var ptr = $(this).parents("tr:first"),
          coli = parseInt($("td.ui-search-oper", ptr).attr("colindex"), 10),
          sval = $.extend({}, $t.p.colModel[coli].searchoptions || {}),
          dval = sval.defaultValue ? sval.defaultValue : "",
          elem
        if ($t.p.colModel[coli].stype === "select") {
          elem = $("td.ui-search-input select", ptr)
          if (dval) {
            elem.val(dval)
          } else {
            elem[0].selectedIndex = 0
          }
        } else {
          elem = $("td.ui-search-input input", ptr)
          elem.val(dval)
        }
        $($t).triggerHandler("jqGridToolbarClearVal", [
          elem[0],
          coli,
          sval,
          dval,
        ])
        if ($.isFunction(p.onClearSearchValue)) {
          p.onClearSearchValue.call($t, elem[0], coli, sval, dval)
        }
        // ToDo custom search type
        if (p.autosearch === true) {
          triggerToolbar()
        }
      })
      this.p.filterToolbar = true
      this.triggerToolbar = triggerToolbar
      this.clearToolbar = clearToolbar
      this.toggleToolbar = toggleToolbar
    })
  },
})

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