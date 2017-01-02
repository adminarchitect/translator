@inject('module', 'scaffold.module')
@inject('actions', 'scaffold.actions')

<tr>
    <th>
        <label for="collection_{{ $item->key }}">
            <input type="checkbox" name="collection[]" id="collection_{{ $item->key }}" value="{{ $item->key }}" class="collection-item simple">
        </label>
    </th>

    <td class="text-right">{{ $item['key'] }}</td>

    <td>
        <?php $locales = $item->locales->groupBy('locale');?>

        @foreach($module->activeLocales() as $locale => $title)
            <textarea name="translates[{{ $item->key }}][{{ $locale }}]" class="form-control translate-area" data-locale="{{ $locale }}"{!! $locale === app('translator')->getLocale() ? '' : ' style="display: none;"' !!}>{{ $locales->has($locale) ? $locales[$locale]->first()['value'] : null }}</textarea>
        @endforeach
    </td>

    <td>
        <div class="btn-group toggle-languages" data-toggle="btn-toggle" style="margin-bottom: 5px;">
            @foreach($module->activeLocales() as $locale => $title)
                <button type="button" class="btn btn-default btn-sm{{ $locale === app('translator')->getLocale() ? ' active' : '' }}" data-locale="{{ $locale }}">{{ $title }}</button>
            @endforeach
        </div>

        <ul class="list-unstyled">
            @foreach($actions->actions()->authorized(auth('admin')->user(), $item) as $action)
                <li>
                    {!! $action->render($item) !!}
                </li>
            @endforeach
        </ul>
    </td>
</tr>