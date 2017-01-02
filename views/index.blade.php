@inject('module', 'scaffold.module')
@inject('columns', 'scaffold.columns')
@inject('actions', 'scaffold.actions')
@inject('filter', 'scaffold.filter')
@inject('template', 'scaffold.template')
@inject('sortable', 'scaffold.sortable')

@extends($template->layout())

@section('total')
<sup class="small">({{ $items->total() }})</sup>
@endsection

@include($template->index('create'))
@include($template->index('filters'))

@section('scaffold.content')
<div class="box-body">
    <div class="input-group-btn">
        @include($template->index('batch'))
        @include($template->index('scopes'))
        <div class="clearfix"></div>
    </div>
</div>

<form method="post" id="collection" action="{{ route('scaffold.batch', ['page' => $module]) }}">
    <?=Form::hidden('batch_action', null, ['id' => 'batch_action'])?>
    <?=Form::token()?>
    <table class="table table-bordered table-striped">
        <thead>
            <tr role="row">
                <th width="10">
                    <label for="toggle_collection">
                        <input type="checkbox" class="simple" id="toggle_collection" />
                    </label>
                </th>
                @each($template->index('header'), $columns, 'column')
                <th class="actions" style="width: 10%; vertical-align: top">{{ trans('administrator::module.actions') }}</th>
            </tr>
        </thead>

        <tbody>
            @each($template->index('row'), $items, 'item')
        </tbody>
    </table>

    <div class="text-center">
        <input type="submit" value="{{ trans('administrator::buttons.save') }}" class="btn btn-primary" id="translates-save" />
    </div>
</form>
@include($template->index('export'))
@endsection

@section('scaffold.content-footer')
    @include($template->index('paginator'))
@endsection

@include($template->index('scripts'))
