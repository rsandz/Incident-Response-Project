/*
	Used to provide descriptions when needing to choose actions, Projects, etc.
*/

$(function() {
    setActions();
	setProjectInfo();
	
	//Bind event listeners
    $("#project-selector")
        .change(setProjectInfo)
        .change(setActions);
    $("#type-selector").change(setActions);
});

/**
 * Sets the action description in the #action-desc element
 * based on the #action-selector value
 * @return void
 */
function setActionInfo() {
    if ($("#action-selector").val() !== "NULL") {
        $.get(
            $("#ajax-link").attr("data") + "/get_info",
            {
                id: $("#action-selector").val(),
                table: "actions"
            },
            function(data) {
                data = $.parseJSON(data);
                $("#action-desc").html(
                    "<strong>Action Description</strong>: </br>" + data.desc
                );
            }
        ).fail(promptError);
    } else {
        $("#action-desc").html("<strong>No Description</strong>");
    }
}

/**
 * Sets the descripiton in #project-desc based on #project-selector
 * @return void
 */
function setProjectInfo() {
    $.get(
        $("#ajax-link").attr("data") + "/get_info",
        { id: $("#project-selector").val(), table: "projects" },
        function(data) {
            data = $.parseJSON(data);
            $("#project-desc").html(
                "<strong>Project Description</strong>: </br>" + data.desc
            );
        }
    ).fail(promptError);
}

/**
 * Sets the actions based on the currently selected project and team
 * @return void
 */
function setActions() {
    selectedProject_id = $("#project-selector").val();
    $.get(
        $("#ajax-link").attr("data") + "/get_action_items",
        {
            project_id: selectedProject_id,
            type_id: $("#type-selector").val()
        },
        function(data) {
            data = $.parseJSON(data);
            $("#action-div").html(data);

            $("#action-selector").change(setActionInfo); //Re-add the even listener
            setActionInfo();
        }
    ).fail(promptError);
}

/**
 * Notify the user if an AJAX error occured.
 * @return void
 */
function promptError() {
    if ($("#errors").length > 0) {
        $("#errors").html(
            '<div class="notification is-danger">An AJAX Error has occured. Try reloading the page or tyrying again later.</div>'
        );
    } else {
        alert("An AJAX Error has occured. Try reloading the page or tyrying again later.");
    }
}
