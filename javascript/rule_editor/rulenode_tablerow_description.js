/*rule_table_row_descption.js
properties contain in this file using for describe to user about what is each row meaning 
*/

toolstip = {

	action:{
        field_desc:{
				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
				object_target:'target product title(for e-commerce website),eg. when user action to product',
				action_target:'what is specific class or id of target target product html element',
				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action'
			}
//		visit:{
//			description:'This is the entry point of any rule. When you make a POST request to the /Engine/rule method, you’re required to provide name of the action that the user just performed in a POST parameter called “action”. When the server received the request, the server will start processing every rule that start with an Action that matches the value in the “action” parameter.',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action'
//			}
//		},
//		review:{
//			description:'When user wrote some review on target page or target product',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action'
//			}
//		},
//		read:{
//			description:'When user read all content on target page or target product',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action'
//			}
//		},
//		like:{
//			description:'When user click like on target page or target product',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		},
//		share:{
//			description:'When user share target page or target product',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		},
//		want:{
//			description:'When user click want on target page or target product',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		},
//		love:{
//			description:'When user click love on target page or target product',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		},
//		spotreview:{
//			description:'When user wrote some spot-review on target page or target place',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		},
//		comment:{
//			description:'When user wrote some comment on target page or target product',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		},
//		following:{
//			description:'When user has following some other users',
//			field_desc:{
//				url:'not-necessary',
//				object_target:'not-necessary',
//				action_target:'not-necessary',
//				regex:'not-necessary'
//			}
//		},
//		follower:{
//			description:'When user has following some other users',
//			field_desc:{
//				url:'not-necessary',
//				object_target:'not-necessary',
//				action_target:'not-necessary',
//				regex:'not-necessary'
//			}
//		},
//		pernah:{
//			description:'When user have been to some places before',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		},
//		timeonsite:{
//			description:'How many time user spend on target page',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		},
//		mau:{
//			description:'When user mau',
//			field_desc:{
//				url:'not-necessary',
//				object_target:'not-necessary',
//				action_target:'not-necessary',
//				regex:'not-necessary'
//			}
//		},
//		fbstatus:{
//			description:'When user post facebook status',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		},
//		fbpost:{
//			description:'When user post things you provided to they status',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		},
//		fbcomment:{
//			description:'When user comment something on facebook comment holder',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		},
//		fblike:{
//			description:'When user click like on facebook like button',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		},
//		tweet:{
//			description:'When user tweet something to they timeline',
//			field_desc:{
//				url:'for the rule to continue, this string must match with the “url” parameter given in the request to trigger this action',
//				object_target:'target product title(for e-commerce website),eg. when user action to product',
//				action_target:'what is specific class or id of target target product html element',
//				regex:'use regular expression when match the url string with “url” parameter given in the request to trigger this action with page url'
//			}
//		}
	},

	condition:{
		counter:{ 
			description:'Each time a Counter is triggered, its count value will decrease by one. A counter will stop the rule and discard the action until the count reaches zero. A counter can have a timeout that will reset the count back to its original value.',
			field_desc:{
				counter_value:'how many times this counter need to trigger before it allows the rule to continue',
				interval:'if the count does not reach zero before the time ran out, the count resets',
				interval_unit:'unit of time out, can be second or day',
				reset_timeout:'reset the timeout each time the counter is triggered'
			}
		},
		cooldown:{ 
			description:'Cooldown can limit the frequency that a user can trigger rules. Once a Cooldown is triggered, its timer starts. If the same Cooldown is triggered again by the same user before the timer reached zero, the Cooldown with stop the rule and discard the action; otherwise, the rule continues and the timer starts again.',
			field_desc:{
				cooldown:'The duration of the timer'
			}
		},
		before:{ 
			description:'When Before is triggered after the specified date and time, the rule will stop and the action will be discarded.',
			field_desc:{
				timestamp:'date and time that you want to stop allowing actions to go through'
			}
		},
		after:{ 
			description:'When After is triggered before the specified date and time, the rule will stop and the action will be discarded.',
			field_desc:{
				timestamp:'date and time that you want to start allowing actions to go through'
			}
		},
		between:{ 
			description:'When Between is triggered outside of the specified period of time, the rule will stop and the action will be discarded.',
			field_desc:{
				start_time:'the beginning of the time period that actions can go through',
				end_time:'the end of the time period that actions can go through'
			}
		},
		daily:{ 
			description:'When Daily is triggered more than once a day, the rule will stop and the action will be discarded.',
			field_desc:{
				time_of_day:'time to start a new day, and allow an action to go through again'
			}
		},
		weekly:{ 
			description:'When Weekly is triggered more than once a week, the rule will stop and the action will be discarded.',
			field_desc:{
				time_of_day:'time of day to start a new week, and allow an action to go through again',
				day_of_week:'day of week to start a new week, and allow an action to go through again'
			}
		},
		monthly:{ 
			description:'When Weekly is triggered more than once a month, the rule will stop and the action will be discarded.',
			field_desc:{
				time_of_day:'time of day to start a new month, and allow an action to go through again',
				day_of_month:'date of month to start a new month, and allow an action to go through again'
			}
		},
		everyNDays:{
			description:'When EveryNDays is triggered more than once every N days, the rule will stop and the action will be discarded.',
			field_desc:{
				num_of_days:'time of day to start a new period, and allow an action to go through again',
				time_of_day:'number of days before a new period begins, and allow an action to go through again'
			}
		},
		userProfile:{
			description:'check whether the profile of user meet condition.',
			field_desc:{
				profile:'profile to check',
				operation:'operation',
				value:'value (ex: gender = male)'
			}
		}
	},

	reward_sequence:{
		point:{ 
			description:'Reward a user with the specified amount of points',
			field_desc:{
				reward_name:'reward name',
                sequence_id:'quantity check by the sequence file',
				loop:   '       repeat sequence index?',
				global: '       the index run globally by all users?'
			}
		},
		exp:{ 
			description:'Reward a user with the specified amount of exp',
			field_desc:{
				reward_name:'reward name',
                sequence_id:'quantity check by the sequence file',
                loop:'repeat sequence index?',
                global: 'the index run globally by all users?'
			}
		},
		badge:{ 
			description:'Reward a item to a user',
			field_desc:{
				reward_name:'item to award',
				item_id:'which item to give to user',
                sequence_id:'quantity check by the sequence file',
                loop:'repeat sequence index?',
                global: 'the index run globally by all users?'
			}
		},
		level:{ 
			description:'Reward a user with the specified amount of level',
			field_desc:{
				reward_name:'reward name',
                sequence_id:'quantity check by the sequence file',
                loop:'repeat sequence index?',
                global: 'the index run globally by all users?'
			}
		},
		customPointReward:{ 
			description:'Reward a user with the specified type and amount of point, where the type and/or amount of point to award can be dynamically calculated and passed in via POST parameters in the request to the /Engine/rule method.',
			field_desc:{
				reward_name:'name of point to award',
                sequence_id:'quantity check by the sequence file',
                loop:'repeat sequence index?',
                global: 'the index run globally by all users?'
			}
		},
		etc:{
			description:'Reward a user with the specified type and amount of point',
			field_desc:{
				reward_name:'name of custom point to award',
                sequence_id:'quantity check by the sequence file',
                loop:'repeat sequence index?',
                global: 'the index run globally by all users?'
			}
		}
	},

    reward_custom: {
        etc: {
            description: 'Reward a user by checking rewards that corresponds with a specific custom parameter',
            field_desc: {
                parameter_name: 'specific custom parameter to check with reward in the file'
            }
        },
    },

	reward:{
		point:{
			description:'Reward a user with the specified amount of points',
			field_desc:{
				reward_name:'reward name',
				quantity:'amount of point to award',
				custom_log:'custom parameter to log',
				hidden_reward:"to hide the reward from event response"
			}
		},
		exp:{
			description:'Reward a user with the specified amount of exp',
			field_desc:{
				reward_name:'reward name',
				quantity:'amount of exp to award',
				custom_log:'custom parameter to log',
				hidden_reward:"to hide the reward from event response"
			}
		},
		badge:{
			description:'Reward a item to a user',
			field_desc:{
				reward_name:'item to award',
				item_id:'which item to give to user',
				quantity:'amount to award (this is usually one, however, the same item can be acquired more than once)'
			}
		},
		level:{
			description:'Reward a user with the specified amount of level',
			field_desc:{
				reward_name:'reward name',
				quantity:'amount of level to award'
			}
		},
		customPointReward:{
			description:'Reward a user with the specified type and amount of point, where the type and/or amount of point to award can be dynamically calculated and passed in via POST parameters in the request to the /Engine/rule method.',
			field_desc:{
				reward_name:'name of point to award',
				quantity:'amount of point to award'
			}
		},
		etc:{
			description:'Reward a user with the specified type and amount of point',
			field_desc:{
				reward_name:'name of custom point to award',
				quantity:'amount of custom point to award',
				custom_log:'custom parameter to log',
				hidden_reward:"to hide the reward from event response"
			}
		}
	},

	feedback: {
		email: {
			description: 'Email',
			field_desc: {
				template_id: 'template id',
				subject: 'email subject'
			}
		},
		sms: {
			description: 'SMS',
			field_desc: {
				template_id: 'template id'
			}
		}
	},

	group: {
		random: {
			description: 'Random',
			field_desc: {
				
			}
		},
		sequence: {
			description: 'Sequence',
			field_desc: {
				
			}
		}
	},
	condition_group: {
		or: {
			description: 'Or Condition',
			field_desc: {

			}
		},
		not: {
			description: 'Not Condition',
			field_desc: {

			}
		}
	}
}
