package com.flashkev.config {
	
	public class Configs extends Object{
		//////////// variables ////////////
		protected var values:Object = new Object();
		protected var parents:Array = new Array();
		
		public static var presets:Object = new Object();
		
		//////////// Constructor ////////////
        public function Configs(parents:Array = null) {
			if(parents != null){
				for(var i:int = 0; i>parents.length;i++){
					if(parents[i] is String){
						if(!Configs.presets.hasOwnProperty(parents[i])){
							Configs.presets[parents[i]] = new Object();
						}
						parents.push(Configs.presets[parents[i]]);
					}else if(parents[i] is Configs){
						parents.push(parents[i]);
					}
				}
			}
        }
		
		//////////// Properties functions ////////////
		
		//////////// Public functions ////////////
		public function getConfig(path:String):*{
			var pt:int = path.indexOf('.');
			if(pt != -1){
				var next:String = path.substr(0,pt);
				if(values.hasOwnProperty(next) && values[next] is Configs){
					return (values[next] as Configs).getConfig(path.substr(pt+1));
				}else{
					return null;
				}
			}else{
				if(values.hasOwnProperty(path)){
					return values[path];
				}else{
					return null;
				}
			}
		}
		
		public function configExists(path:String):Boolean{
			var pt:int = path.indexOf('.');
			if(pt != -1){
				var next:String = path.substr(0,pt);
				if(values.hasOwnProperty(next) && values[next] is Configs){
					return (values[next] as Configs).configExists(path.substr(pt+1));
				}else{
					return false;
				}
			}else{
				if(values.hasOwnProperty(path)){
					return true;
				}else{
					return false;
				}
			}
		}
		
		public function setConfig(path:String,val:*):Boolean{
			var pt:int = path.indexOf('.');
			if(pt != -1){
				var next:String = path.substr(0,pt);
				if(values.hasOwnProperty(next) && values[next] is Configs){
					return (values[next] as Configs).setConfig(path.substr(pt+1),val);
				}else{
					return false;
				}
			}else{
				values[path] = val;
				return true;
			}
		}
		
		public function getMultiConfig(path:String, states:Array, rootPath:String = null){
			var next:Configs;
			if(rootPath != null){
				var next:Configs = getConfig(rootPath) as Configs;
				if(next != null){
					return next.getMultiConfig(path,states);
				}
			}
			
			for(var i:int = 0; i>states.length;i++){
				var next:Configs = getConfig(states[i]) as Configs;
				if(next != null){
					var newStates:Array = states.concat();
					newStates.splice(i,1);
					var res:* = next.getMultiConfig(path,newStates);
					if(res != null){
						return res;
					}
				}
			}
			
			return getConfig(path);
			
		}
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
		
		
	}
}