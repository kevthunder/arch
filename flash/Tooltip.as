package {
	import flash.display.MovieClip;
	import flash.display.InteractiveObject;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import com.flashkev.utils.Margins;
	import flash.display.DisplayObject;
	
	import caurina.transitions.Tweener;
	
	public class Tooltip extends MovieClip {
		//////////// variables ////////////
		protected var _width:Number;
		protected var _height:Number;
		protected var _orientation:String = TooltipBg.RIGHT;
		protected var _content:DisplayObject;
		protected var _margins:Margins = new Margins(3,8,3,8);
		protected var _openingSpeed:int = 300;
		protected var _opened:Boolean = true;
		protected var _prcOpened:Number = 1;
		protected var _bindedTo:InteractiveObject;
		protected var _bindedDist:Number = 5;
		
		
		protected var _bg:TooltipBg;
		protected var _bgPositions:Object = {
				top    : { x:0.5, y:0   },
				right  : { x:1,   y:0.5 },
				bottom : { x:0.5, y:1   },
				left   : { x:0,   y:0.5 }
			};
		protected var _bindedPositions:Object = {
				top    : { x:0.5, y:1   },
				right  : { x:0,   y:0.5 },
				bottom : { x:0.5, y:0   },
				left   : { x:1,   y:0.5 }
			};
		
		//////////// Constructor ////////////
		public function Tooltip(opened = true){
			if(!opened){
				this.prcOpened = 0;
				this.opened = false;
			}
			
			_bg = new TooltipBg();
			addChildAt(_bg,0);
			
			var w:Number = super.width;
			var h:Number = super.height;
			scaleX = 1;
			scaleY = 1;
			width = w;
			height = h;
			
			//addEventListener(Event.ADDED_TO_STAGE,addedToStageHandler);
		}
		
		
		//////////// Properties functions ////////////
		override public function get width():Number{
			return _width;
		}
		override public function set width(val:Number):void{
			if(_width != val){
				_width = val;
				resize();
			}
		}
		
		override public function get height():Number{
			return _height;
		}
		override public function set height(val:Number):void{
			if(_height != val){
				_height = val;
				resize();
			}
		}
		
		public function get orientation():String{
			return _orientation;
		}
		public function set orientation(val:String):void{
			_orientation = val;
			resize();
		}
		
		
		public function get content():DisplayObject{
			return _content;
		}
		public function set content(val:DisplayObject):void{
			if(_content != val){
				if(_content != null){
					removeChild(_content);
				}
				_content = val;
				addChild(_content);
				resize();
			}
		}
		
		
		public function get openingSpeed():int{
			return _openingSpeed;
		}
		public function set openingSpeed(val:int):void{
			_openingSpeed = val;
		}
		
		
		
		public function get opened():Boolean{
			return _opened;
		}
		public function set opened(val:Boolean):void{
			if(_opened!=val){
				_opened = val;
				Tweener.addTween(this, {prcOpened:(_opened?1:0), time:openingSpeed/1000, transition:"easeInOutCubic"});
			}
		}
		
		public function get prcOpened():Number{
			return _prcOpened;
		}
		public function set prcOpened(val:Number):void{
			_prcOpened = val;
			resize();
		}
		
		public function get margins():Margins{
			return _margins.copy();
		}
		public function set margins(val:Margins):void{
			_margins = val;
			resize();
		}
		
		public function get bindedTo():InteractiveObject{
			return _bindedTo;
		}
		public function set bindedTo(val:InteractiveObject):void{
			if(_bindedTo != val){
				if(_bindedTo != null){
				   _bindedTo.removeEventListener(MouseEvent.MOUSE_OVER,bindedOverHandler);
				   _bindedTo.removeEventListener(MouseEvent.MOUSE_OUT,bindedOutHandler);
				}
				_bindedTo = val;
				if(_bindedTo != null){
				   _bindedTo.addEventListener(MouseEvent.MOUSE_OVER,bindedOverHandler);
				   _bindedTo.addEventListener(MouseEvent.MOUSE_OUT,bindedOutHandler);
				   prcOpened = 0;
				   _opened = false;
				}
				resize();
			}
		}
		
		public function get bindedDist():Number{
			return _bindedDist;
		}
		public function set bindedDist(val:Number):void{
			if(_bindedDist != val){
				_bindedDist = val;
				resize();
			}
		}
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		protected function resize(){
			if(content && _bg){
				_bg.orientation = orientation;
				var full_margins:Margins = _margins.add(_bg.margins);
				content.x = full_margins.left;
				content.y = full_margins.top;
				width = content.width + full_margins.left + full_margins.right;
				height = content.height + full_margins.top + full_margins.bottom;
				_bg.width = width*_prcOpened;
				_bg.height = height*_prcOpened;
				_bg.x= (width-_bg.width)*_bgPositions[orientation].x;
				_bg.y= (height-_bg.height)*_bgPositions[orientation].y;
				content.visible = (_prcOpened == 1);
				_bg.visible = (_prcOpened != 0);
				if(bindedTo){
					x = bindedTo.x - width - bindedDist + (bindedTo.width + width + bindedDist*2)*_bindedPositions[orientation].x;
					y = bindedTo.y - height - bindedDist + (bindedTo.height + height + bindedDist*2)*_bindedPositions[orientation].y;
				}
			}
		}
		
		//////////// Event Handlers functions ////////////
		
		protected function bindedOverHandler(e:Event){
			opened = true;
		}
		
		protected function bindedOutHandler(e:Event){
			opened = false;
		}
		
		
	}
	
	
}