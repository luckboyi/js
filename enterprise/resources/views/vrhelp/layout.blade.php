<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @yield("meta")
    <link rel="stylesheet" href="{{ static_res('/vrhelp/style/base.css') }}">
    <link rel="stylesheet" href="{{ static_res('/vrhelp/style/vronline.css') }}">
    <link rel="stylesheet" href="{{ static_res('/vrhelp/style/vrgame.css') }}">
    <link rel="stylesheet" href="{{ static_res('/vrhelp/style/3dbobo.css') }}">
    <link rel="stylesheet" href="{{ static_res('/vrhelp/style/slideStyle.css') }}">
    <script language="JavaScript" src="{{ static_res('/common/js/jquery-1.12.3.min.js') }}"></script>
    <script language="JavaScript" src="{{ static_res('/vrhelp/js/plugin/jquery.SuperSlide.2.1.1.js') }}"></script>
    @yield("head")
    <style type="text/css">
        .main_container .main_con{
            padding-left: 15px;
        }
    </style>
    </style>
</head>
<body>


<div class="main_container clearfix" @if(isset($left)) style="padding-left: 200px" @endif>


@yield("content")


</div>
@yield("javascript")
@if(!isset($left))
<script type="text/javascript">
    $(document).on('click','.game_detail',function(e){
        parent.gameDetail($(this).attr('data-val'))
    })

    $(document).on('click','.video_play',function(e){
        parent.videoDetail($(this).attr('data-val'))
    })

</script>
@endif
<script type="text/javascript">
$('#myIframe').load(function(){
    // $('#myIframe').contents().find('body').css('background','rgba(255, 255, 255, 0)');
})
</script>
</body>
</html>
