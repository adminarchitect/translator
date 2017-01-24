<?php

namespace Terranet\Translator\Actions\Handlers;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Terranet\Administrator\Traits\Actions\ActionSkeleton;
use Terranet\Administrator\Traits\Actions\Skeleton;

class DeleteTranslation
{
    use Skeleton, ActionSkeleton;

    public function name()
    {
        return trans('administrator::buttons.delete');
    }

    public function icon()
    {
        return 'fa fa-trash';
    }

    /**
     * Update single entity.
     *
     * @param Eloquent $entity
     * @return mixed
     */
    public function handle(Eloquent $entity)
    {
        $entity->where('key', $entity->key)->delete();

        return $entity;
    }

    protected function route(Eloquent $entity = null)
    {
        return route('scaffold.action', [
            'module' => app('scaffold.module'),
            'id' => $entity->key,
            'action' => $this->action($entity),
        ]);
    }

    protected function attributes(Eloquent $entity = null)
    {
        return \admin\helpers\html_attributes([
            'onclick' => 'if(!confirm("Are you sure you want to delete this item?")) return false',
        ]);
    }
}