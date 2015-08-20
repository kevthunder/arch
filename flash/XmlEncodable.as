package  {
	import flash.events.EventDispatcher;
	import xmlProps.*;
	import flash.utils.getDefinitionByName;
	import flash.utils.getQualifiedSuperclassName;
	import flash.utils.describeType;
	
	public class XmlEncodable extends EventDispatcher {
		//////////// variables ////////////
		protected var _data:XML;
		protected var _savedVars:Array;
		protected var _savedObjects:Array;
		protected var _tagName:String;
		protected var _xmlDataDefinition:Array;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function XmlEncodable() {
			xmlDataDefinition();
        }
		
		//////////// Properties functions ////////////
		public function get data():XML{
			return _data;
		}
		
		public function get tagName():String{
			return _tagName;
		}
		
		//////////// Static functions ////////////
		static function getXmlDataType(xml:XML,BaseClass:Class):Class{
			var queryClass:Class = BaseClass;
			while(queryClass != XmlEncodable){
				try {
					return queryClass["getXmlDataType"](xml,BaseClass);
			    } catch (e:Error) {}
				var describe = describeType(queryClass);
				//trace(describe);
				//trace(describe..factory.extendsClass[0].@type);
				//var superClassName:String = getQualifiedSuperclassName(new queryClass());
				var superClassName:String = describe..factory.extendsClass[0].@type;
				queryClass = getDefinitionByName(superClassName) as Class;
			}
			return BaseClass;
		}
		
		//////////// Public functions ////////////
		public function export(tagName:String = null):XML{
			if(tagName==null){
				tagName = this.tagName;
			}
			if(tagName!=null && tagName!=''){
				var newXml:XML = new XML("<"+tagName+" />");
				var data:Object = getDataForXml();
				for(var i:int = 0; i<_xmlDataDefinition.length; i++){
					var xmlProp:XmlPropBase = _xmlDataDefinition[i];
					xmlProp.write(newXml,data[xmlProp.name]);
				}
				return newXml;
			}
			return null;
		}
		
		public function importData(xml:XML):Boolean{
			if(tagName == null || xml.name() == tagName){
				var data:Object = new Object();
				for(var i:int = 0; i<_xmlDataDefinition.length; i++){
					var xmlProp:XmlPropBase = _xmlDataDefinition[i];
					if(xmlProp.readable(xml)){
						data[xmlProp.name] = xmlProp.read(xml);
					}
				}
				if(setDataFromXml(data)){
					_data = xml;
					return true;
				}
			}else{
				ClientMain.instance.log("Cant load data : tag name ("+xml.name()+") doesn't match model("+tagName+").");
			}
			return false;
		}
		
		//////////// Private functions ////////////
		protected function xmlDataDefinition(){
			//Only to regroup xml Data related codes
		}
		
		protected function getDataForXml():Object{
			var data:Object =  new Object();
			//==Deprecated support==
			var i:int;
			if(_savedVars !=null){
				for(i = 0; i<_savedVars.length; i++){
					if(varIsReadable(_savedVars[i])){
						if(this[_savedVars[i]] is SavedItemRef){
							data[_savedVars[i]] = (this[_savedVars[i]] as SavedItemRef).id;
						}else{
							data[_savedVars[i]] = this[_savedVars[i]];
						}
					}
				}
			}
			if(_savedObjects !=null){
				for(i = 0; i<_savedObjects.length; i++){
					if(varIsReadable(_savedObjects[i])){
						data[_savedObjects[i]] = this[_savedObjects[i]];
					}
				}
			}
			//== fin Deprecated support ==
			return data;
		}
		protected function setDataFromXml(data:Object):Boolean{
			//==Deprecated support==
			var i:int;
			if(_savedVars !=null){
				for(i = 0; i<_savedVars.length; i++){
					if(data.hasOwnProperty(_savedVars[i]) && varIsReadable(_savedVars[i])){
						if(this[_savedVars[i]] is SavedItemRef){
							(this[_savedVars[i]] as SavedItemRef).id = parseInt(data[_savedVars[i]]);
						}else{
							setVar(_savedVars[i],data[_savedVars[i]]);
						}
					}
				}
			}
			if(_savedObjects !=null){
				for(i = 0; i<_savedObjects.length; i++){
					if(data.hasOwnProperty(_savedObjects[i]) && varIsReadable(_savedObjects[i])){
						var objects:Array = data[_savedObjects[i]];
						if(this[_savedObjects[i]] is Array){
							setVar(_savedObjects[i],objects);
						}else if(this[_savedObjects[i]] is CollectionBase){
							var targetCollection:CollectionBase = this[_savedObjects[i]] as CollectionBase;
							targetCollection.clear();
							targetCollection.importArray(objects);
						}else{
							if(objects.length){
								setVar(_savedObjects[i],objects[0]);
							}
						}
					}
				}
			}
			//== fin Deprecated support ==
			return true;
		}
		
		protected function addXmlDataDefinition(val:XmlPropBase){
			addXmlDataDefinitions([val]);
		}
		
		protected function addXmlDataDefinitions(vals:Array){
			if(vals!=null){
				if(_xmlDataDefinition == null){
					_xmlDataDefinition = vals;
				}else{
					_xmlDataDefinition = _xmlDataDefinition.concat(vals);
				}
			}
		}
		
		
		
		protected function setVar(propName:String,val):Boolean{
			ClientMain.instance.log("Deprecated Function");
			try{
				this[propName] = val;
				return true;
			}catch(e:Error){
				ClientMain.instance.log("could not set "+propName+" : "+e.errorID+' - '+e.name+' - '+e.message);
			}
			return false
		}
		protected function varIsReadable(propName:String):Boolean{
			ClientMain.instance.log("Deprecated Function");
			try{
				var test = this[propName];
				return true;
			}catch(e:Error){
				ClientMain.instance.log("could not read "+propName+" : "+e.errorID+' - '+e.name+' - '+e.message);
			}
			return false;
		}
		
		protected function addSavedVar(varName:String,xmlvarName:String = null){
			ClientMain.instance.log("Deprecated Function");
			if(!_savedVars){
				_savedVars = new Array();
			}
			addXmlDataDefinition(new SimpleXmlProp(varName,xmlvarName));
			_savedVars.push(varName);
		}
		protected function addSavedObject(varName:String,model:XmlModel){
			ClientMain.instance.log("Deprecated Function");
			if(!_savedObjects){
				_savedObjects = new Array();
			}
			addXmlDataDefinition(new EncodableCollectionXmlProp(varName,model.tagName,model.classDef));
			_savedObjects.push(varName);
		}
		
		//////////// Event Handlers functions ////////////

	}
	
}
