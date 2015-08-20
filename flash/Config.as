package  {
	import flash.events.EventDispatcher;
	
	[Event(name="change", type="ConfigEvent")]
	[Event(name="changed", type="ConfigEvent")]
	public class Config extends EventDispatcher{
		//////////// variables ////////////
		protected var _configs:Object = new Object();
		protected var _binds:Object = new Object();
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function Config(init:Object = null) {
			if(init != null ){
				for(var prop:String in init){
					_configs[prop] = init[prop];
				}
			}
        }
		
		//////////// Properties functions ////////////
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function set(name:String,val:Object){
			if(!_configs.hasOwnProperty(name) || _configs[name] != val){
				var old:Object = get(name);
				dispatchEvent(new ConfigEvent(ConfigEvent.CHANGE,name,old,val));
				_configs[name] = val;
				if(!_binds.hasOwnProperty(name)){
					for(var i:int=0;i<_binds.length;i++){
						_binds[i].obj[_binds[i].propName] = val;
					}
				}
				dispatchEvent(new ConfigEvent(ConfigEvent.CHANGED,name,old,val));
			}
		}
		
		public function get(name:String){
			if(_configs.hasOwnProperty(name)){
				return _configs[name];
			}
			return null;
		}
		
		public function bindProp(obj:Object,propName:String,configName:String = null){
			if(configName == null){
				configName = propName;
			}
			if(!_binds.hasOwnProperty(configName)){
				_binds[configName] = new Array();
			}
			_binds[configName].push({obj:obj,propName:propName});
			if(obj[propName] != get(configName)){
				obj[propName] = get(configName);
			}
		}
		public function removeBindsTo(obj:Object){
			for(var configName:String in _binds){
				var binds:Array = _binds[configName];
				var pos:int = 0;
				while( (pos = binds.indexOf(obj)) != -1){
					binds.splice(pos,1);
				}
			}
		}
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		
	}
}