package {
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.geom.Point;
	import com.flashkev.utils.Margins;
	
	public class TooltipBg extends MovieClip {
		//////////// variables ////////////
		protected var _width:Number;
		protected var _height:Number;
		protected var _orientation:String = RIGHT;
		protected var _margins:Object = {
				top    : new Margins(6,0,0,0),
				right  : new Margins(0,7,0,0),
				bottom : new Margins(0,0,6,0),
				left   : new Margins(0,0,0,7)
			};
		protected var _arrowPos:Object = {
				top    : { x:0.5, y:0,   r:-90 },
				right  : { x:1,   y:0.5, r:0   },
				bottom : { x:0.5, y:1,   r:90  },
				left   : { x:0,   y:0.5, r:180 }
			};
		
		public var square:MovieClip;
		public var arrow:MovieClip;
		
		protected var originArrowSize:Point;
		
		public static const TOP:String = 'top';
		public static const RIGHT:String = 'right';
		public static const BOTTOM:String = 'bottom';
		public static const LEFT:String = 'left';
		//////////// Constructor ////////////
		public function TooltipBg(){
			
			var w:Number = super.width;
			var h:Number = super.height;
			scaleX = 1;
			scaleY = 1;
			
			originArrowSize = new Point(arrow.width,arrow.height);
			
			width = w;
			height = h;
			
			
			//addEventListener(Event.ADDED_TO_STAGE,addedToStageHandler);
		}
		
		
		//////////// Properties functions ////////////
		override public function get width():Number{
			return _width;
		}
		override public function set width(val:Number):void{
			_width = val;
			resize();
		}
		
		override public function get height():Number{
			return _height;
		}
		override public function set height(val:Number):void{
			_height = val;
			resize();
		}
		
		public function get orientation():String{
			return _orientation;
		}
		public function set orientation(val:String):void{
			_orientation = val;
			resize();
		}
		
		public function get margins():Margins{
			return _margins[orientation].copy();
		}
		//////////// Public functions ////////////
		
		
		//////////// Private functions ////////////
		protected function resize(){
			arrow.rotation = _arrowPos[orientation].r;
			var squareW:Number = width-margins.left-margins.right;
			var squareH:Number = height-margins.top-margins.bottom;
			if(squareW >= 1 && squareH >= 1){
				square.visible = true;
				square.width = squareW;
				square.height = squareH;
			}else{
				square.visible = false;
			}
			square.x = margins.left;
			square.y = margins.top;
			arrow.x = width*_arrowPos[orientation].x;
			arrow.y = height*_arrowPos[orientation].y;
			arrow.width = Math.min(originArrowSize.x,width);
			arrow.height = Math.min(originArrowSize.y,height);
		}
		
		//////////// Event Handlers functions ////////////
		
	}
	
	
}