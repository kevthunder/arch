package lib.customList {
	import fl.controls.listClasses.ImageCell;
	import flash.display.DisplayObject;
	import fl.controls.*;
	import flash.events.*;
	import flash.text.*;
	import flash.geom.*;
	import fl.core.UIComponent;
	import fl.core.InvalidationType;
	
	[Style(name="imageMask", type="Class")]
	
	public class CustomImageCell extends ImageCell {
		//////////// variables ////////////
		private var imgMask:DisplayObject;
		
		private static var defaultStyles:Object = {
			"imageMask":null
		};
		
		//////////// Constructor ////////////
        public function CustomImageCell() {
			super();
			loader.addEventListener(Event.COMPLETE, completeHandler, false, 0, true);
			width = 1;
			height = 1;
        }
		//////////// Properties functions ////////////
		
		//////////// Public functions ////////////
		public static function getStyleDefinition():Object { 
			return UIComponent.mergeStyles(ImageCell.getStyleDefinition(), defaultStyles);
		}
		
		//////////// Private functions ////////////
		override protected function draw():void {
			if (isInvalid(InvalidationType.STYLES,InvalidationType.STATE)) {
				drawMask();
			}
			super.draw();
		}
		override protected function drawLayout():void {
			super.drawLayout();
			
			if(imgMask!=null){
				imgMask.height = loader.height;
				imgMask.width = loader.width;
				imgMask.x = loader.x;
				imgMask.y = loader.y;
			}
		}
		protected function drawMask():void {
			var oldMask:DisplayObject = imgMask;
			
			var maskStyle:Object = getStyleValue("imageMask");
			if(maskStyle != null){
				imgMask = getDisplayObjectInstance(maskStyle);
				if(imgMask != null){
					//addChildAt(imgMask,1);
					addChild(imgMask);
					imgMask.height = loader.height;
					imgMask.width = loader.width;
					imgMask.x = loader.x;
					imgMask.y = loader.y;
					loader.mask = imgMask;
				}
			}
			if (oldMask != null && oldMask != imgMask) { 
				removeChild(oldMask);
			}
		}
		
		private function completeHandler(e:Event){
			var rect:Rectangle = loader.content.getRect(loader.content);
			loader.setSize(rect.width,rect.height);
			
			var imagePadding:Number = getStyleValue("imagePadding") as Number;
			setSize(rect.width+imagePadding*2,rect.height+imagePadding*2);
			listData.owner.invalidate(InvalidationType.DATA);
		}
		
		
	}
}