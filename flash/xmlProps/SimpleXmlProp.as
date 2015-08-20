package xmlProps {
	
	public class SimpleXmlProp extends XmlPropBase{
		//////////// variables ////////////
		protected var _xmlName:String;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function SimpleXmlProp(name:String,xmlName:String = null) {
			if(xmlName == null){
				xmlName = '@'+name;
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
				return xml[_xmlName].toString();
			}
			return null;
		}
		override public function write(xml:XML,val:*):Boolean{
			if(val != null){
				val = val.toString();
				xml[_xmlName] = val;
				return true;
			}
			return false;
		}
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////

	}
	
}
