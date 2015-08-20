package com.flashkev.customList {
	import flash.display.DisplayObject;
	import fl.core.UIComponent;
	import fl.core.InvalidationType;
	import fl.controls.ButtonLabelPlacement
	
	import com.flashkev.utils.Margins;
	import flash.geom.Point;
	
	[Style(name="autoSize", type="string")]
	[Style(name="textPaddingH", type="number")]
	
	public class CustomSymbolCell extends CustomCellBase {
		//////////// variables ////////////
		private static var defaultStyles:Object = {
			"upSkin":false,
			"downSkin":false,
			"disabledSkin":false,
			"overSkin":false,
			"selectedDisabledSkin":false,
			"selectedDownSkin":false,
			"selectedOverSkin":false,
			"selectedUpSkin":false,
			"defaultSymbol":null,
			"symbolField":"symbol",
			"symbolDataProp":"data"
		};
		
		protected var symbol:DisplayObject;
		protected var originSymbolSize:Point;
		
		//////////// Constructor ////////////
        public function CustomSymbolCell() {
        }
		
		//////////// Properties functions ////////////
		
		//////////// Public functions ////////////
		public static function getStyleDefinition():Object { 
			return UIComponent.mergeStyles(defaultStyles, CustomCellBase.getStyleDefinition());
		}
		
		//////////// Private functions ////////////
		override protected function configUI():void {
			super.configUI();
			textField.visible = false;
		}
		
		override protected function updateContent():void {
			var symbolDef;
			if(data.hasOwnProperty(getStyleValue("symbol")) && data[getStyleValue("symbol")]){
				symbolDef = data[getStyleValue("symbol")];
			}else{
				symbolDef = getStyleValue("defaultSymbol");
			}
			var newSymbol:DisplayObject = getAvailableDisplayObjectInstance(symbolDef,[symbol]) as DisplayObject;
			if(newSymbol && newSymbol != symbol){
				if(symbol != null){
					removeChild(symbol);
				}
				symbol = newSymbol;
				if(symbol != null){
					originSymbolSize = new Point(symbol.width,symbol.height);
					addChild(symbol);
					var prop:String = getStyleValue("symbolDataProp").toString();
					if(symbol.hasOwnProperty(prop)){
						symbol[prop] = data;
					}
				}
			}
			updateSize();
			if(symbol){
				symbol.x = insideRect.x;
				symbol.y = insideRect.y;
				symbol.width = insideRect.width;
				symbol.height = insideRect.height;
			}
		}
		
		
		override protected function getAutoSize(autoSize:String,margins:Margins):Point {
			return originSymbolSize;
		}
		
		
		//////////// Event Handlers functions ////////////
		
	}
}