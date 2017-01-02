<?php

namespace Terranet\Translator\Actions\Handlers;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Terranet\Administrator\Traits\Actions\BatchSkeleton;
use Terranet\Administrator\Traits\Actions\Skeleton;

class SaveTranslations
{
    use Skeleton, BatchSkeleton;

    /**
     * Perform a batch action.
     *
     * @param Eloquent $entity
     * @return mixed
     */
    public function handle(Eloquent $entity)
    {
        foreach(request('translates') as $key => $locales) {
            foreach($locales as $locale => $value) {
                $row = $entity
                    ->firstOrNew([
                        'locale' => $locale,
                        'key' => $key,
                    ]);

                $row->fill(['value' => $value])->save();
            }
        }

        return $entity;
    }
}