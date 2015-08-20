package {
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.geom.Point;
	import com.flashkev.utils.GeomUtils;
	import com.flashkev.utils.NumberUtil;

	public class MovableSkin extends TiledSkin {
		//////////// variables ////////////
		protected var _suggestedRotation:Number = 0; 
		protected var _oldPt:Point = null;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function MovableSkin() {
        }
		
		//////////// Properties functions ////////////
		public function get suggestedRotation():Number{
			return _suggestedRotation
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		override protected function moveHandler(e:Event){
			super.moveHandler(e);
			var newPos:Point = new Point(x,y);
			if(_oldPt != null && !_oldPt.equals(newPos)){
				_suggestedRotation = GeomUtils.solveAngle(_oldPt,newPos,true);
				//trace(_suggestedRotation, _oldPt, newPos);
			}
			_oldPt = newPos; 
		}
		
		
	}
}