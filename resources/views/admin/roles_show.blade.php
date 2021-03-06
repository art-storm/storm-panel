@extends('layouts.admin.layout')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
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
                    <h1 class="m-0 text-dark">Show Role</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->

    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">

                    <form method="POST" action="#">
                        <div class="form-group text-muted col-md-6">
                            <label for="role_name">{{ __('Name') }}</label>
                            <input id="role_name" type="text" class="form-control"
                                   name="role_name" value="{{ $role->role_name }}" disabled>
                        </div>

                        <div class="form-group text-muted col-md-6">
                            <label for="role_display">{{ __('Display name') }}</label>
                            <input id="role_display" type="text" class="form-control"
                                   name="role_display" value="{{ $role->role_display }}" disabled>
                        </div>

                        <div class="form-group text-muted col-md-6">
                            <h3 class="mt-4">{{ __('Permissions') }}</h3>

                            <ul class="permissions checkbox mt-2">
                                @foreach($permissions as $permission)
                                    <li>
                                        <input type="checkbox" id="pperm-{{$permission->id}}" class="form-check-input permission-group" disabled>
                                        <label for="pperm-{{$permission->id}}" class="form-check-label"><strong>{{$permission->name}}</strong></label>
                                        <ul class="mb-3">
                                            @foreach($permission->children as $perm)
                                                <li>
                                                    <input id="permission-{{ $perm->id }}" class="form-check-input the-permission" name="permissions[]" type="checkbox" value="{{ $perm->id }}"
                                                        @if(in_array($perm->id, old('permissions', $permission_role))) checked="checked" @endif disabled>
                                                    <label for="permission-{{ $perm->id }}" class="form-check-label">{{ $perm->name }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="form-group col-md-6 mt-4">
                            <a href="{{ route('admin.roles.index') }}"
                               class="btn btn-secondary btn-sm">Return to list</a>
                        </div>
                    </form>

                </div>
            </div>

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@stop

@push('scripts')
    <script>
        $(document).ready(function() {
            parentChecked();
        });

        function parentChecked() {
            $('.permission-group').each(function () {
                var allChecked = true;
                $(this).siblings('ul').find("input[type='checkbox']").each(function () {
                    if (!this.checked) allChecked = false;
                });
                $(this).prop('checked', allChecked);
            });
        }
    </script>
@endpush
