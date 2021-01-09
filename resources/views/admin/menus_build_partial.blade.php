<ol class="dd-list">
    @foreach($items->where('parent_id', $item->id) as $item)
        <li class="dd-item" data-id="{{ $item->id }}">
            <div class="dd-handle">
                <span style="color:{{ $item->color }}">
                    @if($item->icon_class)
                        <i class="nav-icon {{ $item->icon_class }}"></i>
                    @endif
                    {{ $item->title }}
                </span> &nbsp;
                <small>{{ $item->url }}</small>
            </div>
            <a href="{{ route('admin.menuitems.edit', ['item_id' => $item->id]) }}"
                class="btn btn-primary btn-sm mt-2 dd-edit">{{ __('Edit') }}</a>
            <a href="{{ route('admin.menuitems.destroy', ['item_id' => $item->id]) }}"
                class="btn btn-danger btn-sm mt-2 dd-delete alert-confirm">{{ __('Delete') }}</a>
            @if($item->divider)
                <hr>
            @endif
            @if($item->children)
                @include('admin.menus_build_partial', ['items' => $items])
            @endif
        </li>
    @endforeach
</ol>
