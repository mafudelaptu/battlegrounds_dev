<?php 

class UpdateUserEventHandler {
	
    CONST EVENT= 'user';
    CONST CHANNEL = 'user';
    public function handle($data)
    {
        $redis = Redis::connection();
        $redis->publish(self::CHANNEL, $data);
    }
}
 ?>
