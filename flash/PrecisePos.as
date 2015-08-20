package  {
	import flash.geom.Point;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	[Event(name="change", type="Event")]
	public class PrecisePos extends EventDispatcher{

		//////////// variables ////////////
		protected var _tileId:int = 0;
		protected var _x:Number;
		protected var _y:Number;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
		
		public function PrecisePos(tileId:int = 0,prc:Point = null) {
			if(prc == null){
				prc = new Point(0.5,0.5);
			}
			this.tileId = tileId;
			this.prc = prc;
		}
		
		//////////// Properties functions ////////////
		public function get tileId():int{
			return _tileId;
		}
		public function set tileId(val:int):void{
			if(_tileId != val){
				var oldTile:Tile = tile;
				_tileId = val;
				if(tile == null){
					ClientMain.instance.savedItems.addEventListener(CollectionEvent.ADDED,savedItemAddedHandler);
				}
				if(tile != oldTile){
					updated();
				}
			}
		}
		
		public function get tile():Tile{
			if(tileId == 0){
				return null;
			}
			return ClientMain.instance.savedItems.getItem(Tile.MODEL,tileId) as Tile;
			return null;
		}
		public function set tile(val:Tile):void{
			if(tileId != val.id){
				tileId = val.id;
			}
		}
		public function get prcX():Number{
			return _x;
		}
		public function set prcX(val:Number){
			val = Math.min(1,Math.max(0,val));
			if(_x != val){
				_x = val;
				updated();
			}
		}
		public function get prcY():Number{
			return _y;
		}
		public function set prcY(val:Number){
			val = Math.min(1,Math.max(0,val));
			if(_y != val){
				_y = val;
				updated();
			}
		}
		
		public function get prc():Point{
			return new Point(_x,_y);
		}
		
		public function set prc(val:Point){
			var valX:Number = Math.min(1,Math.max(0,val.x));
			var valY:Number = Math.min(1,Math.max(0,val.y));
			if(_x != valX || _y != valY){
				_x = valX;
				_y = valY;
				updated();
			}
		}
		
		
		public function get tileX():int{
			var t:Tile = tile;
			if(t){
				return t.posX;
			}
			return NaN;
		}
		public function get tileY():int{
			var t:Tile = tile;
			if(t){
				return t.posY;
			}
			return NaN;
		}
		
		
		public function get posX():Number{
			var t:Tile = tile;
			if(t){
				return t.posX + prcX;
			}
			return NaN;
		}
		public function get posY():Number{
			var t:Tile = tile;
			if(t){
				return t.posY + prcY;
			}
			return NaN;
		}
		
		public function get posPt():Point{
			if(tile){
				return new Point(posX,posY);
			}
			return null;
		}
		
		//////////// Static functions ////////////
		
		
		//////////// Public functions ////////////
		public function clone():PrecisePos{
			return new PrecisePos(tileId,prc);
		}
		
		public function setTo(target:PrecisePos){
			if(target != null && (tileId != target.tileId || prcX != target.prcX || prcY != target.prcY)){
				_tileId = target.tileId;
				_x = target.prcX;
				_y = target.prcY;
				updated();
			}
		}
		
		override public function toString():String{
			return '['+tile.toString() + ' +('+_x+','+_y+')]';
		}
		
		//////////// Private functions ////////////
		protected function updated(){
			dispatchEvent(new Event(Event.CHANGE));
		}
		
		//////////// Event Handlers functions ////////////
		protected function savedItemAddedHandler(e:CollectionEvent){
			var t:Tile = e.item as Tile;
			if(t != null && t.id == tileId){
				ClientMain.instance.savedItems.removeEventListener(CollectionEvent.ADDED,savedItemAddedHandler);
				updated();
			}
		}

	}
	
}
