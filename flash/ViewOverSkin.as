package {
	import flash.display.MovieClip;
	import flash.events.Event;

	public class ViewOverSkin extends MovableSkin {
		//////////// variables ////////////
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function ViewOverSkin() {
        }
		
		//////////// Properties functions ////////////
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		override protected function moveHandler(e:Event){
			super.moveHandler(e);
			rotation = suggestedRotation;
		}
		
		
	}
}