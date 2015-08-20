package {
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.geom.ColorTransform;
	import de.polygonal.math.PM_PRNG;

	public class TileSkin extends TiledSkin {
		//////////// variables ////////////
		public var ground:MovieClip;
		
		//////////// Static variables ////////////
		public static var fertilityDecorations:Array = [
			Grass1,
			Grass2
		];
		
		//////////// Constructor ////////////
        public function TileSkin() {
        }
		
		//////////// Properties functions ////////////
		public function get tile():Tile{
			return this.owner as Tile;
		}
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		protected function updateGraphic(){
			var rnd:PM_PRNG = new PM_PRNG();
			rnd.seed = tile.getSeed();
			var prc:Number = Math.min(tile.fertility / 2000,1);
			var start:ColorTransform = new ColorTransform();
			var end:ColorTransform = new ColorTransform(0.3,0.4,0.0,1,60,80,0);
			this.transform.colorTransform = new ColorTransform(
				(end.redMultiplier - start.redMultiplier)*prc + start.redMultiplier,
				(end.greenMultiplier - start.greenMultiplier)*prc + start.greenMultiplier,
				(end.blueMultiplier - start.blueMultiplier)*prc + start.blueMultiplier,
				(end.alphaMultiplier - start.alphaMultiplier)*prc + start.alphaMultiplier,
				(end.redOffset - start.redOffset)*prc + start.redOffset,
				(end.greenOffset - start.greenOffset)*prc + start.greenOffset,
				(end.blueOffset - start.blueOffset)*prc + start.blueOffset,
				(end.alphaOffset - start.alphaOffset)*prc + start.alphaOffset
			)
			if(tile.fertility > 300){
				var maxDecorations:Number = Math.max(0,Math.min((tile.fertility-300)/768,4))+1;
				var rand:Number = rnd.nextDouble();
				var nb:int = rand*maxDecorations;
				//trace('fertility : ',tile.fertility);
				//trace('rnd : ',rand);
				//trace('maxDecorations : ',maxDecorations);
				//trace('needed for one : ',1/maxDecorations);
				//trace('nb : ',nb);
				for(var i:int = 0; i<nb; i++){
					var decorVariation:int = rnd.nextIntRange(0,fertilityDecorations.length-1);
					var decor:MovieClip = new fertilityDecorations[decorVariation]();
					decor.scaleX = 0.5;
					decor.scaleY = 0.5;
					decor.x = rnd.nextDoubleRange(0,TiledObject.baseWidth / 2) + TiledObject.baseWidth/4 - decor.width/2;
					decor.y = rnd.nextDoubleRange(0,TiledObject.baseHeight / 2) + TiledObject.baseHeight/4 - decor.height/2;
					addChild(decor);
				}
			}
		}
		
		//////////// Event Handlers functions ////////////
		
		override protected function displayedHandler(e:Event){
			super.displayedHandler(e);
			updateGraphic()
		}
		override protected function updatedHandler(e:Event){
			super.displayedHandler(e);
			updateGraphic()
		}
		
	}
}