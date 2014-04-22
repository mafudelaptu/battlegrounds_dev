<?php 

class UpdateEventEventHandler {
 
    CONST EVENT = 'event:user';
    CONST CHANNEL = 'event:user';
 
    public function handle($data)
    {
        //$redis = Redis::connection();
        Redis::publish(self::CHANNEL, $data);
    }
}
 ?>
