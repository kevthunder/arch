<?php
class ItemsHandler extends Object {

	
	function createItem(&$options,$params){
		//App::import('Lib', 'SetMulti');
		//debug(SetMulti::filterNot($options,'is_object',-1));
		$defaultData = array(
			'active' => 1,
		);
		$toData = array('item_type_id','character_id','tile_id');
		$needed = array('item_type_id');
		$data = array_merge($defaultData,array_intersect_key($params, array_flip($toData)));
		if(count(array_diff_key(array_flip($needed),$data)) == 0){
			$this->Item = ClassRegistry::init('Item');
			$this->Item->create();
			//debug($data);
			$this->Item->save($data);
		}
		//debug($node);
		//debug($params);
	}
}
?>