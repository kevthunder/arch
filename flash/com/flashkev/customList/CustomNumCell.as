package lib.customList {
	import fl.controls.listClasses.ListData;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import fl.core.UIComponent;
	import fl.core.InvalidationType;
	
	public class CustomNumCell extends CustomCell{
		//////////// variables ////////////
		protected var txtNumber:TextField
		private static var defaultStyles:Object = {
			"numFormat" : "#.",
			"numVAlign" : ALIGN_CENTER
		};
		//////////// Constructor ////////////
        public function CustomNumCell() {
			
        }
		
		//////////// Properties functions ////////////
		
		
		//////////// Public functions ////////////
		public static function getStyleDefinition():Object { 
			return UIComponent.mergeStyles(defaultStyles, CustomCell.getStyleDefinition());
		}
		
		//////////// Private functions ////////////
		override protected function configUI():void {
			super.configUI();
			txtNumber = new TextField();
			addChild(txtNumber);
		}
		
		/*override protected function draw():void {
			super.draw();
		}*/
		
		override protected function drawLayout():void {
			drawNumTextFormat();
			setNumber();
			txtMargins = calculMargins();
			updateSize();
			
			
			positionLabel();
			positionNumber();
			
			// Size background
			background.width = width;
			background.height = height;
		}
		
		protected function setNumber(){
			var numFormat:String = String(getStyleValue("numFormat"));
			txtNumber.text = numFormat.replace("#",listData.index +1);
		}
		
		protected function positionNumber(){
			var txtPad:Number = Number(getStyleValue("textPadding"));
			var numFormat:String = String(getStyleValue("numVAlign"));
			var txtH:Number = txtNumber.textHeight+5;
			
			txtNumber.x = textField.x - txtNumber.textWidth - txtPad
			switch(numFormat){
				case ALIGN_TOP :
					txtNumber.y = txtMargins.top;
					break;
				case ALIGN_BOTTOM :
					break;
					txtNumber.y = height - txtMargins.bottom - txtH;
				case ALIGN_CENTER :
				default :
					txtNumber.y = (height - txtMargins.top - txtMargins.bottom - txtH)/2 + txtMargins.top;
					break;
			}
		}
		
		override protected function calculMargins():Margins {
			var txtPad:Number = Number(getStyleValue("textPadding"));
			var txtMargins:Margins = super.calculMargins();
			
			txtMargins.left += txtNumber.textWidth + txtPad;
			
			return txtMargins;
		}
		
		protected function drawNumTextFormat():void {
			// get avalable textFormats
			var strings:Array = getStateStrings("numTextFormat");
			var formats:Array = new Array();
			for (var i:int = strings.length-1;i>=0;i--){
				var style:Object = getStyleValue(strings[i]);
				if(style is TextFormat){
					formats.push(style);
				}
			}
			
			var strings:Array = getStateStrings("textFormat");
			for (var i:int = strings.length-1;i>=0;i--){
				var style:Object = getStyleValue(strings[i]);
				if(style is TextFormat){
					formats.push(style);
				}
			}
			
			//default textformat
			var uiStyles:Object = UIComponent.getStyleDefinition();
			var defaultTF:TextFormat = enabled ? uiStyles.defaultTextFormat as TextFormat : uiStyles.defaultDisabledTextFormat as TextFormat;
			formats.push(defaultTF);
			
			// merge textFormats
			var tf = mergeTextFormats(formats);
			
			// apply textFormats
			txtNumber.setTextFormat(tf);
			txtNumber.defaultTextFormat = tf;
			
			setEmbedFont();
		}
		
	}
}