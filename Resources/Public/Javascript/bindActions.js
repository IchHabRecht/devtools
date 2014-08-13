(function ($) {

	$(document).ready(function() {
		bindActions();
	});

	function bindActions() {
		$('.list-modified-files').each(function() {
			$(this).data('href', $(this).attr('href'));
			$(this).attr('href', '#');
			$(this).click(function() {
				$('.typo3-extension-manager').mask();
				$.ajax({
					url: $(this).data('href'),
					success: function(data) {
						$('.typo3-extension-manager').unmask();
						TYPO3.Dialog.InformationDialog({
							title: 'Test title',
							msg: data.message
						});
					},
					error: function() {
						$('.typo3-extension-manager').unmask();
					}
				});
			});
		});
	}

}(jQuery));