// playbasis_quest_to_client
{
	_id : ObjectId("1234567890"),
	quest_name : "testquest",
	description : "test quest description",
	hint : "test hint quest",
	image_url : "/no_image.png",
	condition : [
		{
			condition_type : "DATETIME_START",
			condition_id : null,
			condition_value : "2014-05-14"
		},
		{
			condition_type : "DATETIME_END",
			condition_id : null,
			condition_value : "2014-06-14"
		},
		{
			condition_type : "LEVEL_START",
			condition_id : null,
			condition_value : 1
		},
		{
			condition_type : "LEVEL_END",
			condition_id : null,
			condition_value : 5
		},
		{
			condition_type : "QUEST",
			condition_id : ObjectId("1234567890"),
			condition_value : null
		},
		{
			condition_type : "POINT",
			condition_id : ObjectId("1234567890"),
			condition_value : null
		},
		{
			condition_type : "CUSTOM_POINT",
			condition_id : ObjectId("1234567890"),
			condition_value : null
		},
		{
			condition_type : "BADGE",
			condition_id : ObjectId("1234567890"),
			condition_value : null
		},
	],
	rewards : [
		{
			reward_type : "POINT",
			reward_id :  ObjectId("1234567890"),
			reward_value : 10
		},
		{
			reward_type : "CUSTOM_POINT",
			reward_id : ObjectId("1234567890"),
			reward_value : 5
		},
		{
			reward_type : "BADGE",
			reward_id : ObjectId("1234567890"),
			reward_value : 1
		}
	],
	missions : [
		{
			mission_number : 1,
			mission_name : "Test Mission 1",
			description : "Test mission description 1",
			hint : "Test mission hint 1",
			image_url : "/no_image.png",
			completion : 
			[
				{
					completion_type : "ACTION",
					completion_id : ObjectId("1234567890"),
					completion_value : 3
				},
				{
					completion_type : "POINT",
					completion_id : ObjectId("1234567890"),
					completion_value : 10
				},
				{
					completion_type : "CUSTOM_POINT",
					completion_id : ObjectId("1234567890"),
					completion_value : 5
				},
				{
					completion_type : "BADGE",
					completion_id : ObjectId("1234567890"),
					completion_value : 1
				}
			],
			rewards :
			[
				{
					reward_type : "POINT",
					reward_id : ObjectId("1234567890"),
					reward_value : 10
				},
				{
					reward_type : "CUSTOM_POINT",
					reward_id : ObjectId("1234567890"),
					reward_value : 5
				},
				{
					reward_type : "BADGE",
					reward_id : ObjectId("1234567890"),
					reward_value : 1
				},
				{
					reward_type : "EXP",
					reward_id : ObjectId("1234567890"),
					reward_value : 2
				}
			]
		},
		{
			mission_number : 2,
			mission_name : "Test Mission 2",
			description : "Test mission description 2",
			hint : "Test mission hint 2",
			image_url : "/no_image.png",
			completion : 
			[
				{
					completion_type : "ACTION",
					completion_id : ObjectId("1234567890"),
					completion_value : 4
				},
				{
					completion_type : "POINT",
					completion_id : ObjectId("1234567890"),
					completion_value : 11
				},
				{
					completion_type : "CUSTOM_POINT",
					completion_id : ObjectId("1234567890"),
					completion_value : 6
				},
				{
					completion_type : "BADGE",
					completion_id : ObjectId("1234567890"),
					completion_value : 1
				}
			],
			rewards :
			[
				{
					reward_type : "POINT",
					reward_id : ObjectId("1234567890"),
					reward_value : 11
				},
				{
					reward_type : "CUSTOM_POINT",
					reward_id : ObjectId("1234567890"),
					reward_value : 6
				},
				{
					reward_type : "BADGE",
					reward_id : ObjectId("1234567890"),
					reward_value : 1
				},
				{
					reward_type : "EXP",
					reward_id : ObjectId("1234567890"),
					reward_value : 3
				}
			]
		}
	],
	mission_order : true,
	client_id : ObjectId("1234567890"),
	site_id : ObjectId("1234567890"),
	status : true,
	date_added :  ISODate("2014-05-16 00:22:34.000Z"),
	date_modified : ISODate("2014-05-16 00:22:34.000Z")
}

// playbasis_quest_to_player
{
	_id : ObjectId("1234567890"),
	client_id : ObjectId("1234567890"),
	site_id : ObjectId("1234567890"),
	pb_player_id : ObjectId("1234567890"),
	quest_id : ObjectId("1234567890"),
	missions : [
		{
			mission_number : 1,
			completion : 
			[
				{
					completion_type : "ACTION",
					completion_id : ObjectId("1234567890"),
					completion_value : 3
				},
				{
					completion_type : "POINT",
					completion_id : ObjectId("1234567890"),
					completion_value : 10
				},
				{
					completion_type : "CUSTOM_POINT",
					completion_id : ObjectId("1234567890"),
					completion_value : 5
				},
				{
					completion_type : "BADGE",
					completion_id : ObjectId("1234567890"),
					completion_value : 1
				}
			],
			stauts : "join"
		},
		{
			mission_number : 2,
			completion : 
			[
				{
					completion_type : "ACTION",
					completion_id : ObjectId("1234567890"),
					completion_value : 4
				},
				{
					completion_type : "POINT",
					completion_id : ObjectId("1234567890"),
					completion_value : 11
				},
				{
					completion_type : "CUSTOM_POINT",
					completion_id : ObjectId("1234567890"),
					completion_value : 6
				},
				{
					completion_type : "BADGE",
					completion_id : ObjectId("1234567890"),
					completion_value : 1
				}
			],
			status : "unjoin"
		}
	],
	status : "join",
	date_added :  ISODate("2014-05-16 01:22:34.000Z"),
	date_modified : ISODate("2014-05-16 01:22:34.000Z")
}

// playbasis_quest_reward_log
{
	_id : ObjectId("1234567890"),
	client_id : ObjectId("1234567890"),
	site_id : ObjectId("1234567890"),
	pb_player_id : ObjectId("1234567890"),
	quest_id : ObjectId("1234567890"),
	mission_number : 1,
	reward_type : "POINT",
	reward_id : ObjectId("1234567890"),
	reward_name : "point",
	reward_value : 11,
	date_added :  "2014-05-16 01:22:34.000Z",
	date_modified : "2014-05-16 01:22:34.000Z"
}