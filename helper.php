<?php
/**
 * @package     Breakdesigns.CustomFilters
 *
 * @copyright   Copyright © 2010-2021 Breakdesigns.net. All rights reserved.
 * @license     GNU Geneal Public License 2 or later, see COPYING.txt for license details.
 */

// no direct access
defined('_JEXEC') or die();

use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Breakdesigns\Module\CfBreadcrumbs\UrlHandler;

/**
 * Class ModCfBreadcrumbsHelper
 *
 * This class is coupled with the filtering module's settings and OptionsHelper class
 * If there are more than 1 filtering modules in the page, it will folllow only the 1st settings.
 *
 * @since 1.0.0
 */
class ModCfBreadcrumbsHelper
{
    /**
     *
     * @var array
     * @since 1.0.0
     */
    protected $fltSuffix = array(
        'q' => 'keyword_flt',
        'virtuemart_category_id' => 'category_flt',
        'virtuemart_manufacturer_id' => 'manuf_flt',
        'price' => 'price_flt',
        'stock'=>'stock_flt',
        'custom_f' => 'custom_flt'
    );

    /**
     * @var stdClass
     * @since 1.0.0
     */
    protected $filteringModule;

    /**
     * @var stdClass
     * @since 1.0.0
     */
    protected $module;

    /**
     * ModCfBreadcrumbsHelper constructor.
     * @param $module
     * @since 1.0.0
     */
    public function __construct($module)
    {
        $this->module = $module;
        $this->filteringModule = \cftools::getModule();
    }

    /**
     * Main entry function that returns the selections
     *
     * @return array
     * @throws Exception
     * @since 1.0.0
     */
    public function getList()
    {
        $selections = [];
        $selected_flt = \CfInput::getInputs($cached = true);
        if (empty($selected_flt)) {
            return [];
        }

        // selected filters after encoding the output
        $selected_flt = $output = \CfOutput::getOutput($selected_flt);
        $selected['selected_flt'] = $selected_flt;
        $selected['selected_flt_modif'] = $selected_flt;
        $selected['selected_fl_per_flt'] = \CfOutput::getOutput(CfInput::getInputsPerFilter($this->module,
            $cached = true),
            $escape = true, $perfilter = true);

        $optionsHelper = \OptionsHelper::getInstance(\cftools::getModuleparams(), $this->filteringModule);
        $UrlHanlder = new UrlHandler($this->filteringModule, $selected);
        $published_setting_name = '_published';
        $params = new Registry($this->module->params);

        foreach ($selected_flt as $filterName => $values) {
            //the filter key is used as a key for the module's settings
            $filterKey = $filterName;
            $display = 3; //checkbox
            /*
             * Check if it's a color custom filter.
             * In that case we need another display type
             */
            if (strpos($filterName, 'custom_f_') !== false) {
                // get the filter id
                preg_match('/[0-9]+/', $filterName, $mathces);
                $id = $mathces[0];
                $custom_filters = \cftools::getCustomFilters();
                $customfilter = $custom_filters[$id];
                $display = in_array($customfilter->disp_type, [9, 10]) ? 10 : $display;
                $filterKey = 'custom_f';
            }
            if ($params->get($this->fltSuffix[$filterKey] . $published_setting_name) == false) {
                //the filter is unpublished
                continue;
            }

            $filter = ['var_name' => $filterName, 'display' => $display];

            $selections [$filterName] = [];
            if (is_array($values)) {

                if ($filterName == 'price') {
                    $values = $this->formatPrices($values);
                    $filterOption = new stdClass();
                    if (count($values) == 2) {
                        $filterOption->name = implode(' - ', $values);
                    } elseif (isset($values[0]) && !isset($values[1])) {
                        $filterOption->name = JText::_('MOD_CF_BREADCRUMBS_FROM') . ' ' . $values[0];
                    } elseif (isset($values[1]) && !isset($values[0])) {
                        $filterOption->name = JText::_('MOD_CF_BREADCRUMBS_TO') . ' ' . $values[1];
                    }
                    $filterOption->url = Route::_($UrlHanlder->getURL($filter, '', 'clear'));;
                    $filterOption->display = $display;
                    $selections [$filterName][] = $filterOption;
                    continue;
                }
                //multiple entry filters based on db values (categories, manufacturers, custom fields)
                if ($filterName == 'virtuemart_category_id') {
                    $categories = $optionsHelper->getOptions($filterName);
                    $filterOptions = $categories['options'];
                } else {
                    $filterOptions = $optionsHelper->getActiveOptions($filterName);
                }
                foreach ($values as $value) {
                    if (isset($filterOptions[$value])) {
                        $filterOptions[$value]->url = Route::_($UrlHanlder->getURL($filter, $value));
                        $filterOptions[$value]->display = $display;
                        $selections [$filterName][] = $filterOptions[$value];
                    }
                }
            } //single entry e.g.search
            else {
                $filterOption = new stdClass();
                $filterOption->name = $values;
                $filterOption->url = Route::_($UrlHanlder->getURL($filter, '', 'clear'));;
                $filterOption->display = $display;
                $selections [$filterName][] = $filterOption;
            }
        }
        return $this->sort($selections);
    }

    /**
     * Sort the selections according to the setting defined order
     *
     * @param array $selections
     * @return array
     * @since 1.0.0
     */
    protected function sort(array $selections)
    {
        $param_string = isset($this->filteringModule->params) ? $this->filteringModule->params : '';
        $params = new Registry($param_string);
        $sort_order = json_decode(str_replace("'", '"', $params->get('filterlist', "['q', 'virtuemart_category_id', 'virtuemart_manufacturer_id', 'price', 'stock', 'custom_f']")));
        $newSelection = [];
        $selection_keys = array_keys($selections);
        foreach ($sort_order as $sortKey) {
            foreach ($selection_keys as $filterName) {
                if ($sortKey == $filterName || strpos($filterName, $sortKey) !== false) {
                    $newSelection[$filterName] = $selections[$filterName];
                }
            }
        }
        return $newSelection;
    }

    /**
     * Format the prices by setting the currency symbols
     *
     * @param array $prices
     * @return array
     * @throws Exception
     * @since 1.0.0
     */
    protected function formatPrices(array $prices)
    {
        if (empty($prices)) {
            return [];
        }

        $japplication = \Joomla\CMS\Factory::getApplication();
        $jinput = $japplication->input;
        $vendor_currency = \cftools::getVendorCurrency();
        $virtuemart_currency_id = $jinput->get('virtuemart_currency_id', $vendor_currency['vendor_currency'], 'int');
        $currency_id = $japplication->getUserStateFromRequest("virtuemart_currency_id", 'virtuemart_currency_id',
            $virtuemart_currency_id);
        $currency_info = \cftools::getCurrencyInfo($currency_id);
        $symbol_start = '';
        $symbol_end = '';

        if ($currency_info->currency_positive_style) {
            if (strpos($currency_info->currency_positive_style, '{symbol}') == 0) {
                $symbol_start = '&nbsp;' . $currency_info->currency_symbol;
            } else {
                $symbol_end = '<span class="cf_currency">' . $currency_info->currency_symbol . '&nbsp;' . '</span>';
            }
        } else {
            $symbol_start = '<span class="cf_currency">&nbsp;' . $currency_info->currency_symbol . '</span>';
        }

        foreach ($prices as &$price) {
            $price = $symbol_start . $price . $symbol_end;
        }
        return $prices;
    }
}
