package  {
	import flash.events.Event;
	import flash.geom.Point;
	import com.flashkev.utils.NumberUtil;
	
	
	[Event(name="moved", type="TilePositionnedObject")]
	public class TilePositionnedObject extends SkinnedObject implements ITiled{

		//////////// variables ////////////
		protected var _precisePos:PrecisePos = new PrecisePos();
		protected var _zIndex:int = 5;
		
		//////////// Static variables ////////////
		public static const MOVED:String = "moved";
		
		//////////// Constructor ////////////
		public function TilePositionnedObject(data:XML = null) {
			addSavedVar('tileId','@tile_id');
			_precisePos.addEventListener(Event.CHANGE,movedHandler);
			_precisePos.prcX = _precisePos.prcY = 0.5;
			
			this.addEventListener(SavedItem.UNLOADED,unloadedHandler);
			this.addEventListener(SkinnedObject.SKIN_CHANGE,skinChangeHandler)
		}
		
		//////////// Properties functions ////////////
		
		public function get zIndex():Number{
			return _zIndex;
		}
		public function set zIndex(val:Number){
			if(_zIndex != val){
				_zIndex = val;
			}
		}
		
		public function get tileId():int{
			return _precisePos.tileId;
		}
		public function set tileId(val:int):void{
			_precisePos.tileId = val;
		}
		
		public function get tile():Tile{
			return _precisePos.tile;
		}
		public function set tile(val:Tile):void{
			_precisePos.tile = val;
		}
		
		public function get posX():Number{
			return precisePos.posX;
		}
		
		public function get posY():Number{
			return precisePos.posY;
		}
		
		public function get posPt():Point{
			return precisePos.posPt;
		}
		
		override public function set skin(val:Skin){
			var old:Skin = super.skin;
			super.skin = val;
			if(!old && val){
				update();
			}
		}
		
		public function get precisePos():PrecisePos{
			return _precisePos;
		}
		
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		protected function moved(){
			dispatchEvent(new Event(MOVED));
		}
		protected function update(){
			if(tile && skin){
				this.displayed = true;
				moved();
			}else{
				this.displayed = false;
			}
		}
		
		//////////// Event Handlers functions ////////////
		protected function movedHandler(e:Event){
			update();
			dispatchEvent(new Event(MOVED));
		}
		protected function skinChangeHandler(e:Event){
			if(skin){
				var main:ClientMain = ClientMain.instance;
				skin.bindedDisplay = main.ingame.tilesDisplay;
			}
		}
		protected function unloadedHandler(e:Event){
			displayed = false;
		}
	
	}
}
