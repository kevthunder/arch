package  {
	import flash.events.EventDispatcher;
	import flash.events.Event;
	import flash.geom.Point;
	import xmlProps.*;
	
	[Event(name="updated", type="PathFinder")]
	[Event(name="destroyed", type="PathFinder")]
	public class PathFinder extends XmlEncodable{
		//////////// variables ////////////
		protected var _path:Path;
		protected var _lastTile:Tile;
		protected var _curTile:SavedItemRef = new SavedItemRef(0,'tile');
		protected var _nexFinder:PathFinder;
		protected var _entryPoint:Point;
		protected var _exitPoint:Point = new Point(0.5,0.5);
		protected var _prevExitPoint:Point;
		protected var _cachedIndicativeRemaining:Number;
		protected var _cachedLastPathLength:Number = 0;
		protected var _cachedLastFinder:PathFinder;
		protected var _dependencies:Array;
		
		
		//////////// Static variables ////////////
		public static const UPDATED:String = "updated";
		public static const DESTROYED:String = "destroyed";
		
		//////////// Constructor ////////////
        public function PathFinder(path:Path,curTile:Tile=null,lastTile:Tile=null,entryPoint:Point = null) {
			_path = path;
			_lastTile = lastTile;
			_curTile.item = curTile;
			if(entryPoint == null && curTile != null){
				_entryPoint = curTile.getJointPointTo(lastTile);
			}else{
				_entryPoint = entryPoint;
			}
        }
		
		//////////// Properties functions ////////////
		public function get path():Path{
			return _path;
		}
		public function get lastTile():Tile{
			return _lastTile;
		}
		public function get lastFinder():PathFinder{
			return _cachedLastFinder;
		}
		public function get curTile():Tile{
			return _curTile.item as Tile;
		}
		
		public function get entryPoint():Point{
			return _entryPoint.clone();
		}
		public function get exitPoint():Point{
			return _exitPoint.clone();
		}
		
		public function get nexFinder():PathFinder{
			return _nexFinder;
		}
		
		public function set nexFinder(val:PathFinder){
			if(_nexFinder != val){
				_nexFinder = val;
				var oldExit:Point = _exitPoint;
				if(_nexFinder == null){
					_exitPoint = new Point(0.5,0.5);
				}else{
					_nexFinder.setLastTile(curTile);
					if(_nexFinder.prevExitPoint != null){
						_exitPoint = _nexFinder.prevExitPoint;
					}
				}
				if(!oldExit.equals(_exitPoint)){
					updateCache();
				}
			}
		}
		
		public function get prevExitPoint():Point{
			return _prevExitPoint;
		}
		
		public function get segmentLength():Number{
			return _exitPoint.subtract(_entryPoint).length;
		}
		public function get lastPathLength():Number{
			return _cachedLastPathLength;
		}
		public function get pathLength():Number{
			return _cachedLastPathLength + segmentLength;
		}
		public function get indicativeRemaining():Number{
			if(!_cachedIndicativeRemaining){
				var begin:Point = new Point(curTile.posX,curTile.posY);
				var end:Point = new Point(path.to.posX,path.to.posY);
				var dist:Number = end.subtract(begin).length;
				_cachedIndicativeRemaining = dist + pathLength*0.7;
			}
			return _cachedIndicativeRemaining;
		}
		
		
		//////////// Static functions ////////////
		public function calculate(){
			var adjacentTiles:Array = [
				curTile.topTile,
				curTile.rightTile,
				curTile.bottomTile,
				curTile.leftTile
			];
			for(var i:int = 0; i<adjacentTiles.length; i++){
				if(adjacentTiles[i] && adjacentTiles[i] != lastTile && (adjacentTiles[i] as Tile).allowedPathing()){
					var pathFinder:PathFinder = new PathFinder(path,adjacentTiles[i],curTile);
					pathFinder.applyForBest();
				}
			}
		}
		
		//////////// Public functions ////////////
		
		public function getPointAt(at:Number):Point{
			var prc:Number = (at-lastPathLength)/segmentLength;
			if(prc >= 0 && prc <= 1){
				return Point.interpolate(exitPoint,entryPoint,prc);
			}else{
				ClientMain.instance.log("Out of bound");
			}
			return null;
		}
		
		public function pathLengthTo(exitPoint:Point){
			return _cachedLastPathLength + segmentLengthTo(exitPoint);
		}
		public function segmentLengthTo(exitPoint:Point){
			return exitPoint.subtract(_entryPoint).length;
		}
		public function destroy(){
			setLastCachedFinder(null);
			dispatchEvent(new Event(PathFinder.DESTROYED));
		}
		
		//////////// Internal functions ////////////
		
		internal function applyForBest(){
			updateCache();
			if(path.setBestFinder(this)){
			}
		}
		
		internal function setLastTile(val:Tile){
			if(_lastTile != val){
				_lastTile = val;
				updateCache();
			}
		}
		
		//////////// Private functions ////////////
		override protected function xmlDataDefinition(){
			super.xmlDataDefinition();
			addXmlDataDefinitions([
					new SimpleXmlProp('entry_point_x'),
					new SimpleXmlProp('entry_point_y'),
					new SimpleXmlProp('exit_point_x'),
					new SimpleXmlProp('exit_point_y'),
					new SimpleXmlProp('exit_point_y'),
					new SimpleXmlProp('tile_id'),
					new SimpleXmlProp('length')
				]);
		}
		
		override protected function getDataForXml():Object{
			var data:Object = super.getDataForXml();
			
			data['entry_point_x'] = entryPoint.x;
			data['entry_point_y'] = entryPoint.y;
			data['exit_point_x'] = exitPoint.x;
			data['exit_point_y'] = exitPoint.y;
			data['tile_id'] = _curTile.id;
			data['length'] = segmentLength;
			
			return data;
		}
		override protected function setDataFromXml(data:Object):Boolean{
			if(super.setDataFromXml(data)){
				_entryPoint = new Point();
				if(data.hasOwnProperty('entry_point_x')) _entryPoint.x = data['entry_point_x'];
				if(data.hasOwnProperty('entry_point_y')) _entryPoint.y = data['entry_point_y'];
				_exitPoint = new Point();
				if(data.hasOwnProperty('exit_point_x')) _exitPoint.x = data['exit_point_x'];
				if(data.hasOwnProperty('exit_point_y')) _exitPoint.y = data['exit_point_y'];
				if(data.hasOwnProperty('tile_id')) _curTile.id = data['tile_id'];
			
				return true;
			}
			return false;
		}
		
		
		
		protected function getLastFinder():PathFinder{
			if(lastTile){
				return path.getBestFinderAt(lastTile);
			}
			return null;
		}
		protected function updateCache(){
			var l:PathFinder = getLastFinder();
			if(l != null){
				setLastCachedFinder(l);
				var lastPathLength:Number = l.pathLengthTo(_prevExitPoint);
				if(_cachedLastPathLength != l.pathLength){
					_cachedLastPathLength = l.pathLength;
					dispatchEvent(new Event(PathFinder.UPDATED));
				}
			}
		}
		protected function setLastCachedFinder(pathFinder:PathFinder){
			if(_cachedLastFinder != pathFinder){
				if(_cachedLastFinder != null){
					_cachedLastFinder.removeEventListener(PathFinder.UPDATED,lastFinderUpdateHandler);
					_cachedLastFinder.removeEventListener(PathFinder.DESTROYED,lastFinderUpdateHandler);
				}
				_cachedLastFinder = pathFinder;
				if(_cachedLastFinder != null){
					_prevExitPoint = lastTile.getJointPointTo(curTile);
					_cachedLastFinder.addEventListener(PathFinder.UPDATED,lastFinderUpdateHandler);
					_cachedLastFinder.addEventListener(PathFinder.DESTROYED,lastFinderUpdateHandler);
				}
			}
		}
		
		//////////// Event Handlers functions ////////////
		protected function lastFinderUpdateHandler(e:Event){
			updateCache();
		}

	}
	
}
