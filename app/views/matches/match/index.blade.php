@section('content')

<div class="box" style="overflow:visible">
    <div class="box_title">
        <div class="row">
            <div class="col-sm-4">{{$matchData['matchtype']}}</div>
            <div class="col-sm-4 text-center">MatchID: <strong>{{$match_id}}</strong></div>
            <div class="col-sm-4 text-right">
                Matchmode&nbsp;&nbsp;<span class="badge badge-info" class="t" title="{{$matchData->matchmode}}">{{$matchData->mm_shortcut}}</span>&nbsp;&nbsp;
                 Region&nbsp;&nbsp;<span class="badge badge-danger" class="t" title="{{$matchData->region}}">{{$matchData->r_shortcut}}</span>
            </div>
        </div>
    </div>
    <div class="box_content">
        @if(!empty($matchPlayersData))
        @include("matches.match.info", array('matchmode' => $matchData['matchmode'], "region" => $matchData['region'], "mm_shortcut" => $matchData['mm_shortcut'], "r_shortcut" => $matchData['r_shortcut']))
        <div class="row">
            <div class="col-sm-5" style="width:495px;">
                @include("matches.match.team", array('data' => $matchPlayersData[1], "team_id" => 1))
            </div>
            <?php 
                if($matchData->matchtype_id != 2){
                    $lineHeight = "line-height:220px";
                }
                else{
                    $lineHeight = "";
                }
             ?>
            <div class="col-sm-1" style="padding:0; width:98px; {{$lineHeight}}">
                {{HTML::image("img/match/VS_blue.png", "Versus", array("width"=>"100%"))}}
            </div>
            <div class="col-sm-5" style="width:495px;">
                @include("matches.match.team", array('data' => $matchPlayersData[2], "team_id" => 2))
            </div>
        </div>
        
<div class="row">
            <div class="col-sm-8">
                  @if($inMatch && $matchState == "open")
                {{-- in Match und noch nix gemacht --}}
                @include("matches.match.middle_area_result_open")
                
                @elseif($inMatch && $matchState == "submitted")
                {{-- in Match und submitted --}}
                @include("matches.match.middle_area_result_submitted")
                @elseif($matchState == "closed")

                @elseif($matchState == "canceled")
                {{-- Matchresult ist fest --}}
                @include("matches.match.middle_area_result_canceled")
                
                @else
                {{-- Besucher: spieler in Match --}}
                @include("matches.match.middle_area_result_visitor")
                @endif
           
            </div>
            <div class="col-sm-4">
                @if($matchData->matchtype_id != "2" && $inMatch)
            
                @include("matches.match.middle_area_replayUpload", array("uploaded"=>$replaySubmitted))
            
            @endif
            </div>
        </div>

        @include("matches.match.replay.index")

        <?php 

        if($inMatch && ($matchState == "open" || $matchState == "submitted")){

            $disableChat = false;
        }
        else{
            $disableChat = true;
        }

        ?>
        @if($matchData['matchtype_id'] != "2")
        @include("prototypes.chat.chatNew", array("chatname"=>"matchChat".$matchData['id'], "disableChat" => $disableChat, "height"=>"200px"))
        @else
        @if(($matchState =="submitted" || $matchState == "closed") && !empty($screenshots))
        <div class="row">
            <div class="col-sm-6">

                @include("prototypes.chat.chatNew", array("chatname"=>"matchChat".$matchData['id'], "disableChat" => $disableChat,  "height"=>"200px"))

            </div>
            <div class="col-sm-6">

                @include("matches.match.screenshots")
            </div>
        </div>
        @else
        @include("prototypes.chat.chatNew", array("chatname"=>"matchChat".$matchData['id'], "disableChat" => $disableChat, "height"=>"200px"))
        @endif

        @endif

        @else
        <p>access denied!</p>
        @endif
    </div>
</div>
@stop