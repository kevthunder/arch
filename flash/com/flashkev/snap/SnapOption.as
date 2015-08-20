package com.flashkev.snap {

	public class SnapOption{
		//////////// variables ////////////

		//general SnapOptions
		public static const NONE:String = 'none';
		public static const FULL_SCREEN:String = 'full_screen';
		public static const FULL_SCREEN_CROP:String = 'full_screen_crop';
		public static const STRETCH:String = 'stretch';
		public static const STRETCH_CROP:String = 'stretch_crop';
		public static const RELATIVE:String = 'relative';
		public static const RELATIVE_CROP:String = 'relative_crop';
		
		//x SnapOptions
		public static const LEFT:SnapOption = new SnapOption(0);
		public static const RIGHT:SnapOption = new SnapOption(1);
		public static const CENTER:SnapOption = new SnapOption(0.5);
		public static const RELATIVE_TO_LEFT:SnapOption = new SnapOption(0,0,1,0);
		public static const RELATIVE_TO_RIGHT:SnapOption = new SnapOption(0,0,1,1);
		public static const RELATIVE_TO_CENTER:SnapOption = new SnapOption(0,0,1,0.5);
		public static const RELATIVE_TO_WIDTH:SnapOption = new SnapOption(0,0,0,0,1);
		
		//y SnapOptions
		public static const BOTTOM:SnapOption = new SnapOption(1);
		public static const TOP:SnapOption = new SnapOption(0);
		public static const MIDDLE:SnapOption = new SnapOption(0.5);
		public static const RELATIVE_TO_BOTTOM:SnapOption = new SnapOption(0,0,1,1);
		public static const RELATIVE_TO_TOP:SnapOption = new SnapOption(0,0,1,0);
		public static const RELATIVE_TO_MIDDLE:SnapOption = new SnapOption(0,0,1,0.5);
		public static const RELATIVE_TO_HEIGHT:SnapOption = new SnapOption(0,0,0,0,1);
		
		//width SnapOptions
		public static const FULL_WIDTH:SnapOption = new SnapOption(1);
		public static const STRETCH_WIDTH:SnapOption = new SnapOption(0,0,0,0,1);
		public static const RELATIVE_WIDTH:SnapOption = new SnapOption(0,0,1,1);
		
		//height SnapOptions
		public static const FULL_HEIGHT:SnapOption = new SnapOption(1);
		public static const STRETCH_HEIGHT:SnapOption = new SnapOption(0,0,0,0,1);
		public static const RELATIVE_HEIGHT:SnapOption = new SnapOption(0,0,1,1);
		
		//collections
		public static const GENERAL_SNAP_OPTIONS:Array = [NONE,FULL_SCREEN,FULL_SCREEN_CROP,STRETCH,STRETCH_CROP,RELATIVE,RELATIVE_CROP];
		public static const X_SNAP_OPTIONS:Object = {
				'left':LEFT,
				'right':RIGHT,
				'center':CENTER,
				'rel_left':RELATIVE_TO_LEFT,
				'rel_right':RELATIVE_TO_RIGHT,
				'rel_center':RELATIVE_TO_CENTER,
				'rel_to_width':RELATIVE_TO_WIDTH
			};
		public static const Y_SNAP_OPTIONS:Object = {
				'bottom':BOTTOM,
				'top':TOP,
				'middle':MIDDLE,
				'rel_bottom':RELATIVE_TO_BOTTOM,
				'rel_top':RELATIVE_TO_TOP,
				'rel_middle':RELATIVE_TO_MIDDLE,
				'rel_to_height':RELATIVE_TO_HEIGHT
			};
		public static const WIDTH_SNAP_OPTIONS:Object = {
				'full_width':FULL_WIDTH,
				'stretch_width':STRETCH_WIDTH,
				'rel_width':RELATIVE_WIDTH
			};
		public static const HEIGHT_SNAP_OPTIONS:Object = {
				'full_height':FULL_HEIGHT,
				'stretch_height':STRETCH_HEIGHT,
				'rel_height':RELATIVE_HEIGHT
			};
		
		
		//instance
		public var prc:Number;
		public var adjust:Number;
		public var relPrc:Number;
		public var relToPrc:Number;
		public var stretchPrc:Number;
		public var stretchAdjust:Number;
		
		//////////// Constructor ////////////
		public function SnapOption(prc:Number=0,adjust:Number=0,relPrc:Number=0,relToPrc:Number=0,stretchPrc:Number=0,stretchAdjust:Number=0){
			this.prc = prc;
			this.adjust = adjust;
			this.relPrc = relPrc;
			this.relToPrc = relToPrc;
			this.stretchPrc = stretchPrc;
			this.stretchAdjust = stretchAdjust;
		}
      
		//////////// Properties functions ////////////
		
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		
		
	}
}