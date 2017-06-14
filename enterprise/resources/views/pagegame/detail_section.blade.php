@section('media')
            <!-- <div>
                <ul class="clearfix">
                    <li class="fl">
                        <a href="javascript:;"><img src="http://pic.vronline.com/webgames/images/02.png" /></a>
                    </li>
                    <li class="fl">
                        <a href="javascript:;"><img src="http://pic.vronline.com/webgames/images/02.png" /></a>
                    </li>
                </ul>
            </div> -->
@endsection
@section('javascript')
<script type="text/javascript">
webgameLogin.init({
    type:'bind',
    thirdBind: {
        qq: ".qq",
        wx: ".wx",
        wb: ".wb"
    },
    showLoginCaptcha:function(img,con){
        webgameLogin.showLogin();
        webgameLogin.needLoginCaptcha=0;
    }
});
</script>
@endsection
