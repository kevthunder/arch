package com.flashkev.utils{
	public class NumberUtil{
		//////////// variables ////////////

		//////////// Constructor ////////////
		
		public function NumberUtil() {
		}

		//////////// Properties functions ////////////
		

		//////////// Public functions ////////////
		public static function formatNumber(num:Number, thousandMark:String=" ", decimalDelimiter:String=",", nbDecimals:int=6, forceDecimal:Boolean=false, forceIntegers:int=1):String{
			var integers:int = Math.floor(num);
			var decimals:int = Math.floor((num - integers)*Math.pow(10,nbDecimals)+0.01);
			
			var integersString:String = formatInt(integers, thousandMark, forceIntegers);
			
			var decimalsString:String = decimals.toString();
			if(!forceDecimal){
				decimalsString = decimalsString.replace(/0+$/,"");
			}
			
			if(decimalsString){
				return integersString+decimalDelimiter+decimalsString;
			}else{
				return integersString;
			}
			
		}
		public static function formatInt(num:int, thousandMark:String=" ", forceIntegers:int=1):String{
			var integersString:String = Math.abs(num).toString();
			var negative:Boolean = (num < 0);
			integersString = StringUtil.padString(integersString,forceIntegers,"0");
			if(thousandMark.length){
				var i:int = 3;
				while(i<integersString.length){
					integersString = integersString.substr(0,integersString.length-i) + thousandMark + integersString.substr(-i);
					i += 3 + thousandMark.length;
				}
			}
			if(negative){
				integersString = '-'+integersString;
			}
			return integersString;
		}
		public static function parseFormatedNumber(num:String, thousandMark:String=" ", decimalDelimiter:String=","):Number{
			num = num.replace(thousandMark,'');
			num = num.replace(decimalDelimiter,'.');
			return parseFloat(num);
		}
		public static function roundPrecision(num:Number,precision:Number){
			var div:int = Math.round(1/precision);
			//trace(div,Math.pow(2,Math.log(div)*Math.LOG2E));
			return Math.round(num*div)/div;
		}
		
		//////////// Private functions ////////////
		
		
	}
}