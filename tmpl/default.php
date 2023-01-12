<?php
/**
 * @package     Breakdesigns.CustomFilters
 *
 * @Copyright   Copyright © 2010-2021 Breakdesigns.net. All rights reserved.
 * @license     GNU Geneal Public License 2 or later, see COPYING.txt for license details.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

$jinput = Factory::getApplication()->input;
$view = $jinput->get('view', 'products', 'cmd');
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'modules/mod_cf_breadcrumbs/assets/css/tags.css');
$layouts = [3 => '_text', 10 => '_color'];

/*
* view == module is used only when the module is loaded with ajax.
* We want only the form to be loaded with ajax requests.
* The cf_wrapp_all of the primary module, will be used as the container of the ajax response
* Do NOT change the classses and id of the wrapper, are used by the update script
*/
if ($view != 'module'){
?>
<div id="cf_wrapp_all_<?php echo $module->id ?>"
     class="cf_breadcrumbs_wrapper cf_breadcrumbs_wrapper_<?php echo $moduleclass_sfx; ?>"
     data-moduleid="<?php echo $module->id ?>">
    <?php } ?>

    <?php foreach ($list as $filterName => $items) {
        foreach ($items as $item) {
            $layout = $layouts[$item->display];
            require JModuleHelper::getLayoutPath('mod_cf_breadcrumbs', $layout);
        }
    }
    if ($view != 'module'){
    ?>
</div>
<?php } ?>

