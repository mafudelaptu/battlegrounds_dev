<li><a href="{{URL::to('profile/'.$user_id)}}" target="_blank"><i class="icon-user"></i> show Profile</a></li>
@if($inMatch && Auth::user()->id != $playerdata['user_id'] && $matchState=="open")
<li><a href="javascript:void(0)" target="_blank" onclick="sendPingNotification(this)" data-value="{{$playerdata['user_id']}}"><i class="icon-user"></i>ping him!</a></li>	
@endif

<li class="divider"></li>
<li><a href="https://dotabuff.com/players/{{$user_id}}" target="_blank">show DotaBuff-Profile</a></li>
<li><a href="http://steamcommunity.com/profiles/{{$user_id}}/" target="_blank">show Steam-Profile</a></li>