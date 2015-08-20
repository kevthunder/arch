package  {
	import flash.events.Event;
	import flash.geom.Point;
	import com.flashkev.utils.GeomUtils;
	import com.flashkev.utils.NumberUtil;
	import xmlProps.SimpleXmlProp;
	
	
	public class Item extends TilePositionnedObject{

		//////////// variables ////////////
		protected var _title:String;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
		public function Item(data:XML = null) {
			_model = 'item';
			importData(data);
			//skin = new Perso1Skin();
			//skin.bindedDisplay = main.tilesDisplay;
		}
		
		//////////// Properties functions ////////////
		public function get title():String{
			return _title;
		}
		
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		
		//////////// Private functions ////////////
		override protected function xmlDataDefinition(){
			super.xmlDataDefinition();
			addXmlDataDefinitions([
					new SimpleXmlProp('title')
				]);
		}
		override protected function getDataForXml():Object{
			var data:Object = super.getDataForXml();
			
			data['title'] = title;
			
			return data;
		}
		override protected function setDataFromXml(data:Object):Boolean{
			if(super.setDataFromXml(data)){
			
				if(data.hasOwnProperty('title')) _title = data['title'];
			
				return true;
			}
			return false;
		}
		
		
		//////////// Event Handlers functions ////////////
		/*protected function loadedHandler(e:Event){
			
		}
		
		protected function unloadedHandler(e:Event){
		}*/
	
	}
}
