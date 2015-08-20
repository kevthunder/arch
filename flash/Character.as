package  {
	import flash.events.Event;
	import flash.geom.Point;
	import com.flashkev.utils.GeomUtils;
	import com.flashkev.utils.NumberUtil;
	
	
	[Event(name="damaged", type="Character")]
	public class Character extends TilePositionnedObject {

		//////////// variables ////////////
		protected var _name:String;
		protected var _curPath:SavedItemRef = new SavedItemRef(0,'path');
		public var _speed:int = 50;
		protected var _moveAbility:Move = new Move()
		protected var _abilities:Array = new Array();
		
		//////////// Static variables ////////////
		public static const DAMAGED:String = "damaged";
		public static const DIED:String = "died";
		
		//////////// Constructor ////////////
		public function Character(data:XML = null) {
			_abilities.push(_moveAbility);
			
			_model = 'character';
			addSavedVar('name','@name');
			addSavedVar('tileId','@tile_id');
			addSavedVar('_speed','@speed');
			
			addEventListener(SavedItem.LOADED,loadedHandler);
			
			importData(data);
		}
		
		//////////// Properties functions ////////////
		
		public function get name():String{
			return _name;
		}
		public function set name(val:String):void{
			_name = val;
		}
		
		public function get abilities():Array{
			return _abilities.concat();
		}
		
		public function get curPath():Path{
			return _curPath.item as Path
		}
		
		public function get speed():int{
			return _speed
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function select():ConnectionXMLRequest{
			var req:ConnectionXMLRequest = ClientMain.instance.connection.selecteCharacter(this.id);
			req.addEventListener(ConnectionRequest.SUCCESS, selectedHandler);
			return req;
		}
		public function getAbilities():ConnectionXMLRequest{
			var req:ConnectionXMLRequest = new ConnectionXMLRequest('skills','my_listing');
			ClientMain.instance.connection.addXmlRequest(req);
			return req;
		}
		
		public function walkPath(path:Path):Boolean{
			return _moveAbility.walkPath(path,this);
		}
		
		public function setPath(val:Path):Boolean{
			if(_curPath.item!=val){
				if(curPath!=null){
				
				}
				_curPath.item = val;
				skin.removeEventListener(Event.ENTER_FRAME,frameMovingHandler);
				if(curPath!=null){
					curPath.character = this;
					curPath.calculate();
					if(curPath.success){
						skin.addEventListener(Event.ENTER_FRAME,frameMovingHandler);
						//main.log(curPath.export().toXMLString());
						//var main:ClientMain = ClientMain.instance;
						//curPath.startTime = main.getTimer();
						//curPath.save();
					}else{
						return false;
					}
				}
				return true;
			}
			return false
		}
		
		//////////// Private functions ////////////
		protected function updateCurPathPos(){
			if(curPath != null /*&& curPath.loaded*/){
				/*if(val > curPath.length){
					val = curPath.length;
					removeEventListener(Event.ENTER_FRAME,frameMovingHandler);
				}*/
				
				//_curPathPos = val;
				//_precisePos.setTo(curPath.getPrecisePosition(_curPathPos));
				_precisePos.setTo(curPath.getcurrentPos());
			}
		}
		
		//////////// Event Handlers functions ////////////
		protected function loadedHandler(e:Event){
			if(!skin && _skinDefinition.id == 0){
				var main:ClientMain = ClientMain.instance;
				skin = new Perso1Skin();
				skin.bindedDisplay = main.ingame.tilesDisplay;
			}
		}
		
		protected function frameMovingHandler(e:Event){
			updateCurPathPos();
		}
		
		protected function selectedHandler(e:Event){
			ClientMain.instance.selectedCharacter = this;
		}
	
	}
}
