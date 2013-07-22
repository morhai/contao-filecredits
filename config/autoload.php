<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Filecredits
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'HeimrichHannot\ModuleFileCredit'      => 'system/modules/filecredits/modules/ModuleFileCredit.php',

	// Models
	'HeimrichHannot\FileCreditHybridModel' => 'system/modules/filecredits/models/FileCreditHybridModel.php',
	'HeimrichHannot\FileCreditModel'       => 'system/modules/filecredits/models/FileCreditModel.php',

	// Classes
	'HeimrichHannot\FileCredit'            => 'system/modules/filecredits/classes/FileCredit.php',
	'HeimrichHannot\FileCreditNews'        => 'system/modules/filecredits/classes/FileCreditNews.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'filecredit_default'   => 'system/modules/filecredits/templates',
	'mod_filecredit'       => 'system/modules/filecredits/templates',
	'mod_filecredit_empty' => 'system/modules/filecredits/templates',
));