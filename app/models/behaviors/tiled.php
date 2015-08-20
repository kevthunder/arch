<?php
class TiledBehavior extends ModelBehavior {

	var $settings;
	var $defaultOptions = array(
		
	);

	function setup(&$model, $settings = array()) {
		$this->settings[$model->alias] = array_merge($this->defaultOptions, (array)$settings);
		
		if(!in_array('Tile',$model->getAssociated('belongsTo'))){
			$this->User->bindModel(array('belongsTo' => array(
				'Tile' => array(
					'className' => 'Tile',
					'foreignKey' => 'tile_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
				)
			)));
		}
	}
	
	function getPos(&$model,$entry){
		if(is_numeric($entry)){
			$model->Behaviors->attach('Containable');
			$entry = $model->find('first',array('conditions'=>array($model->alias.'.id'=>$entry),'contain'=>array('Tile')));
		}else{
			$entry = $model->updateDataNow($entry);
			//debug('updateData');
		}
		//debug($entry);
		if(!empty($entry[$model->alias]['Tile'])){
			return $model->Tile->getPos($entry[$model->alias]['Tile']);
		}
		if(!empty($entry['Tile'])){
			return $model->Tile->getPos($entry['Tile']);
		}
		if(!empty($entry[$model->alias]['tile_id'])){
			return $model->Tile->getPos($entry[$model->alias]['tile_id']);
		}
		if(!empty($entry['tile_id'])){
			return $model->Tile->getPos($entry['tile_id']);
		}
		return null;
	}
	
	function getInRect(&$model,$options=array()){
		$defaultOpt = array(
			'mode' => 'all',
			'w'=>null, 
			'h'=>null, 
			'centerTile'=>null, 
			'x'=>null, 
			'y'=>null
		);
		$opt = array_merge($defaultOpt,$options);
		
		$rectOpt = array('w','h','centerTile','x','y','aliased');
		$localOpt = array_merge(array('mode'),$rectOpt);
		
		$opt['conditions'][] = $model->alias.".tile_id = Tile.id";
		$rectQuery = $model->Tile->rectQuery(array_intersect_key($opt,array_flip($rectOpt)));
		$opt['conditions'][] = $rectQuery['conditions'];

		$findOption = array_diff_key($opt,array_flip($localOpt));
		$entries = $model->find($opt['mode'],$findOption);
		
		return $entries;
	}
	
}