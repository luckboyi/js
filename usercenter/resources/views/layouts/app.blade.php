<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/8/17
 * Time: 17:14
 */
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/css/bootstrap.css">
    <script src="https://cdn.bootcss.com/jquery/2.1.4/jquery.min.js"></script>
    <link href="{{asset('public/css/webuploader.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/diyUpload.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/webuploader.html5only.min.js')}}"></script>
    <script src="{{asset('public/js/diyUpload.js')}}"></script>
  </head>
  <body>
    @yield('content')

    <!-- jQuery first, then Bootstrap JS. -->

    <script src="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/js/bootstrap.js"></script>
  </body>
</html>