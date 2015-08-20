package com.flashkev.utils{
	import flash.geom.Point;
	
	public class GeomUtils{
		//////////// variables ////////////

		//////////// Constructor ////////////
		
		public function GeomUtils() {
		}

		//////////// Properties functions ////////////
		

		//////////// Public functions ////////////
		public static function solveAngle( pt1:Point, pt2:Object, degrees:Boolean = false):Number {
			if(pt1 == null){
				pt1 = new Point(0,0);
			}
			var angle:Number = Math.atan2( pt2.y - pt1.y, pt2.x - pt1.x);
			if(degrees){
				angle *= (180 / Math.PI)
			}
			return angle;
		}
		
		
		//////////// Private functions ////////////
		
		
	}
}