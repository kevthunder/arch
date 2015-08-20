package com.flashkev.particles{
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.display.DisplayObject;
	import flash.utils.getTimer;
	import caurina.transitions.Equations;
	
	public class ParticleTranslation extends ParticleMod{
		//////////// variables ////////////
		protected var _startTime:uint;
		protected var _timeSpawn:uint;
		protected var _targetData:Object;
		protected var _startData:Object;
		protected var _easing:Object;
		protected var _initTime:uint;
		
		//////////// Static variables ////////////
		public static var defaultTranslations:Object = {
			"easenone" : Equations.easeNone,
			"linear" : Equations.easeNone,
			
			"easeinquad" : Equations.easeInQuad,
			"easeoutquad" : Equations.easeOutQuad,
			"easeinoutquad" : Equations.easeInOutQuad,
			"easeoutinquad" : Equations.easeOutInQuad,
			
			"easeincubic" : Equations.easeInCubic,
			"easeoutcubic" : Equations.easeOutCubic,
			"easeinoutcubic" : Equations.easeInOutCubic,
			"easeoutincubic" : Equations.easeOutInCubic,
			
			"easeinquart" : Equations.easeInQuart,
			"easeoutquart" : Equations.easeOutQuart,
			"easeinoutquart" : Equations.easeInOutQuart,
			"easeoutinquart" : Equations.easeOutInQuart,
			
			"easeinquint" : Equations.easeInQuint,
			"easeoutquint" : Equations.easeOutQuint,
			"easeinoutquint" : Equations.easeInOutQuint,
			"easeoutinquint" : Equations.easeOutInQuint,
			
			"easeinsine" : Equations.easeInSine,
			"easeoutsine" : Equations.easeOutSine,
			"easeinoutsine" : Equations.easeInOutSine,
			"easeoutinsine" : Equations.easeOutInSine,
			
			"easeincirc" : Equations.easeInCirc,
			"easeoutcirc" : Equations.easeOutCirc,
			"easeinoutcirc" : Equations.easeInOutCirc,
			"easeoutincirc" : Equations.easeOutInCirc,
			
			"easeinexpo" : Equations.easeInExpo,
			"easeoutexpo" : Equations.easeOutExpo,
			"easeinoutexpo" : Equations.easeInOutExpo,
			"easeoutinexpo" : Equations.easeOutInExpo,
			
			"easeinelastic" : Equations.easeInElastic,
			"easeoutelastic" : Equations.easeOutElastic,
			"easeinoutelastic" : Equations.easeInOutElastic,
			"easeoutinelastic" : Equations.easeOutInElastic,
			
			"easeinback" : Equations.easeInBack,
			"easeoutback" : Equations.easeOutBack,
			"easeinoutback" : Equations.easeInOutBack,
			"easeoutinback" : Equations.easeOutInBack,
			
			"easeinbounce" : Equations.easeInBounce,
			"easeoutbounce" : Equations.easeOutBounce,
			"easeinoutbounce" : Equations.easeInOutBounce,
			"easeoutinbounce" : Equations.easeOutInBounce
		};
	
		//////////// Constructor ////////////
		public function ParticleTranslation(targetData:Object = null, timeSpawn:uint = 2000, easing:Object=null, startData:Object = null, startTime:uint = 0) {
			_startTime = startTime;
			_timeSpawn = timeSpawn;
			_targetData = targetData;
			_startData = startData;
			_easing = easing;
		}
			
		//////////// Properties functions ////////////
		public function get startTime():uint{
			return _startTime;
		}
		public function set startTime(val:uint){
			_startTime = val;
		}
		public function get timeSpawn():uint{
			return _timeSpawn;
		}
		public function set timeSpawn(val:uint){
			_timeSpawn = val;
		}
		public function get targetData():Object{
			return _targetData;
		}
		public function set targetData(val:Object){
			_targetData = val;
		}
		public function get startData():Object{
			return _startData;
		}
		public function set startData(val:Object){
			_startData = val;
		}
		public function get easing():Object{
			return _easing;
		}
		public function set easing(val:Object){
			_easing = val;
		}
		
			
		//////////// Public functions ////////////
		override public function onInit(){
			_initTime = getTimer();
		}
		
		override public function onTick(){
			var curTime:uint = getTimer() - _initTime;
			if(targetData && curTime >= startTime && curTime <= startTime + timeSpawn){
				if(startData == null){
					startData = new Object();
				}
				var prc:Number = (curTime-startTime)/timeSpawn;
				for(var prop:String in targetData){
					var ptPos:int;
					var target:Object;
					var propName:String = prop;
					if((ptPos = prop.indexOf('.')) != -1){
						target = particle.getMod(prop.substr(0,ptPos));
						propName = prop.substr(ptPos+1);
					}else{
						target = particle;
					}
					if(target && target.hasOwnProperty(propName)){
						if(!startData.hasOwnProperty(prop)){
							startData[prop] = target[propName];
						}
						target[propName] = ease(_easing,curTime-startTime,startData[prop],getChange(targetData[prop],startData[prop]),timeSpawn);
					}
				}
			}
		}
		
		override public function clone():ParticleMod{
			var clone = new ParticleTranslation();
			clone.priority = this.priority;
			clone.startTime = this.startTime;
			clone.timeSpawn = this.timeSpawn;
			clone.targetData = this.targetData;
			clone.startData = this.startData;
			clone.easing = this.easing;
			return clone;
		}
		
		
		//////////// Private functions ////////////
		protected function getChange(target,start=0){
			var modificators:Array = [
				{ patern : '^\\+=(.*)$', funct : function(a:int,b:int){
					return a+b;
				} },
				{ patern : '^-=(.*)$', funct : function(a,b){
					return a-b;
				} },
				{ patern : '^\\*=(.*)$', funct : function(a,b){
					return a*b;
				} }
			];
			for(var i:int = 0; i< modificators.length; i++){
				var patern:RegExp = new RegExp(modificators[i].patern);
				var res:Object = patern.exec(target);
				if(res != null){
					var params:Array = [start];
					for(var j:int = 1; j< res.length; j++){
						params.push(res[j]);
					}
					return modificators[i].funct.apply(this,params) - start;
				}
			}
			return target - start;
			
		}
		protected function ease(easing:Object ,t:Number, b:Number, c:Number, d:Number, p_params:Object = null){
			var easingFunct:Function;
			if(easing is Function){
				easingFunct = easing as Function;
			}else if(defaultTranslations.hasOwnProperty(easing.toString().toLocaleLowerCase())){
				easingFunct = defaultTranslations[easing.toString().toLocaleLowerCase()];
			}
			if(easingFunct == null){
				easingFunct = Equations.easeNone;
			}
			try{
				return easingFunct(t,b,c,d,p_params);
			}catch(e:Error){
				if(e is ArgumentError){
					return easingFunct(t,b,c,d);
				}else{
					throw(e);
				}
			}
		}
			
		//////////// Event Handlers functions ////////////
		
	}
}
