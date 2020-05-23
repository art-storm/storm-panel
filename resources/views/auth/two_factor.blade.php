@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            @if(session()->has('message'))
                <p class="alert alert-info">
                    {{ session()->get('message') }}
                </p>
            @endif

            <div class="card">
                <div class="card-header">{{ __('auth.title_two_factor_auth') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('2fa.check') }}">
                        {{ csrf_field() }}
                        <p class="text-muted">
                            You have received an email which contains two factor login code.
                            If you haven't received it, press <a href="{{ route('2fa.resend') }}">here</a>.
                        </p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            </div>
                            <input name="two_factor_code" type="text"
                                   class="form-control{{ $errors->has('two_factor_code') ? ' is-invalid' : '' }}"
                                   required autofocus placeholder="Two Factor Code">
                            @error('two_factor_code')
                                <div class="invalid-feedback">
                                    {{ $errors->first('two_factor_code') }}
                                </div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary px-4">Verify</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</div>
@endsection
