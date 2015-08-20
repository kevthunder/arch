package com.flashkev.utils{
	public class ArrayUtil{
		//////////// variables ////////////

		//////////// Constructor ////////////
		
		public function ArrayUtil() {
		}

		//////////// Properties functions ////////////
		

		//////////// Public functions ////////////
		public static function intersect(arr1:Array, arr2:Array):Array {
			var arr2_cpy:Array = arr2.slice();
			var res:Array = new Array();
			var i:int; 
			var f:int; 
			for(i = 0; i < arr1.length; i++){
				f = arr2_cpy.indexOf(arr1[i]);
				if(f > -1){
					res.push(arr1[i]);
					arr2_cpy.splice(f,1);
				}
			}
			return res;
		}
		public static function diff(arr1:Array, arr2:Array):Array {
			var arr2_cpy:Array = arr2.slice();
			var res:Array = new Array();
			var i:int; 
			var f:int; 
			for(i = 0; i < arr1.length; i++){
				f = arr2_cpy.indexOf(arr1[i]);
				if(f == -1){
					res.push(arr1[i]);
					arr2_cpy.splice(f,1);
				}
			}
			return res;
		}
		public static function union(arr1:Array, arr2:Array):Array {
			var arr1_cpy:Array = arr1.slice();
			var res:Array = arr1.slice();
			var i:int; 
			var f:int; 
			for(i = 0; i < arr2.length; i++){
				f = arr1_cpy.indexOf(arr2[i]);
				if(f == -1){
					res.push(arr2[i]);
					arr1_cpy.splice(f,1);
				}
			}
			return res;
		}
		
		public static function trimRight(arr:Array):Array {
			var res:Array = arr.slice();
			while(res.length){
				if(res[res.length-1] === null || res[res.length-1] === undefined){
					res.pop();
				}else{
					return res;
				}
			}
			return res;
		}

		
		//////////// Private functions ////////////
		
		
	}
}