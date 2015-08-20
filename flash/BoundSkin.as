package {
	import flash.events.Event;

	public class BoundSkin extends Skin{
		//////////// variables ////////////
		protected var _target:Skin;
		protected var _zOffset:Number = 0;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function BoundSkin() {
			
        }
		
		//////////// Properties functions ////////////
		public function get target():Skin{
			return _target
		}
		public function set target(val:Skin){
			if(_target != val){
				if(_target){
					_target.removeEventListener(Skin.MOVE,targetMoveHandler);
				}
				_target = val;
				if(_target){
					_target.addEventListener(Skin.MOVE,targetMoveHandler);
					this.x = target.x;
					this.y = target.y;
					this.bindedDisplay = target.bindedDisplay;
				}
			}
		}
		public function get zOffset():Number{
			return _zOffset
		}
		public function set zOffset(val:Number){
			if(_zOffset != val){
				_zOffset = val;
				reorder();
			}
		}
		override public function get zIndex():Number{
			return target.zIndex+zOffset;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		override public function destroy(){
			super.destroy();
			target = null;
		}
		
		
		//////////// Private functions ////////////
		override protected function reorder(){
			super.reorder();
		}
		
		//////////// Event Handlers functions ////////////
		protected function targetMoveHandler(e:Event){
			this.x = target.x;
			this.y = target.y;
		}
	}
}