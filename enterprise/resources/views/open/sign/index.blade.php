@inject('blade', 'App\Helper\BladeHelper')
@extends('open.admin.nav')

@section('head')
<script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
@endsection



@section('content')

<div class="ui text container">
      <p class="developer">请选择成为怎样的开发者，选择后无法进行修改</p>
       <div class="ui link cards developer">
        <div class="card" onclick="goTo('user')">
            <div class="content"  >
                <i class="bgreat iconfont-fire iconfire-kaifazhe icon   action-audit"></i>
                <div class="header">个人开发者</div>
                <div class="description">需上传手执身份证</div>
            </div>
        </div>
        <div class="card" onclick="goTo('company')">
            <div class="content" >
                <i class="bgreat iconfont-fire iconfire-qiye icon  action-audit"></i>
                <div class="header">企业开发者</div>
                <div class="description">需上传企业营业执照</div>
            </div>
        </div>

    </div>
</div>


@endsection
@section('javascript')
<script type="text/javascript">
function goTo(name) {
    location.href='/developer/sign/fill/'+name
}
</script>
@endsection
