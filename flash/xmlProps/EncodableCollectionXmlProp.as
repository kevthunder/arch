package xmlProps {
	
	public class EncodableCollectionXmlProp extends XmlPropBase{
		//////////// variables ////////////
		protected var _tagName:String;
		protected var _classDef:Class;
		protected var _initArgs:Array;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function EncodableCollectionXmlProp(name:String,tagName:String,classDef:Class,initArgs:Array = null) {
			_tagName = tagName;
			_classDef = classDef;
			if(initArgs == null){
				initArgs = new Array();
			}
			_initArgs = initArgs;
			super(name);
        }
		
		//////////// Properties functions ////////////
		public function get tagName():String{
			return _tagName;
		}
		public function get classDef():Class{
			return _classDef;
		}
		public function get initArgs():Array{
			return _initArgs;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		override public function readable(xml:XML):Boolean{
			return true;
		}
		override public function read(xml:XML):*{
			var nodes:XMLList = xml[tagName];
			var objects:Array = new Array();
			for(var i:int = 0; i<nodes.length(); i++){
				var obj:XmlEncodable = createObject(nodes[i]);
				if(obj != null){
					objects.push(obj);
				}
			}
			return objects;
		}
		override public function write(xml:XML,val:*):Boolean{
			var objects:Array;
			if(val is Array){
				objects = val;
			}else if(val is CollectionBase){
				var targetCollection:CollectionBase = val as CollectionBase;
				objects = targetCollection.getItems();
			}else{
				objects = [val];
			}
			for(var i:int = 0; i<objects.length; i++){
				var obj:XmlEncodable = objects[i];
				xml.appendChild(obj.export(tagName));
			}
			return true;
		}
		
		//////////// Private functions ////////////
		protected function createObject(data:XML):XmlEncodable{
			if(data.name() == tagName){
				var obj:XmlEncodable = define(classDef,initArgs) as XmlEncodable;
				if(obj != null){
					if(obj.importData(data)){
						return obj;
					}
				}
			}
			return null
		}
		
		protected function define(c:Class,a:Array):Object{
			switch(a.length)
			{
				case 0: {return new c();}
				case 1: {return new c(a[0]); }
				case 2: {return new c(a[0],a[1]);}
				case 3: {return new c(a[0],a[1],a[2]);}
				case 4: {return new c(a[0],a[1],a[2],a[3]);}
				case 5: {return new c(a[0],a[1],a[2],a[3],a[4]);}
				case 6: {return new c(a[0],a[1],a[2],a[3],a[4],a[5]);}
				case 7: {return new c(a[0],a[1],a[2],a[3],a[4],a[5],a[6]);}
				case 8: {return new c(a[0],a[1],a[2],a[3],a[4],a[5],a[6],a[7]);}
				case 9: {return new c(a[0],a[1],a[2],a[3],a[4],a[5],a[6],a[7],a[8]);}
				case 10:{return new c(a[0],a[1],a[2],a[3],a[4],a[5],a[6],a[7],a[8], a[9]);}
				default:{trace("too may arguments!"); return null;}
			}
			return null;
		}
		
		//////////// Event Handlers functions ////////////

	}
	
}
