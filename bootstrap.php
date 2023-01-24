<?php
/**
 * @package     Breakdesigns.Customfilters
 *
 * @Copyright   Copyright © 2010-2021 Breakdesigns.net. All rights reserved.
 * @license     GNU Geneal Public License 2 or later, see COPYING.txt for license details.
 */

JLoader::register('CfInput', JPATH_SITE . '/components/com_customfilters/include/input.php');
JLoader::register('CfOutput', JPATH_SITE . '/components/com_customfilters/include/output.php');
JLoader::register('cftools', JPATH_SITE . '/components/com_customfilters/include/tools.php');
JLoader::register('ModCfBreadcrumbsHelper', dirname(__FILE__) . '/helper.php');
JLoader::register('OptionsHelper', JPATH_SITE . '/modules/mod_cf_filtering/optionsHelper.php');
JLoader::register('Breakdesigns\Module\CfBreadcrumbs\UrlHandler', dirname(__FILE__) . '/UrlHandler.php');
JLoader::register('ProductsQueryBuilder',  JPATH_ROOT . '/components/com_customfilters/models/Products/ProductsQueryBuilder.php');

JLoader::registerNamespace( 'GNZ11' , JPATH_LIBRARIES . '/GNZ11' , $reset = false , $prepend = false , $type = 'psr4' );
JLoader::register( 'seoTools' , JPATH_ROOT . '/components/com_customfilters/include/seoTools.php');
JLoader::register('seoTools_uri' , JPATH_ROOT .'/components/com_customfilters/include/seoTools_uri.php');