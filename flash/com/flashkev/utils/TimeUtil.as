package com.flashkev.utils{
	public class TimeUtil{
		//////////// variables ////////////

		//////////// Constructor ////////////
		
		public function TimeUtil() {
		}

		//////////// Properties functions ////////////
		

		//////////// Public functions ////////////
		public static function formatTime( time:int ):String{
			var res:String = '';
			var h:int = Math.floor(time/3600);
			var m:int = Math.floor(time/60)%60;
			var s:int = time%60;
			if(h >= 0){
				res += h.toString() + ':' + StringUtil.padString(m.toString(),2,"0") + ':';
			}else{
				res += m.toString() + ':';
			}
			res += StringUtil.padString(s.toString(),2,"0");
			return res;
		}
		
		//////////// Private functions ////////////
		
		
	}
}