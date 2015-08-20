package com.flashkev.docroot {
    import flash.display.MovieClip;
    import flash.external.ExternalInterface;
    import flash.system.Capabilities;
	
	public class KRoot extends MovieClip{
		//////////// variables ////////////
		public static var rootPath:String;
		public static var defaultRootPath:String = '../';
		public static var realRoot:KRoot;
		
		//////////// Constructor ////////////
		public function KRoot(){
			if(!KRoot.realRoot){
				KRoot.realRoot = this;
			}
			if(stage){
				if(stage.loaderInfo.parameters.hasOwnProperty("rootpath")){
					rootPath = stage.loaderInfo.parameters["rootpath"];
				}
			}else if(rootPath===null){
				try{
					trace('parentDomain',this.loaderInfo.loader);
					trace('parentDomain',this.loaderInfo.loader.stage);
					if(this.loaderInfo.loader.stage.loaderInfo.parameters.hasOwnProperty("rootpath")){
						rootPath = this.loaderInfo.loader.stage.loaderInfo.parameters["rootpath"];
					}
				}catch(e){
					
				}
			}
		}
      
		//////////// Properties functions ////////////
		
		
		//////////// Public functions ////////////
		public static function getRootPath():String{
			if(rootPath === null){
				return defaultRootPath;
			}
			return rootPath;
		}
		public static function log(msg){
			if(typeof msg == 'xml'){
				msg = msg.toString();
			}
			//trace('log');
			if (ExternalInterface.available && (Capabilities.playerType == "ActiveX" || Capabilities.playerType == "PlugIn")) {
				try {
					ExternalInterface.call("window.console.log",msg);
				} catch (error:SecurityError) {
					trace("A SecurityError occurred: " + error.message + "\n");
				} catch (error:Error) {
					trace("An Error occurred: " + error.message + "\n");
				}
			} else {
				trace(msg);
			}
		}
		public function log(msg){
			KRoot.log(msg);
		}
		
		public static function realPath(path:String){
			var pattern:RegExp = /^([a-zA-Z]+:\/\/|\/)/;
			if(!pattern.test(path)){
				path = getRootPath()+path;
			}
			return path;
		}
		public function realPath(path:String){
			return KRoot.realPath(path);
		}
		
		//////////// Private functions ////////////
		
		
		
	}
}