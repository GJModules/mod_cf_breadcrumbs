<?php
/**
 * @package     Breakdesigns.CustomFilters
 *
 * @Copyright   Copyright © 2010-2021 Breakdesigns.net. All rights reserved.
 * @license     GNU Geneal Public License 2 or later, see COPYING.txt for license details.
 */

defined('_JEXEC') or die;
?>
<a class="cf_tag cf_tag_icon_close" href="<?php echo $item->url?>" rel="nofollow">
    <span class="cf_tag_inner">
        <?= JText::_($item->name);?>
    </span>
    <span _ngcontent-rz-client-c178="" class="catalog-selection__remove-icon">
        <svg _ngcontent-rz-client-c178="" width="16" height="16">
            <use _ngcontent-rz-client-c178="" xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-remove"></use>
        </svg>
    </span>
</a>
