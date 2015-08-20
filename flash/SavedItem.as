package {
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.utils.getDefinitionByName;
	import flash.utils.getQualifiedClassName;
	
	
	import br.com.stimuli.loading.BulkLoader;
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import com.flashkev.docroot.KRoot;
	
	[Event(name="loaded", type="SavedItem")]
	[Event(name="unloaded", type="SavedItem")]
	public class SavedItem extends XmlEncodable{
		//////////// variables ////////////
		protected var _id:int = 0;
		protected var _model:String;
		protected var _loaded:Boolean;
		
		//////////// Static variables ////////////
		public static const UPDATED:String = 'item updated';
		public static const LOADED:String = 'item loaded';
		public static const UNLOADED:String = 'item unloaded';
		protected static var nextId:int = -1;
		
		//////////// Constructor ////////////
		public function SavedItem(data:XML = null){
			importData(data);
		}
		
		//////////// Properties functions ////////////
		public function get id():int{
			if(_id == 0){
				_id = nextId;
				nextId--;
			}
			return _id;
		}
		public function get model():String{
			return _model;
		}
		public function get loaded():Boolean{
			return _loaded;
		}
		
		override public function get tagName():String{
			if(_tagName == null){
				return model;
			}else{
				return _tagName;
			}
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function getSeed():uint{
			var augment:Array = [
				  17970437, 1160832319, 1014932287,  390811790,
				1361743790,    6569190,  235234093, 1150831128, 
				1652883180,  421802530, 2121049609, 2056934745, 
				1035143344, 1852795506,  542041355,  820882058, 
				1477775363, 2055115468, 1423667926, 1348336819, 
				  37734051,  784147957, 1918334906, 1843518252, 
				 115374720,   73867914,  839045179, 1770256422,
				1644494488, 1163259204, 1898387251,  529720709
			];
			return id + augment[id%augment.length];
		}
		public function save():ConnectionXMLRequest{
			var newXml:XML = export();
			if(newXml != null){
				var req:ConnectionXMLRequest = ClientMain.instance.connection.saveXml(newXml);
				req.addEventListener(ConnectionRequest.SUCCESS, savedHandler);
				return req;
			}
			return null;
		}
		override public function importData(data:XML):Boolean{
			if(_data != data && data != null){
				if(data.hasOwnProperty("@id")){
					if(loaded){
						if(id == data.@id) {
							if(super.importData(data)){
								dispatchEvent(new Event(UPDATED));
								return true;
							}else{
								return false;
							}
						}
					}else{
						var oldId:int = _id;
						_id = data.@id;
						if(super.importData(data)){
							load();
							dispatchEvent(new Event(LOADED));
							return true;
						}else{
							_id = oldId;
						}
					}
				}else{
					ClientMain.instance.log("Cant load data : no id.");
				}
			}
			return false;
		}
		
		override public function export(tagName:String = null):XML{
			var newXml:XML = super.export();
			if(newXml != null){
				newXml.@id = id;
			}
			return newXml;
		}
		
		public function unload(){
			if(_loaded == true){
				_loaded = false;
				dispatchEvent(new Event(UNLOADED));
			}
		}
		
		
		//////////// Private functions ////////////
		protected function load(){
			_loaded = true;
			if(ClientMain.instance.savedItems.addSavedItem(this)){
				dispatchEvent(new Event(LOADED));
			}else{
				_loaded = false;
			}
		}
		protected function getClass():Class {
			return Class(getDefinitionByName(getQualifiedClassName(this)));
		}
		
		//////////// Event Handlers functions ////////////
		protected function savedHandler(e:Event){
			var req:ConnectionXMLRequest = e.target as ConnectionXMLRequest;
			if(!loaded && req.xmlResponse.@item_id.toString()){
				//ClientMain.instance.savedItems.removeSavedItem(this);
				_id = parseInt(req.xmlResponse.@item_id.toString());
				load();
			}
			//KRoot.log("result:"+req.xmlResponse.toXMLString());
		}
	}
	
	
}