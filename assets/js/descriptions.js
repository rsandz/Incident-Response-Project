/*
	Used to provide descriptions when needing to choose actions, Projects, etc.
*/

$(function() {
    //Initializes selection for action
    $('select#action').select2({
        ajax: {
            url: $('#ajax-link').attr('data') + '/get_action_items',
            dataType: 'json',
            data: function(params) {
                let query = {
                    term: params.term,
                    project_id: $("#project-selector").val(),
                    type_id: $("#type-selector").val()
                }
                return query;
            },
            processResults: function(data) {
                if (!data.results) {
                    data.results = [{
                        text: 'No Actions Found',
                        id:   'null',
                        disabled: 'disabled'
                    }];
                    console.log(data);
                }
                return data;
            }
        },
        placeholder: 'Select an Action...'
    });
    $('select#action').change(setActionInfo);

	setProjectInfo();
	$('#action').trigger('change');
	//Bind event listeners
    $("#project-selector")
        .change(setProjectInfo)
        .change(() => $('#action').val(null).trigger('change'));
    $("#type-selector").change(() => $('#action').val(null).trigger('change'));


});

/**
 * Sets the action description in the #action-desc element
 * based on the #action-selector value
 * @return void
 */
function setActionInfo() {
    if ($("#action").val() !== "NULL") {
        $('#action-desc').html('<i class="spinner fa fa-spinner fa-pulse fa-fw"></i>'); 
        $.get(
            $("#ajax-link").attr("data") + "/get_info",
            {
                id: $("#action").val(),
                table: "actions"
            },
            function(data) {
                data = $.parseJSON(data);
                $("#action-desc").html(
                    "<strong>Action Description</strong>: </br>" + data.desc
                );
            }
        )
    } else {
        $("#action-desc").html("<strong>No Description</strong>");
    }
}

/**
 * Sets the descripiton in #project-desc based on #project-selector
 * @return void
 */
function setProjectInfo() {
    $('#project-desc').html('<i class="spinner fa fa-spinner fa-pulse fa-fw"></i>'); 
    $.get(
        $("#ajax-link").attr("data") + "/get_info",
        { id: $("#project-selector").val(), table: "projects" },
        function(data) {
            data = $.parseJSON(data);
            $("#project-desc").html(
                "<strong>Project Description</strong>: </br>" + data.desc
            );
        }
    )
}
