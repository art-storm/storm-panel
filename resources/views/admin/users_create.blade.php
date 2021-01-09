@extends('layouts.admin.layout')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">Add</li>
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
                        Add user
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

                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <div class="form-group text-muted col-md-6">
                            <label for="name">{{ __('Name') }}</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" required>

                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-muted col-md-6">
                            <label for="email">{{ __('E-Mail Address') }}</label>
                            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required>

                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-muted col-md-6">
                            <label for="password">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" value="">

                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-muted col-md-6">
                            <label for="role_id">Default role</label>
                            <select name="role_id" class="form-control @error('role_id') is-invalid @enderror">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ ( $role->id == old('role_id') ) ? 'selected' : '' }}>{{ $role->role_display }}</option>
                                @endforeach
                            </select>

                            @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-muted col-md-6">
                            <label for="roles_additional">Additional roles</label>
                            <select name="roles_additional[]" class="roles-select2" multiple="multiple" data-placeholder="Select a role" style="width: 100%;">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ ( in_array($role->id, old('roles_additional', [])) ) ? 'selected' : '' }}>{{ $role->role_display }}</option>
                                @endforeach
                            </select>

                            @error('roles_additional')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                         <div class="form-group col-md-6 mt-4">
                             <a href="{{route('admin.users.index')}}" class="btn btn-default mr-3">{{ __('Cancel') }}</a>
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
            $('.roles-select2').select2({
                theme: 'bootstrap4'
            });
        });
    </script>
@endpush
