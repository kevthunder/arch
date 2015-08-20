package com.flashkev.utils{
	public class StringUtil{
		//////////// variables ////////////

		//////////// Constructor ////////////
		
		public function StringUtil() {
		}

		//////////// Properties functions ////////////
		

		//////////// Public functions ////////////
		public static function ucfirst(string:String):String{
			return string.substr(0,1).toUpperCase() + string.substr(1);
		}
		
		public static function repeat(string:String, numTimes:uint):String
		{
			if(numTimes == 0) return "";
			if(numTimes & 1) return string + repeat(string, numTimes - 1);
			var tmp:String = repeat(string, numTimes/2);
			return tmp + tmp;
		}
		public static function padString(str:String, n:Number, pStr:String= " ", leftSide:Boolean=true):String
		{
			if (str.length < n){
				var pad:String
				if(pStr.length>1){
					pad = repeat(pStr,Math.ceil(n/pStr.length));
					if(leftSide){
						pad = pad.substr(0,n-str.length);
					}else{
						pad = pad.substr(str.length,n-str.length);
					}
				}else{
					pad = repeat(pStr,n-str.length);
				}
				if(leftSide){
					str = pad+str;
				}else{
					str = str+pad;
				}
			}
			
			return str;
		}
		public static function toBool(val):Boolean {
			switch (val) {
				case "1" :
				case "true" :
				case "yes" :
					return true;
				case "0" :
				case "false" :
				case "no" :
					return false;
				default :
					return Boolean(val);
			}
		}


		
		//////////// Private functions ////////////
		
		
	}
}