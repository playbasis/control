var map_player_value = function() {
	var output = {cl_player_id: this.cl_player_id, first_name: this.first_name, gender: this.gender,exp: this.exp, level: this.level, status: this.status};
	var key = {client_id: this.client_id, site_id: this.site_id, pb_player_id: this._id};
	 
	 emit(key, output);
}
var reduce_player_value = function(key, values){
	var reduced = {};
	for (var i in values)
	{
		var data = values[i];
		for(var j in data)
		{
			reduced[j] = data[j];
		}
	}
	return reduced;
}
db.playbasis_player.mapReduce(
	map_player_value,
	reduce_player_value,
	{ out: "playbasis_summary_of_player" }
)
var map_action_log_value = function() {
	if (!this.action_id || !this.action_name || !this.pb_player_id) {
        return;
    }
	
	var output = {};
	output["action_"+this.action_id+""] = {action_id: this.action_id, action_name: this.action_name, action_value: 1};
	var key = {client_id: this.client_id, site_id: this.site_id, pb_player_id: this.pb_player_id};
	 
	 emit(key, output);
}
var reduce_action_log_value = function(key, values){
	var reduced = {};
	for (var i in values)
	{
		var action_data = values[i];
		for(var j in action_data)
		{
			if(reduced[j]){
				reduced[j]["action_value"] += action_data[j]["action_value"];
			}else{
				reduced[j] = action_data[j];
			}
		}
	}
	return reduced;
}
db.playbasis_action_log.mapReduce(
	map_action_log_value,
	reduce_action_log_value,
	{ out: {reduce:"playbasis_summary_of_player"} }
)
var map_reward_value = function() {
	if (!this.reward_id || !this.value || !this.pb_player_id) {
        return;
    }
	 
	 var output = {};
	 output["reward_"+this.reward_id+""] = {reward_id: this.reward_id, value: this.value};
	 var key = {client_id: this.client_id, site_id: this.site_id, pb_player_id: this.pb_player_id};
	 
	 emit(key, output);
}
var reduce_reward_value = function(key, values){
	var reduced = {};
	for (var i in values)
	{
		var reward_data = values[i];
		for(var j in reward_data)
		{
			if(reduced[j]){
				reduced[j]["value"] += reward_data[j]["value"];
			}else{
				reduced[j] = reward_data[j];
			}
		}
	}
	return reduced;
}
db.playbasis_reward_to_player.mapReduce(
	map_reward_value,
	reduce_reward_value,
	{ out: {reduce:"playbasis_summary_of_player"} }
)
var map_badge_value = function() {
	var badge_in_reward = '5215f8ab6d6cfb001e00008f';
	if (!this.badge_id || !this.value || !this.pb_player_id) {
        return;
    }
	 
	 var output = {};
	 output["reward_"+badge_in_reward+""] = {reward_id: ObjectId(badge_in_reward), value: this.value};
	 var key = {client_id: this.client_id, site_id: this.site_id, pb_player_id: this.pb_player_id};
	 
	 emit(key, output);
}
var reduce_badge_value = function(key, values){
	var reduced = {};
	for (var i in values)
	{
		var reward_data = values[i];
		for(var j in reward_data)
		{
			if(reduced[j]){
				reduced[j]["value"] += reward_data[j]["value"];
			}else{
				reduced[j] = reward_data[j];
			}
		}
	}
	return reduced;
}
db.playbasis_reward_to_player.mapReduce(
	map_badge_value,
	reduce_badge_value,
	{ out: {reduce:"playbasis_summary_of_player"} }
)
