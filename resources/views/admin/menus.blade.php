@extends('layouts.admin.layout')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Menus</li>
        </ol>
    </nav>
@stop

@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0 text-dark">Menus</h1>
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
                <h5>Here you can edit the admin and site menus.</h5>
            </div>

            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($menus as $menu)
                                <tr class="text-muted">
                                    <td>{{ $menu->name }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.menus.edit', ['menu_id' => $menu->id]) }}"
                                           class="btn btn-primary btn-sm m-1">{{ __('Edit') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@stop
