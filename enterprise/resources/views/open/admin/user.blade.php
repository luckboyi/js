@inject('blade', 'App\Helper\BladeHelper')
@extends('open.admin.nav')

@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
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
      <td>{{ $data['uid'] }}</td>
    </tr>
    <tr>
      <td>类型</td>
      <td>@if($data['type']==1) 企业开发者 @else 个人开发者 @endif</td>
    </tr>
    @if($data['type']==1)
    <tr>
      <td>公司名称</td>
      <td>{{ $data['name'] }}</td>
    </tr>
    <tr>
      <td>营业执照注册号</td>
      <td>{{ $data['idcard'] }}"</td>
    </tr>
    <tr>
      <td>联系人</td>
      <td>{{ $data['contacts'] }}"</td>
    </tr>
    @else
    <tr>
      <td>姓名</td>
      <td>{{ $data['name'] }}</td>
    </tr>
    <tr>
      <td>身份证</td>
      <td>{{ $data['idcard'] }}</td>
    </tr>
    @endif
    <tr>
      <td>电子邮箱</td>
      <td>{{ $data['email'] }}</td>
    </tr>
     <tr>
      <td>联系地址</td>
      <td>{{ $data['address'] }}</td>
    </tr>
    <tr>
      <td>证件</td>
      <td>
          <a href="{{ $data['url']['idcard'] }}" target="_blank">
              <img class="ui medium rounded image" src="{{ $data['url']['idcard'] }}">
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
