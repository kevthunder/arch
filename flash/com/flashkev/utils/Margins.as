package com.flashkev.utils{
    import flash.display.*;
	import flash.geom.*;

	public class Margins extends Object{
		//////////// variables ////////////
		public var top:Number = 0;
		public var right:Number = 0;
		public var bottom:Number = 0;
		public var left:Number = 0;
		
		//////////// Constructor ////////////
		public function Margins(top:Number = 0,right:Number = 0,bottom:Number = 0,left:Number = 0){
			this.top = top;
			this.right = right;
			this.bottom = bottom;
			this.left = left;
		}
      
		//////////// Properties functions ////////////
		
		
		//////////// Public functions ////////////
		public function copy(){
			return clone();
		}
		public function clone(){
			return new Margins(this.top,this.right,this.bottom,this.left);
		}
		
		public function add(val:Margins){
			return new Margins(this.top+val.top,this.right+val.right,this.bottom+val.bottom,this.left+val.left);
		}
		
		public function subtract(val:Margins){
			return new Margins(this.top-val.top,this.right-val.right,this.bottom-val.bottom,this.left-val.left);
		}
		
		public function addToPoint(pt:Point):Point{
			return new Point(pt.x + right + left,pt.y + top + bottom);
		}
		public function addToRect(rec:Rectangle, outside:Boolean = true, expand:Boolean = true):Rectangle{
			var m:int = expand ? 1 : -1;
			rec = rec.clone();
			if(outside){
				rec.x += left*m;
				rec.y += top*m;
			}
			rec.width += (right + left)*m;
			rec.height += (top + bottom)*m;
			return rec;
		}
		
		public function setAllMargins(val:Number=0){
			this.top = val;
			this.right = val;
			this.bottom = val;
			this.left = val;
		}
		
		public function toString():String{
			return "{top:"+this.top+", right:"+this.right+", bottom:"+this.bottom+", left:"+this.left+"}";
		}
		
		//////////// Private functions ////////////
		
		
	}
}