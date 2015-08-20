package xmlProps {
	
	public class TimeXmlProp extends XmlPropBase{
		//////////// variables ////////////
		protected var _xmlName:String;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function TimeXmlProp(name:String,xmlName:String = null) {
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
				var main:ClientMain = ClientMain.instance;
				return main.connection.serverTimeToTimer(Number(xml[_xmlName].toString()));
			}
			return null;
		}
		override public function write(xml:XML,val:*):Boolean{
			if(val != null){
				var main:ClientMain = ClientMain.instance;
				xml[_xmlName] = main.connection.timerToServerTime(val);
				return true;
			}
			return false;
		}
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////

	}
	
}
