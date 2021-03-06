<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package filecredits
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['miscellaneous']['filecredit'] = 'HeimrichHannot\FileCredit\ModuleFileCredit';

/**
 * Back end modules
 */
array_insert(
    $GLOBALS['BE_MOD']['system'],
    1,
    [
        'filecredit' => [
            'tables' => ['tl_filecredit', 'tl_filecredit_page'],
            'icon'   => 'system/modules/filecredits/assets/img/icon.png',
            'sync'   => ['HeimrichHannot\FileCredit\Backend\FileCredit', 'sync'],
        ],
    ]
);

/**
 * Javascript
 */
if (TL_MODE == 'BE') {
    $GLOBALS['TL_JAVASCRIPT']['filecredits-be'] = 'system/modules/filecredits/assets/js/filecredits_be.js|static';
}


/**
 * Css
 */
if (TL_MODE == 'BE') {
    $GLOBALS['TL_CSS']['filecredits-be'] = 'system/modules/filecredits/assets/css/filecredits_be.css';
}


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['executeResize'][]     = ['HeimrichHannot\FileCredit\Hooks', 'executeResizeHook'];
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = ['HeimrichHannot\FileCredit\Hooks', 'replaceInsertTagsHook'];
$GLOBALS['TL_HOOKS']['getPageLayout'][]     = ['HeimrichHannot\FileCredit\Hooks', 'getPageLayoutHook'];
$GLOBALS['TL_HOOKS']['modifyFrontendPage'][] = ['HeimrichHannot\FileCredit\Hooks', 'modifyFrontendPageHook'];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_filecredit']      = 'HeimrichHannot\FileCredit\FileCreditModel';
$GLOBALS['TL_MODELS']['tl_filecredit_page'] = 'HeimrichHannot\FileCredit\FileCreditPageModel';

/**
 * Crons
 */
$GLOBALS['TL_CRON']['daily']['huh.filecredits.poormanscron'] = ['HeimrichHannot\FileCredit\PoorMansCron', 'daily'];