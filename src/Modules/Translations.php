<?php

namespace Terranet\Translator\Modules;

use Terranet\Administrator\Contracts\Module\Editable;
use Terranet\Administrator\Contracts\Module\Exportable;
use Terranet\Administrator\Contracts\Module\Filtrable;
use Terranet\Administrator\Contracts\Module\Navigable;
use Terranet\Administrator\Contracts\Module\Sortable;
use Terranet\Administrator\Contracts\Module\Validable;
use Terranet\Administrator\Filters\FilterElement;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Traits\Module\AllowFormats;
use Terranet\Administrator\Traits\Module\AllowsNavigation;
use Terranet\Administrator\Traits\Module\HasFilters;
use Terranet\Administrator\Traits\Module\HasForm;
use Terranet\Administrator\Traits\Module\HasSortable;
use Terranet\Administrator\Traits\Module\ValidatesForm;

/**
 * Administrator Resource Translations
 *
 * @package Terranet\Administrator
 */
class Translations extends Scaffolding implements Navigable, Filtrable, Editable, Validable, Sortable, Exportable
{
    use HasFilters, HasForm, HasSortable, ValidatesForm, AllowFormats, AllowsNavigation;

    /**
     * The module Eloquent model
     *
     * @var string
     */
    protected $model = '\App\Translation';

    protected $includeDateColumns = false;

    /**
     * The module title
     *
     * @return mixed
     */
    public function title()
    {
        return trans("administrator::module.resources.translations");
    }

    /**
     * Navigation group which Resource belongs to
     *
     * @return string
     */
    public function group()
    {
        return trans('administrator::module.groups.localization');
    }

    /**
     * Navigation container which Resource belongs to
     * Available: sidebar, tools
     *
     * @return mixed
     */
    public function navigableIn()
    {
        return Navigable::MENU_TOOLS;
    }

    public function columns()
    {
        return $this->scaffoldColumns()
            ->without(['locale']);
    }

    public function filters()
    {
        $keyword = FilterElement::text('keyword');

        $keyword->getInput()->setQuery(function ($query, $value) {
            return $query->where(function($where) use ($value) {
                return $where
                    ->where('key', 'like', '%' . $value . '%')
                    ->orWhere('value', 'like', '%' . $value . '%');
            });
        });

        return $this
            ->scaffoldFilters()
            ->push($keyword)
            ->without(['locale', 'key']);
    }

    public function activeLocales()
    {
        $locale = app('translator')->getLocale();

        return [
            $locale => $locale,
        ];
    }
}