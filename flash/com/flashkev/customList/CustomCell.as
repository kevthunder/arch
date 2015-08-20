package com.flashkev.customList {
	import flash.display.DisplayObject;
	import fl.core.UIComponent;
	import fl.core.InvalidationType;
	import fl.controls.ButtonLabelPlacement
	
	import com.flashkev.utils.Margins;
	import flash.geom.Point;
	
	[Style(name="autoSize", type="string")]
	[Style(name="textPaddingH", type="number")]
	
	public class CustomCell extends CustomCellBase {
		//////////// variables ////////////
		private static var defaultStyles:Object = {
			"textPaddingH":1,
			"textAlign":ALIGN_LEFT,
			"textVAlign":ALIGN_CENTER,
			"multiline":true,
			"wordWrap":true,
			"displayField":"label"
		};
		
		protected var txtMargins:Margins;
		
		//////////// Constructor ////////////
        public function CustomCell() {
        }
		
		//////////// Properties functions ////////////
		override public function set label(value:String):void {
			super.label = value;
			invalidate(InvalidationType.SIZE);
		}
		
		//////////// Public functions ////////////
		public static function getStyleDefinition():Object { 
			return UIComponent.mergeStyles(defaultStyles, CustomCellBase.getStyleDefinition());
		}
		//////////// Private functions ////////////
		override protected function updateContent():void {
			txtMargins = calculMargins();
			updateSize();
			
			positionLabel();
		}
		
		protected function calculMargins():Margins {
			var txtPad:Number = Number(getStyleValue("textPadding"));
			var txtPadH:Number = Number(getStyleValue("textPaddingH"));
			var txtMargins:Margins = getStyleValue("margins") as Margins;
			if(!txtMargins){
				txtMargins = new Margins(txtPadH,txtPad,txtPadH,txtPad);
			}else{
				txtMargins = txtMargins.clone();
			}
			if (icon != null) {
				switch(_labelPlacement){
					case ButtonLabelPlacement.BOTTOM :
						txtMargins.top += icon.height + txtPadH;
						break;
					case ButtonLabelPlacement.LEFT :
						txtMargins.right += icon.width + txtPad;
						break;
					case ButtonLabelPlacement.TOP :
						txtMargins.bottom += icon.height + txtPadH;
						break;
					case ButtonLabelPlacement.RIGHT :
					default:
						txtMargins.left += icon.width + txtPad;
						break;
				}
			}
			return txtMargins;
		}
		
		override protected function getAutoSize(autoSize:String,margins:Margins):Point {
			var size:Point = new Point();
			margins.setAllMargins(0);
			textField.multiline = Boolean(getStyleValue("multiline"));
			textField.wordWrap = false;
			if(autoSize == AUTO_HORIZONTAL || autoSize == AUTO_BOTH){
				var txtW:Number = textField.textWidth+5;
				size.x = txtW + txtMargins.left + txtMargins.right;
			} 
			textField.wordWrap = Boolean(getStyleValue("wordWrap")); 
			if(autoSize == AUTO_VERTICAL || autoSize == AUTO_BOTH){
				textField.width = Math.max(_minWidth,Math.min(_maxWidth,size.x)) - txtMargins.left - txtMargins.right;
				var txtH:Number = textField.textHeight+5;
				size.y = txtH + txtMargins.top + txtMargins.bottom;
			}
			return size;
		}
		
		protected function positionLabel():void {
			if(label.length > 0){
				var textAlign:String = String(getStyleValue("textAlign"));
				var textVAlign:String = String(getStyleValue("textVAlign"));
				var txtW:Number = textField.textWidth+5;
				var txtH:Number = textField.textHeight+5;
				
				textField.visible = true;
				
				switch(textAlign){
					case ALIGN_LEFT :
						textField.x = txtMargins.left;
						break;
					case ALIGN_RIGHT :
						textField.x = width - txtMargins.right - txtW;
						break;
					case ALIGN_CENTER :
					default :
						textField.x = (width - txtMargins.left - txtMargins.right - txtW)/2 + txtMargins.left;
						break;
				}
				
				switch(textVAlign){
					case ALIGN_TOP :
						textField.y = txtMargins.top;
						break;
					case ALIGN_BOTTOM :
						textField.y = height - txtMargins.bottom - txtH;
						break;
					case ALIGN_CENTER :
					default :
						textField.y = (height - txtMargins.top - txtMargins.bottom - txtH)/2 + txtMargins.top;
						break;
				}
				
				textField.width = width - txtMargins.left - txtMargins.right;
				textField.height = height - txtMargins.top - txtMargins.bottom;
			}else{
				textField.visible = false;
			}
			
		}
		
		override protected function drawTextFormat():void {
			var tf = getTextFormat();
			
			// apply textFormats
			textField.setTextFormat(tf);
			textField.defaultTextFormat = tf;
			
			setEmbedFont();
		}
		
		//////////// Event Handlers functions ////////////
		
	}
}