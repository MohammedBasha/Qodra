jQuery(function($) {
    /**
     *  Bind click event on button
     */
    $(".viewLists").click(function() {
    	var btn      = $(this),
            list     = $(this).next(),
            service  = $('#jform_service');
            formdata = $('.cf-service-fields input').add(service);

        if (list.is(":visible")) {
            list.slideUp();
            return;
        }

        for (var i = formdata.length - 1; i >= 0; i--) {
            if ($(formdata[i]).prop('required') && !$(formdata[i]).val() && $(formdata[i]).attr('id') != 'jform_list') {
                var id = $(formdata[i]).attr('id');
                var labelText = $('label[for='+id+']').text().trim();
                throwError("Please enter a valid " + labelText);
                return;
            }
        }

        token = $("#adminForm input[name=task]").prev().attr("name");

    	// Everything seems OK. Let's call service.
        $.ajax({  
            type: "POST",
            url: "index.php?option=com_ajax&format=raw&plugin=convertforms&task=lists&" + token + "=1",
            headers:{
                'X-CSRF-Token': token
            },
            data: formdata.serialize(),
            beforeSend: function() {
                $(".cf-service-fields .alert").remove();
                btn.addClass("cf-working disabled");
            },
            complete: function() {
                btn.removeClass("cf-working disabled");
            },
            success: function(response) {
                // Although the response is not supposed to be empty throw an error in case this happens.
                if (!response) {
                    throwError("Can't get lists");
                    return;
                }
                // The trick below tries to strip out any HTML tags appended by other
                // Joomla extensions to the document. Come on guys, this isn't your context.
                response = $("<div/>").html(response).text();

                // Now, try to parse response as a JSON object. If it fails inform user with an error message.
                try {
                    response = $.parseJSON(response);
                }
                // It's likely to fail if non-JSON text found
                catch(e) {
                    throwError(e + "\n" + response);
                }

                // Cool, We have a valid response! Now let's determine if the submission was successful.
                if (response.error) {
                	throwError(response.error);
                	return;
                }

                fetchLists(btn, response.lists);
            }
        });

        return false;
    }); 

    /**
     *  Bind Click Event to button
     *
     *  @return  bool
     */
    $(document).on("click", ".cflists a", function() {
    	selectList($(this));
    	return false;
    });

    /**
     *  Fetches available lists from API
     *
     *  @param   jQuery    btn   
     *  @param   array     data  The lists array
     *
     *  @return  void
     */
    function fetchLists(btn, data) {
        var html = "";

        data.each(function(list) {
            html = html + '<li><a href="#">' + list.name + ' <span class="id">' + list.id + '</span></a></li>';
        });

        $list = btn.next();
        $list.html(html).slideDown();

        // Auto select list ID
        value = btn.closest("div").find("input").val();
        if (value) {
            $list.find('span:contains("'+value+'")').closest("li").addClass("active");
        }
    }  

    /**
     *  Selects list and prefills the input with the list ID
     *
     *  @return  void
     */
    function selectList(obj) {
        // Remove all active classes
    	$(".cflists li").removeClass("active");

    	listID = obj.find(".id").text();

    	obj.closest("div").find("input").val(listID);
    	obj.parent().addClass("active");
    }

    /**
     *  Shows a balloon error messasge to the user
     *
     *  @param   string  message    The error message
     *
     *  @return  void
     */
    function throwError(message) {
        $(".cf-service-fields .alert").remove();
        $(".cf-service-fields .well-desc").after('<div class="alert alert-error">Error: ' + message + '</div>');
    }
});