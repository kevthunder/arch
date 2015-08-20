package xmlProps {
	import com.flashkev.utils.ObjUtil;
	
	public class ExtractXmlProp extends XmlPropBase{
		//////////// variables ////////////
		protected var _xmlPath:String;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function ExtractXmlProp(name:String,xmlPath:String = null) {
			if(xmlPath == null){
				xmlPath = '@'+name;
			}
			_xmlPath = xmlPath;
			super(name);
        }
		
		//////////// Properties functions ////////////
		public function get xmlPath():String{
			return _xmlPath;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		override public function readable(xml:XML):Boolean{
			return read(xml) !== null;
		}
		override public function read(xml:XML):*{
			return ObjUtil.simpleExtract(xmlPath,xml);
		}
		override public function write(xml:XML,val:*):Boolean{
			if(val != null){
				var target = ObjUtil.simpleExtract(xmlPath,xml);
				if(target !== null){
					target = val.toString();
					return true;
				}
			}
			return false;
		}
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////

	}
	
}
