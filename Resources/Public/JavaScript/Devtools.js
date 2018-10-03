/**
 * Module: TYPO3/CMS/Devtools/Devtools
 */
define(['jquery', 'TYPO3/CMS/Backend/Modal', 'TYPO3/CMS/Backend/Severity'], function($, Modal, Severity) {
    'use strict';

    var Devtools = {};

    Devtools.initialize = function() {
        Devtools.bindActions();
        $('#typo3-extension-list').on('draw.dt', Devtools.bindActions);

    };

    Devtools.bindActions = function() {
        $('.list-modified-files, .update-configuration-file').not('.transformed').each(function() {
            $(this).data('href', $(this).attr('href'));
            $(this).attr('href', '#');
            $(this).addClass('transformed');
            $(this).click(function() {
                $.ajax({
                    url: $(this).data('href'),
                    success: function(data) {
                        if (data.message) {
                            Devtools.showInformationDialog(data.title, data.message);
                        } else {
                            Devtools.showErrorDialog();
                        }
                    },
                    error: function() {
                        Devtools.showErrorDialog();
                    }
                });
            });
        });
    };

    Devtools.showInformationDialog = function(title, message) {
        var btnClass = Severity.getCssClass ? Severity.getCssClass(Severity.info) : Modal.getSeverityClass(Severity.info);
        Modal.confirm(
            title,
            message,
            Severity.info,
            [
                {
                    text: TYPO3.lang['button.ok'] || 'OK',
                    btnClass: 'btn-' + btnClass,
                    active: true,
                    trigger: function() {
                        Modal.dismiss();
                    }
                }
            ]
        );
    };

    Devtools.showErrorDialog = function() {
        var btnClass = Severity.getCssClass ? Severity.getSeverityClass(Severity.error) : Modal.getSeverityClass(Severity.error);
        Modal.confirm(
            TYPO3.lang['devtools.error.title'],
            TYPO3.lang['devtools.error.message'],
            Severity.error,
            [
                {
                    text: TYPO3.lang['button.ok'] || 'OK',
                    btnClass: 'btn-' + btnClass,
                    active: true,
                    trigger: function() {
                        Modal.dismiss();
                    }
                }
            ]
        );
    };

    $(function() {
        Devtools.initialize();
    });

    return Devtools
});
