<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    
    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body style="background-color: #000;">
    <div id="app">
        <!-- MAIN BODY -->
        <!-- VUE COMPONENT TO RENDER VIEW -->
        <page-reader-component></page-reader-component>
    </div>
</body>
<!-- App.js -->
<script src="{{ asset('js/app.js') }}"></script>
</html>