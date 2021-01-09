@extends('layouts.admin.layout')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">Menus</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.menus.edit', ['menu_id' => $menu->id]) }}">Edit</a></li>
            <li class="breadcrumb-item active">Add item</li>
        </ol>
    </nav>
@stop

@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0 text-dark">
                        Add Menu Item for menu <span class="bg-info">'{{ $menu->name }}'</span>
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

            <div class="card">
                <div class="card-body">

                    <form method="POST" action="{{ route('admin.menuitems.store') }}">
                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                        @csrf

                        <div class="form-group text-muted col-md-6">
                            <label for="title">{{ __('Title') }}</label>
                            <input id="title" type="text" class="form-control @error('title') is-invalid @enderror"
                                   name="title" value="{{ old('title') }}" required>

                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-muted col-md-6">
                            <label for="url">{{ __('Url') }}</label>
                            <input id="url" type="text" class="form-control @error('url') is-invalid @enderror"
                                   name="url" value="{{ old('url') }}">

                            @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-muted col-md-6">
                            <label for="icon_class">
                                {{ __('Icon class') }}
                                (Use <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank">Fontawesome Icon</a>)
                            </label>
                            <input id="icon_class" type="text" class="form-control @error('icon_class') is-invalid @enderror"
                                   name="icon_class" value="{{ old('icon_class') }}">

                            @error('icon_class')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-muted col-md-6">
                            <label for="color">{{ __('Color') }}</label>

                            <div class="input-group input-colorpicker">
                                <input type="color" class="form-control" name="color" value="{{ old('color') }}">
                            </div>
                        </div>

                        <div class="form-group text-muted col-md-6 mt-4">
                            <label><input type="checkbox" name="divider" value="1"
                                          @if(old('divider') == '1') checked @endif>
                                - add divider after item
                            </label>
                        </div>

                        <div class="form-group text-muted col-md-6">
                            <label for="target">Open in</label>
                            <select name="target" class="form-control">
                                @foreach ($array_target as $key => $value)
                                    <option value="{{ $key }}" {{ ( $key == old('target') ) ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                         <div class="form-group col-md-6 mt-4">
                             <a href="{{route('admin.menus.edit', ['menu_id' => $menu->id])}}" class="btn btn-default mr-3">{{ __('Cancel') }}</a>
                             <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>

                </div>
            </div>

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@stop
