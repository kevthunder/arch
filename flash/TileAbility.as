package  {
	import flash.geom.Point;
	import flash.display.Graphics;
	
	public class TileAbility extends Ability{
		//////////// variables ////////////
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function TileAbility(name = null) {
			super(name);
        }
		
		//////////// Properties functions ////////////
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		
		//////////// Private functions ////////////
		override protected function useInstance(inst:AbilityInstance){
			var tilesDisplay:TilesDisplay = ClientMain.instance.ingame.tilesDisplay
			var mousePos:Point = new Point(tilesDisplay.mouseX,tilesDisplay.mouseY)
			var pos:Point = tilesDisplay.globalPixelToTilePos(mousePos);
			//trace(main.tilesDisplay.globalPixelToPixel(mousePos),main.tilesDisplay.tilePosToPixel(pos) );
			
			/*var p:Point = main.tilesDisplay.globalPixelToPixel(mousePos);
			var g:Graphics = main.tilesDisplay._layer.graphics;
			g.beginFill(0xff0000);
			g.drawCircle(p.x,p.y,2);
			g.lineStyle(2,0x00FFFF);
			var p:Point = main.tilesDisplay.tilePosToPixel(new Point(Math.floor(pos.x),Math.floor(pos.y))) ;
			g.moveTo(p.x, p.y);
			var p:Point = main.tilesDisplay.tilePosToPixel(new Point(Math.ceil(pos.x),Math.floor(pos.y))) ;
			g.lineTo(p.x, p.y);
			var p:Point = main.tilesDisplay.tilePosToPixel(new Point(Math.ceil(pos.x),Math.ceil(pos.y))) ;
			g.lineTo(p.x, p.y);
			var p:Point = main.tilesDisplay.tilePosToPixel(new Point(Math.floor(pos.x),Math.ceil(pos.y))) ;
			g.lineTo(p.x, p.y);
			var p:Point = main.tilesDisplay.tilePosToPixel(new Point(Math.floor(pos.x),Math.floor(pos.y))) ;
			g.lineTo(p.x, p.y);*/
			
			var clickedTile:Tile = tilesDisplay.displayedTiles.getTileAt(Math.floor(pos.x),Math.floor(pos.y)) as Tile;
			//main.log(pos);
			inst.pos = pos;
			if(clickedTile){
				inst.tile = clickedTile;
				
				var dist:Number = Point.distance(inst.user.tile.posPt,clickedTile.posPt)
				if(isNaN(inst.ability.range) || dist<=inst.ability.range){
					return useOnTile(clickedTile,inst);
				}else{
					trace('out of range');
					var path:Path = new Path();
					path.from = inst.user.tile;
					path.to = clickedTile;
					path.range = inst.ability.range;
					if(inst.user.walkPath(path)){
						return useOnTile(clickedTile,inst);
					}
				}
			}else{
				trace('cant use ability there');
			}
			
			return false;
		}
		
		protected function useOnTile(tile:Tile,inst:AbilityInstance){
			return false;
		}
		
		
		//////////// Event Handlers functions ////////////

	}
	
}
