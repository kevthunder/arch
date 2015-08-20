package {
	import flash.display.MovieClip;
	import flash.geom.Point;
	import flash.events.Event;
	import xmlProps.SimpleXmlProp;
	import xmlProps.ObjXmlProp;
	import com.flashkev.utils.StringUtil;
	import com.flashkev.utils.ObjUtil;
	import flash.utils.getQualifiedClassName;
	
	
	public class Tile extends TiledObject {
		//////////// variables ////////////
		protected var _fertility:int;
		protected var _pathing:Object;
		protected var _tileTypeId:int;
		
		//////////// Static variables ////////////
		public static const MODEL:String = 'tile';
		
		//////////// Constructor ////////////
		public function Tile(data:XML = null){
			_model = MODEL;
			super(data);
			this.addEventListener(SavedItem.LOADED,loadedHandler);
			this.addEventListener(SavedItem.UPDATED,updatedHandler);
			this.addEventListener(SavedItem.UNLOADED,unloadedHandler);
		}
		
		//////////// Properties functions ////////////
		public function get map():TileCollection{
			return ClientMain.instance.ingame.tilesDisplay.displayedTiles;
		}
		public function get topTile():Tile{
			return map.getTilesAt(posX,posY-1).filter(filterTile).shift() as Tile;
		}
		public function get rightTile():Tile{
			return map.getTilesAt(posX+1,posY).filter(filterTile).shift() as Tile;
		}
		public function get bottomTile():Tile{
			return map.getTilesAt(posX,posY+1).filter(filterTile).shift() as Tile;
		}
		public function get leftTile():Tile{
			return map.getTilesAt(posX-1,posY).filter(filterTile).shift() as Tile;
		}
		
		public function get fertility():int{
			return _fertility;
		}
		
		override public function set skin(val:Skin){
			super.skin = val;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function allowedPathing(type:String = "walk"):Boolean{
			if(_pathing.hasOwnProperty(type)){
				return _pathing[type];
			}
			return false;
		}
		public function filterTile(obj:Object, index:int, array:Array){
			return (obj is Tile);
		}
		public function getJointPointTo(target:Tile){
			if(target == topTile){
				return new Point(0.5,0);
			}else if(target == rightTile){
				return new Point(1,0.5);
			}else if(target == bottomTile){
				return new Point(0.5,1);
			}else if(target == leftTile){
				return new Point(0,0.5);
			}
		}
		
		//////////// Private functions ////////////
		override protected function xmlDataDefinition(){
			super.xmlDataDefinition();
			addXmlDataDefinitions([
					new SimpleXmlProp('fertility'),
					new ObjXmlProp('pathing'),
					new SimpleXmlProp('tile_type_id')
				]);
		}
		override protected function getDataForXml():Object{
			var data:Object = super.getDataForXml();
			
			data['fertility'] = fertility;
			data['tile_type_id'] = _tileTypeId;
			
			return data;
		}
		override protected function setDataFromXml(data:Object):Boolean{
			if(super.setDataFromXml(data)){
			
				if(data.hasOwnProperty('fertility')) _fertility = data['fertility'];
				if(data.hasOwnProperty('pathing')){ 
					var pathing:Object = data['pathing'];
					for(var key in pathing){
						pathing[key] = StringUtil.toBool(pathing[key]);
					}
					_pathing = pathing;
				}
				if(data.hasOwnProperty('tile_type_id')) _tileTypeId = data['tile_type_id'];
			
				return true;
			}
			return false;
		}
		
		
		//////////// Event Handlers functions ////////////
		protected function updatedHandler(e:Event){
			var main:ClientMain = ClientMain.instance;
			var newSkin:Skin; 
			if(_tileTypeId != 2){
				_zIndex = 0;
				newSkin = new TileSkin();
			}else{
				_zIndex = -10;
				newSkin = new TileVoidSkin();
			}
			if(!skin || getQualifiedClassName(skin) != getQualifiedClassName(newSkin)){
				skin = newSkin;
				skin.bindedDisplay = main.ingame.tilesDisplay;
			}
		}
		
		protected function loadedHandler(e:Event){
			updatedHandler(e);
			
			displayed = true;
		}
		protected function unloadedHandler(e:Event){
			displayed = false;
		}
	}
	
	
}