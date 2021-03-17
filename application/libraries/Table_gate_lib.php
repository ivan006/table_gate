<?php
class Table_gate_lib
{
	private $CI;

	function __construct()
	{
		// $this->load->helper(array('form', 'url'));
		// $this->load->library('form_validation');

		$this->CI =& get_instance();
		$this->CI->load->database();
	}


	function config_boiler_plate()
	{



		$query = array(
			"SHOW TABLES",
		);
		$query = implode(" ", $query);
		$query_result = $this->CI->db->query($query)->result_array();


		// $query_result = array_column($query_result, 'Field');

		$tables = array();
		foreach ($query_result as $key => $value) {
			$value = reset($value);
			$tables[$value] = array();
		}
		// ksort($tables);


		$tables_and_fields = array();
		foreach ($tables as $key => $value) {

			$query = array(
				"SHOW COLUMNS FROM $key",
			);
			$query = implode(" ", $query);
			$query_result = $this->CI->db->query($query)->result_array();


			$prime_key_index = array_search('PRI', array_column($query_result, 'Key'));
			$tables_and_fields[$key]["primary_key"] = $query_result[$prime_key_index]["Field"];

			$fields = $query_result;

			$tables_and_fields[$key]["state_indicator"] = "";

			$fields_result = array();
			foreach ($fields as $fields_key => $fields_value) {
				$tables_and_fields[$key]["state_indicator_(option_".$fields_key.")"] = $fields_value["Field"];
			}
			// ksort($fields_result);


			// $tables_and_fields[$key] = $fields_result;



			// $query = array(
			// 	"SHOW TABLE STATUS LIKE '$key'",
			// );
			// $query = implode(" ", $query);
			// $query_result = $this->CI->db->query($query)->result_array();
			// $Auto_increment = $query_result[0]["Auto_increment"];
			//
			// $tables_and_fields[$key]["auto_increment"] = $Auto_increment;

		}

		$result["provider"] = "";
		$result["tables"] = $tables_and_fields;
		$result = json_encode($result, JSON_PRETTY_PRINT);
		return $result;

	}

	function configs()
	{
		$result = APPPATH.'g_table_gate/configs.json';
		// include($erd_two_path);
		$result = file_get_contents($result);
		$result = json_decode($result, true);


		$result = json_encode($result, JSON_PRETTY_PRINT);


		return $result;
	}

	function generate_state()
	{
		$configs = APPPATH.'g_table_gate/configs.json';
		// include($erd_two_path);
		$configs = file_get_contents($configs);
		if ($configs == "") {
			$configs = array();
		} else {
			$configs = json_decode($configs, true);
		}

		$tables = $configs["tables"];

		// foreach ($variable as $key => $value) {
		// 	// code...
		// }
		$result = array();

		foreach ($tables as $key => $value) {
			// $result = $this->CI->db->get($key)->result();


			// $items = $this->CI->db->select('count(*) as allcount')->from($key)->get()->result()[0]->allcount;


			$query = array(
				"SHOW TABLE STATUS WHERE `name` LIKE '$key' ;",
			);
			$query = implode(" ", $query);

			$items = 0;
			if (!empty($this->CI->db->query($query)->result_array())) {
				// code...
				$items = $this->CI->db->query($query)->result_array()[0]["Auto_increment"];
				// var_dump($items);
				// exit;
			}

			$items_per_page = 100;
			$pages = ceil($items/$items_per_page);
			if ($pages == 0) {
				$pages = 1;
			}



			$query_result = $this->CI->db->select($value["state_indicator"].",".$value["primary_key"])->from($key)->get()->result_array();

			$query_result_keys = array_column($query_result, $value["primary_key"]);
			$query_result_values = array_column($query_result, $value["state_indicator"]);

			$query_result_2 = array();
			$query_result_2 = array_combine($query_result_keys,$query_result_values);

			// ->limit($items_per_page-1, $start_item-1)


			$sub_result = range(1, $pages, 1);
			$sub_result_2 = array();

			$current_page = 0;
			$max_pages = -1; // -1 = infinity
			foreach ($sub_result as $page) {
				$end_item = $page*$items_per_page;
				$start_item = $end_item-$items_per_page+1;

				$rows = array();
				if ($current_page < $max_pages OR $max_pages == -1) {
					if (1==1) {
						// $rows = $this->CI->db->select($value["state_indicator"].",".$value["primary_key"])->from($key)->limit($items_per_page-1, $start_item-1)->get()->result_array();
						//
						// $query_result_keys = array_column($rows, $value["primary_key"]);
						// $query_result_values = array_column($rows, $value["state_indicator"]);
						// $rows = array_combine($query_result_keys,$query_result_values);
					}

					$rows_range  = range($start_item, $end_item, 1);
					$rows_range = array_fill_keys($rows_range,"");
					$rows = array_intersect_key($query_result_2, $rows_range);
					ksort($rows);
				}
				// $sub_result_2[$start_item."-".$end_item]["pages"] = "$items/$items_per_page";
				$sub_result_2[$start_item."-".$end_item] = $rows;


				$current_page = $current_page+1;

			}
			$result[$key] = $sub_result_2;





			// ## Total number of record with filtering
			// $this->CI->db->select('count(*) as allcount');
			// if($searchQuery != '')
			// $this->CI->db->where($searchQuery);
			// $records = $this->CI->db->get('employees')->result();
			// $totalRecordwithFilter = $records[0]->allcount;
			//
			// ## Fetch records
			// $this->CI->db->select('*');
			// if($searchQuery != '')
			// $this->CI->db->where($searchQuery);
			// $this->CI->db->order_by($columnName, $columnSortOrder);
			// $this->CI->db->limit($rowperpage, $start);
			// $records = $this->CI->db->get('employees')->result();


		}


		$result = json_encode($result, JSON_PRETTY_PRINT);


		return $result;
	}

	function check_saved_state($haystack, $entity_name)
	{

		$matches  = preg_grep('/'.$entity_name.'_TS_(.*?)_TS/i', $haystack);

		if (!empty($matches)) {
			$matches = reset($matches);
			preg_match('/'.$entity_name.'_TS_(.*?)_TS/i', $matches, $matches);
			$last_update = $matches[1];

			$db_dir = $entity_name."_TS_".$last_update."_TS";

			$result = array(
				'dir' => $db_dir,
				'last_update' => $last_update,
			);
		} else {
			$result = false;
		}

		return $result;

	}

	function save_state()
	{

		$database_name = $this->CI->db->database;

		// $g_table_gate_dir = APPPATH.'g_table_gate';
		// $compare_local_and_remote_states_file = $g_table_gate_dir."/compare_local_and_remote_states_$database_name.json";
		// $compare_local_and_remote_states = file_get_contents($compare_local_and_remote_states_file);
		// $compare_local_and_remote_states = json_encode($compare_local_and_remote_states);


		$dir = APPPATH.'g_table_gate/state';
		$dir_scandir = scandir($dir);


		$haystack = $dir_scandir;

		$check_saved_state_for_db = $this->check_saved_state($haystack, $database_name);

		// $result = array();

		if ($check_saved_state_for_db == false) {

			$now = date('Y-m-d H=i=s');
			$db_dir = $database_name."_TS_".$now."_TS";
			$db_full_dir = $dir."/".$db_dir;
			mkdir($db_full_dir);

			$result[] = str_replace($dir,"",$db_full_dir);

		} else {

			$db_dir = $check_saved_state_for_db["dir"];

		}

		$haystack_2 = scandir($dir."/".$db_dir);

		$generate_state = $this->generate_state();
		$generate_state = json_decode($generate_state);

		foreach ($generate_state as $table => $row_groups) {
			$check_saved_state_table = $this->check_saved_state($haystack_2, $table);

			if ($check_saved_state_table == false) {
				$now = date('Y-m-d H=i=s');
				$table_dir = $table."_TS_".$now."_TS";
				$table_full_dir = $dir."/".$db_dir."/".$table_dir;
				mkdir($table_full_dir);


				// $result[] = str_replace($dir,"",$table_full_dir);
			}	else {
				$table_dir = $check_saved_state_table["dir"];
				$table_full_dir = $dir."/".$db_dir."/".$table_dir;
			}


			$haystack_3 = scandir($table_full_dir);

			foreach ($row_groups as $row_group_name => $row_group_value) {
				$check_saved_state_row_group = $this->check_saved_state($haystack_3, $row_group_name);

				$row_group_value_json = json_encode($row_group_value, JSON_PRETTY_PRINT);

				if ($check_saved_state_row_group == false) {

					$now = date('Y-m-d H=i=s');
					$row_group_dir = $row_group_name."_TS_".$now."_TS".".json";
					$row_group_full_dir = $table_full_dir."/".$row_group_dir;
					file_put_contents($row_group_full_dir,$row_group_value_json);


					// $result[] = str_replace($dir,"",$row_group_full_dir);
				}	else {

					$row_group_dir = $check_saved_state_row_group["dir"].".json";
					$row_group_full_dir = $table_full_dir."/".$row_group_dir;
					$row_group_value_json_current = file_get_contents($row_group_full_dir);

					if ($row_group_value_json_current !== $row_group_value_json) {
						$now = date('Y-m-d H=i=s');
						$row_group_dir_new = $row_group_name."_TS_".$now."_TS".".json";
						$row_group_full_dir_new = $table_full_dir."/".$row_group_dir_new;

						file_put_contents($row_group_full_dir,$row_group_value_json);
						rename($row_group_full_dir, $row_group_full_dir_new);
					}
				}
			}
		}
	}

	function read_state_details($path)
	{


		$result = array();

		$file_loc = APPPATH.'g_table_gate/state';

		$level_1_root_scandir = scandir($file_loc);


		$level_2_database = $this->CI->db->database;
		$level_2_database = $this->check_saved_state($level_1_root_scandir, $level_2_database);

		if ($level_2_database !== false) {

			$level_2_database = $level_2_database["dir"];
			$file_loc = $file_loc."/".$level_2_database;
			$level_2_database_scandir = scandir($file_loc);

			$level_3_table = explode("--",$path);
			$level_3_table = $level_3_table[0];
			$level_3_table = $this->check_saved_state($level_2_database_scandir, $level_3_table);


			if ($level_3_table !== false) {
				$level_3_table = $level_3_table["dir"];
				$file_loc = $file_loc."/".$level_3_table;
				$level_3_table_scandir = scandir($file_loc);

				$level_4_row_group = explode("--",$path);
				$level_4_row_group = $level_4_row_group[1];
				$level_4_row_group = $this->check_saved_state($level_3_table_scandir, $level_4_row_group);

				if ($level_4_row_group !== false) {

					$level_4_row_group = $level_4_row_group["dir"];
					$file_loc = $file_loc."/".$level_4_row_group;
					$level_4_row_group_scandir = file_get_contents($file_loc.".json");
					$level_4_row_group_scandir = json_decode($level_4_row_group_scandir, true);
					$result = $level_4_row_group_scandir;
				}
			}
		}

		return $result;

	}

	function read_state_api($type, $path)
	{

		$result = "";
		$this->save_state();

		if ($type == "overview") {

			$result = $this->read_state_overview();

		}	elseif ($type == "details") {
			$result = $this->read_state_details($path);
		}

		return $result;

	}

	function read_state_overview()
	{



		$result = array();

		$level_1_file_loc = APPPATH.'g_table_gate/state';

		$level_1_root_scandir = scandir($level_1_file_loc);


		$level_2_database = $this->CI->db->database;
		$level_2_database = $this->check_saved_state($level_1_root_scandir, $level_2_database);

		if ($level_2_database !== false) {
			$level_2_database = $level_2_database["dir"];
			$level_2_file_loc = $level_1_file_loc."/".$level_2_database;
			$level_2_database_scandir = scandir($level_2_file_loc);

			preg_match('/(.*?)_TS_(.*?)_TS/i', $level_2_database, $preg_match_result);
			$level_2_database_name = $preg_match_result[1];
			$result["."] = $preg_match_result[2];


			// $level_3_table = explode("--",$path);
			// $level_3_table = $level_3_table[0];
			$level_3_tables = $level_2_database_scandir;
			foreach ($level_3_tables as $level_3_table_index => $level_3_table) {

				if ($level_3_table !== "." AND $level_3_table !== "..") {

					$level_3_file_loc = $level_2_file_loc."/".$level_3_table;
					$level_3_table_scandir = scandir($level_3_file_loc);

					// $time["timestamp"] = filemtime($level_3_file_loc);
					// $time["datetime"] = date('Y-m-d H=i=s', $time["timestamp"]);
					// $time["datetime"] = str_replace('=', ':', $time["datetime"]);
					// $time["timestamp_2"] = strtotime($time["datetime"]);
					// echo "<pre>";
					// var_dump($time);




					preg_match('/(.*?)_TS_(.*?)_TS/i', $level_3_table, $preg_match_result);
					$level_3_table_name = $preg_match_result[1];
					$result["."."/".$level_3_table_name] = $preg_match_result[2];

					$level_4_row_groups = $level_3_table_scandir;
					foreach ($level_4_row_groups as $level_4_row_group_index => $level_4_row_group) {

						if ($level_4_row_group !== "." AND $level_4_row_group !== "..") {
							$level_4_file_loc = $level_3_file_loc."/".$level_4_row_group;


							preg_match('/(.*?)_TS_(.*?)_TS/i', $level_4_row_group, $preg_match_result);
							$level_4_row_group = $preg_match_result[1];
							$result["."."/".$level_3_table_name."/".$level_4_row_group] = $preg_match_result[2];

							// touch($level_4_file_loc, $someTimestamp);
							// $result[$level_2_database][$level_3_table][$level_4_row_group] = array();
							// $level_4_file_loc = $level_3_file_loc."/".$level_4_row_group;
							// $level_4_row_group_scandir = file_get_contents($level_4_file_loc.".json");
							// $level_4_row_group_scandir = json_decode($level_4_row_group_scandir, true);
							// $result = $level_4_row_group_scandir;
						}
					}
				}

			}


		}

		return $result;

	}

	function compare_local_and_remote_states()
	{

		$configs = APPPATH.'g_table_gate/configs.json';
		// include($erd_two_path);
		$configs = file_get_contents($configs);
		if ($configs == "") {
			$configs = array();
		} else {
			$configs = json_decode($configs, true);
		}

		$provider_state = file_get_contents("https://".$configs["provider"]."/read_state_api/overview/1");
		// /read_state_api/row_group/groups--1-100
		$provider_state = json_decode($provider_state, true);





		$consumer_state = $this->read_state_overview();
		// $consumer_state = $this->read_state_overview();
		// read_state_details


		if ($consumer_state !== $provider_state) {
			$result = array(
				"identical" => "no",
				"consumer_state" => $consumer_state,
				"provider_state" => $provider_state
			);
		}	else {
			$result = array(
				"identical" => "yes"
			);
		}

		$result = json_encode($result, JSON_PRETTY_PRINT);
		return $result;

	}

}
