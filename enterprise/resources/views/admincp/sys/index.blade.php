@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('content')

<style>
.sidetop  .ui.vertical.menu{
  border:none;
}
.sidetop .ui.vertical.menu .item {
  background: #fff;
}
.sidetop .ui.vertical.menu .active.item,.sidetop .ui.vertical.menu .active.item:hover,.sidetop .ui.vertical.menu .item:hover{
  background: #fff;
}
.sidetop .ui.vertical.menu .item .menu .active.item {
    color:#2277DA;
}
</style>
<div class="ui small button right floated blue" onclick="show()"><i class="plus icon"></i>新建</div>






@endsection

@section('javascript')
<script type="text/javascript">


$('.ui.checkbox')
  .checkbox()
;

$('select.dropdown')
  .dropdown()
;


function show() {
 $('.ui.modal')
  .modal('show')
;
}


var content = [
  { title: 'Andorra' },
  { title: 'United Arab Emirates' },
  { title: 'Afghanistan' },
  { title: 'Antigua' },
  { title: 'Anguilla' },
  { title: 'Albania' },
  { title: 'Armenia' },
  { title: 'Netherlands Antilles' },
  { title: 'Angola' },
  { title: 'Argentina' },
  { title: 'American Samoa' },
  { title: 'Austria' },
  { title: 'Australia' },
  { title: 'Aruba' },
  { title: 'Aland Islands' },
  { title: 'Azerbaijan' },
  { title: 'Bosnia' },
  { title: 'Barbados' },
  { title: 'Bangladesh' },
  { title: 'Belgium' },
  { title: 'Burkina Faso' },
  { title: 'Bulgaria' },
  { title: 'Bahrain' },
  { title: 'Burundi' }
  // etc
];
$('.ui.search')
  .search({
    source: content
  })
;


$('.floating.dropdown')
  .dropdown()
;

function allowDrop(ev)
{
  ev.preventDefault();
}

function drag(ev)
{
  var id = ev.target.id;
  ev.dataTransfer.setData("id",id);
}
function drop(ev)
{
  ev.preventDefault();
  var id = ev.dataTransfer.getData("id");
  var drop = $(ev.target).parents(".card");
  var dragHtml = $("#"+id).clone().prop("outerHTML");
  var dropHtml = drop.prop("outerHTML")
  $("#"+id).replaceWith(dropHtml);
  drop.replaceWith(dragHtml);
}
</script>
@endsection