package xmlProps {
	
	public class RefXmlProp extends XmlPropBase{
		//////////// variables ////////////
		protected var _xmlName:String;
		protected var _model:String;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function RefXmlProp(name:String,xmlName:String,model:String) {
			_xmlName = xmlName;
			_model = model;
			super(name);
        }
		
		//////////// Properties functions ////////////
		public function get xmlName():String{
			return _xmlName;
		}
		public function get model():String{
			return _model;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		override public function readable(xml:XML):Boolean{
			return xml.hasOwnProperty(xmlName);
		}
		override public function read(xml:XML):*{
			if(readable(xml)){
				return new SavedItemRef(parseInt(xml[_xmlName].toString()),model);
			}
			return null;
		}
		override public function write(xml:XML,val:*):Boolean{
			var ref:SavedItemRef = val as SavedItemRef;
			if(val != null){
				xml[_xmlName] = ref.id.toString();
				return true;
			}
			return false;
		}
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////

	}
	
}
