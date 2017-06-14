@inject('blade', 'App\Helper\BladeHelper')
@extends('open.admin.nav')

@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
<script type="text/javascript" src="/admincp/public.js"></script>
@endsection



@section('content')


<h4 class="ui top attached block header">
    账号信息
</h4>
  <div class="ui bottom attached segment">

  <table class="ui very basic user table">
  <tbody>
    <tr>
      <td style="width:10%;">用户ID</td>
      <td>1000377</td>
    </tr>
    <tr>
      <td>类型</td>
      <td>个人开发者</td>
    </tr> <tr>
      <td>姓名</td>
      <td>1000377</td>
    </tr> <tr>
      <td>身份证</td>
      <td>1000377</td>
    </tr> <tr>
      <td>电子邮箱</td>
      <td>1000377</td>
    </tr> <tr>
      <td>联系地址</td>
      <td>1000377</td>
    </tr> <tr>
      <td>证件</td>
      <td>
        <a href="http://google.com" target="_blank">
            <img class="ui medium rounded image" src="http://www.semantic-ui.cn/images/wireframe/image-text.png">
        </a>
    </td>

    </tr>
  </tbody>
</table>

</div>








@endsection
@section('javascript')
<script type="text/javascript">
function add() {
    modal_edit = $('.ui.modal.modal-add').modal('show');
}
</script>
@endsection
