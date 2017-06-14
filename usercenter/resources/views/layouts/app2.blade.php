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
    {{--<script src="https://cdn.bootcss.com/jquery/2.1.4/jquery.min.js"></script>--}}
    <link href="{{asset('css/webuploader.css')}}" rel="stylesheet">


    <script src="{{asset('js/jquery-1.7.1.min.js')}}"></script>
    <script src="{{asset('js/ajaxfileupload.js')}}"></script>
    <script src="{{asset('js/artDialog4.1.6/jquery.artDialog.js?skin=default')}}"></script>
    <script src="{{asset('js/artDialog4.1.6/plugins/iframeTools.js')}}"></script>


  </head>
  <body>
    @yield('content')

    <!-- jQuery first, then Bootstrap JS. -->

    {{--<script src="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/js/bootstrap.js"></script>--}}
  </body>
</html>