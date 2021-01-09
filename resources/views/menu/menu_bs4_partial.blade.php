<div class="dropdown-menu">
    @foreach($items->where('parent_id', $item->id) as $item)
        <a class="dropdown-item" href="{{ $item->url }}" target="{{ $item->target }}">
            <span style="color:{{ $item->color }}">
                @if($item->icon_class)
                    <i class="nav-icon {{ $item->icon_class }}"></i>
                @endif
                {{ $item->title }}
            </span>
        </a>
        @if($item->divider)
            <div class="dropdown-divider"></div>
        @endif
    @endforeach
</div>
