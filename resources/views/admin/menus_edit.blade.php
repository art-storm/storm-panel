@extends('layouts.admin.layout')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">Menus</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
@stop

@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0 text-dark">Menus
                        <a href="{{ route('admin.menuitems.create', ['menu_id' => $menu->id]) }}" id="addButton" class="btn btn-primary ml-4">Add Menu Item</a>
                    </h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->

    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->

            <div class="callout callout-info">
                @if($menu->name == 'admin')
                    <h5>Here you can edit the admin menu.</h5>
                @else
                    <h5>Here you can edit the {{ $menu->name }} menu.</h5>
                    <p>You can output a menu anywhere on your site by calling &nbsp;
                        <span class="bg-info">&nbsp; menu('{{ $menu->name }}') &nbsp;</span>
                    </p>
                @endif
            </div>

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert">
                    {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">

                    <div class="dd" id="menu_builder">
                        <ol class="dd-list">
                            @foreach($items->where('parent_id', 0) as $item)
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
                    </div>
                </div>
            </div>

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@stop

@push('scripts')
<script>
    $(document).ready(function() {
        $('.dd').nestable({
            maxDepth: 2
        });

        $('.dd').on('change', function (e) {
            $.ajax({
                type: "PUT",
                url: '{{ route('admin.menus.update', ['menu_id' => $menu->id]) }}',
                data: {
                    order: JSON.stringify($('.dd').nestable('serialize')),
                    _token: '{{ csrf_token() }}'
                },
             })
            .done(function (data) {
                toastr.options = {
                    "closeButton": true,
                    "timeOut": "2000",
                };
                toastr.success(data);
            });

        });

        $("#alert").delay(5000).slideUp(200, function () {
            $(this).alert('close');
        });

        $(document).on("click", ".alert-confirm", function(e) {
            e.preventDefault();
            var message = "All nested items will also be removed.";
            var title = "Are you sure want to delete this menu item ?";
            confirmDelete(e.target.href, message, title);
        });
    });

</script>
@endpush
