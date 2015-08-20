package com.flashkev.docroot {
	import flash.display.StageScaleMode;
	import flash.display.StageAlign;
	import flash.events.Event;
	import flash.geom.Rectangle;
	import com.flashkev.utils.Margins;

	public class ResizableRoot extends SimpleRootLoader{
		//////////// variables ////////////
		public var margin:Margins = new Margins(0,0,0,0);
		
		private var _originStageRect:Rectangle;
		//////////// Constructor ////////////
        public function ResizableRoot() {
			if(stage){
				updateOriginalStageRect();
			}else{
				addEventListener(Event.ADDED_TO_STAGE,addedToStageHandler);
			}
        }
		//////////// Properties functions ////////////
		public function get originalStageRect():Rectangle{
			return _originStageRect;
		}
		public function get stageRect():Rectangle{
			var rect:Rectangle = new Rectangle(0,0,stage.stageWidth,stage.stageHeight);
			switch(stage.align){
				case StageAlign.BOTTOM : 
					rect.x = (originalStageRect.width-stage.stageWidth)/2;
					rect.y = originalStageRect.height-stage.stageHeight;
					break;
				case StageAlign.BOTTOM_LEFT : 
					rect.x = 0;
					rect.y = originalStageRect.height-stage.stageHeight;
					break;
				case StageAlign.BOTTOM_RIGHT : 
					rect.x = originalStageRect.width-stage.stageWidth;
					rect.y = originalStageRect.height-stage.stageHeight;
					break;
				case StageAlign.LEFT : 
					rect.x = 0;
					rect.y = (originalStageRect.height-stage.stageHeight)/2;
					break;
				case StageAlign.RIGHT : 
					rect.x = originalStageRect.width-stage.stageWidth;
					rect.y = (originalStageRect.height-stage.stageHeight)/2;
					break;
				case StageAlign.TOP : 
					rect.x = (originalStageRect.width-stage.stageWidth)/2;
					rect.y = 0;
					break;
				case StageAlign.TOP_LEFT : 
					rect.x = 0;
					rect.y = 0;
					break;
				case StageAlign.TOP_RIGHT : 
					rect.x = originalStageRect.width-stage.stageWidth;
					rect.y = 0;
					break;
				default :
					rect.x = (originalStageRect.width-stage.stageWidth)/2;
					rect.y = (originalStageRect.height-stage.stageHeight)/2;
					break;
			}
			rect.x += margin.left;
			rect.y += margin.top;
			rect.width -= margin.left+margin.right;
			rect.height -= margin.top+margin.bottom;
			return rect;
		}
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		protected function updateOriginalStageRect(){
			stage.scaleMode = StageScaleMode.SHOW_ALL;
			_originStageRect = new Rectangle(0,0,stage.stageWidth,stage.stageHeight);
			stage.scaleMode = StageScaleMode.NO_SCALE;
		}
		
		//////////// Event Handlers functions ////////////
		protected function addedToStageHandler(e:Event){
			updateOriginalStageRect();
		}
		
	}
}