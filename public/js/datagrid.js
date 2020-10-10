(function ( $ ) {

	$.fn.datagrid = function(options) {

		var table 	= $(this);
		var data 	= [];
		var searchInputElementOriginal = {};
		var delay = (function(){
            var timer = 0;
            return function(callback, ms){
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
            };
        })();
		var config 	= $.extend({
			url 				: '',
			primaryField		: '',
			sortBy				: '',
			orderBy				: 'DESC',
	        pagingElement 		: '',
	        optionPagingElement : '',
	        searchFromDateElement : '',
			searchToDateElement : '',
	        searchFieldElement	: '',
	        searchInputElement	: '',
	        pageInfoElement		: '',
	        rowNumber			: false, 
	        rowCheck			: false,
	        columns 			: [],
	        mergeCells			: [],
	       	activePageNumber	: 1,
	        itemsPerPage		: 10,
	        itemsPerPageOption 	: [10, 25, 50, 100],
	        delayTime 			: 1000,
	        rowChecked 			: [],
	        queryParams 		: {},
	        rowDetail			: {},

	        noRecordText		: 'Tidak ada data ditemukan...',
	        loadingDataText		: 'Memuat data...',
	        firstText			: '<i class="fa fa-angle-double-left"></i>',
	        prevText			: '<i class="fa fa-angle-left"></i>',
	        nextText			: '<i class="fa fa-angle-right"></i>',
	        lastText			: '<i class="fa fa-angle-double-right"></i>',
	        showingText			: 'Menampilkan',
	        ofText				: 'dari',
	        entriesText			: 'data',

			chart				: false,
			chartElement		: '',
			chartbarColors		: ['#177bbb'],
			chartxLabelMargin	: 1,
			chartxLabelAngle	: 45,
			chartxkey			: '',
			chartykey			: [],
			chartlabels			: [],
			chartReverse		: false,

			ajaxFinish			: (function(){}),
	    }, options);

		var chart;

		if (config.chart == true) {
			chart = new Morris.Bar({
				element: config.chartElement,
				xLabelMargin: config.chartxLabelMargin,
				xLabelAngle: config.chartxLabelAngle,
				barColors: config.chartbarColors,
				xkey: config.chartxkey,
				ykeys: config.chartykey,
				labels: config.chartlabels,
			});
		}

		// Create the table parts
		function BuildTableSection() {
			var tableString = "<thead></thead><tbody></tbody>";
			$(table).append(tableString);
		}

		// Merged cells
		function PrintMergeCells() {

			// Create a wrapper element for the merged cells
			var theadMergeCellsString = "<tr id='thead-merge-cells'></tr>";
			$(table).find('thead').append(theadMergeCellsString);

			// Show column number if it is enabled
			if (config.rowNumber == true) {
				var rowNumberString = "<th sortable='false' style='text-align: center; width: 20px;' rowspan='2'>No</th>";
				$(table).find('#thead-merge-cells').append(rowNumberString);
			}

			// Show column checkbox if it is enabled
			if (config.rowCheck == true) {
				var rowCheckString = "<th sortable='false' style='text-align: center; width: 20px;' rowspan='2'><input type='checkbox'></th>";
				$(table).find('#thead-merge-cells').append(rowCheckString);
				CheckAllRow();
			}

			if (!$.isEmptyObject(config.rowDetail)) {
				var rowDetail = "<th sortable='false' style='text-align: center; width: 20px;' rowspan='2'></th>";
				$(table).find('#thead-merge-cells').append(rowDetail);
			}

			// Check each merged cells by comparing an columns array and mergecells array
			config.columns.forEach(function(row_column, i) {

				var print_merge_thead = false;
				var align 		= '';
				var colspan 	= '';
				var title 		= '';

				// Check for each merged cells
				// If it is merged cells save property columns and show
				config.mergeCells.forEach(function(row_cell) {
					if (i == row_cell.index) {
						print_merge_thead = true;
						align 		= row_cell.align;
						colspan 	= row_cell.colspan;
						title 		= row_cell.title;
					}
				});

				// Check if the position of these cells exist between the merged cells
				// If yes then do not need to display anything
				if (!print_merge_thead) {
					config.mergeCells.forEach(function(row_cell) {
						if (i > row_cell.index && i < row_cell.index + row_cell.colspan) {
							print_merge_thead = null;
						}
					});
				}

				// Show the elements according to the conditions above
				var columnString = '';
				if (print_merge_thead) {
					columnString = "<th sortable='false' style='text-align: " + align + ";' colspan='" + colspan + "'>" + title + "</th>";
				} else if (print_merge_thead == false) {
					columnString = "<th title='" + row_column.field + "' sortable='" + row_column.sortable + "' style='text-align: " + row_column.align + "; width: " + row_column.width + "px;' rowspan='2'>" + row_column.title + "</th>"; 
				}

				$(table).find('#thead-merge-cells').append(columnString);
			});
		}

		// Regular cells
		function PrintNormalCells() {
			
			// Create a wrapper element for the regular cells
			var theadTitleString = "<tr id='thead-title'></tr>";
			$(table).find('thead').append(theadTitleString);

			// Show column number if it is enabled and mergecells array not defined
			if (config.rowNumber == true && config.mergeCells.length == 0) {
				var rowNumberString = "<th sortable='false' style='text-align: center; width: 20px;'>No</th>";
				$(table).find('#thead-title').append(rowNumberString);
			}
			
			// Show column checkbox if it is enabled and mergecells array not defined
			if (config.rowCheck == true && config.mergeCells.length == 0) {
				var rowCheckString = "<th sortable='false' style='text-align: center; width: 20px;'><input type='checkbox'></th>";
				$(table).find('#thead-title').append(rowCheckString);
				CheckAllRow();
			}

			if (!$.isEmptyObject(config.rowDetail) && config.mergeCells.length == 0) {
				var rowDetail = "<th sortable='false' style='text-align: center; width: 20px;'></th>";
				$(table).find('#thead-title').append(rowDetail);
			}
			
			// Show each column
			config.columns.forEach(function(row, i) {

				// Check if the position of these cells exist between the merged cells
				// If yes then show the cells but otherwise it does not need to display anything
				var print_normal_thead = false;
				if (config.mergeCells.length > 0) {
					config.mergeCells.forEach(function(row_cell) {
						if (i >= row_cell.index && i < row_cell.index + row_cell.colspan) {
							print_normal_thead = true;
						}
					});
				} else {
					print_normal_thead = true;
				}

				// Show the elements according to the conditions above
				var columnString;
				if (print_normal_thead) { 
					columnString = "<th title='" + row.field + "' sortable='" + row.sortable + "' style='text-align: " + row.align + "; width: " + row.width + "px;'>" + row.title + "</th>";
				}

				$(table).find('#thead-title').append(columnString);
			});
		}

		// Post data
		function AjaxRequest(activePageNumber, itemsPerPage, dataSearch, complete) {
			var postData = $.extend({
				limit 		: parseInt(activePageNumber) * itemsPerPage - itemsPerPage,
				offset 		: itemsPerPage, 
				sort 		: config.sortBy, 
				order 		: config.orderBy,
				dataSearch  : dataSearch
			}, config.queryParams);

			// Post data
			$.ajax({
				url 		: config.url,
				type 		: 'post',
				dataType 	: 'json',
				data 		: postData,
				success: function(responseJsonData) {
					data = responseJsonData;
					complete();
				}
			});
		}

		function datagridMessage(message) {
			var rowCount = config.columns.length + (GetUsedRow() + 1);
			var loading_temp = "<tr><td colspan='" + rowCount + "' style='text-align: center;'>" + message + "</td></tr>";
			$(table).find('tbody').html(loading_temp);
		}

		// Display data
		function DisplayData(activePageNumber, itemsPerPage, dataSearch) {

			// Loading status
			datagridMessage(config.loadingDataText);

			// Ajax post
			data = AjaxRequest(activePageNumber, itemsPerPage, dataSearch, function() {
				var pageNumber 	= (parseInt(activePageNumber) * itemsPerPage - itemsPerPage) + 1;
				var temp 		= "";
				var rowData 	= "";
				var rowIndex 	= "";

				if (data.total >= 1) {
					for (var i = 0; i < data.rows.length; i++) {
						temp += "<tr class='main-row'>";

						// Show column number if it is enabled
						if (config.rowNumber) {
							temp += "<td style='text-align: center;'>" + pageNumber + "</td>";
						}

						// Show column checkbox if it is enabled
						if (config.rowCheck) {
							temp += "<td class='checkbox-column' style='text-align: center;'><input value='" + data.rows[i][config.primaryField] + "' type='checkbox'></td>";
						}

						if (!$.isEmptyObject(config.rowDetail)) {
							temp += "<td style='text-align: center;'>" +
										"<div class='detail-link' data-id='" + i + "' data-expand='false' style='width: 10px; height: 4px; margin-left: auto; margin-right: auto; cursor: pointer; margin-top: 8px; background-color: #595959; border: 1px solid #595959; -border-radius: 0.1em; -moz-border-radius: 0.1em; -webkit-border-radius: 0.1em;'>" + 
											"<div style='width: 4px; height: 10px; cursor: pointer; margin-top: -4px; margin-left: 2px; background-color: #595959; border: 1px solid #595959; -border-radius: 0.1em; -moz-border-radius: 0.1em; -webkit-border-radius: 0.1em;'></div>" +
										"</div>" +
									"</td>";
						}

						// check if the column is worth undefined then call the anonymous function
						config.columns.forEach(function(rowColumn) {
							if (typeof data.rows[i][rowColumn.field] !== 'undefined') {
								temp += "<td style='text-align: " + rowColumn.align + "; width: " + rowColumn.width + "px;' class='"+rowColumn.class+"'>" + data.rows[i][rowColumn.field] + "</td>";
							} else if (typeof rowColumn.rowStyler !== 'undefined') {
								rowData = data.rows[i];
								rowIndex = i;
								temp += "<td style='text-align: " + rowColumn.align + "; width: " + rowColumn.width + "px;' class='"+rowColumn.class+"'>" + rowColumn.rowStyler(data.rows[i], i) + "</td>";
							} else {
								temp += "<td style='text-align: " + rowColumn.align + "; width: " + rowColumn.width + "px;' class='"+rowColumn.class+"'>Undefined</td>";	
							}
						});

						temp += "</tr>";

						pageNumber++;
					}

					$(table).find('tbody').html(temp);
				} else {
					// Loading status
					datagridMessage(config.noRecordText);
				}

				// Display Chart
				if (config.chart == true) {
					DisplayChart(data);
				}

				// Paging data
				PagingData(activePageNumber, itemsPerPage);

				// Check uncheck row
				CheckRow();

				// Page info
				PageInfo(activePageNumber, itemsPerPage);

				// Detail row
				ShowDetailRow();	

				config.ajaxFinish();

			});
		}

		// Display Chart
		function DisplayChart(data) {
			var ChartData = [];
			var setData	  = [];
			var data_row  = data.rows;

			if (data.rows.length > 0) {
				$.each(data.rows, function(value) {
					var temparr = {};
					eval('temparr.' + config.chartxkey + ' = this.' + config.chartxkey);
					$.each(config.chartykey, function(index, val) {
						var chartykey = this;
						eval('temparr.' + chartykey + ' = ' + eval('data_row[value].' + chartykey) );
					});
					ChartData.push(temparr);
				});
				
				if (config.chartReverse == true) {
					$.each(ChartData, function(index, val) {
						setData.push(ChartData[(ChartData.length - 1) - index]);
					});
				} else {
					setData = ChartData;
				}
			} else {
					var temparr = {};
					eval('temparr.' + config.chartxkey + ' = "' + config.noRecordText + '"');
					$.each(config.chartykey, function(index, val) {
						var chartykey = this;
						eval('temparr.' + chartykey + ' = 0');
					});
				setData.push(temparr);
			}
			chart.setData(setData);
		}

		// Display paging
		function PagingData(activePageNumber, itemsPerPage) {

			var temp = '';
			var first_link_disabled = activePageNumber > 1 ? '' : 'class="disabled"';
			temp += '<li ' + first_link_disabled + ' title="first"><a href="javascript:void(0);">' + config.firstText + '</a></li>';
			temp += '<li ' + first_link_disabled + ' title="prev"><a href="javascript:void(0);">' + config.prevText + '</a></li>';

			if (activePageNumber == Math.ceil(data.total / itemsPerPage) && activePageNumber - 2 >= 1) {
				temp += '<li title="' + (activePageNumber - 2) + '"><a href="javascript:void(0);">' + (activePageNumber - 2) + '</a></li>';
			}

			if (activePageNumber - 1 >= 1) {
				temp += '<li title="' + (activePageNumber - 1) + '"><a href="javascript:void(0);">' + (activePageNumber - 1) + '</a></li>';
			}

			temp += '<li class="active" title="' + activePageNumber + '"><a href="javascript:void(0);">' + activePageNumber + '</a></li>';

			if (activePageNumber + 1 <= Math.ceil(data.total / itemsPerPage)) {
				temp += '<li title="' + (activePageNumber + 1) + '"><a href="javascript:void(0);">' + (activePageNumber + 1) + '</a></li>';
			}

			if (activePageNumber == 1 && Math.ceil(data.total / itemsPerPage) > 2) {
				temp += '<li title="' + (activePageNumber + 2) + '"><a href="javascript:void(0);">' + (activePageNumber + 2) + '</a></li>';
			}

			var last_link_disabled = activePageNumber < Math.ceil(data.total / itemsPerPage) ? '' : 'class="disabled"';
			temp += '<li ' + last_link_disabled + ' title="next"><a href="javascript:void(0);">' + config.nextText + '</a></li>';
			temp += '<li ' + last_link_disabled + ' title="last"><a href="javascript:void(0);">' + config.lastText + '</a></li>';

			$(config.pagingElement).html(temp);
		
			if (Math.ceil(data.total / itemsPerPage) > 1) {
				$(config.pagingElement).children('li').each(function() {
					$(this).on('click', function() {
						var isDisabled = $(this).hasClass('disabled');
						if (isDisabled != true) {
							if (this.title == "prev" && activePageNumber - 1 >= 1) {
								DisplayData(activePageNumber - 1, itemsPerPage, SearchInput());
								config.activePageNumber--;
							
							} else if (this.title == "next" && activePageNumber + 1 <= Math.ceil(data.total / itemsPerPage)) {
								DisplayData(activePageNumber + 1, itemsPerPage, SearchInput());
								config.activePageNumber++;
							
							} else if (this.title == "first") {
								DisplayData(1, itemsPerPage, SearchInput());
								config.activePageNumber = 1;
							
							} else if (this.title == "last") {
								var last_page_numer = Math.ceil(data.total / itemsPerPage);
								DisplayData(last_page_numer, itemsPerPage, SearchInput());
								config.activePageNumber = last_page_numer;
							
							} else if (parseInt(this.title) <= activePageNumber + 2 || parseInt(this.title) >= activePageNumber - 2) {
								DisplayData(parseInt(this.title), itemsPerPage, SearchInput());
								config.activePageNumber = this.title;
							}
						}
					});
				});
			} else {
				$(config.pagingElement).children('li').each(function() {
					$(this).off();
				});
			}
		}

		// Option paging
		function OptionPaging() {
			// Option item perpage
			var tempOption = "";
			for (var i = 0; i < config.itemsPerPageOption.length; i++) {
				tempOption += "<option value='" + config.itemsPerPageOption[i] + "'>" + config.itemsPerPageOption[i] + "</option>";
			}

			$(config.optionPagingElement).html(tempOption);
			$(config.optionPagingElement).val(config.itemsPerPage);

			$(config.optionPagingElement).on('change', function() {
				config.itemsPerPage = $(config.optionPagingElement).children('option:selected').val();
				DisplayData(1, config.itemsPerPage, SearchInput());
			});
		}

		// Check used row
		function GetUsedRow() {
			var temp = -1;
			config.rowNumber == true ? temp += 1 : '';
			config.rowCheck == true ? temp += 1 : '';
			!$.isEmptyObject(config.rowDetail) ? temp += 1 : '';
			return temp;
		}

		// Chek uncheck row
		function CheckRow() {
			$(table).find('.checkbox-column').each(function() {
				$(this).children().each(function(index, object) {		
				
					// Check Checkbox sesuai array
					for (var i = 0; i < config.rowChecked.length; i++) {
						if (config.rowChecked[i] == $(this).attr('value')) {
							$(this).prop("checked", true);
						}
					}

					$(this).on('click', function() {
						var temp, found = false;
						for (var i = 0; i < config.rowChecked.length; i++) {
							if (config.rowChecked[i] == this.value) {
								found = true;
								temp = i;
							}
						}

						if (!found) {
							config.rowChecked[config.rowChecked.length] = parseInt(this.value);
						} else {
							config.rowChecked.splice(temp, 1);
						}
						
						CheckTheadCheckbox();
					});
				});
			});

			CheckTheadCheckbox();
		}

		function CheckTheadCheckbox() {
			// Uncheck thead checkbox
			var selector 	= $(table).children('thead').find('input[type="checkbox"]');
			var arr 		= GetAllCheckbox();
			var boolCheck	= true;	

			for (var z = 0; z < arr.length; z++) {
				if (!$(arr[z]).prop("checked")) {
					boolCheck = false;
				}
			}

			if (boolCheck) {
				selector.prop('checked', true);
			} else {
				selector.prop('checked', false);
			}
		}

		function GetAllCheckbox() {
			var arr = [];
			$(table).find('.checkbox-column').each(function() {
				$(this).children().each(function(index, object) {
					arr[arr.length] = $(this);
				});
			});

			return arr;
		}

		function CheckAllRow() {
			var selector = $(table).children('thead').find('input[type="checkbox"]');
			$(selector).on('click', function() {

				var arr = GetAllCheckbox();
				
				if ($(this).prop('checked')) {				
					for (var z = 0; z < arr.length; z++) {
						$(arr[z]).prop('checked', true);
	
						var temp, found = false;
						for (var i = 0; i < config.rowChecked.length; i++) {
							if (config.rowChecked[i] == $(arr[z]).attr('value')) {
								found = true;
								temp = i;
							}
						}

						if (!found) {
							config.rowChecked[config.rowChecked.length] = parseInt($(arr[z]).attr('value'));
						}
					}
				} else {			
					for (var z = 0; z < arr.length; z++) {
						$(arr[z]).prop('checked', false);

						var temp, found = false;
						for (var i = 0; i < config.rowChecked.length; i++) {
							if (config.rowChecked[i] == $(arr[z]).attr('value')) {
								found = true;
								temp = i;
							}
						}

						if (found) {
							config.rowChecked.splice(temp, 1);
						}
					}
				}
			});
		}

		// Search data
	    function Search() {
	    	// Save original search input element
	    	searchInputElementOriginal = {
	    		selector : config.searchInputElement,
	    		element : $(config.searchInputElement).clone()	
	    	};

			config.columns.forEach(function(rowColumn, key) {
				if (rowColumn.search) {
					// Check for custom search
					if (rowColumn.custom_search == undefined) {
						$(config.searchFieldElement).append('<option value="' + rowColumn.field + '">' + rowColumn.title + '</option>');
					} else {
						$(config.searchFieldElement).append('<option data-option="' + key + '" value="' + rowColumn.field+ '">' + rowColumn.title + '</option>');
					}
				}
			});

			$(config.searchFromDateElement).on('changeDate', function() {
				delay(function(){
	    			DisplayData(1, config.itemsPerPage, SearchInput());
				}, config.delayTime );
	    	});

	    	$(config.searchFromDateElement).change(function() {
				delay(function(){
	    			DisplayData(1, config.itemsPerPage, SearchInput());
				}, config.delayTime );
	    	});

	    	$(config.searchToDateElement).on('changeDate', function() {
	    		delay(function(){
	    			DisplayData(1, config.itemsPerPage, SearchInput());
				}, config.delayTime );
	    	});

	    	$(config.searchToDateElement).change(function() {
	    		delay(function(){
	    			DisplayData(1, config.itemsPerPage, SearchInput());
				}, config.delayTime );
	    	});

	    	$(config.searchInputElement).on('keyup', function() {
		        delay(function(){
	    			DisplayData(1, config.itemsPerPage, SearchInput());	 
			    }, config.delayTime );
	    	});

	    	$(config.searchFieldElement).on('change', function() {
	    		CustomSearch($(this));
	    	});
	    }
	    
	    // Generate Custom search
	    function CustomSearch(el) {
	    	var costum_search = $(el).find('option:selected').attr('data-option');
    		if (costum_search == undefined) {
    			$(config.searchInputElement).replaceWith(searchInputElementOriginal.element);
		    	$(config.searchInputElement).on('keyup', function() {
		    		delay(function(){
		    			DisplayData(1, config.itemsPerPage, SearchInput());
					}, config.delayTime );
		    	});
    		} else {
    			var select = $('<select></select>');
    			var column = $(el).find('option:selected').attr('value');
    			config.columns.forEach(function(rowColumn, key) {
					if (rowColumn.field == column) {
						if (searchInputElementOriginal.selector.indexOf('.') == -1) {
							var selector = searchInputElementOriginal.selector.replace('#', '');
							$(select).attr('id', selector);
							$(select).attr('class', rowColumn.custom_search.appendClass);
						} else {
							var selector = searchInputElementOriginal.selector.replace('.', '');
							$(select).attr('class', rowColumn.custom_search.appendClass + ' ' + selector);
						}
						
						rowColumn.custom_search.option.forEach(function(rowColumn_, key_) {
							$(select).append('<option value="' + rowColumn_.value + '">' + rowColumn_.text + '</option>');
						});
					}
				});
				$(select).on('change', function() {
		    		delay(function(){
		    			DisplayData(1, config.itemsPerPage, SearchInput());
					}, config.delayTime );
		    	});
				$(config.searchInputElement).replaceWith(select);
    		}
    		
			delay(function(){
				DisplayData(1, config.itemsPerPage, SearchInput());
			}, config.delayTime );
	    }

	    // Search data
	    function SearchInput() {
	    	var from_date 	= $(config.searchFromDateElement).val();
	    	var to_date 	= $(config.searchToDateElement).val();
	    	var field 		= $(config.searchFieldElement).val();
    		var value 		= $(config.searchInputElement).val();
    		var temp 		= { field : field, value : value };

			if (value == '' && from_date == '' && to_date == '') {
    			temp = '';
    		} else {
    			temp =  { field : field, value : value, from_date : from_date, to_date : to_date};
    		}
			
			return temp;
	    }

	    // Page info
	    function PageInfo(activePageNumber, itemsPerPage) {

	    	if (data.total >= 1) {
				var limit, offset;
				
				limit 		= ((activePageNumber * itemsPerPage) - itemsPerPage) + 1;
				if (activePageNumber == Math.ceil(data.total / itemsPerPage)) {
					offset = (activePageNumber * itemsPerPage) - ((activePageNumber * itemsPerPage) - data.total);
				} else {
					offset = (activePageNumber * itemsPerPage);
				}
				
				$(config.pageInfoElement).html(config.showingText + ' ' + limit + ' - ' + offset + ' ' + config.ofText + ' ' + data.total + ' ' + config.entriesText);
	    	} else {
	    		$(config.pageInfoElement).html(config.showingText + ' ' + 0 + ' - ' + 0 + ' ' + config.ofText + ' ' + 0 + ' ' + config.entriesText);
	    	}
	    }

	    function SortArrow() {
	    	$(table).children('thead').children().children().each(function(index, object) {
				if ($(object).attr('sortable') != 'false') {
					var div = $('<div class="span_wrapper"></div>');
					$(div).css({
						'position' : 'relative',
						'display'	: 'inline'
					});
			    	var arrow_up 	= $('<span></span>');
					$(arrow_up).css({
						'width' 		: '0px',
						'height' 		: '0px',
						'border'	 	: '4px solid transparent',
						'border-bottom'	: '5px solid #ccc',
						'position'		: 'absolute',
						'margin-left'	: '5px',
						'margin-top'	: '0px'
					});
					$(div).append(arrow_up);

					var arrow_down 	= $('<span></span>');
					$(arrow_down).css({
						'width' 		: '0px',
						'height' 		: '0px',
						'border'	 	: '4px solid transparent',
						'border-top' 	: '5px solid #ccc',
						'position'		: 'absolute',
						'margin-left'	: '5px',
						'margin-top'	: '11px'
					});
					$(div).append(arrow_down);
					$(this).append(div);
				}
			});
	    }

	    // Sort data
	    function SortData() {
	    	// Set sort data by primary field
	    	config.sortBy = config.primaryField;
	    	SortArrow();

			$(table).children('thead').children().children().each(function(index, object) {
				if ($(object).attr('sortable') != 'false') {
					$(this).css('cursor', 'pointer');					
					$(this).on('click', function() {	
						$(table).children('thead').children().children().each(function(index, object) {
							//$(this).children('span').remove();
							$(this).children('div.span_wrapper').remove();
						});

						SortArrow();
						var div = $('<div class="span_wrapper"></div>');
						$(div).css({
							'position' : 'relative',
							'display'	: 'inline'
						});
						var arrow = $('<span></span>');
						$(div).append(arrow);
						$(this).append(div);
						
						if ($(this).attr('data-sortby') == null || $(this).attr('data-sortby') == 'DESC') {
							$(this).attr('data-sortby', 'ASC');
							$(this).children().children('span').css({
								'width' 		: '0px',
								'height' 		: '0px',
								'border'	 	: '4px solid transparent',
								'border-bottom'	: '5px solid #333',
								'position'		: 'absolute',
								'margin-left'	: '5px',
								'margin-top'	: '3px'
							});
						} else {
							$(this).attr('data-sortby', 'DESC');
							$(this).children().children('span').css({
								'width' 		: '0px',
								'height' 		: '0px',
								'border'	 	: '4px solid transparent',
								'border-top' 	: '5px solid #333',
								'position'		: 'absolute',
								'margin-left'	: '5px',
								'margin-top'	: '7px'
							});
						}

						config.sortBy 	= this.title;
						config.orderBy 	= $(this).attr('data-sortby');

						DisplayData(config.activePageNumber, config.itemsPerPage, SearchInput());
					});	
				}
			});
	    }

	    function ShowDetailRow() {
	    	$(table).find('.detail-link').each(function() {
	    		$(this).off();
	    		$(this).on('click', function() {
	    			var rowIndex = $(this).attr('data-id');
	    			if ($(this).attr('data-expand') == 'false') {
						var formatter = "<tr id='detail-row-" + rowIndex + "' style='display: none;'>" +
											"<td colspan='3'></td>" +
											"<td colspan='9' class='detail-content'>" +
												config.rowDetail.formatter(data.rows[rowIndex], rowIndex)											
											"</td>" +
										"</tr>";
						$(formatter).insertAfter($(this).closest("tr")).fadeIn();
						config.rowDetail.onExpandRow(data.rows[rowIndex], rowIndex);

						var temp = "<div class='detail-link' data-id='" + rowIndex + "' data-expand='true' style='width: 10px; height: 4px; margin-left: auto; margin-right: auto; cursor: pointer; margin-top: 8px; background-color: #595959; border: 1px solid #595959; -border-radius: 0.1em; -moz-border-radius: 0.1em; -webkit-border-radius: 0.1em;'></div>";
						$(this).closest("td").html(temp);
	    			} else {
	    				$(this).closest("tr").next().fadeOut(function() {
	    					$(this).remove();
	    				});

	    				var temp = "<div class='detail-link' data-id='" + rowIndex + "' data-expand='false' style='width: 10px; height: 4px; margin-left: auto; margin-right: auto; cursor: pointer; margin-top: 8px; background-color: #595959; border: 1px solid #595959; -border-radius: 0.1em; -moz-border-radius: 0.1em; -webkit-border-radius: 0.1em;'>" + 
										"<div style='width: 4px; height: 10px; cursor: pointer; margin-top: -4px; margin-left: 2px; background-color: #595959; border: 1px solid #595959; -border-radius: 0.1em; -moz-border-radius: 0.1em; -webkit-border-radius: 0.1em;'></div>" +
									"</div>";
						$(this).closest("td").html(temp);
	    			}

	    			ShowDetailRow();
	    		});
	    	});
	    }

	    this.reload = function() {
	    	DisplayData(config.activePageNumber, config.itemsPerPage, SearchInput());
	    }

		this.getChecked = function() {
			return config.rowChecked;
		}

		this.setChecked = function(arr) {
			for (var i = 0; i < arr.length; i++) {
				var temp = false;
				for (var z = 0; z < config.rowChecked.length; z++) {
					if (config.rowChecked[z] == arr[i]) {
						temp = true;
					}
				}

				if (!temp) {
					config.rowChecked[config.rowChecked.length] = arr[i];
				}
			}

			DisplayData(config.activePageNumber, config.itemsPerPage, SearchInput());
		}

		this.setUnchecked = function(arr) {
			for (var i = 0; i < arr.length; i++) {
				var index, temp = false;
				for (var z = 0; z < config.rowChecked.length; z++) {
					if (config.rowChecked[z] == arr[i]) {
						temp = true;
						index = z;
					}
				}

				if (temp) {
					config.rowChecked.splice(index, 1);
				}
			}

			DisplayData(config.activePageNumber, config.itemsPerPage, SearchInput());
		}
	
		this.getRowData = function(rowIndex) {
			return rowIndex == 'all' ? data.rows : data.rows[rowIndex];
		}

		function textMode(child, child_index, rowIndex, styler, onEdit, onSave) {
			if (child_index > GetUsedRow()) {
				var temp, field_name;
				config.columns.forEach(function(row_column, i) {
					if ((i + GetUsedRow() + 1) == child_index) {
						temp 		= row_column.editable;
						field_name 	= row_column.field;
					}
				});

				if (temp) {
					var temp 	= $(child).html();
					var object 	= styler(field_name, temp);
					
					// Check for last child element
					var lastElement;
					config.columns.forEach(function(row_column, i) {
						if (row_column.editable) {
							lastElement = i;
						}
					});

					if ($(child).attr('inline-edit') != 'active') {
						$(child).attr('inline-edit', 'active');
						$(child).html('<form>' + object + '</form>');

						if (lastElement + GetUsedRow() + 1 == child_index) {
							onEdit();
						}
					} else {
						$(child).attr('inline-edit', 'not-active');
						var element = $(child).children('form').serializeArray();
						$(child).html(element[0].value);
						data.rows[rowIndex][field_name] = element[0].value;
						
						if (lastElement + GetUsedRow() + 1 == child_index) {
							onSave();
						}
					}
				}
			}
		}

		function rowModifier(row, columnIndex, rowIndex, styler, onEdit, onSave) {
			$(row).children().each(function(child_index, child_object) {
				if (columnIndex == 'all') {
					textMode($(this), child_index, rowIndex, styler, onEdit, onSave);
				} else {
					if (child_index == (columnIndex + GetUsedRow() + 1)) {
						textMode($(this), child_index, rowIndex, styler, onEdit, onSave);
					}
				}
			});
		}

		this.editable = function(editableOptions) {

			var arrConfig 	= $.extend({
				rowIndex 	: 'all',
				columnIndex : 'all',
				styler 		: function(field_name, value) {
					return value;
				},
				onEdit 		: function() {
					console.log("Edited");
				},
				onSave 		: function() {
					console.log("Saved");
				}
		    }, editableOptions);

			$(table).children('tbody').children('.main-row').each(function(index, object) {
				if (arrConfig.rowIndex == 'all') {
					rowModifier($(this), arrConfig.columnIndex, index, arrConfig.styler, arrConfig.onEdit, arrConfig.onSave);
				} else {
					if (index == arrConfig.rowIndex) {
						rowModifier($(this), arrConfig.columnIndex, index, arrConfig.styler, arrConfig.onEdit, arrConfig.onSave);
					}
				}
			});
		}

		this.queryParams = function(params) {
			config.queryParams = $.extend(config.queryParams, params);
		}

		// Main function
	    this.run = function() {

			// Create the table parts
			BuildTableSection();

			// View merged cells if the mergecells array is defined
			if (config.mergeCells.length > 0) {
				PrintMergeCells();
			}

			// View regular column header table
			PrintNormalCells();

			// Sort data
			SortData();

			// Display data
			DisplayData(config.activePageNumber, config.itemsPerPage, SearchInput());

			// Option paging
			OptionPaging();

			// Search
			Search();
		}

	    return this;

	};

}( jQuery ));