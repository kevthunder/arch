package  {
	
	public class XmlModel {
		//////////// variables ////////////
		protected var _tagName:String;
		protected var _classDef:Class;
		
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function XmlModel(tagName,classDef:Class) {
			_tagName = tagName;
			_classDef = classDef;
        }
		
		//////////// Properties functions ////////////
		public function get tagName():String{
			return _tagName;
		}
		public function get classDef():Class{
			return _classDef;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function createObject(data:XML):XmlEncodable{
			if(data.name() == tagName){
				var obj:XmlEncodable = new classDef() as XmlEncodable;
				if(obj != null){
					if(obj.importData(data)){
						return obj;
					}
				}
			}
			return null
		}
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////

	}
	
}
