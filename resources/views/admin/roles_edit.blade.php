@extends('layouts.admin.layout')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
            <li class="breadcrumb-item active">{{ __('Edit') }}</li>
        </ol>
    </nav>
@stop

@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0 text-dark">Edit Role</h1>
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

                    <form method="POST" action="{{ route('admin.roles.update', ['role_id' => $role->id]) }}">
                        @method('PUT')
                        @csrf

                        <div class="form-group text-muted col-md-6">
                            <label for="role_name">{{ __('Name') }}</label>
                            <input id="role_name" type="text" class="form-control @error('role_name') is-invalid @enderror"
                                   name="role_name" value="{{ old('role_name', $role->role_name) }}" required>

                            @error('role_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-muted col-md-6">
                            <label for="role_display">{{ __('Display name') }}</label>
                            <input id="role_display" type="text" class="form-control @error('role_display') is-invalid @enderror"
                                   name="role_display" value="{{ old('role_display', $role->role_display) }}" required>

                            @error('role_display')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-muted col-md-6">
                            @error('permissions')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror

                            <h3 class="mt-2">{{ __('Permissions') }}</h3>
                            <a href="#" class="permission-select-all">{{ __('Select all') }}</a> /
                            <a href="#"  class="permission-deselect-all">{{ __('Deselect all') }}</a>

                            <ul class="permissions checkbox mt-3">
                                @foreach($permissions as $permission)
                                    <li>
                                        <input type="checkbox" id="pperm-{{$permission->id}}" class="form-check-input permission-group">
                                        <label for="pperm-{{$permission->id}}" class="form-check-label"><strong>{{$permission->name}}</strong></label>
                                        <ul class="mb-3">
                                            @foreach($permission->children as $perm)
                                                <li>
                                                    <input id="permission-{{ $perm->id }}" class="form-check-input the-permission" name="permissions[]" type="checkbox" value="{{ $perm->id }}"
                                                        @if(in_array($perm->id, old('permissions', $permission_role))) checked="checked" @endif >
                                                    <label for="permission-{{ $perm->id }}" class="form-check-label">{{ $perm->name }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="form-group col-md-6 mt-4">
                            <a href="{{route('admin.roles.index')}}" class="btn btn-default mr-3">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
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
        $('.permission-group').on('change', function () {
            $(this).siblings('ul').find("input[type='checkbox']").prop('checked', this.checked);
        });

        $('.permission-select-all').on('click', function () {
            $('ul.permissions').find("input[type='checkbox']").prop('checked', true);
            return false;
        });

        $('.permission-deselect-all').on('click', function () {
            $('ul.permissions').find("input[type='checkbox']").prop('checked', false);
            return false;
        });

        parentChecked();

        $('.the-permission').on('change', function () {
            parentChecked();
        });

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
