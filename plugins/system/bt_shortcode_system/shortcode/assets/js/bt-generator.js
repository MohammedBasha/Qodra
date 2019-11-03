jQuery.noConflict();
var $ = jQuery;
$(document).ready(function ($) {
	// Prepare data
	var $generator = $('#bt-generator'),
		$search = $('#bt-generator-search'),
		$filter = $('#bt-generator-filter'),
		$filters = $filter.children('a'),
		$choices = $('#bt-generator-choices'),
		$choice = $choices.find('span'),
		$settings = $('#bt-generator-settings'),
		$prefix = $('#bt-compatibility-mode-prefix'),
		$result = $('#bt-generator-result'),
		$selected = $('#bt-generator-selected'),
		ajaxUrl = $("#plg-url").val() + 'index.php?btaction=bt_shortcode_system';
		mce_selection = '';
	// Focus search field when popup is opened
	$search.focus();
		
	// back to list all shortcode	
	$('#bt-generator').on('click', '.bt-generator-home', function (e) {
		
		// Clear search field
		$search.val('');
		// Hide settings
		$settings.html('').hide();
		// Remove narrow class
		$generator.removeClass('bt-generator-narrow');
		// Show filters
		$filter.show();
		// Show choices panel
		$choices.show();
		$choice.show();
		// Clear selection
		mce_selection = '';
		// Focus search field
		$search.focus();
		e.preventDefault();
	});
	
	// Search field
	$search.on({
		focus: function () {
			// Clear field
			$(this).val('');
			// Hide settings
			$settings.html('').hide();

			// Show choices panel
			$choices.show();
			$choice.css({
				opacity: 1
			});
			// Show filters
			$filter.show();
		},
		blur: function () {},
		keyup: function (e) {
			var val = $(this).val(),
				regex = new RegExp(val, 'gi');
			// Hide all choices
			$choice.css({
				opacity: 0.2
			});
			// Find searched choices and show
			$choice.each(function () {
				// Get shortcode name
				var id = $(this).data('shortcode'),
					name = $(this).data('name'),
					desc = $(this).data('desc'),
					group = $(this).data('group');
				// Show choice if matched
				if (id.match(regex) !== null) $(this).css({
					opacity: 1
				});
				else if (name.match(regex) !== null) $(this).css({
					opacity: 1
				});
				else if (desc.match(regex) !== null) $(this).css({
					opacity: 1
				});
				else if (group.match(regex) !== null) $(this).css({
					opacity: 1
				});
			});
		}
	});


	// Filters
	$filters.click(function (e) {
		// Prepare data
		var filter = $(this).data('filter');
		// If filter All, show all choices
		if (filter === 'all') $choice.css({
			opacity: 1
		});
		// Else run search
		else {
			var regex = new RegExp(filter, 'gi');
			// Hide all choices
			$choice.css({
				opacity: 0.2
			});
			// Find searched choices and show
			$choice.each(function () {
				// Get shortcode name
				var group = $(this).data('group');
				// Show choice if matched
				if (group.match(regex) !== null) $(this).css({
					opacity: 1
				});
			});
		}
		e.preventDefault();
	});
	
	// Click on shortcode choice
	$choice.on('click', function (e) {
		// Prepare data
		var shortcode = $(this).data('shortcode');
		
		// Load shortcode options
		$.ajax({
			type: 'POST',
			url: ajaxUrl+'&task=settings',
			data: {
				shortcode: shortcode
			},
			beforeSend: function () {
				$choices.hide();
				// Show loading animation
				$settings.addClass('bt-generator-loading').show();
				// Add narrow class
				$generator.addClass('bt-generator-narrow');
				// Hide filters
				$filter.hide();
			},
			success: function (data) {
				// Hide loading animation
				$settings.removeClass('bt-generator-loading');
				// Insert new HTML
				$settings.html(data);
				
				// Apply selected text to the content field
				if (typeof mce_selection !== 'undefined' && mce_selection !== '') $('#bt-generator-content').val(mce_selection);
				// Init content of table
				
				// Init range pickers
				$('.bt-generator-range-picker').each(function (index) {
					var $picker = $(this),
						$val = $picker.find('input'),
						min = $val.attr('min'),
						max = $val.attr('max'),
						step = $val.attr('step');
					// Apply noUIslider
					$val.simpleSlider({
						snap: true,
						step: step,
						range: [min, max]
					});
					$val.attr('type', 'text').show();
					$val.on('keyup blur', function (e) {
						$val.simpleSlider('setValue', $val.val());
					});
				});
				// Init switches
				$('.bt-generator-switch').on('click',function (e) {
					// Prepare data
					var $switch = $(this),
						$value = $switch.children('input'),
						is_on = $value.val() === 'yes';
					// Disable
					if (is_on) {;
						// Change value
						$value.val('no').trigger('change');
						$switch.removeClass('bt-generator-switch-yes').addClass('bt-generator-switch-no');
					}
					// Enable
					else {
						// Change value
						$value.val('yes').trigger('change');
						$switch.removeClass('bt-generator-switch-no').addClass('bt-generator-switch-yes');
					}
					e.preventDefault();
				});
				
				
				// Init color pickers
				$('.bt-generator-select-color').each(function (index) {
					$(this).find('.bt-generator-select-color-wheel').filter(':first').farbtastic('.bt-generator-select-color-value:eq(' +
						index + ')');
					$(this).find('.bt-generator-select-color-value').focus(function () {
						$('.bt-generator-select-color-wheel:eq(' + index + ')').show();
					});
					$(this).find('.bt-generator-select-color-value').blur(function () {
						$('.bt-generator-select-color-wheel:eq(' + index + ')').hide();
					});
				});
				// Init shadow pickers
				$('.bt-generator-shadow-picker').each(function (index) {
					var $picker = $(this),
						$fields = $picker.find('.bt-generator-shadow-picker-field input'),
						$hos = $picker.find('.bt-generator-shadow-hos'),
						$vos = $picker.find('.bt-generator-shadow-vos'),
						$blur = $picker.find('.bt-generator-shadow-blur'),
						$colorPicker = {
							value: $picker.find('.bt-generator-shadow-picker-color-value'),
							wheel: $picker.find('.bt-generator-shadow-picker-color-wheel')
						},
						$val = $picker.find('.bt-generator-attr');
					// Init color picker
					$colorPicker.wheel.farbtastic($colorPicker.value);
					$colorPicker.value.focus(function () {
						$colorPicker.wheel.show();
					});
					$colorPicker.value.blur(function () {
						$colorPicker.wheel.hide();
					});
					// Handle text fields
					$fields.on('change blur keyup', function () {
						$val.val($hos.val() + 'px ' + $vos.val() + 'px ' + $blur.val() + 'px ' + $colorPicker.value.val()).trigger('change');
					});
				});
				// init border picker
				$('.bt-generator-border-picker').each(function(){
					var $picker = $(this),
						$inputFields = $picker.find('.bt-generator-border-picker-field input'),
						$selectField = $picker.find('.bt-generator-border-picker-field select'),
						$size = $picker.find('.bt-generator-border-size'),
						$style = $picker.find('.bt-generator-border-style '),
						$colorPicker = {
							value: $picker.find('.bt-generator-border-picker-color-value'),
							wheel: $picker.find('.bt-generator-border-picker-color-wheel')
						},
						$val = $picker.find('.bt-generator-attr');
					 
					// Init color picker
					$colorPicker.wheel.farbtastic($colorPicker.value);
					$colorPicker.value.focus(function () {
						$colorPicker.wheel.show();
					});
					$colorPicker.value.blur(function () {
						$colorPicker.wheel.hide();
					});
					// Handle text fields
					$inputFields.on('change blur keyup', function () {
						$val.val($size.val() + 'px ' + $style.val() +' '+ $colorPicker.value.val()).trigger('change');
					});
					$selectField.on('change',function(){
						$val.val($size.val() + 'px ' + $style.val()+' ' + $colorPicker.value.val()).trigger('change');
					});
					
					
				});
				// init icon picker
				$('.bt-generator-icon-picker-button').each(function(){
					iconpickerClicked(this);
					
				});
				
				//init image source select
				$('.bt-generator-isp').each(function () {
					var $picker = $(this),
						$sources = $picker.find('.bt-generator-isp-sources'),
						$source = $picker.find('.bt-generator-isp-source'),
						$add_media = $picker.find('.bt-generator-isp-add-media'),
						$dir_list = $picker.find('#dir-list');
						$upload_image = $picker.find('.bt-generator-isp-upload-media');
						$images = $picker.find('.bt-generator-isp-images'),
						$cats = $picker.find('.bt-generator-isp-categories'),
						$val = $generator.find('#bt-generator-content'),
						$imagesSelect = $picker.find('#images-to-select');
						mediaUrl = $picker.attr('baseUrl');
						 
					// Update hidden value
					var update = function () {
						var val = 'none',
							content = '',
							source = $sources.val();
						// Media library
						if (source === 'media') {
							var images = [];
							$images.find('span').each(function (i) {
								image = '['+ $prefix.val()+'image';
								image += ' src="'+$(this).data('url')+'"';
								image += ' title="'+$(this).data('name') + '"';
								image += ' link="'+($(this).data('link')?$(this).data('link'):'') + '"';
								image += ' parent_tag="'+$selected.val()+'"' + ']';
								images[i] =image;
							});
							if (images.length > 0) content = images.join('\n');
							 
						}
						// Category
						else if (source === 'category') {
							var categories = $cats.val() || [];
							if (categories.length > 0){
								$.ajax({
									 type: 'POST',
									 url: ajaxUrl+'&task=imagescat',
									 data:{
										cats  : categories,
										limit : $('#bt-generator-attr-limit',$generator).val(),
										prefix: $prefix.val()
									 },
									 success: function(data){
										 $val.val(data).trigger('change');
									 }
								});
							} 
						}
						// Deselect
						else if (source === '0'){
							val = 'none';
						}
						// Other options
						else {
							val = source;
						}
						if (content !== ''){ 
							$val.val(content).trigger('change');
						}
					}
					// Switch source
					$sources.on('change', function (e) {
						var source = $(this).val();
						e.preventDefault();
						$source.removeClass('bt-generator-isp-source-open');
						if (source.indexOf(':') === -1) $picker.find('.bt-generator-isp-source-' + source).addClass('bt-generator-isp-source-open');
						update();
					});
					// Remove image
					$images.on('click', 'span i.fa-times', function () {
						$(this).parent('span').css('border-color', '#f03').fadeOut(300, function () {
							$(this).remove();
							update();
						});
					});
					// Edit image
					$images.on('click','span i.fa-edit',function(){
						var image = $(this).parent();
						var editPopup = '<div id="overlay"></div> <div class="wrap-popup">'
											+'<div id="btsc-dialog" >'
											+	'<ul>'
											+		'<li>'
											+			'<label class="btsc-title-lbl" for="btsc-title" title="">Title</label>'
											+			'<input class="btsc-title" type="text" size="90" value="'+$(image).data('name')+'" name="btsc-title">'
											+ 			'<div style="clear: both"></div>'
											+		'</li>'
											+		'<li>'
											+			'<label class="btsc-link-lbl" for="btsc-link" title="">Link</label>'
											+			'<input class="btsc-link" type="text" size="90" name="btsc-title">'
											+ 			'<div style="clear: both"></div>'	
											+		'</li>'
											+	'</ul>'
											+	'<div data-index="'+$(image).index()+'" style="clear: both; margin:0 20px; padding: 0 10px;">'
											+		'<label></label>'
											+		'<button class="btsc-dialog-ok btn btn-small" style="margin-left: 10px;">OK</button>'
											+		'<button class="btsc-dialog-cancel btn btn-small" style="margin-left: 10px;">Cancel</button>'
											+	'</div>'
											+'</div>'
											+'<i class="close-dialog fa fa-times"></i>'
										+'</div>';
						$('.bt-generator-isp-source-media',$picker).append(editPopup);
					});
					// edit done
					$('.bt-generator-isp-source-media',$picker).on('click','.btsc-dialog-ok',function(){
						// get current image edit
						 index = $(this).parent().data('index');
						 image = $('span',$images).eq(index);
						 // get info edit
						 title = $('.btsc-title').val();  
						 link = $('.btsc-link').val();
						 //set data
						 $(image).data('name',title);
						 $(image).data('link',link);
						 // update field
						 update();
						 // remove popup
						 $('.wrap-popup').remove();
						 $('#overlay').remove();
						return false;
					});
					
					// close dialog edit image
					$('.bt-generator-isp-source-media',$picker).on('click','.btsc-dialog-cancel',function(){
						$('.wrap-popup').remove();
						$('#overlay').remove();
						return false;
					});
					$('.bt-generator-isp-source-media',$picker).on('click','.close-dialog',function(){
						$('.wrap-popup').remove();
						$('#overlay').remove();
						return false;
					});
					
					// Upload image
					var uploader = new qq.FileUploader({
						element: document.getElementById('file-uploader'),
						action:  $("#plg-url").val() + 'index.php',
						allowedExtensions: ['jpeg', 'jpg', 'gif', 'png'],
					 	params: { type: 'image',task:'upload',btaction:'bt_shortcode_system'},
					 	onComplete: function(id, fileName, result){
					 		$('.qq-upload-list > li:eq('+id+')').delay(1000).hide(400);
					 		if(result.success ){
					 			$images.find('em').remove();
					 			$images.append('<span data-url="'+result.fileUrl+'" data-name="'+fileName+'">'
										+'<img alt="" src="'+mediaUrl+result.fileUrl +'">'
										+'<i class="fa fa-times"></i>'
										+'<i class="fa fa-edit"></i>'
										+'</span>');
					 			update();
					 		}
					 	}
					});
					
					// add selected class for new image uploaded
					$('li',$imagesSelect).each(function(){
		 				el = $(this);
		 				for(i=0; i<=count; i++){ 
		 					if(el.data('name')== filesName[i]){
		 						el.click();
		 					}
		 				}
		 			});
					//Change folder images
					$dir_list.change(function(){
						if($('li',$imagesSelect).length){
							$imagesSelect.html('');
							$imagesSelect.hide();
							$add_media.click();
						}
					});
					// Add image
					$add_media.click(function (e) {
						e.preventDefault();
						 $.ajax({
							 type: 'POST',
							 url: ajaxUrl+'&task=images',
							 data:{
								folder: $('#dir-list option:selected').val() 
							 },
							 success: function(data){
								 if(data == 'DIR_FALSE'){
									 message = '<li class="btsc-message" style="color:red;">Not found images shortcode folder!</li>';
									 $imagesSelect.append(message);
									 $imagesSelect.show();
									 //alert("Not found images shortcode folder!");
								 }else if(data == 'IMAGE_NULL'){
									 message = '<li class="btsc-message" style="color:red;">Not found any image in folder!</li>';
									 $imagesSelect.append(message);
									 $imagesSelect.show();
									//alert("Not found any image in folder!"); 
								 }else{
									 $('#bt-generator-attr-source').val($sources.val() + ':' + $('#dir-list option:selected').val() );
									 images = $.parseJSON(data);
									 if(images.length > 0){
										 if($('li',$imagesSelect).length >0){
											 $('li',$imagesSelect).each(function(){
												 el = $(this);
												 for(i=0; i<images.length ; i++){
													 if(el.data('name')== images[i]){
														 images.splice(i,1);
													 }
												 }
											 });
											 
										 }
										 // hide add_image button
										 $add_media.hide();
										 for(i=0; i<images.length ; i++){
											 image = '<li class="attachment save-ready details selected" data-name="'+images[i]+'">'
												 		+'<div class="attachment-preview">'
											 			+	'<div class="thumbnail">'
											 			+		'<div class="centered">'
											 			+			'<img  src="'+mediaUrl + images[i]+'">'
											 			+		'</div>'
											 			+	'</div>'
														+	'<a class="check" title="Deselect" href="#">'
														+		'<div class="media-modal-icon"></div>'
														+	'</a>'
														+'</div>'
													 +'</li>';	
											 $imagesSelect.append(image);
											 $imagesSelect.show();
										 }
										 $('li',$imagesSelect).each(function(){
											el = $(this);
											el.unbind('click');
											el.click(function(e){
												e.preventDefault();
												if($(this).hasClass('details selected')){
													$(this).removeClass('details selected');
												}else{
													$(this).addClass('details selected');
												}
											});
										 });
										// button add images selected & cancel button
										$('.add-media-selected').show();
										$('.cancel-media-selected').show();
										 
										 $('.cancel-media-selected').click(function(e){
											 e.preventDefault();
											 $add_media.show();
											 $('.add-media-selected').hide();
											 $imagesSelect.html('');
											 $(this).hide();
										 });
										 
										 $('.media-button-select').click(function(e){
											 e.preventDefault();
											 $images.find('em').remove();
											 $add_media.show();
											 $('.add-media-selected').hide();
											 $('.cancel-media-selected').hide();
											 $('li.selected',$imagesSelect).each(function(){
												 imageUrl= $(this).data('name');
												 imageName = imageUrl.split(/[\\/]/).pop();
												 $images.append('<span data-url="'+imageUrl+'" data-name="'+imageName+'">'
													+'<img alt="" src="'+mediaUrl+imageUrl +'">'
													+'<i class="fa fa-times"></i>'
													+'<i class="fa fa-edit"></i>'
													+'</span>');
											 });
											 $imagesSelect.html('');
											 update();
										 });
										// generator image list 
									 }
								 }
							 },
							 error: function (){
								 alert("Ajax request cannot success.");
							 }
						 });
					});
					// Sort images
					$images.sortable({
						revert: 400,
						containment: $picker,
						tolerance: 'pointer',
						stop: function () {
							update();
						}
					});
					
					// Select categories and terms
					$cats.on('change', update);
				});
				
				$selected.val(shortcode);
				
				
				/**
				 * For price table
				 */
				var priceTableContainer = $('.bt-generator-price-table-field-container');
				var columns = priceTableContainer.siblings('.bt-generator-price-columns');
				var rows = priceTableContainer.siblings('.bt-generator-price-rows');
				
				generatePriceTableForm(priceTableContainer, columns.val(), rows.val());
				
				columns.on('change', function(){ generatePriceTableForm(priceTableContainer, columns.val(), rows.val());});
				rows.on('change', function(){ generatePriceTableForm(priceTableContainer, columns.val(), rows.val());});
				
				/**
				 * For table
				 */
				var tableContainer = $('.bt-generator-table-field-container');
				var tableColumns = tableContainer.siblings('.bt-generator-table-columns');
				var tableRows = tableContainer.siblings('.bt-generator-table-rows');
				
				generateTableForm(tableContainer, tableColumns.val(), tableRows.val());
				
				tableColumns.on('change', function(){ generateTableForm(tableContainer, tableColumns.val(), tableRows.val());});
				tableRows.on('change', function(){ generateTableForm(tableContainer, tableColumns.val(), tableRows.val());});
				
				$('.bt-generator-table-apply').trigger('click');
				/**
				 * For Columns
				 */
				var columnsContainer = $('.bt-columns-container');
				var columnButtons = columnsContainer.next().find('span.bt-columns-layout-button');
				columnButtons.on('click', function(){
					generateColumns(columnsContainer, this);
				});
				
				columnsContainer.find('.bt-column i').on('click', function(){
						mergeColumns(columnsContainer, this);
				});
				
				/**
				 * For TAbs
				 */
				var tabsContainer = $('.bt-generator-tabs-container');
				var tabNumber = $('.bt-generator-tabs');
				tabNumber.on('change', function(){generateTabs(tabsContainer, this)});
				generateTabs(tabsContainer, tabNumber);
			},
			dataType: 'html'
		});
	});
	
	// Presets manager - Add preset
	$('#bt-generator').on('click', '.bt-preset-new', function (e) {
		// Prepare data
		var $container = $(this).parents('.bt-generator-presets'),
			$list = $('.bt-presets-list'),
			id = new Date().getTime();
		// Ask for preset name
		var name = prompt('Please enter a name for new preset','New preset');
		// Name is entered
		if (name !== '' && name !== null) {
			// Hide default text
			$list.find('b').hide();
			// Add new option
			$list.append('<span data-id="' + id + '"><em>' + name + '</em><i class="fa fa-times"></i></span>');
			// Perform AJAX request
			add_preset(id, name);
		}
	});
	/**
	 * Create new preset with specified name from current settings
	 */
	function add_preset(id, name) {
		// Prepare shortcode name and current settings
		var shortcode = $('.bt-generator-presets').data('shortcode'),
			settings = get_settings();
		// Perform AJAX request
		$.ajax({
			type: 'POST',
			url: ajaxUrl +'&task=add_preset',
			data: {
				id: id,
				name: name,
				shortcode: shortcode,
				settings: settings
			},
			success: function(html){
			}
		});
	}
	function get_settings(){
		// Prepare data
		var query = $selected.val(),
			$settings = $('#bt-generator-settings .bt-generator-attr'),
			content = $('#bt-generator-content').val(),
			data = {};
		// Add shortcode attributes
		$settings.each(function (i) {
			// Prepare field and value
			var $this = $(this),
				value = '',
				name = $this.attr('name');
			// Selects
			if ($this.is('select')) value = $this.find('option:selected').val();
			// Other fields
			else value = $this.val();
			// Check that value is not empty
			if (value == null) value = '';
			// Save value
			data[name] = value;
		});
		// Add content
		data['content'] = content.toString();
		// Return data
		return data;
	}
	
	// Presets manager - remove preset
	$('#bt-generator').on('click', '.bt-presets-list i', function (e) {
		// Prepare data
		var $list = $(this).parents('.bt-presets-list'),
			$preset = $(this).parent('span'),
			id = $preset.data('id');
		// Remove DOM element
		$preset.remove();
		// Show default text if last preset was removed
		if ($list.find('span').length < 1) $list.find('b').show();
		// Perform ajax request
		remove_preset(id);
		// Prevent <span> action
		e.stopPropagation();
		// Prevent default action
		e.preventDefault();
	});

	/**
	 * Remove preset by ID
	 */
	function remove_preset(id) {
		// Get current shortcode name
		var shortcode = $('.bt-generator-presets').data('shortcode');
		// Perform AJAX request
		$.ajax({
			type: 'POST',
			url: ajaxUrl + '&task=remove_preset',
			data: {
				id: id,
				shortcode: shortcode
			}
		});
	}
	
	// Presets manager - load preset
	$('#bt-generator').on('click', '.bt-presets-list span', function (e) {
		// Prepare data
		var shortcode = $('.bt-generator-presets').data('shortcode'),
			id = $(this).data('id'),
			$insert = $('.bt-generator-insert');
		// Hide popup
		//$('.bt-preset-popup').hide();
		// Get the preset
		$.ajax({
			type: 'GET',
			url: ajaxUrl+'&task=load_preset',
			data: {
				id: id,
				shortcode: shortcode
			},
			beforeSend: function () {
				// Disable insert button
				$insert.addClass('button-primary-disabled').attr('disabled', true);
			},
			success: function (data) {
				// Enable insert button
				$insert.removeClass('button-primary-disabled').attr('disabled', false);
				// Set new settings
				apply_preset(data);
			},
			dataType: 'json'
		});
		// Prevent default action
		e.preventDefault();
		e.stopPropagation();
	});
	// apply preset data for setting
	function apply_preset(data) {
		// Prepare data
		var $settings = $('#bt-generator-settings .bt-generator-attr'),
			$content = $('#bt-generator-content');
		// Loop through settings
		$settings.each(function () {
			var $this = $(this),
				name = $this.attr('name');
			// Data contains value for this field
		 
			if (data.hasOwnProperty(name)) {
				// Set new value
				$this.val(data[name]);
				$this.trigger('keyup').trigger('change');
			}
		});
		// Set content
		if (data.hasOwnProperty('content')) $content.val(data['content']).trigger('keyup').trigger('change');
		// Update preview
		update_preview();
	}


	// Insert shortcode
	$('#bt-generator').on('click', '.bt-generator-insert', function (e) {
		// Prepare data
		var shortcode = parse();
		// Save shortcode to div
		$result.text(shortcode);
		// Prevent default action
		e.preventDefault();
		// Insert shortcode
		  window.parent.btInsertShortCode(shortcode);
		  window.parent.SqueezeBox.close();	
	});
	function parse() {
		// Prepare data
		var query = $selected.val(),
			prefix = $prefix.val(),
			$settings = $('#bt-generator-settings .bt-generator-attr-container:not(.bt-generator-skip) .bt-generator-attr'),
			content = $('#bt-generator-content').val(),
			result = new String('');
		// Open shortcode
		result += '[' + prefix + query;
		// Add shortcode attributes
		$settings.each(function () {
			// Prepare field and value
			var $this = $(this),
				value = '';
			// Selects
			if ($this.is('select')) value = $this.find('option:selected').val();
			// Other fields
			else value = $this.val();
			// Check that value is not empty
			if (value == null) value = '';
			else if (typeof value === 'array') value = value.join(',');
			// Add attribute
			if (value !== '') result += ' ' + $(this).attr('name') + '="' + $(this).val().toString().replace(/"/gi, "'") + '"';
		});
		// End of opening tag
		result += ']';
		// Wrap shortcode if content presented
		if (content != 'false') result += content + '[/' + prefix + query + ']';
		// Return result
		return result;
	}

	
	
	$('#bt-generator').on('click', '.btn-up-to-top', function (e) {
		//scroll top
		$('html').animate({scrollTop: 0}, 500);
	});
	// preview shortcode
	$('#bt-generator').on('click', '.bt-generator-toggle-preview', function (e) {
		// Prepare data
		
		
		var $preview = $('#bt-generator-preview'),
			$button = $(this);
		
		// Show preview box
		$preview.addClass('bt-generator-loading').show();
		
		//scroll down		
		$('body').animate({scrollTop: $preview.offset().top}, 500);
		
		// Bind updating on settings changes
		$settings.find('input, textarea, select').on('change keyup blur ', function () {
			update_preview();
		});
		// Update preview box
		update_preview(true);
		var offset = $('#bt-generator-preview').offset();
		$('html').animate({scrollTop: offset.top}, 500);
		// Prevent default action
		e.preventDefault();
	});

	$('#bt-generator').on('click', '.btn-media', function(){
		$(this).magnificPopup({type: 'iframe'}).magnificPopup('open');
		return false;
	});
	var update_preview_timer,
	update_preview_request;

	function update_preview(forced) {
	// Prepare data
		var $preview = $('#bt-generator-preview'),
			shortcode = parse(),
			previous = $result.text();
		// Check forced mode
		forced = forced || false;
		// Break if preview box is hidden (preview isn't enabled)
		if (!$preview.is(':visible')) return; 
		// Check shortcode is changed is this is not a forced mode
		if (shortcode === previous && !forced) return;
		// Run timer to filter often calls
		window.clearTimeout(update_preview_timer);
		update_preview_timer = window.setTimeout(function () {
			update_preview_request = $.ajax({
				type: 'POST',
				url: ajaxUrl+'&task=preview',
				cache: false,
				data: {
					shortcode: shortcode
				},
				beforeSend: function () {
					// Abort previous requests
					if (typeof update_preview_request === 'object') update_preview_request.abort();
					// Show loading animation
					$preview.addClass('bt-generator-loading').html('');
				},
				success: function (data) {
					// Hide loading animation and set new HTML
					$preview.html(data).removeClass('bt-generator-loading');
				},
				dataType: 'html'
			});
		}, 300);
		// Save shortcode to div
		$result.text(shortcode);
	}
	
	/**
	 * Function generatePriceTableForm
	 * 
	 */
	function generatePriceTableForm(container, columns, rows){
		
		var pricetable;
		var currentRows = 0;
		var currentColumns = 0;
		var i, removedNumber, addedNumber, row; 
		if(container.data('pricetable')){
			pricetable = container.data('pricetable');
			currentColumns = pricetable.find('.data-column').size();
			currentRows = pricetable.find('.data-column').eq(0).find('.data-row').size();
		}else{
			pricetable = $('<div>').addClass('data-table');
		}

		
		if(columns < currentColumns){
			removedNumber = currentColumns - columns;
		
			for(i = 0; i < removedNumber; i++){
				pricetable.find('.data-column:last').remove();
			}
		}else{
			addedNumber = columns - currentColumns;
			for(i = 0; i < addedNumber; i++){
				var column = $('<div>').addClass('data-column');
				
				row = $('<div>').addClass('data-row-s');
				row.html('<input type="text" value="Column Title ' + (currentColumns + i + 1) + '" class="data-title" />');
				column.append(row);
				
				row = $('<div>').addClass('data-row-s');
				row.html(
					'<input type="text" value="" class="data-image" id="price-table-data-image-' + (currentColumns + i + 1) + '"/>'
					+ '<a rel="{handler: \'iframe\', size: {x: 800, y: 500}}" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=&amp;author=&amp;fieldid=price-table-data-image-' + (currentColumns + i + 1) + '" onclick="SqueezeBox.fromElement(this, {handler:\'iframe\', size: {x: 830, y: 600}}); return false;" title="Select Media" class="button  bt-generator-field-action button-secondary"><i style="margin-right:5px;" class="fa fa-image"></i> Select Media</a>'
				);
				column.append(row);
				
				row = $('<div>').addClass('data-row-s');
				row.html('<input type="text" value="Price ' + (currentColumns + i + 1) + '" class="data-price" />');
				column.append(row);
				
				row = $('<div>').addClass('data-row-s');
				row.html('<input type="text" value="" placeholder="Purchase Link ' + (currentColumns + i + 1) + '" class="data-purchase-link" />');
				column.append(row);
				
				
				var addedRows = currentRows ? currentRows : rows;
				for(var j = 0; j < addedRows; j++){
					row = $('<div>').addClass('data-row');
					row.append($('<input>').attr({type: 'text'}).addClass('data-field').val('Data Field ' + (j + 1)));	
					column.append(row);
				}

				pricetable.append(column);
			}
		}
		
		
		
		if(currentColumns != 0 && rows != currentRows){
			if(rows < currentRows){
				removedNumber = currentRows - rows;
				pricetable.find('.data-column').each(function(){
					for(i = 0; i < removedNumber; i++){
						$(this).find('.data-row:last').remove();
					}
				});
				
			}else{
				addedNumber = rows - currentRows;
				pricetable.find('.data-column').each(function(){
					for(i = 0; i < addedNumber; i++){
						row = $('<div>').addClass('data-row');
						row.append($('<input>').attr({type: 'text'}).addClass('data-field').val('Data Field ' + (currentRows + i + 1)));	
						$(this).append(row);
					}
					
				});
			}
		}	
		var width = 100 / pricetable.find('.data-column').size();
		pricetable.find('.data-column').width(width + '%');
		
		container.append(pricetable);
		container.data('pricetable', pricetable);
		pricetable.find('.data-clear').remove();
		pricetable.append('<div class="data-clear" style="clear: both;"></div>');
		
	}
	
	$('#bt-generator').on('click', '.bt-generator-pricetable-apply',function(){
		var container = $('.bt-generator-price-table-field-container');
		var pricetable = container.data('pricetable');
		if(typeof(pricetable) == 'undefined') return false;		
		var content = '';
		pricetable.find('.data-column').each(function(){
			var title = $(this).find('.data-title').val();
			var price = $(this).find('.data-price').val();
			var image = $(this).find('.data-image').val();
			var purchase_link = $(this).find('.data-purchase-link').val();
			var data = '';
			$(this).find('.data-row').each(function(){
				data += $(this).find('.data-field').val() + ';';
			});
			data = data.substring(0, data.length - 1);
			content += '[' + $prefix.val() + 'pricecol title="' + title + '" price="' + price + '" image="' + image + '" purchase_link="' + purchase_link + '" detail="' + data + '"]';
		});
		
		$('#bt-generator-content').html(content);
	});
	
	/**
	 * Function generateTableForm
	 * 
	 */
	function generateTableForm(container, columns, rows){
		var table;
		var currentRows = 0;
		var currentColumns = 0;
		var i, removedNumber, addedNumber, row; 
		if(container.data('table')){
			table = container.data('table');
			currentColumns = table.find('.data-column').size();
			currentRows = table.find('.data-column').eq(0).find('.data-row').size();
		}else{
			table = $('<div>').addClass('data-table');
		}

		
		if(columns < currentColumns){
			removedNumber = currentColumns - columns;
		
			for(i = 0; i < removedNumber; i++){
				table.find('.data-column:last').remove();
			}
		}else{
			addedNumber = columns - currentColumns;
			for(i = 0; i < addedNumber; i++){
				var column = $('<div>').addClass('data-column');
				
				row = $('<div>').addClass('data-row-s');
				row.html('<input type="text" value="Column Title ' + (currentColumns + i + 1) + '" class="data-title" />');
				column.append(row);
				
				var addedRows = currentRows ? currentRows : rows;
				for(var j = 0; j < addedRows; j++){
					row = $('<div>').addClass('data-row');
					row.append($('<input>').attr({type: 'text'}).addClass('data-field').val('Data Field ' + (j + 1)));	
					column.append(row);
				}

				table.append(column);
			}
		}
		
		
		
		if(currentColumns != 0 && rows != currentRows){
			if(rows < currentRows){
				removedNumber = currentRows - rows;
				table.find('.data-column').each(function(){
					for(i = 0; i < removedNumber; i++){
						$(this).find('.data-row:last').remove();
					}
				});
				
			}else{
				addedNumber = rows - currentRows;
				table.find('.data-column').each(function(){
					for(i = 0; i < addedNumber; i++){
						row = $('<div>').addClass('data-row');
						row.append($('<input>').attr({type: 'text'}).addClass('data-field').val('Data Field ' + (currentRows + i + 1)));	
						$(this).append(row);
					}
					
				});
			}
		}	
		var width = 100 / table.find('.data-column').size();
		table.find('.data-column').width(width + '%');
		
		container.append(table);
		container.data('table', table);
		table.find('.data-clear').remove();
		table.append('<div class="data-clear" style="clear: both;"></div>');
		
	}
	
	$('#bt-generator').on('click', '.bt-generator-table-apply',function(){
		
		var container = $('.bt-generator-table-field-container');
		var table = container.data('table');
		if(typeof(table) == 'undefined') return false;		
		var content = '<table>';
		var rows = new Array();
		rows[0] = '';
		table.find('.data-column').each(function(){
			rows[0] += '<th>' + $(this).find('.data-title').val() + '</th>';
			var j = 1;
			$(this).find('.data-row').each(function(){
				var data = '<td>' + $(this).find('.data-field').val() + '</td>' ;
				if(typeof(rows[j]) == 'undefined'){
					rows[j] = data ;
				}else{
					rows[j]  +=  data;
				}
				j++;
			});
		});

		for(var i = 0; i < rows.length; i++){
			content+= '<tr>' + rows[i] + '</tr>';
		}
		content += '</table>';
		$('#bt-generator-content').html(content);
	});
	
	/**
	 * Function generateColumns
	 */
	function generateColumns(container, button){
		var numberCol = $(button).html();
		var col = 12/numberCol;
		container.find('.bt-column').remove();
		for(var i = 0; i < numberCol; i++){
			var column = $('<div>').addClass('bt-column col-xs-12 col-sm-' + col + ' col-md-' + col + ' col-lg-' + col).attr('data-col', col);
			column.append($('<div>').append($('<i>').addClass('fa fa-arrows-h')));		
			container.prepend(column);
			column.find('i').on('click', function(){mergeColumns(container, this)});
		}
		$(button).siblings().removeClass('selected');
		$(button).addClass('selected');
		columnChangeContent(container);
	}
	
	function mergeColumns(container, i){
		var column = $(i).parents('.bt-column');
		var index = column.index();
		var col = column.data('col');
		var colMerged = 0;
		
		if(index == container.find('.bt-column').size() - 1){
			colMerged = column.prev().data('col');
			column.prev().remove();
		}else{
			colMerged = column.next().data('col');
			column.next().remove();
		}
		
		var newCol = col + colMerged;
		column.removeClass('col-xs-12 col-sm-' + col + ' col-md-' + col + ' col-lg-' + col);
		column.addClass('col-xs-12 col-sm-' + newCol + ' col-md-' + newCol + ' col-lg-' + newCol);
		column.attr('data-col', col + colMerged);
		column.data('col', col + colMerged);
		columnChangeContent(container);
	}
	function columnChangeContent(container){
		var content = '';
		container.find('.bt-column').each(function(){
			content += '[' + $prefix.val() + 'column class="' + $(this).attr('class') + '"]';
			content += 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.';
			content += '[/'+ $prefix.val() + 'column]';
			content += "\n";
		});
		
		
		$('#bt-generator-content').html(content);
	}
	
	function generateTabs(container, tabsNumber){
		var currentNumber = container.find('.bt-generator-tab').size();
		
		tabsNumber = $(tabsNumber).val();

		if(currentNumber > tabsNumber){
			for(var i = 0; i < currentNumber - tabsNumber; i++){
				container.find('.bt-generator-tab:last').remove();
			}
		}else{
			for(var i = 0; i < tabsNumber - currentNumber; i++){
				var tab = $('<div>').addClass('bt-generator-tab');
				var tabHtml = '<div class="bt-generator-attr-container">';
				tabHtml += '<input type="text" class="bt-generator-icon-picker-value" id="bt-generator-tab-icon-' + (currentNumber + i + 1) + '" value="" name="tab-icon' + (currentNumber + i + 1) + '" placeholder="Tab Icon ' + (currentNumber + i + 1) + '"/>';
				tabHtml += '<div class="bt-generator-field-actions">';
				tabHtml += '<a class="button bt-generator-icon-picker-button bt-generator-field-action" href="javascript:;"><i style="margin-right:5px;" class="fa fa-folder-open"></i> Icon picker</a>';
				tabHtml	+= '</div>';
				tabHtml += '<div class="bt-generator-icon-picker bt-generator-clearfix"><input type="text" placeholder="Filter icons" class="widefat"></div>';
				tabHtml += '</div>';
				tabHtml += '<div class="bt-generator-attr-container"><input type="text" class="tab-title" name="tab-title-' + (currentNumber + i + 1) + '" value="Tab Title ' + (currentNumber + i + 1) + '"/></div>';
				tabHtml += '<div class="bt-generator-attr-container"><textarea class="tab-content " name="tab-content-' + (currentNumber + i + 1) + '">Tab Content ' + (currentNumber + i + 1) + '</textarea></div>';
				tabHtml += '<div class="bt-generator-attr-container"><input type="text" class="tab-class" name="tab-class-' + (currentNumber + i + 1) + '" value="" placeholder = "Tab Class ' + (currentNumber + i + 1) + '"/></div>';
				tab.html(tabHtml);
				tab.find('.bt-generator-icon-picker-button').each(function(){iconpickerClicked(this)});
				container.append(tab);
			}
		}
		
	}
	
	$('#bt-generator').on('click', '.bt-generator-tabs-apply',function(){
		
		var container = $('.bt-generator-tabs-container');
		var tabs = container.find('.bt-generator-tab');
		if(tabs.size() <=0 ) return false;		
		var content = '';
		tabs.each(function(){
			content += '[' + $prefix.val() + 'tab ';
			var icon = $(this).find('.bt-generator-icon-picker-value').val();
			var tabTitle = $(this).find('.tab-title').val();
			var tabContent = $(this).find('.tab-content').val();
			var tabClass = $(this).find('.tab-class').val();
			if (icon !='') content += 'icon="' + icon + '" ';
			if (tabClass != '') content += 'class="' + tabClass + '" ';
			content += 'title="' + tabTitle + '"]' + tabContent + '[/' + $prefix.val() + 'tab]';
		});
		
		$('#bt-generator-content').html(content);
	});
	
	function iconpickerClicked(button){
		
		var $button = $(button),
		$field = $button.parents('.bt-generator-attr-container'),
		$val = $field.find('.bt-generator-icon-picker-value'),
		$iconList = $field.find('.bt-generator-icon-picker'),
		$filterIcon = $iconList.find('input:text');
		
		// get list icon
		$button.on('click', function(){
			$iconList.toggleClass('bt-generator-icon-picker-visiable');
			if($iconList.hasClass('bt-generator-icon-loaded')) return;
			$.ajax({
				type:'post',
				url: ajaxUrl + '&task=icon',
				dataType: 'html',
				beforeSend: function (){
					$filterIcon.hide();
					// show loading animation
					$iconList.addClass('bt-generator-loading');
					// mark icons is loaded
					$iconList.addClass('bt-generator-icon-loaded');
				},
				success: function (data){
					$iconList.removeClass('bt-generator-loading');
					$filterIcon.show();
					$iconList.append(data);
					var $icons = $iconList.children('i');
					$icons.click(function (){
						$val.val('icon:' + $(this).attr('title')).trigger('change');
						$iconList.removeClass('bt-generator-icon-picker-visiable');
					});
					$filterIcon.on({
						keyup:function (){
							// get val of filter
							var val = $(this).val();
							// hide all icons
							$icons.hide();
							// macth regex and show icon matched
							regex = new RegExp(val, 'gi');
							$icons.each(function (){
								//get icon name
								var name = $(this).attr('title');
								if(name.match(regex) !== null) $(this).show();
							});
						},
						focus: function (){
							$(this).val('');
							$icons.show();
						}
					});
				}
			});
		});
	}
});
