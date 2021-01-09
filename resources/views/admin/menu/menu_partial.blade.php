<ul class="nav nav-treeview">
    @foreach($items->where('parent_id', $item->id) as $item)
        <li class="nav-item">
            <a class="nav-link @if($item->active) active @endif" href="{{ $item->url }}" target="{{ $item->target }}">
                &nbsp;&nbsp;
                @if($item->icon_class)
                    <i class="nav-icon {{ $item->icon_class }}"></i>
                @endif
                <p>{{ $item->title }}</p>
            </a>
        </li>
        @if($item->divider)
            <li class="dropdown-divider"></li>
        @endif
    @endforeach
</ul>
