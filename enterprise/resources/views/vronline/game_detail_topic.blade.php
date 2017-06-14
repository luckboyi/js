@if($topic && is_array($topic))
            <h3 class="til">热点专题</h3>
            <ul class="ad_con">
              @for($i = 0; $i < count($topic); $i++)
              <li>
                <a href="{{$topic[$i]['target_url']}}" target="_blank">
                  <img src="//image.vronline.com/{{$topic[$i]['cover']}}" >
                </a>
              </li>
              @endfor
            </ul>
@endif