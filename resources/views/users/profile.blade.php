@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-12 margin-header">
            <h1 class="page-header">@lang('profile.title_profile')</h1>
            <div class="border-bottom mb-3"></div>
        </div>
        <!-- /.col-12 -->
    </div>
    <!-- /.row -->

    <div class="row" id="app-profile">
        <div class="col-12">

            @if (session('success'))
                <div class="alert alert-success alert-dismissable">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>{{ session('success') }}</strong>
                </div>
            @endif

            <form method="POST" action="{{ route('users.profile_update') }}" name="ProfileForm" id="ProfileForm">
            {{ csrf_field() }}

            <div class="form-group row">
                <label for="role" class="col-lg-3 control-label form-item-label text-lg-right">User roles</label>

                <div class="col-lg-4">
                    <div class="form-item-textnode">
                        <span>$user->role->display_name</span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="email" class="col-lg-3 control-label form-item-label text-lg-right">E-mail</label>

                <div class="col-lg-4">
                    <div class="form-item-textnode">
                        <a href="{{ route('email.change.form') }}">{{ $user->email }}</a>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="password" class="col-lg-3 control-label form-item-label text-lg-right">Password</label>

                <div class="col-lg-4">
                    <div class="form-item-textnode">
                        <a href="{{ route('password.change.form') }}">********</a>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="name" class="col-lg-3 control-label form-item-label text-lg-right">Name</label>

                <div class="col-lg-4">
                    <input id="name" type="name" class="form-control @error('name') is-invalid @enderror"
                           name="name" value="{{ $user->name }}" required>

                    @if ($errors->has('name'))
                        <div class="invalid-feedback">
                            <i class="fa fa-exclamation-triangle"></i> {{ $errors->first('name') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label for="name" class="col-lg-3 control-label form-item-label text-lg-right">Two Factor Auth</label>

                <div class="col-lg-4">
                    <user-profile-2fa
                        :state="{{ $user->two_factor_state }}"
                        :method="'{{ $user->two_factor_method }}'"
                    >
                    </user-profile-2fa>
                </div>
            </div>

            <br>

            <div class="form-group row">
                <div class="col-lg-4 offset-lg-3">
                    <input class="btn btn-primary" type="submit" value="Change">
                </div>
            </div>

            <br><br>

            </form>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->

@endsection
