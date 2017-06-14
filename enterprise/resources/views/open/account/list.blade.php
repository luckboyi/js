@inject('blade', 'App\Helper\BladeHelper')
@extends('open.nav')



@section('head')
<link rel="stylesheet" href="{{static_res('/open/assets/art-dialog/css/ui-dialog.css')}}" />
<script src="{{static_res('/open/assets/art-dialog/js/dialog-min.js')}}"></script>
<script src="{{static_res('/open/js/common.js')}}"></script>
@endsection


@section('content')
<!--内容-->
<div class="container container-list">
    <div class="container-head clearfix">
        <button type="button" class="btn btn-sub-account-edit">
            <span class="icon icon-small"><i class="icon icon-small icon-plus"></i></span> 添加子账号
        </button>
    </div>
    <div class="table-con">
        <table class="personal-table" border="0">
         <tr class="title">
                <th>ID</th>
                <th>账号</th>
                <th>操作</th>
            </tr>
            @if(count($accounts)<1)
                <tr><td colspan="4">暂无数据</td></tr>
            @else
                @foreach ($accounts as $account)
                <tr>
                <td>{{ $account["uid"] }}</td>
                <td>{{ $account["contacts"] }}</td>
                <td class="btn-cancel" data-id="{{ $account["uid"] }}" data-name="{{ $account["contacts"] }}"><a href="#">取消关联</a></td>
                </tr>
                @endforeach
            @endif
        </table>
    </div>

</div>
@endsection


@section('javascript')
<script type="text/javascript">
$(function(){
    $(".btn-cancel").click(function() {
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        myDialog.confirm({
        title:"取消关联",
        content:"你确定取消关联"+name+"的账号吗?",
        ok:function(){
           cancelAccount(id);
        }
      });
    });
});

function cancelAccount(uid) {
    $.post("/delAccount",{uid:uid},function(res){
        console.log(res);
        if(res.code==0) {
            Open.showMessage("取消关联成功",1500,function(){
                location.reload();
            });
        } else {
            Open.showMessage(res.msg);
        }
    },"json");
}
</script>
@endsection