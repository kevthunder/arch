package {
	import flash.display.MovieClip;
	import flash.display.DisplayObject;
	import flash.events.Event;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import com.flashkev.viewer.Viewer;
	import com.flashkev.docroot.KRoot;
	import flash.geom.Matrix;
	import com.flashkev.utils.NumberUtil;
	
	public class TilesDisplay extends MovieClip implements IDisplay{
		//////////// variables ////////////
		public static var chunkLen:int = 5;
		public static var chunkBuffer:int = 1;
		
		protected var _displayedTiles:TileCollection;
		//protected var _displayedLink:CollectionLink;
		protected var _background:Viewer;
		public var _layer:MovieClip;
		protected var _width:Number;
		protected var _height:Number;
		protected var _camera:Camera;
		protected var _active:Boolean;
		protected var _updatedRect:Rectangle;
		protected var _updateRequest:KeepTilesUpdated;
		
		//////////// Constructor ////////////
		public function TilesDisplay(){
			_updateRequest = new KeepTilesUpdated();
			_updateRequest.reloadable = true;
			
			camera = new Camera();
			_background = new Viewer();
			addChild(_background);
			_layer = new MovieClip();
			addChild(_layer);
			
			var w:Number = super.width;
			var h:Number = super.height;
			scaleX = 1;
			scaleY = 1;
			width = w;
			height = h;
			
			addEventListener(Event.ADDED_TO_STAGE,addedToStageHandler);
			
			displayedTiles = new TileCollection(true);
		}
		
		
		//////////// Properties functions ////////////
		public function get active():Boolean{
			return _active;
		}
		public function set active(val:Boolean){
			if(_active != val){
				_active = val;
				if(_active){
					ClientMain.instance.connection.addStaticXmlRequest(_updateRequest);
				}else{
					ClientMain.instance.connection.removeXmlRequest(_updateRequest);
				}
			}
		}
		public function get camera():Camera{
			return _camera;
		}
		public function set camera(val:Camera):void{
			if(_camera != val){
				if(_camera != null){
					_camera.removeEventListener(Event.CHANGE,camChangeHandler);
				}
				_camera = val;
				if(_camera != null){
					_camera.addEventListener(Event.CHANGE,camChangeHandler);
					camChangeHandler();
				}
			}
		}
		
		public function get displayedTiles():TileCollection{
			return _displayedTiles;
		}
		public function set displayedTiles(val:TileCollection):void{
			_displayedTiles = val;
			//_displayedTiles.addEventListener(CollectionEvent.ADDED,addedTileHandler);
			//_displayedTiles.addEventListener(CollectionEvent.REMOVED,removedTileHandler);
			//_displayedLink = new CollectionLink(ClientMain.instance.savedItems, _displayedTiles, itemFilter);
		}
		
		override public function get width():Number{
			return _width;
		}
		override public function set width(val:Number):void{
			_width = val;
			_background.width = _width;
			_background.x = _width/-2;
		}
		
		override public function get height():Number{
			return _height;
		}
		override public function set height(val:Number):void{
			_height = val;
			_background.height = _height;
			_background.y = _height/-2;
		}
		public function get tileRotation():Number{
			return Math.PI/4;
		}
		public function get tileRatio():Number{
			return 2;
		}
		//////////// Public functions ////////////
		/*public function loadXml(xml:XML){
			if(xml.hasOwnProperty("tile")){
				if(displayedTiles == null){
					displayedTiles = new TileCollection();
				}
				for(var i = 0; i<xml.tile.length();i++){
					var tile:Tile = new Tile(xml.tile[i]);
					displayedTiles.addTile(tile);
				}
			}
		}*/
		public function globalPixelToTilePos(pos:Point):Point{
			return pixelToTilePos(globalPixelToPixel(pos));
		}
		
		public function globalPixelToPixel(pos:Point):Point{
			return pos.add(new Point(-_layer.x,-_layer.y));
		}
		
		public function precisePosToPixel(pos:PrecisePos){
			return tilePosToPixel(new Point(pos.posX,pos.posY));
		}
		public function tilePosToPixel(pos:Point){
			var pt:Point = new Point(NumberUtil.roundPrecision((pos.x) * TiledObject.baseWidth,0.05),NumberUtil.roundPrecision((pos.y) * TiledObject.baseHeight,0.05));
			var trans:Matrix = new Matrix();
			trans.rotate(tileRotation);
			pt = trans.transformPoint(pt);
			pt.x = pt.x*tileRatio;
			return pt;
		}
		public function pixelToTilePos(pos:Point){
			var pt:Point = pos.clone();
			pt.x = pt.x/tileRatio;
			var trans:Matrix = new Matrix();
			trans.rotate(-tileRotation);
			pt = trans.transformPoint(pt);
			return new Point(pt.x / TiledObject.baseWidth , pt.y / TiledObject.baseHeight);
		}
		
		public function itemFilter(item:SavedItem){
			return item is SkinnedObject;
		}
		
		public function addSkin(skin:Skin):void{
			var pos:int =0;
			_layer.addChildAt(skin,pos);
			if(skin.owner is Tile){
				_displayedTiles.addTile(skin.owner as Tile);
			}
		}
		public function removeSkin(skin:Skin):void{
			if(_layer.contains(skin)){
				_layer.removeChild(skin);
			}
			if(skin.owner is Tile){
				_displayedTiles.removeTile(skin.owner as Tile);
			}
		}
		
		//////////// Private functions ////////////
		public function calculUpdatedRect(){
			if(active){
				var w = (Math.ceil(width/TiledObject.baseWidth/chunkLen)+chunkBuffer)*chunkLen;
				var h = (Math.ceil(height/TiledObject.baseHeight/chunkLen)+chunkBuffer)*chunkLen;
				var newRect:Rectangle = new Rectangle(
						Math.floor(camera.posX - w/2),
						Math.floor(camera.posY - h/2),
						w,
						h
					);
				if(_updatedRect == null || !newRect.equals(_updatedRect)){
					//var oldRect = _updatedRect;
					_updatedRect = newRect;
					_updateRequest.rect = newRect;
					//ClientMain.instance.connection.getTiles(newRect,oldRect);
				}
			}
		}
		
		
		//////////// Event Handlers functions ////////////
		protected function addedToStageHandler(e:Event){
			_background.display(KRoot.realPath('img/stars.jpg'));
		}
		
		/*protected function addedTileHandler(e:CollectionEvent){
			ClientMain.instance.log("newItem");
			var pos:int =0;
			_layer.addChildAt((e.item as SkinnedObject).skin,pos);
		}
		
		protected function removedTileHandler(e:CollectionEvent){
			_layer.removeChild((e.item as SkinnedObject).skin);
		}*/
		protected function camChangeHandler(e:Event = null){
			active = (_camera.tile != null);
			if(active){
				var pos:Point = precisePosToPixel(_camera);
				_layer.x = -pos.x;
				_layer.y = -pos.y;
			}
			calculUpdatedRect();
		}
	}
	
	
}