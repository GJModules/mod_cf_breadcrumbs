<?php
/**
 * @package     Breakdesigns.CustomFilters
 *
 * @Copyright   Copyright © 2010-2021 Breakdesigns.net. All rights reserved.
 * @license     GNU Geneal Public License 2 or later, see COPYING.txt for license details.
 */

defined('_JEXEC') or die;

$color_multi = explode('|', $item->name);
$count_colors = count($color_multi);
$width = 2 / $count_colors;
?>

<a class="cf_tag cf_tag_color cf_tag_icon_close" href="<?php echo $item->url?>" rel="nofollow">
    <?php
    foreach ($color_multi as $color) :?><span class="cf_tag_inner" style="background-color:#<?php echo $color?>; width:<?php echo $width?>em;">&nbsp;
        </span><?php endforeach;?>
</a>
