@extends('layouts.admin.layout')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
    </nav>
@stop

@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0 text-dark">Users
                        <a href="{{ route('admin.users.create') }}"
                           class="btn btn-primary ml-4">Add New</a>
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

                    <form method="GET" action="{{ route('admin.users.index') }}">
                        <div class="form-row">
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">@</span>
                                    </div>
                                    <input type="text" name="email" class="form-control" id="email"
                                           value="{{ $data['email'] }}" placeholder="Email">
                                </div>
                                <small id="emailHelp" class="form-text text-muted">'*' represents zero, one, or multiple characters</small>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-4">
                                <select class="custom-select mr-sm-2" name="role_id" id="role_id">
                                    <option value="" selected>Select role</option>
                                    @foreach ($data['roles'] as $role)
                                        <option value="{{ $role->id }}" {{ ( $role->id == $data['role_id'] ) ? 'selected' : '' }}>{{ $role->role_display }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-4">
                                <select class="custom-select mr-sm-2" name="sort_by" id="sort_by">
                                    <option value="" selected>Sort by</option>
                                    @foreach($data['sorting'] as $key => $value)
                                        <option value="{{ $key }}" @if($key == $data['sort_by']) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                                <small id="emailHelp" class="form-text text-muted">Default sort by email</small>
                            </div>
                            <div class="col-md-2 col-sm-6 mb-4">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>

                    <div class="row">

                        <h4 class="text-muted">
                            Found users: {{ $users->total() }}
                            <small>(showing {{ $users->firstItem() }} to {{ $users->lastItem() }})</small>
                        </h4>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col" class="d-none d-xl-table-cell">Created at</th>
                                <th scope="col">Activated</th>
                                <th scope="col" class="d-none d-sm-table-cell">Default role</th>
                                <th scope="col" class="d-none d-xl-table-cell">Roles</th>
                                <th scope="col">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                            <tr class="text-muted">
                                <td scope="row">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="d-none d-xl-table-cell">{{ $user->created_at }}</td>
                                <td>@if ($user->is_activate) Activated @else Non activated @endif</td>
                                <td class="d-none d-sm-table-cell">{{ $user->role->role_display }}</td>
                                <td class="d-none d-xl-table-cell">
                                    <span class="d-inline-block text-truncate" style="max-width: 14rem">
                                        @foreach($user->additionalRoles as $role)
                                            @if($loop->last)
                                                {{ $role->role_display }}
                                            @else
                                                {{ $role->role_display }},
                                            @endif
                                        @endforeach
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.show', ['user_id' => $user->id] + request()->query()) }}"
                                       class="btn btn-info btn-sm m-1">{{ __('View') }}</a>
                                    <a href="{{ route('admin.users.edit', ['user_id' => $user->id]) }}"
                                       class="btn btn-primary btn-sm m-1">{{ __('Edit') }}</a>
                                    <a href="{{ route('admin.users.destroy', ['user_id' => $user->id] + request()->query()) }}"
                                       class="btn btn-danger btn-sm m-1 alert-confirm">Delete</a>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3 justify-content-center">
                        {{ $users->withQueryString()->links() }}
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
            $("#alert").delay(5000).slideUp(200, function () {
                $(this).alert('close');
            });

            $(document).on("click", ".alert-confirm", function(e) {
                e.preventDefault();
                var message = "Are you sure want to delete this user ?";
                confirmDelete(e.target.href, message);
            });
        });
    </script>
@endpush
