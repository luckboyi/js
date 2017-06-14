<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>加载minipay</title>
        <script type="text/javascript" src="http://pic.vronline.com/common/js/jquery-1.12.3.min.js"></script>
        <script type="text/javascript" src="http://pic.vronline.com/pay/minipay.js"></script>
</head>
<body>
        <button type="" class="open">open</button>
</body>
<script type="text/javascript">
        $(".open").click(function(){
                VRminipay.open({
                        paytoken:"{{$payToken}}",
                        appid:1,
                        serverid:1,
                        openid:"{{$openid}}",
                        price:2,
                        total:2,
                        num:1,
                        url:"//image.vronline.com/vrgameimg/pub/1000015/logo?v=100",
                        extra1:"10017|game",
                        itemid:"1000015",
                        item:"raw data",
                        isdev:1,
                        from:'vrgame'
                });
        });
</script>
</html>
