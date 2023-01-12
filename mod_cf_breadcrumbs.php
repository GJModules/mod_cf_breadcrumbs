<?php
/**
 * @package     Breakdesigns.CustomFilters
 *
 * @copyright   Copyright © 2010-2021 Breakdesigns.net. All rights reserved.
 * @license     GNU Geneal Public License 2 or later, see COPYING.txt for license details.
 */

// no direct access
use Joomla\CMS\Factory;

defined('_JEXEC') or die();


//load dependencies
require_once dirname(__FILE__) . '/bootstrap.php';

$language = Factory::getLanguage();
$language->load('mod_cf_filtering');

$helper = new ModCfBreadcrumbsHelper($module);
$list = $helper->getList();
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

require JModuleHelper::getLayoutPath('mod_cf_breadcrumbs', $params->get('layout', 'default'));
