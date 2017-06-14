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
    <link href="{{asset('css/webuploader.css')}}" rel="stylesheet">


    <script language="JavaScript" src="http://pic.vronline.com/common/js/jquery-1.12.3.min.js"></script>
    <script src="{{asset('js/ajaxfileupload.js')}}"></script>
    <script src="{{asset('js/jquery.Jcrop_new.js')}}"></script>
    <link href="{{asset('css/jquery.Jcrop.min.css')}}" rel="stylesheet">
    <script src="{{asset('js/artDialog4.1.6/jquery.artDialog.js?skin=default')}}"></script>
    <script src="{{asset('js/artDialog4.1.6/plugins/iframeTools.js')}}"></script>


  </head>
  <body>
    @yield('content')

    <!-- jQuery first, then Bootstrap JS. -->

    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  </body>
</html>