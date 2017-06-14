<?php
/**
 * Created by PhpStorm.
 * User: kira
 * Date: 2016/9/5
 * Time: 16:35
 */
?>
@inject('blade', 'App\Helper\BladeHelper')
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title>VRonline-运营系统</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <meta content="" name="description" />
        <meta content="Mosaddek" name="author" />
        @yield('meta')
        <link rel="stylesheet" href="http://pic.vronline.com/webgame/style/jason.css">
        {{--<link rel="stylesheet" href="http://pic.vronline.com/open/style/cikonss.css" />--}}
        <link href="{{asset('assets/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{asset('assets/bootstrap/css/bootstrap-responsive.min.css')}}" rel="stylesheet">
        <link href="{{asset('assets/bootstrap/css/bootstrap-fileupload.css')}}" rel="stylesheet">
        <link href="{{asset('assets/font-awesome/css/font-awesome.css')}}" rel="stylesheet">
        <link href="{{asset('assets/art-dialog/css/ui-dialog.css')}}" rel="stylesheet">
        <link href="{{asset('css/style.css')}}" rel="stylesheet">
        <link href="{{asset('css/style-responsive.css')}}" rel="stylesheet">
        <link href="{{asset('css/style-default.css')}}" rel="stylesheet" id="style_color" >

        <!-- 引入日历样式 -->
        <link href="{{asset('plugins/bootstrap/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet"  media="screen">

        <!-- 引入多级菜单 -->
        <link href="{{asset('plugins/bootstrap/css/bootstrap-submenu.min.css')}}" rel="stylesheet" media="screen"/>

        <!-- 引入表格排序 -->
        <link href="{{asset('css/sort-tables.css')}}" rel="stylesheet"/>

        <!-- 引入 multiselect  -->
        <link href="{{asset('plugins/bootstrap/css/bootstrap-multiselect.css')}}" rel="stylesheet" media="screen"/>

        @yield('css')
    </head>
    <!-- END HEAD -->
    <!-- BEGIN BODY -->
    <body class="fixed-top">
        <!-- BEGIN HEADER -->
        <div id="header" class="navbar navbar-inverse navbar-fixed-top">
            <!-- BEGIN TOP NAVIGATION BAR -->
            <div class="navbar-inner">
                <div class="container-fluid">
                    <!-- BEGIN LOGO -->
                    <a class="brand" href="admin/index">
                        <img src="{{asset('images/admin_logo.png')}}" alt="VR-online" />
                    </a>
                    <a class="btn btn-navbar" id="main_menu_trigger" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="arrow"></span>
                    </a>
                    <div class="top-nav">
                        <ul class="nav pull-right top-menu">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="line-height: 30px;">
                                    <span class="username">{{ $admin_username }}</span>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu extended logout">
                                    <li><a id="logout" href="javascript:;"><i class="icon-key"></i>退出</a></li>
                                </ul>
                            </li>
                            <!-- END USER LOGIN DROPDOWN -->
                        </ul>
                        <!-- END TOP NAVIGATION BAR -->
                    </div>
                </div>
            </div>
            <!-- END HEADER -->
        </div>
        <!-- BEGIN CONTAINER -->
        <div id="container" class="row-fluid">
            <!-- BEGIN 侧边栏 -->
            <div class="sidebar-scroll">
                <div id="sidebar" class="nav-collapse collapse">
                    <!-- BEGIN 侧边栏菜单 -->
                    <ul class="sidebar-menu">
                        @foreach ($menuTree[0] as $menuid)
                        <li class="sub-menu{{ isset($menuList[$menuid]["current"])?" active open":"" }}">
                            <a class="" href="javascript:;">
                                <i class="{{ $menuList[$menuid]["icon"] }}"></i>
                                <span>{{ $menuList[$menuid]["name"] }}</span>
                                @if (isset($menuTree[$menuid]))
                                <span class="arrow"></span>
                                @endif
                            </a>
                            @if (isset($menuTree[$menuid]))
                            <ul class="sub">
                                @foreach ($menuTree[$menuid] as $subMenuId)
                                <li class="flag{{ isset($menuList[$subMenuId]["current"])?" active":"" }}" menu-id="{{ $menuList[$subMenuId]["id"] }}">
                                    <a class="" href="{{ $menuList[$subMenuId]["routes_name"]?$blade->checkRoute($menuList[$subMenuId]["routes_name"]):"javascript:;" }}">
                                        {{ $menuList[$subMenuId]["name"] }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endforeach
                    </div>
                </div>
                <!-- END 侧边栏 -->
                <div id="main-content">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </div>
            </div>
            <!--右侧区块展示-->
            <!-- END CONTAINER -->

            <!-- BEGIN FOOTER -->
            <div id="footer">
                2016 &copy; MetroAdmin.
            </div>
            <!-- END FOOTER -->
            <!-- BEGIN JAVASCRIPTS -->
            <!-- Load javascripts at bottom, this will reduce page load time -->
            <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.min.js"></script>
            <script src="{{asset('js/jquery.nicescroll.js')}}"></script>
            <script src="{{asset('assets/jquery-slimscroll/jquery-ui-1.9.2.custom.min.js')}}"></script>
            <script src="{{asset('assets/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>
            <script src="{{asset('assets/bootstrap/js/bootstrap.min.js')}}"></script>

            <!-- 引入日期选择框 -->
            <script src="{{asset('plugins/bootstrap/js/bootstrap-datetimepicker.min.js')}}"></script>

            <!-- 引入多级菜单 -->
            <script type="text/javascript" src="{{asset('plugins/bootstrap/js/bootstrap-submenu.min.js')}}"></script>

            <!-- 引入表格排序 -->
            <script type="text/javascript" src="{{asset('js/sorttable.js')}}"></script>

            <!-- 引入 multiselect  -->
            <script type="text/javascript" src="{{asset('plugins/bootstrap/js/bootstrap-multiselect.js')}}"></script>

            <!-- 引入 Lodash -->
            <script type="text/javascript" src="{{asset('js/lodash.js')}}"></script>

            <!-- 引入 Echarts -->
            <script type="text/javascript" src="{{asset('js/echarts.js')}}"></script>
            <!-- 引入 常用方法 common -->
            <script type="text/javascript" src="{{asset('js/common-my.js')}}"></script>
            <!-- 引入 JQUERYfORM -->
            <script src="{{asset('js/jquery.form.js')}}"></script>

            <!-- ie8 fixes -->
            <!--[if lt IE 9]>
            <script src="{{asset('js/excanvas.js')}}"></script>
            <script src="{{asset('js/respond.js')}}"></script>
            <![endif]-->
            <script src="{{asset('js/jquery.scrollTo.min.js')}}"></script>
            <script src="{{asset('assets/art-dialog/js/dialog-min.js')}}"></script>
            <script src="{{asset('assets/art-dialog/js/dialog-plus-min.js')}}"></script>


            <!--common script for all pages-->
            <script src="{{asset('js/common-scripts.js')}}"></script>
            <script src="{{asset('js/common.js')}}"></script>
            <!--script for this page only-->
            <script type="text/javascript" src="http://pic.vronline.com/open/assets/upload/moxie.min.js"></script>
            <script type="text/javascript" src="http://pic.vronline.com/open/assets/upload/plupload.min.js"></script>
            <script type="text/javascript" src="http://pic.vronline.com/open/assets/upload/swfobject.js"></script>
            <script type="text/javascript" src="http://pic.vronline.com/open/assets/upload/fireuploader.js"></script>

            <script type="text/javascript" src="http://pic.vronline.com/open/js/open.js"></script>
            <!-- END JAVASCRIPTS -->


        </body>
        <!-- END BODY -->
    </html>
    <!--最后添加的js代码-->
    @yield('javascript')


    <script type="text/javascript">
    $(function(){
    init();
    });

    // 初始化函数
    function init(){

    // 每次需要用到这个页面的时候，记得把perm带过来
    var perm = "{{ $perm }}";

    if (perm != 'all') {
    var permList = perm.split(',');
    //console.dir(permList);
    var length = $(".flag").length;
    $.each($(".flag"),function(i,e){
    var id=$(e).attr("menu-id");
    if (permList.indexOf(id) < 0) {
    // 移除这个li
    $(e).remove();
    }
    });
    }else if(perm == ''){
    location.href="/user/login";
    }


    }

    // 退出函数
    $("#logout").click( function () {
    location.href="/user/logout";
    });

    </script>
