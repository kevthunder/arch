package xmlProps {
	
	public class ObjXmlProp extends XmlPropBase{
		//////////// variables ////////////
		protected var _xmlName:String;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function ObjXmlProp(name:String,xmlName:String = null) {
			if(xmlName == null){
				xmlName = name;
			}
			_xmlName = xmlName;
			super(name);
        }
		
		//////////// Properties functions ////////////
		public function get xmlName():String{
			return _xmlName;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		override public function readable(xml:XML):Boolean{
			return xml.hasOwnProperty(xmlName);
		}
		override public function read(xml:XML):*{
			if(readable(xml)){
				return decode(xml[_xmlName][0]);
			}
			return null;
		}
		override public function write(xml:XML,val:*):Boolean{
			if(val != null){
				val = encode(val);
				xml[_xmlName] = val;
				return true;
			}
			return false;
		}
		
		//////////// Private functions ////////////
		protected function decode(xml:XML):Object{
			var obj = new Object();
			var attrs:XMLList = xml.attributes();
			var i:int;
			for(i = 0; i<attrs.length(); i++){
				var attr:XML = attrs[i];
				obj[attr.name().toString()] = attr.toString();
			}
			var children:XMLList = xml.children();
			for(i = 0; i<children.length(); i++){
				var child:XML = children[i];
				if(obj.hasOwnProperty(child.name().toString())){
					if(!obj[child.name().toString()] is Array){
						obj[child.name().toString()] = [obj[child.name()]];
					}
					obj[child.name().toString()].push(decode(child));
				}else{
					obj[child.name().toString()] = decode(child);
				}
			}
			return obj;
		}
		protected function encode(obj:Object,parentXml:XML = null):XML{
			if(parentXml == null){
				parentXml = new XML();
			}
			for(var key in obj){
				var val = obj[key]
				if(val is int || val is String || val is Boolean || val is Number){
					parentXml['@'+key] = val;
				}else if(val is Array){
					encode(val,parentXml[key]);
				}else{
					encode(val,parentXml[key]);
				}
			}
			return parentXml;
		}
		
		//////////// Event Handlers functions ////////////

	}
	
}
