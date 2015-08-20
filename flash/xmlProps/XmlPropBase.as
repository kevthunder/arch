package xmlProps {
	import flash.errors.IllegalOperationError;
	
	public class XmlPropBase {

		//////////// variables ////////////
		protected var _name:String;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function XmlPropBase(name:String) {
			_name = name;
        }
		
		//////////// Properties functions ////////////
		public function get name():String{
			return _name;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function readable(xml:XML):Boolean{
			throw new IllegalOperationError('this method should be overriden in a subclass');
			return false;
		}
		public function read(xml:XML):*{
			throw new IllegalOperationError('this method should be overriden in a subclass');
			return null;
		}
		public function write(xml:XML,val:*):Boolean{
			throw new IllegalOperationError('this method should be overriden in a subclass');
			return false;
		}
		
		//////////// Private functions ////////////
		
		//////////// Event Handlers functions ////////////
	}
	
}
