(function ($) {

	$(document).ready(function () {
		bindActions();
		$('#typo3-extension-list').on('draw.dt', bindActions);
	});

	function bindActions() {
		$('.list-modified-files, .update-configuration-file').not('.transformed').each(function () {
			$(this).data('href', $(this).attr('href'));
			$(this).attr('href', '#');
			$(this).addClass('transformed');
			$(this).click(function () {
				$('.typo3-extension-manager').mask();
				$.ajax({
					url: $(this).data('href'),
					success: function (data) {
						if (data.message) {
							showInformationDialog(data.title, data.message);
						} else {
							showErrorDialog();
						}
					},
					error: function () {
						showErrorDialog();
					}
				});
			});
		});
	}

	function showInformationDialog(title, message) {
		$('.typo3-extension-manager').unmask();
		TYPO3.Dialog.InformationDialog({
			title: title,
			msg: message
		});
	}

	function showErrorDialog() {
		$('.typo3-extension-manager').unmask();
		TYPO3.Dialog.ErrorDialog({
			title: TYPO3.l10n.localize('devtools.error.title'),
			msg: TYPO3.l10n.localize('devtools.error.message')
		});
	}

}(jQuery));