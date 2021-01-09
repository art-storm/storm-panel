<ul class="navbar-nav">
    @foreach($items->where('parent_id', 0) as $item)
        @if(!$item->children)
            <li class="nav-item">
                <a class="nav-link" href="{{ $item->url }}" target="{{ $item->target }}">
                <span style="color:{{ $item->color }}">
                    @if($item->icon_class)
                        <i class="nav-icon {{ $item->icon_class }}"></i>
                    @endif
                    {{ $item->title }}
                </span>
                </a>
            </li>
        @else
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="{{ $item->url }}" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                <span style="color:{{ $item->color }}">
                    @if($item->icon_class)
                        <i class="nav-icon {{ $item->icon_class }}"></i>
                    @endif
                    {{ $item->title }}
                </span>
                </a>
                @include('menu.menu_bs4_partial', ['items' => $items])
            </li>
        @endif
    @endforeach
</ul>

@push('scripts')
    <script>
        $(document).ready(function() {
            $("div.dropdown-menu a.active").parents("li").addClass('active');
        });
    </script>
@endpush
