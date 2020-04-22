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

    <div class="row">
        <div class="col-12">

            <h4>@lang('profile.password_change')</h4><br>

            <form method="POST" action="{{ route('password_change') }}" name="passwordChangeForm" id="passwordChangeForm">
            {{ csrf_field() }}

            <div class="form-group row">
                <label for="password_current" class="col-md-3 control-label form-item-label text-md-right">@lang('profile.password_current')</label>

                <div class="col-md-6">
                    <input name="password_current" type="password" placeholder="@lang('profile.password_current')" id="password_current"
                           class="form-control @error('password_current') is-invalid @enderror" required>
                    @if ($errors->has('password_current'))
                        <div class="invalid-feedback">
                            <i class="fa fa-exclamation-triangle"></i> {{ $errors->first('password_current') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label for="password" class="col-md-3 control-label form-item-label text-md-right">@lang('profile.password_new')</label>

                <div class="col-md-6">
                    <input name="password" type="password" placeholder="@lang('profile.password_new')" id="password"
                           class="form-control @error('password') is-invalid @enderror" required>
                    @if ($errors->has('password'))
                        <div class="invalid-feedback">
                            <i class="fa fa-exclamation-triangle"></i> {{ $errors->first('password') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label for="password_confirmation" class="col-md-3 control-label form-item-label text-md-right">@lang('profile.password_repeat')</label>

                <div class="col-md-6">
                    <input name="password_confirmation" type="password" placeholder="@lang('profile.password_repeat')" id="password_confirmation"
                           class="form-control @error('password_confirmation') is-invalid @enderror" required>
                    @if ($errors->has('password_confirmation'))
                        <div class="invalid-feedback">
                            <i class="fa fa-exclamation-triangle"></i> {{ $errors->first('password_confirmation') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6 offset-md-3 my-3">
                    <input class="btn btn-primary mr-2" type="submit" value="@lang('common.button.change')">
                    <a href="{{route('users_profile')}}" class="btn btn-light">@lang('common.button.cancel')</a>
                </div>
            </div>
            </form>

        </div>
        <!-- /.col-12 -->
    </div>
    <!-- /.row -->

@endsection
