@extends('layouts.frontend')

@section('title', trans('messages.sender.create'))
    
@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')
    
    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li><a href="{{ action("SenderController@index") }}">{{ trans('messages.verified_senders') }}</a></li>
        </ul>
        <h1>
            <span class="text-semibold"><i class="icon-plus-circle2"></i> {{ trans('messages.sender.create') }}</span>
        </h1>
    </div>

@endsection

@section('content')
    <form enctype="multipart/form-data" action="{{ action('SenderController@store') }}" method="POST" class="form-validate-jqueryz">
        {{ csrf_field() }}
        
        @include('senders._form')
    <form>
                
@endsection
