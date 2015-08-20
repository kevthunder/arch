package  {
	import flash.events.Event;
	import flash.geom.Point;
	import com.flashkev.utils.GeomUtils;
	import com.flashkev.utils.NumberUtil;
	
	
	public class Structure extends TilePositionnedObject{

		//////////// variables ////////////
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
		public function Structure(data:XML = null) {
			_model = 'structure';
			importData(data);
			//skin = new Perso1Skin();
			//skin.bindedDisplay = main.tilesDisplay;
		}
		
		//////////// Properties functions ////////////
		
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		/*protected function loadedHandler(e:Event){
			
		}
		
		protected function unloadedHandler(e:Event){
		}*/
	
	}
}
