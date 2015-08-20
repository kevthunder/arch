<?php
class TimeUtil extends Object {

	function relTime(){
		return TimeUtil::realToRelTime(microtime(true));
	}
	
	function relToRealTime($rel){
		return floor($rel/1000+mktime(0,0,0,0,0,2011));
	}
	
	function realToRelTime($real){
		return floor(($real-mktime(0,0,0,0,0,2011))*1000);
	}
}
?>