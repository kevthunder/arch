package {
	import flash.display.MovieClip;
	import flash.events.Event;

	public class ViewSideSkin extends MovableSkin {
		//////////// variables ////////////
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function ViewSideSkin() {
        }
		
		//////////// Properties functions ////////////
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		
		//////////// Event Handlers functions ////////////
		override protected function moveHandler(e:Event){
			super.moveHandler(e);
			//trace(suggestedRotation);
			if(suggestedRotation > 90 || suggestedRotation < -90){
				scaleX = -1;
			}else{
				scaleX = 1;
			}
		}
		
		
	}
}