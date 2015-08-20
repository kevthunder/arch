package com.flashkev.utils{
	// Ex:
	// myBtn.addEventListener(MouseEvent.CLICK,FunctionPlusParam.makeFunct(myFunct,'foo'));
	// function myFunct(e:Event,caption){
	//		trace(caption); // output : foo
	// }
	//
	// Or :
	//
	// var catcher = new FunctionPlusParam(myFunct,'foo');
	// myBtn.addEventListener(MouseEvent.CLICK,catcher.call);
	// function myFunct(e:Event,caption){
	//		trace(caption); // output : foo
	// }
	public class FunctionPlusParam extends Object
	{
		//////////// variables ////////////
		private var _addedParams:Array;
		private var _funct:Function;

		//////////// Constructor ////////////
		public function FunctionPlusParam(funct:Function,... rest)
		{
			_funct = funct;
			_addedParams = rest;
		}

		//////////// Properties functions ////////////
		
		//////////// functions ////////////
		public function addParam(... rest){
			_addedParams = _addedParams.concat(rest);
		}
		public function call(... rest){
			_funct.apply(this, rest.concat(_addedParams));
		}
		
		public static function makeFunct(funct:Function,... rest):Function{
			var newFunct:Function = function(... rest2){
										funct.apply(this, rest2.concat(rest));
									};
			return newFunct;
		}
	}
}