@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-12 margin-header">
            <h1 class="page-header">@lang('registration.title_welcome')</h1>
            <div class="border-bottom mb-3"></div>
        </div>
        <!-- /.col-12 -->
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-12">

            <h4>@lang('registration.text_welcome')<br><br>
                <a href="{{ route('users.profile') }}">@lang('registration.link_profile') →</a>
            </h4>
            <br><br>

        </div>
        <!-- /.col-12 -->
    </div>
    <!-- /.row -->

@endsection