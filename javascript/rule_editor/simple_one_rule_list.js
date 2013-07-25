{
    "rule_id": "14",
    "client_id": "3",
    "site_id": "15",
    "name": "Burufly_Rule_test",
    "description": "Description for this rules",
    "tags": "new,basic,custom3,promotion",
    "active_status": "active",
    "date_added": "12/12/2012",
    "date_modified": "12/12/2012",
    "jigsaw_set": [
        {
            "id": 1,
            "name": "review",
            "description": "review action jigsaw",
            "specific_id": "7",
            "category": "ACTION",
            "sort_order": 0,
            "dataSet": [
                {
                    "param_name": "url",
                    "label": "URL",
                    "placeholder": "Page URL here.",
                    "sortOrder": "0",
                    "field_type": "text",
                    "value": "www.playbasis.com/*"
                },
                {
                    "param_name": "object_target",
                    "label": "Target (Product)",
                    "placeholder": "Item(Product) id",
                    "sortOrder": "1",
                    "field_type": "text",
                    "value": ""
                },
                {
                    "param_name": "action_target",
                    "label": "Element (ID/Class)",
                    "placeholder": "Specific id or class",
                    "sortOrder": "2",
                    "field_type": "text",
                    "value": ""
                },
                {
                    "param_name": "regex",
                    "label": "Regex",
                    "placeholder": "",
                    "sortOrder": "3",
                    "field_type": "boolean",
                    "value": "true"
                }
            ],
            "config": {
                "action_id": "7",
                "action_name": "review",
                "url": "www.playbasis.com/*",
                "regex": "true",
                "action_target": "eg_custom_target_class_or_target_id",
                "object_target": "eg_shoe_bags_whatever"
            }
        },
        {
            "id": "20002",
            "name": "every_n_day",
            "description": "Do action every n days",
            "category": "CONDITION",
            "specific_id": "",
            "sort_order": 1,
            "dataSet": [
                {
                    "param_name": "num_of_days",
                    "label": "n Days",
                    "placeholder": "How many days ?",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "10"
                },
                {
                    "param_name": "time_of_day",
                    "label": "start time (of Day)",
                    "placeholder": "Start day at time ...",
                    "sortOrder": "1",
                    "field_type": "time",
                    "value": "00:00"
                }
            ],
            "config": {
                "time_of_day": "00:00",
                "num_of_days": "10"
            }
        },
        {
            "id": "30001",
            "name": "justExp",
            "description": "User earn exp",
            "category": "REWARD",
            "specific_id": "44",
            "sort_order": 2,
            "dataSet": [
                {
                    "param_name": "reward_name",
                    "label": "Exp",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "read_only",
                    "value": "justExp"
                },
                {
                    "param_name": "item_id",
                    "label": "ItemID",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "hidden",
                    "value": ""
                },
                {
                    "param_name": "quantity",
                    "label": "Exp",
                    "placeholder": "How many ...",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "100"
                }
            ],
            "config": {
                "reward_id": "44",
                "reward_name": "justExp",
                "item_id": "",
                "quantity": "100"
            }
        },
        {
            "id": "19672",
            "name": "counter",
            "description": "Do the same action n times",
            "category": "CONDITION",
            "specific_id": "",
            "sort_order": 3,
            "dataSet": [
                {
                    "param_name": "counter_value",
                    "label": "Times",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "10"
                },
                {
                    "param_name": "interval",
                    "label": "Time interval",
                    "sortOrder": "1",
                    "field_type": "number",
                    "value": "10000"
                },
                {
                    "param_name": "interval_unit",
                    "label": "",
                    "sortOrder": "",
                    "field_type": "hidden",
                    "value": "second"
                },
                {
                    "param_name": "reset_timeout",
                    "label": "in a Rows",
                    "sortOrder": "2",
                    "field_type": "boolean",
                    "value": "true"
                }
            ],
            "config": {
                "counter_value": "10",
                "interval": "180",
                "interval_unit": "second",
                "reset_timeout": "true"
            }
        },
        {
            "id": "30002",
            "name": "badge",
            "description": "User earn Badge",
            "category": "REWARD",
            "specific_id": "55",
            "sort_order": 4,
            "dataSet": [
                {
                    "param_name": "reward_name",
                    "label": "Name",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "read_only",
                    "value": "justBadge"
                },
                {
                    "param_name": "item_id",
                    "label": "Item id",
                    "placeholder": "Item id",
                    "sortOrder": "0",
                    "field_type": "collection",
                    "value": "14322"
                },
                {
                    "param_name": "quantity",
                    "label": "Quantity",
                    "placeholder": "How many ...",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "1"
                }
            ],
            "config": {
                "reward_id": "55",
                "reward_name": "justBadge",
                "item_id": "14322",
                "quantity": "1"
            }
        },
        {
            "id": "20003",
            "name": "before_date",
            "description": "Do the action before date-time",
            "category": "CONDITION",
            "specific_id": "",
            "sort_order": 5,
            "dataSet": [
                {
                    "param_name": "timestamp",
                    "label": "Date-Time",
                    "sortOrder": "0",
                    "field_type": "date_time",
                    "value": "1359091200"
                }
            ],
            "config": {
                "timestamp": "1359091200"
            }
        },
        {
            "id": "20004",
            "name": "after_date",
            "description": "Do the action after date-time",
            "category": "CONDITION",
            "specific_id": "",
            "sort_order": 6,
            "dataSet": [
                {
                    "param_name": "timestamp",
                    "label": "Date-Time",
                    "placeholder": "Select date time",
                    "sortOrder": "0",
                    "field_type": "date_time",
                    "value": "1359091200"
                }
            ],
            "config": {
                "timestamp": "1359091200"
            }
        },
        {
            "id": "19521",
            "name": "coin",
            "description": "User earn coin",
            "category": "REWARD",
            "specific_id": "66",
            "sort_order": 7,
            "dataSet": [
                {
                    "param_name": "reward_name",
                    "label": "Name",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "read_only",
                    "value": "justCoin"
                },
                {
                    "param_name": "item_id",
                    "label": "Item id",
                    "placeholder": "Item id",
                    "sortOrder": "0",
                    "field_type": "hidden",
                    "value": ""
                },
                {
                    "param_name": "quantity",
                    "label": "Quantity",
                    "placeholder": "How many ...",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "3"
                }
            ],
            "config": {
                "reward_id": "66",
                "reward_name": "justCoin",
                "item_id": "",
                "quantity": "3"
            }
        },
        {
            "id": "19521",
            "name": "point",
            "description": "User earn point",
            "category": "REWARD",
            "specific_id": "661",
            "sort_order": 7,
            "dataSet": [
                {
                    "param_name": "reward_name",
                    "label": "Name",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "read_only",
                    "value": "justPoint"
                },
                {
                    "param_name": "item_id",
                    "label": "Item id",
                    "placeholder": "Item id",
                    "sortOrder": "0",
                    "field_type": "hidden",
                    "value": ""
                },
                {
                    "param_name": "quantity",
                    "label": "Quantity",
                    "placeholder": "How many ...",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "3"
                }
            ],
            "config": {
                "reward_id": "661",
                "reward_name": "justPoint",
                "item_id": "",
                "quantity": "3"
            }
        },
        {
            "id": "30004",
            "name": "coupon",
            "description": "User earn coupon",
            "category": "REWARD",
            "specific_id": "77",
            "sort_order": 8,
            "dataSet": [
                {
                    "param_name": "reward_name",
                    "label": "Name",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "read_only",
                    "value": "justCoupon"
                },
                {
                    "param_name": "item_id",
                    "label": "Item id",
                    "placeholder": "Item id",
                    "sortOrder": "0",
                    "field_type": "collection",
                    "value": "12334"
                },
                {
                    "param_name": "quantity",
                    "label": "Quantity",
                    "placeholder": "How many ...",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "3"
                }
            ],
            "config": {
                "reward_id": "77",
                "reward_name": "justCoupon",
                "item_id": "12334",
                "quantity": "1"
            }
        },
        {
            "id": "30005",
            "name": "virtual_good",
            "description": "User earn virtual_good",
            "category": "REWARD",
            "specific_id": "88",
            "sort_order": 9,
            "dataSet": [
                {
                    "param_name": "reward_name",
                    "label": "Name",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "read_only",
                    "value": "just_virtual_good"
                },
                {
                    "param_name": "item_id",
                    "label": "Item id",
                    "placeholder": "Item id",
                    "sortOrder": "0",
                    "field_type": "collection",
                    "value": "102"
                },
                {
                    "param_name": "quantity",
                    "label": "Quantity",
                    "placeholder": "How many ...",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "1"
                }
            ],
            "config": {
                "reward_id": "88",
                "reward_name": "just_virtual_good",
                "item_id": "102",
                "quantity": "1"
            }
        },
        {
            "id": "20005",
            "name": "between",
            "description": "Do the action between time",
            "category": "CONDITION",
            "specific_id": "",
            "sort_order": 10,
            "dataSet": [
                {
                    "param_name": "start_time",
                    "label": "Start time",
                    "placeholder": "Begin at .. ",
                    "sortOrder": "0",
                    "field_type": "time",
                    "value": "00:00"
                },
                {
                    "param_name": "end_time",
                    "label": "End time",
                    "placeholder": "End at .. ",
                    "sortOrder": "1",
                    "field_type": "time",
                    "value": "00:00"
                }
            ],
            "config": {
                "start_time": "00:00",
                "end_time": "00:00"
            }
        },
        {
            "id": "30006",
            "name": "level",
            "description": "User earn level",
            "category": "REWARD",
            "specific_id": "99",
            "sort_order": 11,
            "dataSet": [
                {
                    "param_name": "reward_name",
                    "label": "Name",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "read_only",
                    "value": "justLevel"
                },
                {
                    "param_name": "item_id",
                    "label": "",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "hidden",
                    "value": ""
                },
                {
                    "param_name": "quantity",
                    "label": "Level",
                    "placeholder": "How many ...",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "1"
                }
            ],
            "config": {
                "reward_id": "30006",
                "reward_name": "justLevel",
                "item_id": "",
                "quantity": "1"
            }
        },
        {
            "id": "20006",
            "name": "cooldown",
            "description": "set time waitting time to do next action again",
            "category": "CONDITION",
            "specific_id": "",
            "sort_order": 12,
            "dataSet": [
                {
                    "param_name": "cooldown",
                    "placeholder": "Cooldown in....",
                    "label": "Times",
                    "sortOrder": "0",
                    "field_type": "cooldown",
                    "value": "3600"
                }
            ],
            "config": {
                "cooldown": "3600"
            }
        },
        {
            "id": "30007",
            "name": "discount",
            "description": "User earn discount",
            "category": "REWARD",
            "specific_id": "11",
            "sort_order": 13,
            "dataSet": [
                {
                    "param_name": "reward_name",
                    "label": "Name",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "read_only",
                    "value": "justDiscount"
                },
                {
                    "param_name": "item_id",
                    "label": "Item",
                    "placeholder": "item_id",
                    "sortOrder": "0",
                    "field_type": "collection",
                    "value": "104"
                },
                {
                    "param_name": "quantity",
                    "label": "Quantity",
                    "placeholder": "How many ...",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "1"
                }
            ],
            "config": {
                "reward_id": "11",
                "reward_name": "justDiscount",
                "item_id": "104",
                "quantity": "1"
            }
        },
        {
            "id": "20007",
            "name": "daily",
            "description": "Do the same action every day",
            "category": "CONDITION",
            "specific_id": "",
            "sort_order": 14,
            "dataSet": [
                {
                    "param_name": "time_of_day",
                    "label": "start time (of Day)",
                    "placeholder": "Start day at time ...",
                    "sortOrder": "0",
                    "field_type": "time",
                    "value": "00:00"
                }
            ],
            "config": {
                "time_of_day": "00:00"
            }
        },
        {
            "id": "30021",
            "name": "exp",
            "description": "User earn exp",
            "category": "REWARD",
            "specific_id": "22",
            "sort_order": 2,
            "dataSet": [
                {
                    "param_name": "reward_name",
                    "label": "",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "read_only",
                    "value": "justExp"
                },
                {
                    "param_name": "item_id",
                    "label": "",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "hidden",
                    "value": ""
                },
                {
                    "param_name": "quantity",
                    "label": "Exp",
                    "placeholder": "How many ...",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "10"
                }
            ],
            "config": {
                "reward_id": "22",
                "reward_name": "justExp",
                "item_id": "",
                "quantity": "10"
            }
        },
        {
            "id": "20008",
            "name": "weekly",
            "description": "Do the same action every week",
            "category": "CONDITION",
            "specific_id": "",
            "sort_order": 16,
            "dataSet": [
                {
                    "param_name": "time_of_day",
                    "label": "start time (of Day)",
                    "placeholder": "Start day at time ...",
                    "sortOrder": "0",
                    "field_type": "time",
                    "value": "00:00"
                },
                {
                    "param_name": "day_of_week",
                    "label": "start day (of week)",
                    "placeholder": "Day abbreviation (eg. mon,fri,sun)",
                    "sortOrder": "1",
                    "field_type": "text",
                    "value": "mon"
                }
            ],
            "config": {
                "time_of_day": "00:00",
                "day_of_week": "mon"
            }
        },
        {
            "id": "30008",
            "name": "prize",
            "description": "User earn prize",
            "category": "REWARD",
            "specific_id": "33",
            "sort_order": 17,
            "dataSet": [
                {
                    "param_name": "reward_name",
                    "label": "name",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "read_only",
                    "value": "justPrize"
                },
                {
                    "param_name": "item_id",
                    "label": "Item",
                    "placeholder": "Item id",
                    "sortOrder": "0",
                    "field_type": "collection",
                    "value": "105"
                },
                {
                    "param_name": "quantity",
                    "label": "Exp",
                    "placeholder": "How many ...",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "1"
                }
            ],
            "config": {
                "reward_id": "33",
                "reward_name": "justPrize",
                "item_id": "105",
                "quantity": "1"
            }
        },
        {
            "id": "20081",
            "name": "monthly",
            "description": "Do the same action every month",
            "category": "CONDITION",
            "specific_id": "",
            "sort_order": 16,
            "dataSet": [
                {
                    "param_name": "time_of_day",
                    "label": "start time (of Day)",
                    "placeholder": "Start day at time ...",
                    "sortOrder": "0",
                    "field_type": "time",
                    "value": "00:00"
                },
                {
                    "param_name": "day_of_month",
                    "label": "start day (of month)",
                    "placeholder": "Date (eg. 1,15,31)",
                    "sortOrder": "1",
                    "field_type": "text",
                    "value": "1"
                }
            ],
            "config": {
                "time_of_day": "00:00",
                "day_of_month": "1"
            }
        },
        {
            "id": 2,
            "name": "customPointReward",
            "description": "customPointReward",
            "specific_id": "",
            "category": "REWARD",
            "sort_order": 18,
            "dataSet": [
                {
                    "param_name": "reward_name",
                    "label": "Name",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "text",
                    "value": "customPointReward"
                },
                {
                    "param_name": "quantity",
                    "label": "Exp",
                    "placeholder": "How many ...",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "10"
                }
            ],
            "config": {
                "reward_name": "customPointReward",
                "quantity": 10
            }
        },
        {
            "id": 2,
            "name": "Burufly Point",
            "description": "reward",
            "specific_id": "44",
            "category": "REWARD",
            "sort_order": 19,
            "dataSet": [
                {
                    "param_name": "reward_name",
                    "label": "name",
                    "placeholder": "",
                    "sortOrder": "0",
                    "field_type": "read_only",
                    "value": "Burufly Point"
                },
                {
                    "param_name": "item_id",
                    "label": "Item",
                    "placeholder": "Item id",
                    "sortOrder": "0",
                    "field_type": "hidden",
                    "value": ""
                },
                {
                    "param_name": "quantity",
                    "label": "Quantity",
                    "placeholder": "How many ...",
                    "sortOrder": "0",
                    "field_type": "number",
                    "value": "15"
                }
            ],
            "config": {
                "reward_id": "44",
                "reward_name": "Burufly Point",
                "item_id": "",
                "quantity": "15"
            }
        }
    ]
}