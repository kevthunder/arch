package com.flashkev.genericList{
	
	import fl.data.DataProvider;
	import fl.events.DataChangeEvent;
	import fl.events.DataChangeType;
	
	import fl.core.UIComponent;
	import fl.core.InvalidationType;
	
	import com.flashkev.utils.ArrayUtil;
	
	
	public class GenericList extends UIComponent{
		//////////// variables ////////////
		protected var _dataProvider:DataProvider;
		protected var renderers:Array = new Array();
		protected var changedItems:Array;
		
		private static var defaultStyles:Object = {
				skin:"List_skin",
				cellRenderer:GenericRenderer
			};
		
		//////////// Constructor ////////////
        public function GenericList() {
			super();
        }
		//////////// Properties functions ////////////
		public function get dataProvider():DataProvider {
			// return the original data source:
			return _dataProvider;
		}
		
		[Collection(collectionClass="fl.data.DataProvider", collectionItem="fl.data.SimpleCollectionItem", identifier="item")]
		public function set dataProvider(value:DataProvider):void {
			if (_dataProvider != null) {
				_dataProvider.removeEventListener(DataChangeEvent.DATA_CHANGE,dataChangeHandler);
				_dataProvider.removeEventListener(DataChangeEvent.PRE_DATA_CHANGE, preChangeHandler);
			}
			_dataProvider = value;
			_dataProvider.addEventListener(DataChangeEvent.DATA_CHANGE,dataChangeHandler,false,0,true);
			_dataProvider.addEventListener(DataChangeEvent.PRE_DATA_CHANGE,preChangeHandler,false,0,true);
			invalidateList();
		}
		
		public function get length():uint {
			return _dataProvider.length;
		}
		
		//////////// Public functions ////////////
		public static function getStyleDefinition():Object { 
			return UIComponent.mergeStyles(defaultStyles,UIComponent.getStyleDefinition());
		}
		public function invalidateList():void {
			invalidate(InvalidationType.DATA);
		}
		//// proxied dataProvider functions ////
		public function addItem(item:Object):void {
			if(getItemIndex(item) == -1){
				_dataProvider.addItem(item);
				invalidateList();
			}
		}
		public function addItemAt(item:Object,index:uint):void {
			if(getItemIndex(item) == -1){
				_dataProvider.addItemAt(item,index);
				invalidateList();
			}
		}
		public function removeAll():void {
			_dataProvider.removeAll();
		}
		public function getItemAt(index:uint):Object {
			return _dataProvider.getItemAt(index);
		} 
		public function getItemIndex(item:Object):int {
			return _dataProvider.getItemIndex(item);
		} 
		public function removeItem(item:Object):Object {
			return _dataProvider.removeItem(item);
		}
		public function removeItemAt(index:uint):Object {
			return _dataProvider.removeItemAt(index);
		}
		public function replaceItemAt(item:Object, index:uint):Object {
			return _dataProvider.replaceItemAt(item, index);
		}
		//////////// Private functions ////////////
		protected function removeRenderer(index:int){
			renderers[index].dispatchEvent(new GenericRendererEvent(GenericRendererEvent.REMOVE));
		}
		protected function createRenderer(data:Object,index:int):IGenericRenderer{
			var rendererClass:Class = getStyleValue("cellRenderer") as Class;
			var renderer:IGenericRenderer;
			if(rendererClass){
				renderer = new rendererClass() as IGenericRenderer;
				renderer.data = data;
				renderer.ownerList = this;
				if(!renderer){
					trace("Invalid renderer Class");
				}
			}else{
				trace("Invalid renderer Class");
			}
			return renderer;
		}
		protected function initRenderer(renderer:IGenericRenderer,pos:int){
			renderer.dispatchEvent(new GenericRendererEvent(GenericRendererEvent.INIT));
		}
		protected function moveRenderer(oldPos:int,pos:int){
			renderers[oldPos].dispatchEvent(new GenericRendererEvent(GenericRendererEvent.MOVE));
		}
		
		override protected function configUI():void {
			dataProvider = new DataProvider();
			super.configUI();
		}
		
		override protected function draw():void {
			drawLayout();
			
			super.draw();
		}
		protected function drawLayout():void {
			if (isInvalid(InvalidationType.DATA)) {
				drawList();
			}
		}
		protected function drawList():void {
			var i:int;
			changedItems = getRenderersData();
			if(_dataProvider){
				changedItems = ArrayUtil.union(changedItems,_dataProvider.toArray());
			}
			var newRenderers:Array = renderers.slice();
			for(i=0; i<changedItems.length; i++){
				var item = changedItems[i];
				var posInProvider:int = -1;
				if(_dataProvider){
					posInProvider = _dataProvider.getItemIndex(item);
				}
				var posInRenderer:int = getItemRendererIndex(item);
				var renderer:IGenericRenderer;
				if(posInRenderer != -1 && newRenderers.hasOwnProperty(i) && newRenderers[i].data == item){
					delete newRenderers[i];
				}
				if(posInProvider == -1){
					removeRenderer(posInRenderer);
				}else{
					if(posInRenderer == -1){
						renderer = createRenderer(item,posInProvider);
						initRenderer(renderer,posInProvider);
					}else{
						renderer = getRendererAt(posInRenderer);
						if(posInRenderer != posInProvider){
							moveRenderer(posInRenderer,posInProvider);
						}
					}
					newRenderers[posInProvider] = renderer;
				}
			}
			renderers = ArrayUtil.trimRight(newRenderers);
		}
		
		protected function getItemRendererIndex(data):int{
			for(var i:int=0; i<renderers.length; i++){
				if(renderers[i].data==data){
					return i;
				}
			}
			return -1;
		}
		protected function getItemRenderer(data):IGenericRenderer{
			return getRendererAt(getItemRendererIndex(data));
		}
		protected function getRendererAt(index:int):IGenericRenderer{
			return renderers[index];
		}
		protected function getRenderersData(start:int=0,end:int=int.MAX_VALUE){
			var res:Array = new Array();
			if(renderers){
				end = Math.min(end,renderers.length-1);
				var i:int;
				for(i=start; i<=end; i++){
					res.push(renderers[i].data);
				}
			}
			return res;
		}
		
		
		//////////// Event Handlers functions ////////////
		protected function dataChangeHandler(event:DataChangeEvent):void {
			switch (event.changeType) {
				case DataChangeType.REMOVE:
				case DataChangeType.ADD:
				case DataChangeType.INVALIDATE:
				case DataChangeType.REMOVE_ALL:
				case DataChangeType.REPLACE:
				case DataChangeType.INVALIDATE_ALL:
				case DataChangeType.CHANGE:
				case DataChangeType.SORT:
					break;
				default:
					break;
			}
			invalidate(InvalidationType.DATA);
		}
		protected function preChangeHandler(event:DataChangeEvent):void {
			switch (event.changeType) {
				case DataChangeType.REMOVE:
				case DataChangeType.ADD:
				case DataChangeType.INVALIDATE:
				case DataChangeType.REMOVE_ALL:
				case DataChangeType.REPLACE:
				case DataChangeType.INVALIDATE_ALL:
				case DataChangeType.CHANGE:
				case DataChangeType.SORT:
					break;
				default:
					break;
			}
		}
		
	}
}