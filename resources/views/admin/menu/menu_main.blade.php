<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    @foreach($items->where('parent_id', 0) as $item)
        <li class="nav-item @if($item->children) has-treeview @endif">
            <a class="nav-link @if($item->active) active @endif" href="{{ $item->url }}" target="{{ $item->target }}">
                @if($item->icon_class)
                    <i class="nav-icon {{ $item->icon_class }}"></i>
                @endif
                <p>{{ $item->title }}
                    @if($item->children)
                        <i class="right fas fa-angle-left"></i>
                    @endif
                </p>
             </a>
            @if($item->children)
                @include('admin.menu.menu_partial', ['items' => $items])
            @endif
        </li>
    @endforeach
</ul>

@push('scripts')
<script>
    $(document).ready(function() {
        $("ul.nav-treeview a.active").parents("li").addClass('menu-open').children("a").addClass('active');
    });
</script>
@endpush
