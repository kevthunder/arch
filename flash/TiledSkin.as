package {
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.geom.Point;
	import flash.geom.Matrix;

	public class TiledSkin extends Skin {
		//////////// variables ////////////
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function TiledSkin() {
        }
		
		//////////// Properties functions ////////////
		override public function get zIndex():Number{
			return tiledOwner.zIndex*10 + y;
		}
		
		public function get tiledOwner():ITiled{
			return _owner as ITiled;
		}
		
		override public function set owner(val:SkinnedObject){
			if(val is ITiled){
				if(_owner != val){
					if(_owner != null){
						_owner.removeEventListener(TilePositionnedObject.MOVED,moveHandler);
						_owner.removeEventListener(Character.DAMAGED,damagedHandler);
					}
					super.owner = val;
					if(_owner != null){
						repositioning();
						_owner.addEventListener(TilePositionnedObject.MOVED,moveHandler);
						_owner.addEventListener(Character.DAMAGED,damagedHandler);
					}
				}
			}
		}
		
		
		public function get tilesDisplay():TilesDisplay{
			return _bindedDisplay as TilesDisplay;
		}
		
		override public function set bindedDisplay(val:IDisplay){
			val = val as TilesDisplay;
			var old:TilesDisplay = tilesDisplay;
			super.bindedDisplay = val;
			if(!old && val){
				repositioning();
			}
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		
		override protected function displayedHandler(e:Event){
			super.displayedHandler(e);
			if(_bindedDisplay != null){
				repositioning();
			}
		}
		protected function repositioning(){
			if(tilesDisplay && displayed){
				var pos:Point = new Point(tiledOwner.posX,tiledOwner.posY);
				pos = tilesDisplay.tilePosToPixel(pos);
				if(x != pos.x){
					x = pos.x;
				}
				if(y != pos.y){
					y = pos.y;
					reorder();
				}
			}
		}
		
		protected function moveHandler(e:Event){
			repositioning();
		}
		
		protected function damagedHandler(e:EventFromSaved){
			if(tilesDisplay != null){
				tilesDisplay.addChild(new DamageLabel(x,x,parseInt(e.savedEvent.data.@damage.toString())*-1));
			}
		}
		
	}
}