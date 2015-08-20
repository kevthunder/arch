package  {
	import flash.events.EventDispatcher;
	import flash.events.Event;
	
	public class TileCollection extends CollectionBase{
		//////////// variables ////////////
		protected var _tiles:Array = new Array();
		protected var _multiple:Boolean = false
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
		public function TileCollection(multiple = false) {
			_multiple = multiple;
			// constructor code
		}
		
		//////////// Properties functions ////////////
		public function get multiple():Boolean{
			return _multiple;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		override public function addItem(item:Object):Boolean{
			return addTile(item as ITiled);
		}
		override public function removeItem(item:Object):Boolean{
			return removeTile(item as ITiled);
		}
		override public function hasItem(item:Object):Boolean{
			return hasTile(item as ITiled);
		}
		override public function getItems():Array{
			return null;
		}
		
		public function addTile(tile:ITiled):Boolean{
			if(getTileIndex(tile) == -1){
				if(_multiple || getTileIndexAt(tile.posX,tile.posY) == -1){
					_tiles.push(tile);
					dispatchEvent(new CollectionEvent(CollectionEvent.ADDED,tile));
					return true;
				}
			}
			return false;
		}
		public function removeTile(tile:ITiled):Boolean{
			var index:int = getTileIndex(tile);
			return removeTileAtIndex(index);
		}
		
		public function hasTile(tile:ITiled):Boolean{
			return getTileIndex(tile) != -1;
		}
		
		public function getTilesAt(x:int,y:int):Array{
			var res:Array = new Array();
			var indexes = getTileIndexesAt(x,y);
			for(var i:int = 0; i<indexes.length; i++){
				res.push(_tiles[indexes[i]]);
			}
			return res;
		}
		
		public function getTileAt(x:int,y:int,num:int=0):ITiled{
			return _tiles[getTileIndexAt(x,y,0,num)];
		}
		
		//////////// Private functions ////////////
		protected function removeTileAtIndex(index:int):Boolean{
			if(index == -1){
				var tile = _tiles[index];
				_tiles.splice(index,1);
				dispatchEvent(new CollectionEvent(CollectionEvent.REMOVED,tile));
				return true;
			}
			return false;
		}
		protected function getTileIndexesAt(x:int,y:int):Array{
			var res:Array = new Array();
			var index = -1;
			while((index = getTileIndexAt(x,y,index+1)) != -1){
				res.push(index);
			}
			return res;
		}
		protected function getTileIndexAt(x:int,y:int,start:int=0,num:int=0):int{
			var n:int = -1;
			for(var i:int = start; i<_tiles.length; i++){
				if(_tiles[i].posX == x && _tiles[i].posY == y){
					n++
					if(n == num){
						return i;
					}
				}
			}
			return -1;
		}
		protected function getTileIndex(tile:ITiled,start:int=0):int{
			for(var i:int = start; i<_tiles.length; i++){
				if(_tiles[i] == tile){
					return i;
				}
			}
			return -1;
		}
		
		//////////// Event Handlers functions ////////////
		
		
		
		
		

	}
	
}
