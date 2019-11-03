jQuery(function($) {

    // Language strings
    var lang = {
        "CHOICE_ERROR" : "This item must contain at least one choice"
    };

	$(document).on('change', ".nr-choice-item input.nr-choice-default", function(e) {
		var $this = $(this),
			list  = $this.closest(".nr_choices");

		list.find('input[type=radio]').not($this).prop('checked', false).trigger("update");
		
		$(document).trigger('nrChoicesUpdate');
	});

	$(document).on('change', ".nr-choice-settings .showvalues", function(e) {
		var $this = $(this),
			list  = $this.closest(".nr-choice-settings").prev(),
			isChecked = $this.is(":checked");

		// Show or hide inputs
		list.find('input.nr-choice-value').css({
			"display" : isChecked ? "block" : "none"
		});
	});

	/**
	 *  Remove Choice Event
	 */
	$(document).on("click", ".nr-choice-remove", function(e) {
		e.preventDefault();
		remove($(this));
	});

	/**
	 *  Add Choice Event
	 */
	$(document).on("click", ".nr-choice-add", function(e) {
		e.preventDefault();
		add($(this));
	});

	/**
	 *  Plugin Initializer
	 */
	function init() {
		// Initialize UI Sortable
		initSortable();
        
        // Make some fixes on newly created repeatable fields
        $(document).on("subform-row-add", function(event, row) {

        	$row = $(row);

            if ($row.find(".nr_choices").length) {
                $(".nr_choices").sortable(sortableOptions);

                // Fix wrong "for" attribute for newly created labels
                $row.find(".nr-choice-settings label").each(function() {
                	$(this).attr("for", $(this).prev().attr("id"));
                });
            }
        })

        // Support ConvertForms Fields Manager and reinitialize sortable whenever a new field is added.
        $(document).on("afterAddField", function(event) {
	        initSortable();
        })
	}

	init();

	/**
	 *  Add sorting capabilities to choices
	 *
	 *  @return  void
	 */
	function initSortable() {
		el = $(".nr_choices");

		if (el.length == 0) {
			return;
		}

        el.sortable({
            handle: ".nr-choice-sort",
            update: function() {
                $(document).trigger("nrChoicesUpdate");
            }
        });	
	}

	/**
	 *  Remove Choice Method
	 *
	 *  @param   Object  el 
	 *
	 *  @return  void
	 */
	function remove(el) {
		var list  = el.closest(".nr_choices"),
			total = parseInt(list.find(".nr-choice-item").length),
			min   = parseInt(list.data("min"));
		
		// Wait my friend. You can't remove everything.
		if (total == min) {
			alert(lang['CHOICE_ERROR']);
			return;
		}
		
		el.closest(".nr-choice-item").remove();

		// Trigger Update Event
		$(document).trigger('nrChoicesUpdate');
	}

	/**
	 *  Add Choice
	 *
	 *  @param  Object  el
	 */
	function add(el) {
		var parent    = el.closest(".nr-choice-item"),
			checked   = parent.find('input[type=radio]').is(':checked'),
			list      = el.closest(".nr_choices"),
			id        = parseInt(list.attr("data-nextid")),
			fieldName = list.attr("data-fieldname"),
			choice    = parent.clone().insertAfter(parent);

		// Fix wrong fieldname when the choice field is within a repeatable field
		repeatableField = list.closest(".subform-repeatable-group");

		if (repeatableField.length && repeatableField[0].hasAttribute("data-new")) {
			// Only if it's a new field
			repeatableIndex = repeatableField.data('group');
			fieldName = fieldName.replace("[fieldsX]", "["+repeatableIndex+"]");
		}

		// Fix new choice IDs and field names
		choice.attr('data-id', id);
		choice.find('input.nr-choice-label').val('').attr('name', fieldName+'[choices]['+id+'][label]');
		choice.find('input.nr-choice-value').val('').attr('name', fieldName+'[choices]['+id+'][value]');
		choice.find('input.nr-choice-default').attr('name', fieldName+'[choices]['+id+'][default]').prop('checked', false);

		// For a reason the parent gets unchecked. Let's recheck it if it needs so.
		if (checked == true) {
			parent.find('input[type=radio]').prop('checked', true);
		}

		// Increase next id
		id++;
		list.attr("data-nextid", id);

		// Trigger Update Event
		$(document).trigger('nrChoicesUpdate');
	}

})