
<!-- BEGIN PAGE CONTENT-->

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8" />
    <title>VRonline-开放平台</title>
     <script language="JavaScript" src="http://pic.vronline.com/open/js/jquery-1.12.3.min.js"></script>

    </head>
<body>
   <input name="file1" type="file" />
   <input name="file2" type="file" />

   <label class="up">点我吧</label>
   <script type="text/javascript">


   $(function() {
        $(".up").click(function() {
            var files1 = $("input[name='file1']").prop("files");
            var files2 = $("input[name='file2']").prop("files");
            var files = [];
            files.push(files1[0]);
            files.push(files2[0]);
            up(files,function(){

            },function(){

            });
        })
   })


   function up(files) {

        var arr = [];
        $.each(files,function(a,b) {
            var reader = new FileReader();
            reader.readAsDataURL(b);
            reader.onload = function(e){
               arr.push(this.result)
            }
        })

        setTimeout(function(){
            obj = JSON.stringify(arr);
            $.post("/upload/test",{json:obj},function() {

            });
        },1000)
   }

   </script>
</body>
</html>