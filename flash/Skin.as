package {
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.geom.Point;
	import flash.geom.Matrix;


	[Event(name="move", type="Skin")]
	public class Skin extends MovieClip {
		//////////// variables ////////////
		protected var _owner:SkinnedObject; 
		protected var _bindedDisplay:IDisplay;
		protected var _animator:SequencesAnimator;
		protected var _zIndex:Number = 0;
		public var isMain:Boolean = true; //quickfix
		
		//////////// Static variables ////////////
		public static const MOVE:String = "move";
		
		//////////// Constructor ////////////
        public function Skin() {
			_animator = new SequencesAnimator();
        }
		
		//////////// Properties functions ////////////
		
		
		override public function set x(val:Number):void{
			if(x != val){
				super.x = val;
				moved();
			}
			
		}
		override public function set y(val:Number):void{
			if(y != val){
				super.y = val;
				moved();
			}
		}
		public function get pos():Point{
			return new Point(x,y);
		}
		public function set pos(val:Point):void{
			if(x != val.x || y != val.y){
				super.x = val.x;
				super.y = val.y;
				moved();
			}
		}
		
		public function get zIndex():Number{
			return _zIndex;
		}
		public function get displayed():Boolean{
			return owner.displayed;
		}
		
		public function get animator():SequencesAnimator{
			return _animator;
		}
		
		public function get owner():SkinnedObject{
			return _owner;
		}
		public function set owner(val:SkinnedObject){
			if(_owner != val){
				if(_owner != null){
					if(_owner.skin == this){
						_owner.skin == null;
					}
					_owner.removeEventListener(SkinnedObject.DISPLAYED,displayedHandler);
					_owner.removeEventListener(SkinnedObject.HIDDED,hiddedHandler);
					_owner.removeEventListener(SavedItem.UPDATED,updatedHandler);
				}
				var oldVal:SkinnedObject = _owner;
				_owner = val;
				dispatchEvent(new ConfigEvent(ConfigEvent.CHANGED,'owner',oldVal,val));
				if(_owner != null){
					if(isMain){ //quickfix
						_owner.skin = this;
					}
					_owner.addEventListener(SkinnedObject.DISPLAYED,displayedHandler);
					_owner.addEventListener(SkinnedObject.HIDDED,hiddedHandler);
					_owner.addEventListener(SavedItem.UPDATED,updatedHandler);
					if(_owner.displayed && _bindedDisplay != null){
						if(this is BlackFlameSkin){
							var test = "test";
						}
						_bindedDisplay.addSkin(this);
						reorder();
					}
				}else if(_bindedDisplay != null){
					_bindedDisplay.removeSkin(this);
				}
			}
		}
		public function get bindedDisplay():IDisplay{
			return _bindedDisplay;
		}
		public function set bindedDisplay(val:IDisplay){
			if(_bindedDisplay != val){
				if(_bindedDisplay != null){
					_bindedDisplay.removeSkin(this);
				}
				_bindedDisplay = val;
				if(_bindedDisplay != null && owner != null && owner.displayed){
					_bindedDisplay.addSkin(this);
					reorder();
				}
			}
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function destroy(){
			bindedDisplay = null;
			owner = null;
			animator.destroy();
			_animator = null;
		}
		
		//////////// Private functions ////////////
		protected function moved(){
			dispatchEvent(new Event(Skin.MOVE));
		}
		protected function reorder(){
			if(parent != null && parent.numChildren > 1){
				var i:int = parent.getChildIndex(this);
				while(i > 0 && ((parent.getChildAt(i-1) as Skin) == null || (parent.getChildAt(i-1) as Skin).zIndex > zIndex)){
					i--;
				}
				while(i < parent.numChildren-1 && ((parent.getChildAt(i+1) as Skin) == null || (parent.getChildAt(i+1) as Skin).zIndex < zIndex)){
					i++;
				}
				parent.setChildIndex(this,i);
			}
		}
		
		//////////// Event Handlers functions ////////////
		protected function displayedHandler(e:Event){
			if(_bindedDisplay != null){
				_bindedDisplay.addSkin(this);
				reorder();
			}
		}
		protected function hiddedHandler(e:Event){
			if(_bindedDisplay != null){
				_bindedDisplay.removeSkin(this);
			}
		}
		protected function updatedHandler(e:Event){
		}
		
	}
}