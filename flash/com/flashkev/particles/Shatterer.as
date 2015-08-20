package com.flashkev.particles{
	import flash.display.BitmapData;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.geom.Matrix;
	import flash.geom.ColorTransform;
	import flash.display.Sprite;
	import flash.display.MovieClip;
	import flash.display.Bitmap;
	
	
	public class Shatterer extends Object{
		//////////// variables ////////////
		private var _shatterTarget:MovieClip = null;
		private var _cols:Number = 5;
		private var _rows:Number = 5;
		private var _pushPoint:Point;
		private var _pushStrength:Number = 25;
		private var _pushPenetration:Number = 10;
		private var _shardClass:Class = MovieClip;
		
		
		private var sourceBitmap:BitmapData;
		private var shatterMatrix:Array;
		private var shards:Array;
		
		//////////// Constructor ////////////
		public function Shatterer() {
			shards = new Array();
			_pushPoint = new Point(0,0);
		}
			
		//////////// Properties functions ////////////
		public function get shatterTarget():MovieClip{
			return _shatterTarget;
		}
		
		public function set shatterTarget(val:MovieClip){
			_shatterTarget = val;
		}
		
		public function get cols():Number{
			return _cols;
		}
		
		public function set cols(val:Number){
			_cols = val;
		}
		
		public function get rows():Number{
			return _rows;
		}
		
		public function set rows(val:Number){
			_rows = val;
		}
		
		public function get pushStrength():Number{
			return _pushStrength;
		}
		
		public function set pushStrength(val:Number){
			_pushStrength = val;
		}
		
		public function get shardClass():Class{
			return _shardClass;
		}
		
		public function set shardClass(val:Class){
			_shardClass = val;
		}
		
		/*public function get pushX():Number{
			return _pushPoint.x;
		}
		
		public function set pushX(val:Number){
			_pushPoint.x = val;
		}
		
		public function get pushY():Number{
			return _pushPoint.y;
		}
		
		public function set pushY(val:Number){
			_pushPoint.y = val;
		}*/
			
		//////////// Public functions ////////////
		public function start(){
			if(_shatterTarget){
				shatterMatrix = new Array();
				for(var i:Number= 0; i<=_rows; i++){
					for(var j:Number= 0; j<=_cols; j++){
						var index:Number = j*(_rows+1)+i;
						shatterMatrix[index] = new Point(_shatterTarget.x + j*_shatterTarget.width/_cols, _shatterTarget.y + i*_shatterTarget.height/_rows);
						if(j > 0 && j < _cols){
							shatterMatrix[index].x += (Math.random()-0.5)*_shatterTarget.width/_cols;
						}
						if(i > 0 && i < _rows){
							shatterMatrix[index].y += (Math.random()-0.5)*_shatterTarget.height/_rows;
						}
						shatterMatrix[index].x =  Math.round(shatterMatrix[index].x);
						shatterMatrix[index].y =  Math.round(shatterMatrix[index].y);
					}
				}
				sourceBitmap = new BitmapData(_shatterTarget.width, _shatterTarget.height, true, 0x00000000);
				sourceBitmap.draw(_shatterTarget);
				for(var i:Number= 0; i<_rows; i++){
					for(var j:Number= 0; j<_cols; j++){
						createShard(
									shatterMatrix[j*(_rows+1)+i],
									shatterMatrix[(j+1)*(_rows+1)+i],
									shatterMatrix[(j+1)*(_rows+1)+i+1]
								);
						createShard(
									shatterMatrix[j*(_rows+1)+i],
									shatterMatrix[(j)*(_rows+1)+i+1],
									shatterMatrix[(j+1)*(_rows+1)+i+1]
								);
					}
				}
				/*createShard(
							new Point(_shatterTarget.x+30,_shatterTarget.y+20),
							new Point(_shatterTarget.x+_shatterTarget.width-30,_shatterTarget.y+20),
							new Point(_shatterTarget.x+_shatterTarget.width-30,_shatterTarget.y+_shatterTarget.height-20)
						);*/
				_shatterTarget.visible = false;
			}
		}
		//////////// Private functions ////////////
		private function createShard(pt1:Point,pt2:Point,pt3:Point){
			var shard:MovieClip = new shardClass() as MovieClip;
			if(shard){
				var rect:Rectangle = new Rectangle();
				rect.x = Math.min(pt1.x,Math.min(pt2.x,pt3.x));
				rect.y = Math.min(pt1.y,Math.min(pt2.y,pt3.y));
				rect.width = Math.max(pt1.x,Math.max(pt2.x,pt3.x)) - rect.x;
				rect.height = Math.max(pt1.y,Math.max(pt2.y,pt3.y)) - rect.y;
			
				shard.x = rect.x;
				shard.y = rect.y;
				shard.graphics.beginFill(0xFF0000);
				shard.graphics.moveTo(pt1.x-rect.x, pt1.y-rect.y);
				shard.graphics.lineTo(pt2.x-rect.x, pt2.y-rect.y);
				shard.graphics.lineTo(pt3.x-rect.x, pt3.y-rect.y);
				shard.graphics.lineTo(pt1.x-rect.x, pt1.y-rect.y);
				shard.graphics.endFill();
				var triangleBitmap:BitmapData = new BitmapData(rect.width, rect.height, true, 0x00CCCCCC);
				triangleBitmap.draw(shard);
				shard.graphics.clear();
				
				var shardBitmap:BitmapData = new BitmapData(rect.width, rect.height, true, 0x00CCCCCC);
				var clipRect:Rectangle = new Rectangle(rect.x-_shatterTarget.x, rect.y-_shatterTarget.y, rect.width, rect.height);
				shardBitmap.copyPixels(sourceBitmap, clipRect, new Point(0,0), triangleBitmap, new Point(0,0), false);
				
				var emptyBitmap:BitmapData = new BitmapData(rect.width, rect.height, true, 0x00000000);
				if(emptyBitmap.compare(sourceBitmap) == 0){
				}else{
					shard.addChild(new Bitmap(shardBitmap));
					_shatterTarget.parent.addChildAt(shard,_shatterTarget.parent.getChildIndex(_shatterTarget));
					shards.push(shard);
					//var dist:Point = new Point(shard.x-_pushPoint.x,shard.y-_pushPoint.y);
					var dist:Point = new Point((Math.random()-0.5)*200,Math.random()*-50);
					shard.velocity = new Point(_pushStrength/(dist.x/_pushPenetration+sign(dist.x)),_pushStrength/(dist.y/_pushPenetration+sign(dist.y)));
					shard.rotationVelocity = (Math.random()-0.5)*5;
				}
			}
		}
		
		private function sign(num:Number) {
			return num < 0 ? -1 : 1;
		}
		
		//////////// Event Handlers functions ////////////
		
	}
}
