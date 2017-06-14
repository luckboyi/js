
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>第三方后台</title>
    <link rel="stylesheet" href="/open/style/base.css">
    <link rel="stylesheet" href="/open/style/personal_center.css">
    <link rel="stylesheet" href="/open/style/jason.css">
    <link rel="stylesheet" href="/open/style/cikonss.css" />
    <script type="text/javascript" src="/js/jquery.min.js"></script>
</head>

<body>
<div id="container">
<a href="#" id="pickfiles">upload</a>
</div>
</body>
</html>
 </body>
<script type="text/javascript" src="/open/upload/moxie.min.js"></script>
<script type="text/javascript" src="/open/upload/plupload.min.js"></script>
<script type="text/javascript" src="/open/upload/fireupload.js"></script>
<script type="text/javascript">

var fup = fireUpload(
{
    browse_button : 'pickfiles',
    container: document.getElementById('container'),
    url : '/upload/start',
    dir_name:"webgame",
    assign_name:'idcard.jpg',
    filters : {
        max_file_size : '5mb',
        mime_types: [
            {title : "Image files", extensions : "jpg"},
        ]
    },
    init: {
        'FileUploaded': function(up, file, info) {
            console.log(up)
            console.log(file)
            console.log(info.data.name)
            console.log("uploaded")
        },
        'Error': function(up, err, errTip) {
             console.log("error")
                console.log(up,err,errTip)
        }
    }
});
</script>
</html>