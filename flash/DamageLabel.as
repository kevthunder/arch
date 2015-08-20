package {
	import flash.display.Sprite;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import flash.events.Event;
	import flash.utils.getTimer;

	public class DamageLabel extends Sprite{
		//////////// variables ////////////
		protected var _amount:int;
		protected var damageField:TextField;
		protected var timeout:int = 3000;
		protected var startAt:int;
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function DamageLabel(x:int,y:int,amount:int) {
			this.x = x;
			this.y = y;
			_amount = amount;
			addEventListener(Event.ADDED_TO_STAGE,addedToStageHandler);
        }
		
		//////////// Properties functions ////////////
		public function get amount():int{
			return _amount;
		}
		/*public function set amount(val:int):void{
			_amount = val;
		}*/
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function destroy(){
			parent.removeChild(this);
			removeEventListener(Event.ENTER_FRAME,enterFrameHandler);
			removeEventListener(Event.ADDED_TO_STAGE,addedToStageHandler);
		}
		
		//////////// Private functions ////////////
		protected function show(){
			damageField = new TextField();
			var tf:TextFormat = new TextFormat(null,11,(amount<0)?0xff0000:0x00ff00);
			damageField.text = Math.abs(amount).toString();
			damageField.setTextFormat(tf);
			damageField.x = damageField.textWidth/2;
			damageField.width = damageField.textWidth+10;
			addChild(damageField);
			addEventListener(Event.ENTER_FRAME,enterFrameHandler);
			startAt = getTimer();
		}
		
		//////////// Event Handlers functions ////////////
		protected function addedToStageHandler(e:Event){
			show();
		}
		protected function enterFrameHandler(e:Event){
			var now = getTimer();
			if(now-startAt > timeout){
				destroy();
			}
			damageField.y -= 0.1;
		}
		
	}
}