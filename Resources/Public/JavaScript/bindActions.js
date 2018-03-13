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
		top.TYPO3.Modal.confirm(
			title,
			message,
			top.TYPO3.Severity.info,
			[{
				text: TYPO3.lang['button.ok'] || 'OK',
				btnClass: 'btn-' + top.TYPO3.Modal.getSeverityClass(top.TYPO3.Severity.info),
				active: true,
				trigger: function() {
					top.TYPO3.Modal.dismiss();
				}
			}]
		);
	}

	function showErrorDialog() {
		top.TYPO3.Modal.confirm(
			TYPO3.lang['devtools.error.title'],
			TYPO3.lang['devtools.error.message'],
			top.TYPO3.Severity.error,
			[{
				text: TYPO3.lang['button.ok'] || 'OK',
				btnClass: 'btn-' + top.TYPO3.Modal.getSeverityClass(top.TYPO3.Severity.error),
				active: true,
				trigger: function() {
					top.TYPO3.Modal.dismiss();
				}
			}]
		);
	}

}(jQuery));