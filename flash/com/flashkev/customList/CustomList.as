//v0.1.6.4
package com.flashkev.customList {
	import flash.display.DisplayObject;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.events.Event;
	import flash.utils.Dictionary;
	import flash.utils.getDefinitionByName;
	
	import fl.core.UIComponent;
	import fl.core.InvalidationType;
    import fl.controls.SelectableList;
	import fl.controls.ScrollBar;
    import fl.controls.ScrollPolicy;
    import fl.controls.ScrollBarDirection;
	import fl.controls.listClasses.ICellRenderer;
	import fl.controls.listClasses.CellRenderer;
	import fl.controls.listClasses.ImageCell;
	import fl.controls.listClasses.ListData;
	import fl.controls.listClasses.TileListData;
	import fl.events.ListEvent;
	import fl.events.ScrollEvent;
	
	import com.flashkev.utils.Margins;
	import com.flashkev.utils.ArrayUtil;
	import com.flashkev.display.CustomScrollBar;
	import com.flashkev.state.IStated;
	import com.flashkev.state.StateEvent;
	
	[Style(name="cellSpacing", type="Number")]
	public class CustomList extends SelectableList{
		//////////// variables ////////////
		protected var _mainDirection:String = CustomListDirection.HORIZONTAL;
		protected var _horizontalDirection:String = CustomListDirection.LEFT_TO_RIGHT;
		protected var _verticalDirection:String = CustomListDirection.TOP_TO_BOTTOM;
		protected var _minCellWidth:Number = 1;
		protected var _minCellHeight:Number = 1;
		protected var _maxCellWidth:Number = 4096;
		protected var _maxCellHeight:Number = 4096;
		
		protected var margins:Margins;
		protected var cellRenderers:Array;
		
		protected var _customStates:Array;
		protected var _sourceField:String = "source";
		
		
		private static var defaultStyles:Object = {
				"margins":false,
				"skin":Invisible,
				"cellRenderer":CustomCell,
				"multipleToggle":false,
				"cellSpacing":1,
				"nbColumn":false,
				"stretchedColumn":false,
				"autoSize":AUTO_NONE,
				"minHeight":1,
				"minWidth":1,
				"maxHeight":4096,
				"maxWidth":4096,
				"scrollBarClass":ScrollBar
			};
		
		public static const AUTO_NONE:String = 'none';
		public static const AUTO_HORIZONTAL:String = 'horizontal';
		public static const AUTO_VERTICAL:String = 'vertical';
		public static const AUTO_BOTH:String = 'both';
			
		//////////// Constructor ////////////
		public function CustomList(){
			SCROLL_BAR_STYLES.width = "scrollBarWidth";
			SCROLL_BAR_STYLES.upArrowHeight = "upArrowHeight";
			SCROLL_BAR_STYLES.downArrowHeight = "downArrowHeight";
			SCROLL_BAR_STYLES.thumbFixedHeight = "thumbFixedHeight";
			cellRenderers = [];
		}
      
		//////////// Properties functions ////////////
		[Inspectable(enumeration="horizontal,vertical", defaultValue="horizontal")]
		public function get mainDirection():String { 
			return _mainDirection;
		}
		public function set mainDirection(value:String):void {
			_mainDirection = value;
			invalidate(InvalidationType.STYLES);
		}
		
		[Inspectable(enumeration="left to right,right to left", defaultValue="left to right")]
		public function get horizontalDirection():String { 
			return _horizontalDirection;
		}
		public function set horizontalDirection(value:String):void {
			_horizontalDirection = value;
			invalidate(InvalidationType.STYLES);
		}
		
		[Inspectable(enumeration="top to bottom,bottom to top", defaultValue="top to bottom")]
		public function get verticalDirection():String { 
			return _verticalDirection;
		}
		public function set verticalDirection(value:String):void {
			_verticalDirection = value;
			invalidate(InvalidationType.STYLES);
		}
		
		
		public function get sourceField():String { 
			return _sourceField;
		}
		public function set sourceField(value:String):void {
			_sourceField = value;
			invalidate(InvalidationType.DATA);
		}
		
		[Inspectable(defaultValue=1, type="Number")]
		public function get minCellWidth():Number{
			return _minCellWidth;
		}
		public function set minCellWidth(val:Number):void{
			_minCellWidth = val;
			invalidate(InvalidationType.SIZE);
		}
		
		[Inspectable(defaultValue=1, type="Number")]
		public function get minCellHeight():Number{
			return _minCellHeight;
		}
		public function set minCellHeight(val:Number):void{
			_minCellHeight = val;
			invalidate(InvalidationType.SIZE);
		}
		
		[Inspectable(defaultValue=4096, type="Number")]
		public function get maxCellWidth():Number{
			return _maxCellWidth;
		}
		public function set maxCellWidth(val:Number):void{
			_maxCellWidth = val;
			invalidate(InvalidationType.SIZE);
		}
		
		[Inspectable(defaultValue=4096, type="Number")]
		public function get maxCellHeight():Number{
			return _maxCellHeight;
		}
		public function set maxCellHeight(val:Number):void{
			_maxCellHeight = val;
			invalidate(InvalidationType.SIZE);
		}
		
		public function get customStates():Array{
			return _customStates;
		}
		
		public function set customStates(val:Array){
			_customStates = val;
		}
		//////////// Public functions ////////////
		public static function getStyleDefinition():Object { 
			return UIComponent.mergeStyles(defaultStyles, SelectableList.getStyleDefinition());
		}
		
		public function scrollTo(i:int):Boolean{
			var oldScrollH = horizontalScrollPosition;
			var oldScrollV = verticalScrollPosition;
			var item:Object = _dataProvider.getItemAt(i);
			var renderer:CellRenderer = itemToCellRenderer(item) as CellRenderer;
			
			//trace(oldScrollH,oldScrollV)
			horizontalScrollPosition = renderer.x;
			verticalScrollPosition = renderer.y;
			return (oldScrollH != horizontalScrollPosition || oldScrollV != verticalScrollPosition);
		}
		public function getItemIndex(item:Object):int {
			return _dataProvider.getItemIndex(item);
		} 
		public function getItemsIndexes(items:Object):Array {
			var ids:Array = new Array();
			if(_dataProvider){
				if(!(items is Array)){
					items = [items];
				};
				var i:int = 0;
				for(i=0; i<items.length; i++){
					var index:int = getItemIndex(items[i]);
					if(index > -1){
						ids.push(index);
					}
				}
			}
			return ids;
		} 
		public function getItemsAtIndexes(ids:Object):Array {
			var items:Array = new Array();
			if(_dataProvider){
				if(!(ids is Array)){
					ids = [ids];
				};
				var i:int = 0;
				for(i=0; i<ids.length; i++){
					var item:Object = getItemAt(ids[i]);
					if(item){
						items.push(item);
					}
				}
			}
			return items;
		} 
		
		public function setItemsStateVal(stateName:String, stateVal:Object, items:Object = "all"):int{
			if(_dataProvider){
				if(items == "all"){
					items = _dataProvider.toArray();
				}else if(!(items is Array)){
					items = [items];
				};
				var i:int = 0;
				var j:int = 0;
				var ids:Array = new Array();
				for(i=0; i<items.length; i++){
					if(setItemStateVal(stateName,stateVal,items[i])){
						j++;
					}
				}
				return j;
			}
			return 0;
		}
		public function setItemsIdStateVal(stateName:String, stateVal:Object, items:Object = "all"):int{
			return setItemsStateVal(stateName, stateVal, getItemsAtIndexes(items));
			
		}
		public function setItemStateVal(stateName:String, stateVal:Object, items:Object):Boolean{
			if(_dataProvider){
				var renderer:IStated = itemToCellRenderer(items) as IStated;
				if(renderer){
					return renderer.setStateVal(stateName,stateVal);
				}
			}
			return false;
		}
		public function setItemsStateSwitch(stateName:String, items:Object = "all", stateVal:Boolean = true):int{
			if(_dataProvider){
				if(items == "all"){
					items = _dataProvider.toArray();
				}else if(!(items is Array)){
					items = [items];
				};
				var inverseCount:int = setItemsStateVal(stateName, !stateVal, ArrayUtil.diff(_dataProvider.toArray(),items as Array));
				return setItemsStateVal(stateName, stateVal, items);
			}
			return 0;
		}
		public function setItemsIdStateSwitch(stateName:String, items:Object = "all", stateVal:Boolean = true):int{
			return setItemsStateSwitch(stateName, getItemsAtIndexes(items), stateVal);
		}
		public function getMachingStateItems(stateName:String, stateVal:Object):Array{
			var items:Array = new Array();
			if(_dataProvider){
				var i:int;
				for(i=0; i<cellRenderers.length; i++){
					var renderer:IStated = cellRenderers[i] as IStated;
					if(renderer){
						var state = renderer.getStateVal(stateName);
						if(state == stateVal){
							items.push((renderer as CellRenderer).data);
						}
					}
				}
			}
			return items;
		}
		public function getMachingStateItemsId(stateName:String, stateVal:Object):Array{
			return getItemsIndexes(getMachingStateItems(stateName,stateVal));
		}
		
		
		//////////// Private functions ////////////
		override protected function configUI():void {
			//useFixedHorizontalScrolling = true;
			_horizontalScrollPolicy = ScrollPolicy.AUTO;
			_verticalScrollPolicy = ScrollPolicy.ON;
			
			super.configUI();
		}
		
		override protected function setHorizontalScrollPosition(scroll:Number,fireEvent:Boolean=false):void {
			list.x = -scroll;
			invalidate(InvalidationType.SCROLL);
			super.setHorizontalScrollPosition(scroll, true);
		}
		
		override protected function setVerticalScrollPosition(scroll:Number,fireEvent:Boolean=false):void {
			list.y = -scroll;
			invalidate(InvalidationType.SCROLL);
			super.setVerticalScrollPosition(scroll, true);
		}
		
		override protected function draw():void {
			if (isInvalid(InvalidationType.STYLES)){
				drawScrollBars();
			}
			if (isInvalid(InvalidationType.STYLES,InvalidationType.SIZE,InvalidationType.DATA,InvalidationType.SELECTED)) {
				drawList();
			}
			if (isInvalid(InvalidationType.RENDERER_STYLES)){
				updateRendererStyles()
			}
			if (isInvalid(InvalidationType.STYLES)) {
				// drawLayout is expensive, so only do it if margin has changed:
				if (margins != getStyleValue("margins")) {
					invalidate(InvalidationType.SIZE,false);
				}
			}
			super.draw();
		}
		
		override public function itemToLabel(item:Object):String {
			if(item.hasOwnProperty("label")){
				return item["label"];
			}
			return "";
		}		
		
		override protected function drawLayout():void {
			updateSize();
			calculateAvailableSize();
			calculateContentWidth();
			
			background.width = width;
			background.height = height;

			resizeScrollBars();
			
			drawDisabledOverlay();
			
			drawListHolder();
		}
		
		override protected function drawList():void {
			var i:uint;
			var item:Object;
			var renderer:ICellRenderer;
			var col = 0;
			var row = 0;
			var dist_row = 0;
			var max_dist_row = 0;
			var dist_col = 0;
			var cur_dist_col = 0;
			
			var cellSpacing:Number = getStyleValue("cellSpacing") as Number;
			if(isNaN(cellSpacing)){
				cellSpacing = 0;
			}
			
			if(isNaN(availableWidth)){
				availableWidth = width;
			}
			if(isNaN(availableHeight)){
				availableHeight = height;
			}
			
			var nbColumn:Number = getStyleValue("nbColumn") as Number;
			var stretchedColumn:Boolean = getStyleValue("stretchedColumn") as Boolean;
			if(stretchedColumn){
				if(nbColumn > 0){
					nbColumn = Math.min(_dataProvider.length,nbColumn);
				}else{
					nbColumn = _dataProvider.length;
				}
			}
			if(nbColumn > 0){
				if(_mainDirection == CustomListDirection.HORIZONTAL){
					minCellWidth = maxCellWidth = (availableWidth-(nbColumn-1)*cellSpacing)/nbColumn;
				}else{
					minCellHeight = maxCellHeight = (availableHeight-(nbColumn-1)*cellSpacing)/nbColumn;
				}
			}
			
			var curRenderers = [];
			for(i=0; i<_dataProvider.length; i++){
				//renderer = drawRenderer(i);
				//curRenderers.push(renderer);
				item = _dataProvider.getItemAt(i);
				renderer = null;
				for (var index in cellRenderers){
					if(cellRenderers[index].data === item){
						renderer = cellRenderers[index];
						break;
					}
				}
				//renderer = itemToCellRenderer(item);
				if(renderer == null){
					renderer = getDisplayObjectInstance(getStyleValue("cellRenderer")) as ICellRenderer;
					var rendererSprite:Sprite = renderer as Sprite;
					if (rendererSprite != null) {
						rendererSprite.addEventListener(MouseEvent.CLICK,handleCellRendererClick,false,0,true);
						rendererSprite.addEventListener(MouseEvent.ROLL_OVER,handleCellRendererMouseEvent,false,0,true);
						rendererSprite.addEventListener(MouseEvent.ROLL_OUT,handleCellRendererMouseEvent,false,0,true);
						rendererSprite.addEventListener(Event.CHANGE,handleCellRendererChange,false,0,true);
						rendererSprite.doubleClickEnabled = true;
						rendererSprite.addEventListener(MouseEvent.DOUBLE_CLICK,handleCellRendererDoubleClick,false,0,true);
						
						if (rendererSprite["setStyle"] != null) {
							for (var n:String in rendererStyles) {
								rendererSprite["setStyle"](n, rendererStyles[n])
							}
						}
					}
					var rendererStated:IStated = renderer as IStated;
					if (rendererStated) {
						if(customStates){
							for(var j:int = 0; j<customStates.length; j++){
								rendererStated.states.addState(customStates[j].clone());
							}
						}
						rendererStated.states.addEventListener(StateEvent.STATE_CHANGE,stateChangeHandler);
					}
					renderer.data = item;
				}else{
					//
				}
				curRenderers.push(renderer);
				
				if(!list.contains(renderer as Sprite)){
					list.addChild(renderer as Sprite);
				}
				
				var label:String = itemToLabel(item);
				var source:Object = null;
				if (_sourceField != null && item.hasOwnProperty(_sourceField)) {
					source = item[_sourceField];
				}
				
				renderer.selected = (_selectedIndices.indexOf(i) != -1);
				renderer.listData = new TileListData(label,null,source,this,i,row,col) as ListData;
				
				var autoSizeRenderer:IAutoSizeCell = renderer as IAutoSizeCell;
				if(autoSizeRenderer){
					autoSizeRenderer.minWidth = minCellWidth;
					autoSizeRenderer.minHeight = minCellHeight;
					autoSizeRenderer.maxWidth = maxCellWidth;
					autoSizeRenderer.maxHeight = maxCellHeight;
				}
				var uiRenderer:UIComponent = renderer as UIComponent;
				uiRenderer.drawNow();
				if(!autoSizeRenderer){
					if(uiRenderer.width < _minCellWidth || uiRenderer.height < _minCellHeight){
						uiRenderer.setSize(Math.max(uiRenderer.width,_minCellWidth),Math.max(uiRenderer.height,_minCellHeight));
						uiRenderer.drawNow();
					}
					if(uiRenderer.width > _maxCellWidth || uiRenderer.height > _maxCellHeight){
						uiRenderer.setSize(Math.min(uiRenderer.width,_maxCellWidth),Math.min(uiRenderer.height,_maxCellHeight));
						uiRenderer.drawNow();
					}
				}
				
				var availableRowSize:Number = 0;
				var cellSize:Number = 0;
				if(_mainDirection == CustomListDirection.HORIZONTAL){
					availableRowSize = availableWidth
					cellSize = uiRenderer.width
				}else{
					availableRowSize = availableHeight
					cellSize = uiRenderer.height
				}
				if(dist_row+cellSize > availableRowSize){
					dist_col += cur_dist_col + cellSpacing;
					max_dist_row = Math.max(max_dist_row,dist_row-cellSpacing);
					cur_dist_col = 0;
					dist_row = 0;
					col = 0;
					row++;
					renderer.listData = new TileListData(label,null,source,this,i,row,col) as ListData;
				}
				var cur_dist_row;
				if(_mainDirection == CustomListDirection.HORIZONTAL){
					if(_horizontalDirection == CustomListDirection.LEFT_TO_RIGHT){
						uiRenderer.x = dist_row;
					}else{
						uiRenderer.x = availableWidth-dist_row-uiRenderer.width;
					}
					if(_verticalDirection == CustomListDirection.TOP_TO_BOTTOM){
						uiRenderer.y = dist_col;
					}else{
						uiRenderer.y = availableHeight-dist_col-uiRenderer.height;
					}
					dist_row += uiRenderer.width+cellSpacing;
					cur_dist_col = Math.max(cur_dist_col,uiRenderer.height);
				}else{
					if(_horizontalDirection == CustomListDirection.LEFT_TO_RIGHT){
						uiRenderer.x = dist_col;
					}else{
						uiRenderer.x = availableWidth-dist_col-uiRenderer.width;
					}
					if(_verticalDirection == CustomListDirection.TOP_TO_BOTTOM){
						uiRenderer.y = dist_row;
					}else{
						uiRenderer.y = availableHeight-dist_row-uiRenderer.height;
					}
					dist_row += uiRenderer.height+cellSpacing;
					cur_dist_col = Math.max(cur_dist_col,uiRenderer.width);
				}
				col++;
			}
			dist_col += cur_dist_col;
			max_dist_row = Math.max(max_dist_row,dist_row-cellSpacing);
			if(_mainDirection == CustomListDirection.HORIZONTAL){
				if(contentHeight != dist_col){
					contentHeight = dist_col;
					invalidate(InvalidationType.SIZE,false);
				}
				contentWidth = max_dist_row;
			}else{
				contentHeight = max_dist_row;
				if(contentWidth != dist_col){
					contentWidth = dist_col;
					invalidate(InvalidationType.SIZE,false);
				}
				
			}
			
			for(i=0;i<cellRenderers.length;i++){
				if(cellRenderers[i] != null && curRenderers.indexOf(cellRenderers[i]) == -1 && list.contains(cellRenderers[i])){
					list.removeChild(cellRenderers[i]);
				}
			}
			
			availableCellRenderers = [];
			activeCellRenderers = curRenderers;
			cellRenderers = curRenderers.slice();
			
			invalidItems = new Dictionary(true);
			renderedItems = new Dictionary(true);
		}
		
		override protected function calculateAvailableSize():void {
			var scrollBarWidth:Number = ScrollBar.WIDTH;
			
			
			// figure out which scrollbars we need
			var availHeight:Number;
			availHeight = height - margins.top - margins.bottom;
			vScrollBar = (_verticalScrollPolicy == ScrollPolicy.ON) || (_verticalScrollPolicy == ScrollPolicy.AUTO && contentHeight > availHeight);
			
			var availWidth:Number;
			availWidth = width - margins.left - margins.right;
			if (vScrollBar) { availWidth -= scrollBarWidth; }
			
			var maxHScroll:Number = (useFixedHorizontalScrolling) ? _maxHorizontalScrollPosition : contentWidth - availWidth;
			hScrollBar = (_horizontalScrollPolicy == ScrollPolicy.ON) || (_horizontalScrollPolicy == ScrollPolicy.AUTO && maxHScroll > 0);
			if (hScrollBar) { availHeight -= scrollBarWidth; }
			
			// catch the edge case of the horizontal scroll bar necessitating a vertical one:
			if (hScrollBar && !vScrollBar && _verticalScrollPolicy == ScrollPolicy.AUTO && contentHeight > availHeight) {
				vScrollBar = true;
				availWidth -= scrollBarWidth;
			}
			availableHeight = availHeight;
			availableWidth = availWidth;
		}
		
		protected function updateSize():void {
			var autoSize = String(getStyleValue("autoSize"));
			var minHeight = Number(getStyleValue("minHeight"));
			var minWidth = Number(getStyleValue("minWidth"));
			var maxHeight = Number(getStyleValue("maxHeight"));
			var maxWidth = Number(getStyleValue("maxWidth"));
			var padding:Number = contentPadding = Number(getStyleValue("contentPadding"));
			margins = getStyleValue("margins") as Margins;
			if(!margins){
				margins = new Margins(padding,padding,padding,padding);
			}
			
			if(autoSize == AUTO_HORIZONTAL || autoSize == AUTO_BOTH){
				width = Math.min(maxWidth,Math.max(minWidth,contentWidth+margins.left+margins.right));
			}
			if(autoSize == AUTO_VERTICAL || autoSize == AUTO_BOTH){
				height = Math.min(maxHeight,Math.max(minHeight,contentHeight+margins.top+margins.bottom));
			}
			
		}
		
		/*override protected function updateRendererStyles():void {
			
		}*/
		
		protected function drawScrollBars():void {
			// set up vertical scroll bar:
			var oldScrollBar:ScrollBar = _verticalScrollBar;
			_verticalScrollBar = getAvailableDisplayObjectInstance(getStyleValue("scrollBarClass"),[oldScrollBar]) as ScrollBar;
			if (oldScrollBar != _verticalScrollBar) {
				if(_verticalScrollBar != null){
					_verticalScrollBar.addEventListener(ScrollEvent.SCROLL,handleScroll,false,0,true);
					_verticalScrollBar.visible = false;
					_verticalScrollBar.lineScrollSize = defaultLineScrollSize;
					addChild(_verticalScrollBar);
					copyStylesToChild(_verticalScrollBar,SCROLL_BAR_STYLES);
				}
				if(oldScrollBar != null){
					removeChild(oldScrollBar); 
				}
			}
			
			// set up horizontal scroll bar:
			oldScrollBar = _horizontalScrollBar;
			_horizontalScrollBar = getAvailableDisplayObjectInstance(getStyleValue("scrollBarClass"),[oldScrollBar]) as ScrollBar;
			if (oldScrollBar != _horizontalScrollBar) {
				if(_verticalScrollBar != null){
					_horizontalScrollBar.direction = ScrollBarDirection.HORIZONTAL;
					_horizontalScrollBar.addEventListener(ScrollEvent.SCROLL,handleScroll,false,0,true);
					_horizontalScrollBar.visible = false;
					_horizontalScrollBar.lineScrollSize = defaultLineScrollSize;
					addChild(_horizontalScrollBar);
					copyStylesToChild(_horizontalScrollBar,SCROLL_BAR_STYLES);
				}
				if(oldScrollBar != null){
					removeChild(oldScrollBar); 
				}
			}
		}
		
		protected function resizeScrollBars():void {
			if (vScrollBar) {
				_verticalScrollBar.visible = true;
				_verticalScrollBar.x = width - ScrollBar.WIDTH - margins.left;
				_verticalScrollBar.y = margins.top;
				_verticalScrollBar.height = availableHeight;
			} else {
				_verticalScrollBar.visible = false;
			}
			
			_verticalScrollBar.setScrollProperties(availableHeight, 0, contentHeight - availableHeight, verticalPageScrollSize);
			setVerticalScrollPosition(_verticalScrollBar.scrollPosition, false);

			if (hScrollBar) {
				_horizontalScrollBar.visible = true;
				_horizontalScrollBar.x = margins.left;
				_horizontalScrollBar.y = height - ScrollBar.WIDTH - margins.top;
				_horizontalScrollBar.width = availableWidth;
			} else {
				_horizontalScrollBar.visible = false;
			}
			
			_horizontalScrollBar.setScrollProperties(availableWidth, 0, (useFixedHorizontalScrolling) ? _maxHorizontalScrollPosition : contentWidth - availableWidth, horizontalPageScrollSize);
			setHorizontalScrollPosition(_horizontalScrollBar.scrollPosition, false);
		}
		
		override protected function drawDisabledOverlay():void {
			if (enabled) {
				if (contains(disabledOverlay)) { removeChild(disabledOverlay); }
			} else {
				disabledOverlay.x = margins.left;
				disabledOverlay.y = margins.top;
				disabledOverlay.width = availableWidth;
				disabledOverlay.height = availableHeight;
				disabledOverlay.alpha = getStyleValue("disabledAlpha") as Number;
				addChild(disabledOverlay);
			}
		}
		
		protected function drawListHolder(){
			listHolder.x = margins.left;
			listHolder.y = margins.top;
			contentScrollRect = listHolder.scrollRect;
			contentScrollRect.width = availableWidth;
			contentScrollRect.height = availableHeight;
			listHolder.scrollRect = contentScrollRect;
		}
		
		protected function getAvailableDisplayObjectInstance(skin:Object,availableObjects:Array=null,elseNew:Boolean=true):DisplayObject {
			var classDef:Class = null;
			var found:DisplayObject = null;
			if (skin is Class) { 
				classDef = skin as Class; 
			} else if (skin is DisplayObject) {
				(skin as DisplayObject).x = 0;
				(skin as DisplayObject).y = 0;
				return skin as DisplayObject;
			}
			
			try {
				classDef = getDefinitionByName(skin.toString()) as Class;
			} catch(e:Error) {
				try {
					classDef = loaderInfo.applicationDomain.getDefinition(skin.toString()) as Class;
				} catch (e:Error) {
					// Nothing
				}
			}
			
			if (classDef == null) {
				return null;
			}
			
			if(availableObjects){
				for(var i:int = 0;i<availableObjects.length && found == null;i++){
					if(availableObjects[i] is classDef){
						found = availableObjects[i] as DisplayObject;
					}
				}
			}
			
			if(found){
				return found;
			}else if(elseNew){
				return (new classDef()) as DisplayObject;
			}else{
				return null;
			}
			
		}
		
		//////////// Event Handlers functions ////////////
		override protected function handleCellRendererClick(event:MouseEvent):void {
			if (!_enabled) { return; }
			var renderer:ICellRenderer = event.currentTarget as ICellRenderer;
			var itemIndex:uint = renderer.listData.index;
			// this event is cancellable:
			if (!dispatchEvent(new ListEvent(ListEvent.ITEM_CLICK,false,true,renderer.listData.column,renderer.listData.row,itemIndex,renderer.data)) || !_selectable) { return; }
			var selectIndex:int = selectedIndices.indexOf(itemIndex);
			var i:int;
			if (!_allowMultipleSelection) {
				if (selectIndex != -1) {
					return;
				} else {
					renderer.selected = true;
					_selectedIndices = [itemIndex];
				}
				lastCaretIndex = caretIndex = itemIndex;
			} else {
				if (event.shiftKey) {
					var oldIndex:uint = (_selectedIndices.length > 0) ? _selectedIndices[0] : itemIndex;
					_selectedIndices = [];
					if (oldIndex > itemIndex) {
						for (i = oldIndex; i >= itemIndex; i--) {
							_selectedIndices.push(i);
						}
					} else {
						for (i = oldIndex; i <= itemIndex; i++) {
							_selectedIndices.push(i);
						}
					}
					caretIndex = itemIndex;
				} else if (event.ctrlKey || getStyleValue("multipleToggle") as Boolean) {
					if (selectIndex != -1) {
						renderer.selected = false;
						_selectedIndices.splice(selectIndex,1);
					} else {
						renderer.selected = true;
						_selectedIndices.push(itemIndex);
					}
					caretIndex = itemIndex;
				} else {
					_selectedIndices = [itemIndex];
					lastCaretIndex = caretIndex = itemIndex;
				}
			}
			dispatchEvent(new Event(Event.CHANGE));
			invalidate(InvalidationType.DATA);
		}
		protected function stateChangeHandler(e:StateEvent){
			
		}
		
	}
}