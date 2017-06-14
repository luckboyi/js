<div class="ui icon input" style="width:400px">
  <input type="text" class="action-search" placeholder="搜索游戏" value="{{ $search }}">
  @if($search)
  <i class="large remove link icon"></i>
  @else
  <i class="large search link icon"></i>
  @endif
</div>
