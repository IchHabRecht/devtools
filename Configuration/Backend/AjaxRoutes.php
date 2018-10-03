<?php
use IchHabRecht\Devtools\Controller\Slot\Extensionmanager\ProcessActions\ModifiedFilesController;
use IchHabRecht\Devtools\Controller\Slot\Extensionmanager\ProcessActions\UpdateConfigurationFileController;

return [
    'DevtoolsFilesModifiedList' => [
        'path' => '/files/modified/list',
        'target' => ModifiedFilesController::class . '::listFiles',
    ],
    'DevtoolsFilesModifiedUpdate' => [
        'path' => '/files/modified/update',
        'target' => UpdateConfigurationFileController::class . '::updateConfigurationFile',
    ],
];
