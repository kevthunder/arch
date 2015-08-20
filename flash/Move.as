package  {
	
	public class Move extends TileAbility{
		//////////// variables ////////////
		
		//////////// Static variables ////////////
		
		//////////// Constructor ////////////
        public function Move() {
			super('Move');
        }
		
		//////////// Properties functions ////////////
		
		//////////// Static functions ////////////
		
		//////////// Public functions ////////////
		public function walkPath(path:Path,user:Character){
			var inst:AbilityInstance = getInstance(user);
			
			return usePath(path,inst);
		}
		
		//////////// Private functions ////////////
		override protected function useOnTile(tile:Tile,inst:AbilityInstance){
			if(inst.user.tile && tile.allowedPathing()){
				//trace('i\'m going to',tile.posX,tile.posY);
				var path:Path = new Path();
				path.to = tile;
				
				return usePath(path,inst);
			}
			trace('cant walk there');
			return false;
		}
		
		protected function usePath(path:Path,inst:AbilityInstance){
			path.from = inst.user.tile;
			inst.user.setPath(path);
			
			var req:ConnectionXMLRequest = new ConnectionXMLRequest('skills','move');
			req.params = {
				skill_id : id,
				tile_id : path.to.id
			}
			req.content = path.export();
			req.priority = 1;
			return ClientMain.instance.connection.addXmlRequest(req);
		}
		
		
		//////////// Event Handlers functions ////////////

	}
	
}
