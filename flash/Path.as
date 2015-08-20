package {
	import flash.events.Event;
	import flash.geom.Point;
	import xmlProps.*;

	public class Path extends SavedItem {
		//////////// variables ////////////
		protected var _from:SavedItemRef = new SavedItemRef(0,'tile');
		protected var _to:SavedItemRef = new SavedItemRef(0,'tile');
		protected var _range:Number = 0;
		protected var _character:SavedItemRef = new SavedItemRef(0,'character');
		protected var _buffer:Array = new Array();
		protected var _finalFinder:PathFinder;
		protected var _bestFinders:Object = new Object();
		protected var _maxIteration:int = 2000;
		protected var _steps:Array = new Array();
		protected var _calculing:Boolean = false;
		protected var _calculed:Boolean = false;
		protected var _startTime:int = 0;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function Path() {
			var main:ClientMain = ClientMain.instance;
			main.addEventListener(Event.ENTER_FRAME,enterFrameHandler);
			addEventListener(SavedItem.LOADED,loadHandler);
			addEventListener(SavedItem.UNLOADED,unloadHandler);
			_model = 'path';
        }
		
		//////////// Properties functions ////////////
		public function get fromTileId():int{
			return _from.id;
		}
		public function set fromTileId(val:int){
			_from.id = val;
		}
		
		public function get toTileId():int{
			return _to.id;
		}
		public function set toTileId(val:int){
			_to.id = val;
		}
		
		public function get from():Tile{
			return _from.item as Tile;
		}
		public function set from(val:Tile){
			_from.item = val;
		}
		
		public function get to():Tile{
			return _to.item as Tile;
		}
		public function set to(val:Tile){
			_to.item = val;
		}
		
		public function get range():Number{
			return _range;
		}
		public function set range(val:Number){
			_range = val;
		}
		
		public function get character():Character{
			return _character.item as Character;
		}
		public function set character(val:Character){
			_character.item = val;
		}
		
		public function get startTime():int{
			return _startTime;
		}
		public function set startTime(val:int){
			_startTime = val;
		}
		
		public function get endTime():int{
			var total = length *100 *1000 / character.speed;
			return _startTime + total;
		}
		
		public function get calculed():Boolean{
			return _calculed;
		}
		
		public function get success():Boolean{
			return _steps.length > 0;
		}
		
		public function get length():Number{
			if(_steps.length){
				return (_steps[_steps.length-1] as PathFinder).pathLength;
			}
			return NaN;
		}
		
		
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		public function getcurrentPos():PrecisePos{
			var prc:Number;
			if(_startTime != 0){
				var enapsed:int = ClientMain.instance.getTimer() - _startTime;
				var total = length *100 *1000 / character.speed;
				prc = enapsed / total;
				prc = Math.min(1,prc);
			}else{
				prc = 0;
			}
			return getPrecisePosition(prc* length);
		}
		public function getPrecisePosition(at:Number):PrecisePos{
			var pos:Object = new Object;
			var finder:PathFinder = getPosition(at);
			var pt:Point = finder.getPointAt(at);
			if(pt != null){
				return new PrecisePos(finder.curTile.id,pt);
			}
			return null;
		}
		public function getPosition(at:Number):PathFinder{
			var pos:int = 0;
			while(_steps[pos].pathLength < at){
				pos++;
			}
			return _steps[pos] as PathFinder;
		}
		public function calculate(){
			if(_calculed == false){
				_calculing = true;
				var startFinder:PathFinder = new PathFinder(this,from,null,character.precisePos.prc);
				startFinder.applyForBest();
				var i:int=0;
				while(_buffer.length && i < _maxIteration){
					var pathFinder:PathFinder = removeFromBufferAt(0);
					//trace('i\'m testing in',pathFinder.curTile.posX,pathFinder.curTile.posY);
					pathFinder.calculate();
					i++;
				}
				//trace('iterations :',i);
				_calculed = true;
			}
		}
		
		//////////// internal functions ////////////
		internal function setBestFinder(pathFinder:PathFinder):Boolean{
			var tile:Tile = pathFinder.curTile;
			if(_calculing && pathFinder && (!_bestFinders.hasOwnProperty(tile.id) || _bestFinders[tile.id].pathLength > pathFinder.pathLength)){
				var old:PathFinder = _bestFinders[tile.id];
				_bestFinders[tile.id] = pathFinder;
				if(old){
				   old.destroy();
				}
				if(isDestReached(pathFinder)){
					setFinalFinder(pathFinder);
				}else{
					addToBuffer(pathFinder);
				}
				return true;
			}
			return false;
		}
		internal function getBestFinderAt(tile:Tile):PathFinder{
			if(calculed){
				for(var i:int; i < _steps.length; i++){
					if((_steps[i] as PathFinder).curTile == tile){
						return (_steps[i] as PathFinder);
					}
				}
			}
			return _bestFinders[tile.id];
		}
		
		//////////// Private functions ////////////
		
		override protected function xmlDataDefinition(){
			super.xmlDataDefinition();
			addXmlDataDefinitions([
					new SimpleXmlProp('start_tile_id'),
					new SimpleXmlProp('end_tile_id'),
					new SimpleXmlProp('character_id'),
					new TimeXmlProp('start_time'),
					new EncodableCollectionXmlProp('steps','step',PathFinder,[this])
				]);
		}
		
			
		override protected function getDataForXml():Object{
			var data:Object = super.getDataForXml();
			
			data['start_tile_id'] = fromTileId;
			data['end_tile_id'] = toTileId;
			data['character_id'] = _character.id;
			if(_startTime != 0){
				data['start_time'] = _startTime;
			}
			data['steps'] = _steps;
			
			return data;
		}
		override protected function setDataFromXml(data:Object):Boolean{
			if(super.setDataFromXml(data)){
			
				if(data.hasOwnProperty('start_tile_id')) fromTileId = data['start_tile_id'];
				if(data.hasOwnProperty('end_tile_id')) toTileId = data['end_tile_id'];
				if(data.hasOwnProperty('character_id')) _character.id = data['character_id'];
				if(data.hasOwnProperty('start_time')) _startTime = data['start_time'];
				if(data.hasOwnProperty('steps')){ 
					_steps = data['steps'];
					_calculed = true;
					for(var i:int; i+1<_steps.length; i++){
						(_steps[i] as PathFinder).nexFinder = (_steps[i+1] as PathFinder);
					}
				};
				
			
				return true;
			}
			return false;
		}
		
		protected function addToBuffer(pathFinder:PathFinder){
			var pos = 0;
			while(pos<_buffer.length && _buffer[pos].indicativeRemaining < pathFinder.indicativeRemaining){
				pos++
			}
			_buffer.splice(pos,0,pathFinder);
			pathFinder.addEventListener(PathFinder.DESTROYED,bufferedDestroyedHandler)
		}
		protected function removeFromBuffer(pathFinder:PathFinder){
			removeFromBufferAt(_buffer.indexOf(pathFinder));
		}
		protected function removeFromBufferAt(pos:int):PathFinder{
			if(pos != -1){
				var pathFinder:PathFinder = _buffer[pos];
				_buffer.splice(pos,1);
				pathFinder.removeEventListener(PathFinder.DESTROYED,bufferedDestroyedHandler);
				return pathFinder;
			}
			return null
		}
		protected function ClearBuffer(){
			_calculing = false;
			_buffer = new Array();
		}
		
		protected function isDestReached(pathFinder:PathFinder):Boolean{
			var tile:Tile = pathFinder.curTile;
			if(range == 0){
				return (tile == to);
			}else{
				return Point.distance(tile.posPt,to.posPt) <= range;
			}
			return false;
		}
		
		protected function setFinalFinder(pathFinder:PathFinder){
			ClearBuffer();
			_finalFinder = pathFinder;
			var backFinder:PathFinder = pathFinder;
			//trace('================');
			while(backFinder!=null){
				_steps.unshift(backFinder);
				//trace('i\'m passing in',backFinder.curTile.posX,backFinder.curTile.posY);
				backFinder.curTile.skin.alpha = 0.5;
				
				if(backFinder.lastFinder != null){
					backFinder.lastFinder.nexFinder = backFinder;
				}
				backFinder = backFinder.lastFinder;
			}
			ClientMain.instance.log("Path calculated");
		}
		
		//////////// Event Handlers functions ////////////
		protected function bufferedDestroyedHandler(e:Event){
			removeFromBuffer(e.target as PathFinder);
		}
		protected function enterFrameHandler(e:Event){
			if(startTime != 0){
				var main:ClientMain = ClientMain.instance;
				var time:int = main.getTimer();
				//trace('Began since :',time-startTime);
				//trace('end in :',endTime-time);
				if(time>=startTime && time<=endTime){
					if(character.curPath != this){
						character.setPath(this);
					}
				}else if(time>endTime){
					if(character.curPath == this){
						character.setPath(null);
					}
					if(time>endTime+main.connection.buffer){
						this.unload();
					}
				}
			}
		}
		protected function loadHandler(e:Event){
		}
		
		protected function unloadHandler(e:Event){
			var main:ClientMain = ClientMain.instance;
			main.removeEventListener(Event.ENTER_FRAME,enterFrameHandler);
		}
	}
}