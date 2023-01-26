<?php
/**
 * @package     Breakdesigns.CustomFilters
 *
 * @Copyright   Copyright © 2010-2021 Breakdesigns.net. All rights reserved.
 * @license     GNU Geneal Public License 2 or later, see COPYING.txt for license details.
 */

defined( '_JEXEC' ) or die;

/**
 * @var stdClass $module
 * @var string   $moduleclass_sfx
 * @var array    $list      - Массив включенных фильтров
 * @var int      $counter   - Количество найденных товаров
 * @var array    $resetLink - ссылка сбросить
 */

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

$__v = ModCfBreadcrumbsHelper::getModuleVersion();

if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
{
//    echo'<pre>';print_r( $counter );echo'</pre>'.__FILE__.' '.__LINE__;

}

$jinput   = Factory::getApplication()->input;
$view     = $jinput->get( 'view' , 'products' , 'cmd' );
$document = Factory::getDocument();
$document->addStyleSheet( Uri::root().'modules/mod_cf_breadcrumbs/assets/css/tags.css?v=' . $__v  );
$layouts = [ 3 => '_text' , 10 => '_color' ];

if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
{
//	echo'<pre>';print_r( $counter );echo'</pre>'.__FILE__.' '.__LINE__;

}


/*
* view == module is used only when the module is loaded with ajax.
* We want only the form to be loaded with ajax requests.
* The cf_wrapp_all of the primary module, will be used as the container of the ajax response
* Do NOT change the classses and id of the wrapper, are used by the update script
*/

$titles = array(' товар', ' товара', ' товаров');
$_prodTxt = \GNZ11\Document\Text::declOfNum ( $counter , $titles );


if ( $view != 'module' )
{
?>
<div id="cf_wrapp_all_<?php echo $module->id ?>"
     class="cf_breadcrumbs_wrapper cf_breadcrumbs_wrapper_<?php echo $moduleclass_sfx; ?>"
     data-moduleid="<?php echo $module->id ?>">
	<?php }
?>

    <ul  class="breadcrumb <?= !empty($list) ?: 'empty' ?>">
        <li class="item_catalog-selection">
            <span class="catalog-selection__label ng-star-inserted">
                Выбрано <?= $counter . ' ' . $_prodTxt ?>
            </span>
        </li>


        <li class="item_catalog-selection reset-link">
            <a class="cf_tag cf_tag_icon_close" href="<?= $resetLink['url'] ?>" rel="nofollow">
                <span class="cf_tag_inner">
                    <?=JText::_( $resetLink['name'] );?>
                </span>
                <!--<span _ngcontent-rz-client-c178="" class="catalog-selection__remove-icon">
                <svg _ngcontent-rz-client-c178="" width="16" height="16">
                    <use _ngcontent-rz-client-c178="" xmlns:xlink="http://www.w3.org/1999/xlink"
                         xlink:href="#icon-remove"></use>
                </svg>
            </span>-->
            </a>
        </li>

        <?php foreach ( $list as $filterName => $items )
	    {
		    foreach ( $items as $i => $item )
		    {
			    $layout = $layouts[ $item->display ];
                ?>
                <li class="item_catalog-selection">
	                <?php require ModuleHelper::getLayoutPath( 'mod_cf_breadcrumbs' , $layout );?>
                </li>


                <?php
		    }
	    }?>
    </ul>


    <?php
	if ( $view != 'module' )
	{
	?>
</div>
<svg style="display: none">
    <defs id="symbols">
        <symbol viewBox="0 0 24 24" id="icon-remove">
            <path d="m18.295 7.11511c.3894-.38936.3894-1.02064 0-1.41-.3893-.38936-1.0206-.38936-1.41 0l-4.885 4.88499-4.88499-4.88499c-.38936-.38936-1.02063-.38936-1.41 0-.38936.38936-.38936 1.02064 0 1.41l4.88499 4.88499-4.88499 4.885c-.38936.3894-.38936 1.0206 0 1.41.38937.3894 1.02064.3894 1.41 0l4.88499-4.885 4.885 4.885c.3894.3894 1.0207.3894 1.41 0 .3894-.3894.3894-1.0206 0-1.41l-4.885-4.885z"></path>
        </symbol>
    </defs>
</svg>
<?php } ?>

