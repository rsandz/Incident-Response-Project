$(function() {
	setupConfirmModal('#delete-users', '#delete-confirm');
});

/**
 * Setups a Confirm modal's JS.
 * ----------------------------
 * @author Ryan Sandoval, June 2018
 *
 * Binds an onclick callback onto the launch modal button (launchModalBtn). When clicked, it will toggle
 * the 'is-active' class on the modal (modalDiv).
 *
 * The script will also bind an onclick callback to any elements with the 'modal-cancel' class that will
 * toggle the 'is-active' class on the nearest modal parent. Thus it is best used for buttons canceling 
 * the modal within the modal div itself.
 * 
 * Use with BULMA CSS and the modal styling.
 * 
 * @param  {string} launchModalBtn The jQuery selector for the delete button that opens the modal
 * @param  {string} modal        The selector for the modal.
 */
function setupConfirmModal(launchModalBtn, modalDiv)
{
	$(launchModalBtn).click(function(){
		$(modalDiv).toggleClass('is-active');
	});
	$('.modal-cancel').click(function() {
		$(this).parentsUntil('modal').toggleClass('is-active');
	});
}