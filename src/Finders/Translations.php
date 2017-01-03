<?php

namespace Terranet\Translator\Finders;

use Terranet\Administrator\Services\Finder;

class Translations extends Finder
{
    /**
     * Fetch all items from repository
     *
     * @return mixed
     */
    public function fetchAll()
    {
        $query = $this->getQuery()
            ->select('key')
            ->groupBy('key')
            ->with(['locales' => function($query) {
                $query->orderBy('locale');
            }])
            ->orderBy('key')
        ;

        return $query->paginate($this->perPage());
    }

    public function find($key, $columns = ['*'])
    {
        $this->model = $this->model->newQueryWithoutScopes()->where('key', $key)->first($columns);

        return $this->model;
    }
}