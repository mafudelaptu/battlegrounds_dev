        <div class="modal-header">
                <div class="blackH2">        
                        <div class="pull-right" style="font-size:14px; width: 400px;text-align: right; text-transform: none;">
                                Your Pool: <span id="userPoolSpan"></span>
                        </div>
                        MATCH<green>MAKING</green>
                </div>
        </div>
        
        <div class="modal-body">
                @include("findMatch.modals.matchmaking.mmStats")
                <hr>
                <div class="row">
                        <div class="col-sm-3">
                                 <div id="searchClockArea" align="center">

                                <div id="circleG">
                                    <div id="circleG_1" class="circleG">
                                    </div>
                                    <div id="circleG_2" class="circleG">
                                    </div>
                                    <div id="circleG_3" class="circleG">
                                    </div>
                                </div>

                                <h4>
                                        <span id="matchMakingClock"></span>
                                </h4>
                                <label class="checkbox" class="t"
                                title="enlarge your elo-search-range"> <input
                                type="checkbox" name="forceSearching" id="forceSearching">
                                force Searching <a href="help/faq#forceSearching" target="_blanc"><i
                                class="icon-question-sign"></i></a>
                        </label>
                </div>

                         <div id="MatchmakingTimeNotification">
                                <h4 class="blackH4">
                                        <i class="icon-time"></i> MATCH<green>MAKING</green>
                                </h4>
                                <div>
                                        Matchmaking is every minute. Next Matchmaking in <strong><span id="nextMatchmaking"></span></strong> seconds
                                </div>
                        </div>
                       
                <h4>Searching details</h4>
                <div class="MatchMakingInfo">

                        @include("findMatch.modals.matchmaking.mmInfo")
                </div>
        </div>
        <div class="col-sm-9">
                <div class="queueChat">
                        <!-- Chat includen -->
                        {{-- @include("prototypes.chat.chat", array("chatname"=>"MatchmakingChat")) --}}
                        @include("prototypes.chat.chatNew", array("chatname" => "queueChat", "noHeader"=>true, "height"=>"330px"))
                </div>
        </div>
</div>
<br />
</div>
<div class="modal-footer">
        <button class="button btn-danger" id="leaveQueueButton">Leave Queue!</button>
</div>