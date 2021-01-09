@extends('layouts.admin.layout')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">{{ __('View') }}</li>
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
                        Viewing user
                        <a href="{{ route('admin.users.edit', ['user_id' => $user->id]) }}"
                           class="btn btn-primary btn-sm ml-3">Edit</a>
                        <a href="{{ route('admin.users.destroy', ['user_id' => $user->id] + request()->query()) }}"
                           class="btn btn-danger btn-sm">Delete</a>
                        <a href="{{ route('admin.users.index', request()->query()) }}"
                           class="btn btn-secondary btn-sm">Return to list</a>
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

                    <div class="row">

                        <table class="table table-hover table-borderless col-6 text-muted">
                            <tbody>
                            <tr>
                                <td class="col-2"><h4 class="mt-1">Name</h4></td>
                                <td class="align-middle">{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td class="col-2"><h4 class="mt-1">Email</h4></td>
                                <td class="align-middle">{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td class="col-2" nowrap><h4 class="mt-1">Default role</h4></td>
                                <td class="align-middle">{{ $user->role->role_display }}</td>
                            </tr>
                            <tr>
                                <td class="col-2 align-top"><h4>Roles</h4></td>
                                <td class="align-middle">
                                    <ul>
                                    @foreach($user->additionalRoles as $role)
                                        <li>{{ $role->role_display }}</li>
                                    @endforeach
                                    </ul>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>

                    <h3 class="mt-3 text-muted">Additional info</h3>
                    <hr>

                    <table class="table table-hover table-borderless col-6 text-muted">
                        <tbody>
                        <tr>
                            <td class="col-2"><b>User status:</b></td>
                            <td>@if ($user->is_activate) Activated @else Non activated @endif</td>
                        </tr>
                        <tr>
                            <td class="col-2" nowrap><b>Two factor auth:</b></td>
                            <td>@if ($user->two_factor_method) {{ $user->two_factor_method }} @else Disabled @endif</td>
                        </tr>
                        <tr>
                            <td class="col-2"><b>Created at:</b></td>
                            <td>{{ $user->created_at }}</td>
                        </tr>
                        <tr>
                            <td class="col-2"><b>Created IP:</b></td>
                            <td>{{ long2ip($user->created_ip) }}</td>
                        </tr>
                        <tr>
                            <td class="col-2"><b>Updated at:</b></td>
                            <td>{{ $user->updated_at }}</td>
                        </tr>
                        <tr>
                            <td class="col-2"><b>Updated IP:</b></td>
                            <td>{{ long2ip($user->updated_ip) }}</td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@stop
