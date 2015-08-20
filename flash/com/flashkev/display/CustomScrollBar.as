package com.flashkev.display {
	
	
	import fl.core.UIComponent;
	import fl.core.InvalidationType;
	import fl.controls.ScrollBar;
	import fl.controls.ScrollBarDirection;
	
	
	
	public class CustomScrollBar extends ScrollBar{
		//////////// variables ////////////
		
		
		
		private static var defaultStyles:Object = {
			"width" : 15,
			"upArrowHeight" : 14,
			"downArrowHeight" : 14,
			"thumbFixedHeight" : false
		};
			
			
		//////////// Constructor ////////////
		public function CustomScrollBar(){
			
		}
      
		//////////// Properties functions ////////////
		public function get size():Number {
			return (direction == ScrollBarDirection.HORIZONTAL) ? width : height;
		}
		public function set size(val:Number) {
			if(direction == ScrollBarDirection.HORIZONTAL){
				width = val;
			}else{
				height = val;
			};
		}
		
		//////////// Public functions ////////////
		public static function getStyleDefinition():Object { 
			return UIComponent.mergeStyles(defaultStyles, ScrollBar.getStyleDefinition());
		}
		
		
		
		//////////// Private functions ////////////
		override protected function draw():void {	
			if (isInvalid(InvalidationType.STYLES, InvalidationType.SIZE)) {
				updateArrow();
				updateTrack();
				updateThumb();
			}
			if (isInvalid(InvalidationType.STYLES, InvalidationType.STATE)) {
				setStyles();
			}
			// Call drawNow on nested components to get around problems with nested render events:
			downArrow.drawNow();
			upArrow.drawNow();
			track.drawNow();
			thumb.drawNow();
			validate();
		}
		
		protected function updateTrack(){
			var w:Number = Number(getStyleValue("width"));
			track.setSize(w, Math.max(0, size-(downArrow.height + upArrow.height)));
			track.move(0, upArrow.height);
		}
		
		protected function updateArrow(){
			var w:Number = Number(getStyleValue("width"));
			var upArrowHeight:Number = Number(getStyleValue("upArrowHeight"));
			var downArrowHeight:Number = Number(getStyleValue("downArrowHeight"));
			upArrow.setSize(w, upArrowHeight);
			downArrow.setSize(w, downArrowHeight);
			downArrow.move(0,  Math.max(upArrow.height, size-downArrow.height));
		}
		
		override protected function updateThumb():void {
			var w:Number = Number(getStyleValue("width"));
			var thumbFixedHeight:Number = Number(getStyleValue("thumbFixedHeight"));
			var per:Number = maxScrollPosition - minScrollPosition + pageSize;
			if (track.height <= 12 || maxScrollPosition <= minScrollPosition || (per == 0 || isNaN(per))) {
				thumb.setSize(w,12);
				thumb.visible = false;
			}else{
				var h:Number;
				if(thumbFixedHeight > 0) {
					h = thumbFixedHeight;
				}else{
					h = Math.max(13,pageSize / per * track.height);
				}
				thumb.setSize(w,h);
				thumb.y = track.y+(track.height-thumb.height)*((scrollPosition-minScrollPosition)/(maxScrollPosition-minScrollPosition));
				thumb.visible = enabled;
			}
		}
		//////////// Event Handlers functions ////////////
		
		
	}
}