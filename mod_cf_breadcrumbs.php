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

/**
 * @var Joomla\Registry\Registry $params
 * @var stdClass                 $module
 * @var ModCfFilteringHelper     $FilteringHelper
 */

//load dependencies
require_once dirname(__FILE__) . '/bootstrap.php';

$language = Factory::getLanguage();
$language->load('mod_cf_filtering');


$app = \Joomla\CMS\Factory::getApplication();
$juri = \Joomla\CMS\Uri\Uri::getInstance();
$filterUrl = $juri->getPath();
$view = $app->input->get('view' , false , 'STRING ') ;
$app->input->set('filter-url' , md5( $filterUrl ) );

 
//echo'<pre>';print_r( $filterUrl );echo'</pre>'.__FILE__.' '.__LINE__;
//echo'<pre>';print_r( $juri );echo'</pre>'.__FILE__.' '.__LINE__;
//echo'<pre>';print_r( $app->input );echo'</pre>'.__FILE__.' '.__LINE__;


$cacheparams = new stdClass;
$cacheparams->cachemode = 'safeuri';
$cacheparams->class = 'ModCfBreadcrumbsHelper';
$cacheparams->method = 'getDataModCfBreadcrumbsCache';
$cacheparams->methodparams = [ $module , $params ];
$cacheparams->modeparams = [
	'Itemid' => 'INT',
	'virtuemart_category_id' => 'ARRAY',
	'virtuemart_manufacturer_id' => 'ARRAY',
	'filter-url' => 'STRING',
];
// Отключить Cache - для Developer
if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
{
//	$params->set('owncache' , 0 );

}

//$params->set('owncache' , 0 );

$option = $app->input->get('option') ;
$list = [] ;
if ( ( $view != 'productdetails' ) &&  ($option == 'com_virtuemart' || $option == 'com_customfilters')   )
{
 	$list = \Joomla\CMS\Helper\ModuleHelper::moduleCache($module, $params, $cacheparams);


	 // Получить количество найденных товаров
	$prefix = 'CustomfiltersModel' ;
	$path = JPATH_ROOT.'/components/com_customfilters/models';
	JModelLegacy::addIncludePath( $path , 'CustomfiltersModel' );
	/**
	 * @var CustomfiltersModelProducts $ModelProducts
	 */
	$ModelProducts = JModelLegacy::getInstance( 'Products' , $prefix ,  $config = array() );
	$ProductListing = $ModelProducts->getProductListing();
	$counter = count( $ProductListing );

	$ResultFilterDescription = $app->get('ResultFilterDescription');

	$counter = $ResultFilterDescription['{{COUNT_PRODUCT_INT}}'] ;

	$resetLink = $list[0] ;
	unset( $list[0] ) ;



	$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');
	require JModuleHelper::getLayoutPath('mod_cf_breadcrumbs', $params->get('layout', 'default'));

}#END IF


