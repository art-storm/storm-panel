@extends('layouts.admin.layout')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Roles</li>
        </ol>
    </nav>
@stop

@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0 text-dark">Roles
                    <a href="{{ route('admin.roles.create') }}"
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
                        <h4 class="text-muted">
                            Total roles: {{ $roles->count() }}
                        </h4>
                        <table id="rolesTable" class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Display name</th>
                                <th scope="col">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($roles as $role)
                            <tr class="text-muted">
                                <td scope="row">{{ $role->role_name }}</td>
                                <td>{{ $role->role_display }}</td>
                                <td>
                                    <a href="{{ route('admin.roles.show', ['role_id' => $role->id]) }}"
                                       class="btn btn-info btn-sm m-1">{{ __('View') }}</a>
                                    @if(!App\Http\Controllers\Admin\RoleController::denyEditDeleteRole($role->id))
                                    <a href="{{ route('admin.roles.edit', ['role_id' => $role->id]) }}"
                                       class="btn btn-primary btn-sm m-1">{{ __('Edit') }}</a>
                                    <a href="{{ route('admin.roles.destroy', ['role_id' => $role->id]) }}"
                                       class="btn btn-danger btn-sm m-1 alert-confirm">Delete</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>

                </div>
            </div>

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@stop

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#rolesTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                "paging": false,
                "searching": false,
                "info": false,
                "columnDefs": [{
                    "targets": [2], // column or columns numbers
                    "orderable": false,  // set orderable for selected columns
                }],
            });

            $("#alert").delay(5000).slideUp(200, function () {
                $(this).alert('close');
            });

            $(document).on("click", ".alert-confirm", function(e) {
                e.preventDefault();
                var message = "For all users who had a role to delete will set default role 'user_registered'.";
                var title = "Are you sure want to delete this role ?";
                confirmDelete(e.target.href, message, title);
            });
        });
    </script>
@endpush
