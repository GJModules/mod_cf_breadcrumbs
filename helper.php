<?php
/**
 * @package     Breakdesigns.CustomFilters
 *
 * @copyright   Copyright © 2010-2021 Breakdesigns.net. All rights reserved.
 * @license     GNU Geneal Public License 2 or later, see COPYING.txt for license details.
 */

// no direct access
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
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

	public static function getDataModCfBreadcrumbsCache( $module , $params ){
		$helper = new ModCfBreadcrumbsHelper($module);
		try
		{
		    // Code that may throw an Exception or Error.
			$list = $helper->getList();
		    // throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
		}
		catch (\Exception $e)
		{
		    // Executed only in PHP 5, will not be reached in PHP 7
		    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
		    echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
		    die(__FILE__ .' '. __LINE__ );
		}

		return $list ;
	}

	/**
	 * @throws Exception
	 */
	protected function getModuleCfFilter(){
		$moduleCfFilter = \Joomla\CMS\Helper\ModuleHelper::getModule( 'mod_cf_filtering'  );
		$moduleCfFilterParam = new JRegistry( $moduleCfFilter->params ) ;
		// Получить все фильтры с опциями для модуля
		$FilteringHelper = new ModCfFilteringHelper( $moduleCfFilterParam, $moduleCfFilter);

		return $FilteringHelper->getFilters();

	}


    /**
     * Основная функция входа, которая возвращает выбор
     * ---
     * Main entry function that returns the selections
     *
     * @return array
     * @throws Exception
     * @since 1.0.0
     */
    public function getList()
    {
        $selections = [];
	    /**
	     * @var array $selected_flt - список фильтров категорий + выбранные опции фильтра
	     */
		$selected_flt = \CfInput::getInputs($cached = true);

        if (empty($selected_flt)) {
            return [];
        }

        // selected filters after encoding the output
        $selected_flt = $output = \CfOutput::getOutput($selected_flt);
		

		
        $selected['selected_flt'] = $selected_flt;
        $selected['selected_flt_modif'] = $selected_flt;
        $selected['selected_fl_per_flt'] = \CfOutput::getOutput( CfInput::getInputsPerFilter($this->module,
            $cached = true),
            $escape = true, $perfilter = true);

        $optionsHelper = \OptionsHelper::getInstance(\cftools::getModuleparams(), $this->filteringModule );



        $UrlHanlder = new UrlHandler( $this->filteringModule, $selected );
        $published_setting_name = '_published';
        $params = new Registry( $this->module->params );


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

	    $Filters = $this->getModuleCfFilter();

	    /**
	     * @var string $PatchToVmCategory -- ссылка  на категорию
	     */
		$PatchToVmCategory = seoTools_uri::getPatchToVmCategory( $selected_flt['virtuemart_category_id'][0] ) ;

	    foreach ( $Filters as $filter )
	    {
			$Options =  $filter->getOptions() ;
		    foreach ( $Options  as $item )
		    {
			    if ( $item->type == 'clear' || !$item->selected ) continue ;   #END IF

			    $VarName = $filter->getVarName() ;
			    foreach ( $selections[$VarName] as &$selection )
			    {
				    if ( $selection->id != $item->id  ) continue ; #END IF
				    $selection->url = $item->option_sef_url->sef_url ;

				    if ( $selection->url == '/component/virtuemart/Itemid0' )
				    {
					    $selection->url = $PatchToVmCategory ;
				    }#END IF
				    $selection->name = self::getTextTranslateString($selection->name);
				    if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
				    {
//				        echo'<pre>';print_r( $selection->name );echo'</pre>'.__FILE__.' '.__LINE__;
				        
				    }
			    }#END FOREACH
		    }#END FOREACH
	    }#END FOREACH


	    $selections = $this->sort($selections);

	    $resetLink =  [
				'name' => 'Сбросить' ,
				'url' => $PatchToVmCategory , 
			] ;
		array_unshift($selections , $resetLink )  ;
		if ($_SERVER['REMOTE_ADDR'] ==  DEV_IP )
		{
			
//		    echo'<pre>';print_r( $selections );echo'</pre>'.__FILE__.' '.__LINE__;
//		    die(__FILE__ .' '. __LINE__ );

		}
        return $selections ;
    }

	/**
	 * Найти языковые константы и перевести
	 * etc/ {DIZELNII}
	 * @param $name
	 *
	 * @return string
	 * @since 3.9
	 */
	public static function getTextTranslateString( $name ){
		preg_match_all("/{(.*?)}/", $name ,$matches, PREG_PATTERN_ORDER);

		foreach ($matches[0] as $i => $value) {
			$name = str_replace($value, Text::_( $matches[1][$i] ), $name );
		}
		return $name ;
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

	/**
	 * Получить версию модуля из файла манифеста
	 * ---
	 * @return string - версия модуля - используется как MEDIA VERSION - для загрузки ресурсов
	 * @since 3.9
	 * TODO - добавить в шаблон Создания модуля
	 */
	public static function getModuleVersion():string
	{
		$doc           = \Joomla\CMS\Factory::getDocument();
		$scriptOptions = $doc->getScriptOptions( 'mod_cf_breadcrumbs' );
		if ( isset( $scriptOptions[ 'version' ] ) )
		{
			return $scriptOptions[ 'version' ];
		}#END IF

		$xml_file = __DIR__.'/mod_cf_breadcrumbs.xml';
		$dom      = new DOMDocument( "1.0" , "utf-8" );
		$dom->load( $xml_file );

		/**
		 * @var string $__v version mod_cf_filtering
		 */
		$version = $dom->getElementsByTagName( 'version' )->item( 0 )->textContent;
		$doc->addScriptOptions( 'mod_cf_filtering' , [ 'version' => $version ] , true );

		return $version;
	}
}
