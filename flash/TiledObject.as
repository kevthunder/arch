package  {
	import flash.display.MovieClip;
	import com.flashkev.docroot.KRoot;
	import flash.geom.Point;
	
	public class TiledObject extends SkinnedObject implements ITiled{
		//////////// variables ////////////
		protected var _posX:Number = 0;
		protected var _posY:Number = 0;
		protected var _zIndex:Number = 0;
		
		
		//////////// Static variables ////////////
		public static var baseWidth:Number = 25;
		public static var baseHeight:Number = 25;
		
		//////////// Constructor ////////////
		public function TiledObject(data:XML = null) {
			addSavedVar('posX','@x');
			addSavedVar('posY','@y');
			importData(data);
		}
		
		//////////// Properties functions ////////////
		public function get zIndex():Number{
			return _zIndex;
		}
		
		public function get posPt():Point{
			return new Point(_posX,_posY);
		}
		/*public function set posPt(val:Point):void{
			moveTo(val.x,val.y);
		}*/
		
		public function get posX():Number{
			return _posX;
		}
		public function set posX(val:Number){
			_posX = val;
		}
		
		public function get posY():Number{
			return _posY;
		}
		public function set posY(val:Number){
			_posY = val;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function moveTo(x,y){
			_posX = x;
			_posY = y;
		}
		override public function toString():String{
			return '(' + posX.toString() + ',' + posY.toString() + ')';
		}
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		

		

	}
	
}
