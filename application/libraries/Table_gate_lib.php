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


	function db_to_configs()
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

		$result = json_encode($tables_and_fields, JSON_PRETTY_PRINT);
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

	function configs_to_state_json()
	{
		$tables = APPPATH.'g_table_gate/configs.json';
		// include($erd_two_path);
		$tables = file_get_contents($tables);
		if ($tables == "") {
			$tables = array();
		} else {
			$tables = json_decode($tables, true);
		}

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

	function changes()
	{
		$dir = APPPATH.'g_table_gate/state';
		$dir_scandir = scandir($dir);

		$database_name = $this->CI->db->database;

		$haystack = $dir_scandir;

		$dir_and_timestamp_for_db = $this->dir_and_timestamp($haystack, $database_name);

		$result = array();

		if ($dir_and_timestamp_for_db == false) {

			$db_dir = $database_name;
			$db_full_dir = $dir."/".$db_dir;

			$result[str_replace($dir,"",$db_full_dir)] = "missing";

		} else {

			$db_dir = $dir_and_timestamp_for_db["dir"];

			$haystack_2 = scandir($dir."/".$db_dir);

			$configs_to_state_json = $this->configs_to_state_json();
			$configs_to_state_json = json_decode($configs_to_state_json);

			foreach ($configs_to_state_json as $table => $row_groups) {
				$dir_and_timestamp_table = $this->dir_and_timestamp($haystack_2, $table);

				if ($dir_and_timestamp_table == false) {
					$table_dir = $table;
					$table_full_dir = $dir."/".$db_dir."/".$table_dir;

					$result[str_replace($dir,"",$table_full_dir)] = "missing";
				}	else {
					$table_dir = $dir_and_timestamp_table["dir"];
					$table_full_dir = $dir."/".$db_dir."/".$table_dir;

					$haystack_3 = scandir($table_full_dir);

					foreach ($row_groups as $row_group_name => $row_group_value) {
						$dir_and_timestamp_row_group = $this->dir_and_timestamp($haystack_3, $row_group_name);

						$row_group_value_json = json_encode($row_group_value, JSON_PRETTY_PRINT);

						if ($dir_and_timestamp_row_group == false) {

							$row_group_dir = $row_group_name;
							$row_group_full_dir = $table_full_dir."/".$row_group_dir;

							$result[str_replace($dir,"",$row_group_full_dir)] = "missing";
						}	else {

							$row_group_dir = $dir_and_timestamp_row_group["dir"];
							$row_group_full_dir = $table_full_dir."/".$row_group_dir.".json";
							$row_group_value_json_current = file_get_contents($row_group_full_dir);

							if ($row_group_value_json_current !== $row_group_value_json) {
								$row_group_dir_new = $row_group_name;
								$row_group_full_dir_new = $table_full_dir."/".$row_group_dir_new;

								$result[str_replace($dir,"",$row_group_full_dir)] = "changed";
							}
						}
					}
				}
			}
		}

		$result = json_encode($result, JSON_PRETTY_PRINT);

		$g_table_gate_dir = APPPATH.'g_table_gate';
		$changes_file = $g_table_gate_dir."/changes_$database_name.json";
		file_put_contents($changes_file, $result);
		$result = file_get_contents($changes_file);

		return $result;

	}

	function sync()
	{

		$database_name = $this->CI->db->database;

		$g_table_gate_dir = APPPATH.'g_table_gate';
		$changes_file = $g_table_gate_dir."/changes_$database_name.json";
		$changes = file_get_contents($changes_file);
		$changes = json_encode($changes);


		$dir = APPPATH.'g_table_gate/state';
		$dir_scandir = scandir($dir);


		$haystack = $dir_scandir;

		$dir_and_timestamp_for_db = $this->dir_and_timestamp($haystack, $database_name);

		$result = array();

		if ($dir_and_timestamp_for_db == false) {

			$now = date('Y-m-d H-i-s');
			$db_dir = $database_name."_TS_".$now."_TS";
			$db_full_dir = $dir."/".$db_dir;
			mkdir($db_full_dir);

			$result[] = str_replace($dir,"",$db_full_dir);

		} else {

			$db_dir = $dir_and_timestamp_for_db["dir"];

		}

		$haystack_2 = scandir($dir."/".$db_dir);

		$configs_to_state_json = $this->configs_to_state_json();
		$configs_to_state_json = json_decode($configs_to_state_json);

		foreach ($configs_to_state_json as $table => $row_groups) {
			$dir_and_timestamp_table = $this->dir_and_timestamp($haystack_2, $table);

			if ($dir_and_timestamp_table == false) {
				$now = date('Y-m-d H-i-s');
				$table_dir = $table."_TS_".$now."_TS";
				$table_full_dir = $dir."/".$db_dir."/".$table_dir;
				mkdir($table_full_dir);


				$result[] = str_replace($dir,"",$table_full_dir);
			}	else {
				$table_dir = $dir_and_timestamp_table["dir"];
				$table_full_dir = $dir."/".$db_dir."/".$table_dir;
			}


			$haystack_3 = scandir($table_full_dir);

			foreach ($row_groups as $row_group_name => $row_group_value) {
				$dir_and_timestamp_row_group = $this->dir_and_timestamp($haystack_3, $row_group_name);

				$row_group_value_json = json_encode($row_group_value, JSON_PRETTY_PRINT);

				// var_dump($dir_and_timestamp_row_group);
				// echo "<br>";
				// var_dump($haystack_3);
				// echo "<br>";
				// var_dump($table);
				// echo "<br>";
				// echo "<br>";


				if ($dir_and_timestamp_row_group == false) {

					$now = date('Y-m-d H-i-s');
					$row_group_dir = $row_group_name."_TS_".$now."_TS".".json";
					$row_group_full_dir = $table_full_dir."/".$row_group_dir;
					file_put_contents($row_group_full_dir,$row_group_value_json);


					$result[] = str_replace($dir,"",$row_group_full_dir);
				}	else {

					$row_group_dir = $dir_and_timestamp_row_group["dir"].".json";
					$row_group_full_dir = $table_full_dir."/".$row_group_dir;
					$row_group_value_json_current = file_get_contents($row_group_full_dir);

					if ($row_group_value_json_current !== $row_group_value_json) {
						$now = date('Y-m-d H-i-s');
						$row_group_dir_new = $row_group_name."_TS_".$now."_TS".".json";
						$row_group_full_dir_new = $table_full_dir."/".$row_group_dir_new;

						file_put_contents($row_group_full_dir,$row_group_value_json);
						rename($row_group_full_dir, $row_group_full_dir_new);
					}

				}

			}




			// echo "<pre>";
			// var_dump($dir_scandir);
			// exit;
		}

		$result = json_encode($result, JSON_PRETTY_PRINT);
		return $result;

	}

	function dir_and_timestamp($haystack, $entity_name)
	{

		$matches  = preg_grep ('/'.$entity_name.'_TS_(.*?)_TS/i', $haystack);

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

	// function state_dir_to_state_json()
	// {
	// 	$this->changes();
	//
	//
	// 	$dir = APPPATH.'g_table_gate/state';
	// 	$dir_scandir = scandir($dir);
	//
	// 	$database_name = $this->CI->db->database;
	//
	// 	$haystack = $dir_scandir;
	//
	// 	$dir_and_timestamp_for_db = $this->dir_and_timestamp($haystack, $database_name);
	//
	// 	$result = array();
	//
	// 	if ($dir_and_timestamp_for_db == false) {
	//
	// 	} else {
	//
	// 		$haystack_2 = scandir($dir."/".$dir_and_timestamp_for_db["dir"]);
	// 		$db_dir = $dir_and_timestamp_for_db["dir"];
	//
	//
	// 		$configs_to_state_json = $this->configs_to_state_json();
	// 		$configs_to_state_json = json_decode($configs_to_state_json);
	//
	// 		foreach ($configs_to_state_json as $table => $row_groups) {
	// 			$dir_and_timestamp_table = $this->dir_and_timestamp($haystack_2, $table);
	//
	// 			if ($dir_and_timestamp_table == false) {
	//
	// 			}	else {
	// 				$table_dir = $dir_and_timestamp_table["dir"];
	// 				$table_full_dir = $dir."/".$db_dir."/".$table_dir;
	//
	//
	// 				$haystack_3 = scandir($table_full_dir);
	//
	// 				$result[$table_dir] = array();
	//
	// 				foreach ($row_groups as $row_group_name => $row_group_value) {
	// 					$dir_and_timestamp_row_group = $this->dir_and_timestamp($haystack_3, $row_group_name);
	//
	// 					$row_group_value_json = json_encode($row_group_value, JSON_PRETTY_PRINT);
	//
	// 					if ($dir_and_timestamp_row_group == false) {
	//
	// 					}	else {
	//
	// 						$row_group_dir = $dir_and_timestamp_row_group["dir"].".json";
	// 						$row_group_full_dir = $table_full_dir."/".$row_group_dir;
	// 						$row_group_value_json_current = file_get_contents($row_group_full_dir);
	// 						$row_group_value_array_current = json_decode($row_group_value_json_current);
	//
	// 						$result[$table_dir][$row_group_dir] = $row_group_value_array_current;
	// 					}
	//
	// 				}
	//
	// 			}
	//
	// 		}
	//
	// 	}
	//
	// 	$result = json_encode($result, JSON_PRETTY_PRINT);
	// 	return $result;
	//
	// }

	function state_json_to_state_dir()
	{
		$state_json = $this->configs_to_state_json();


		return $state_json;

	}

	function sync_api($type, $path)
	{
		$result = "";
		if ($type == "all") {
			$result = $state_json = $this->configs_to_state_json();
		}	elseif ($type == "row_group") {
			$result = $state_json = $this->row_group_state($path);
		}

		return $result;

	}

	function row_group_state($path)
	{

		$database_name = $this->CI->db->database;

		$g_table_gate_dir = APPPATH.'g_table_gate';
		$changes_file = $g_table_gate_dir."/changes_$database_name.json";
		$changes = file_get_contents($changes_file);
		$changes = json_encode($changes);


		$dir = APPPATH.'g_table_gate/state';
		$dir_scandir = scandir($dir);


		$haystack = $dir_scandir;

		$dir_and_timestamp_for_db = $this->dir_and_timestamp($haystack, $database_name);

		// $result = array();

		if ($dir_and_timestamp_for_db == false) {

			$now = date('Y-m-d H-i-s');
			$db_dir = $database_name."_TS_".$now."_TS";
			$db_full_dir = $dir."/".$db_dir;
			mkdir($db_full_dir);

			// $result[] = str_replace($dir,"",$db_full_dir);

		} else {

			$db_dir = $dir_and_timestamp_for_db["dir"];

		}

		$haystack_2 = scandir($dir."/".$db_dir);

		$configs_to_state_json = $this->configs_to_state_json();
		$configs_to_state_json = json_decode($configs_to_state_json, true);
		// echo "<pre>";
		// var_dump($configs_to_state_json);
		// exit;

		$path_array = explode("--",$path);
		$table = $path_array[0];
		$row_group_name = $path_array[1];

		if (isset($configs_to_state_json[$table])) {
			$row_groups = $configs_to_state_json[$table];

			$dir_and_timestamp_table = $this->dir_and_timestamp($haystack_2, $table);

			if ($dir_and_timestamp_table == false) {
				$now = date('Y-m-d H-i-s');
				$table_dir = $table."_TS_".$now."_TS";
				$table_full_dir = $dir."/".$db_dir."/".$table_dir;
				mkdir($table_full_dir);


				// $result[] = str_replace($dir,"",$table_full_dir);
			}	else {
				$table_dir = $dir_and_timestamp_table["dir"];
				$table_full_dir = $dir."/".$db_dir."/".$table_dir;
			}


			$haystack_3 = scandir($table_full_dir);

			if (isset($configs_to_state_json[$table][$row_group_name])) {
				$row_group_value = $configs_to_state_json[$table][$row_group_name];

				$dir_and_timestamp_row_group = $this->dir_and_timestamp($haystack_3, $row_group_name);

				$row_group_value_json = json_encode($row_group_value, JSON_PRETTY_PRINT);
				$result = $row_group_value;

				// var_dump($dir_and_timestamp_row_group);
				// echo "<br>";
				// var_dump($haystack_3);
				// echo "<br>";
				// var_dump($table);
				// echo "<br>";
				// echo "<br>";


				if ($dir_and_timestamp_row_group == false) {

					$now = date('Y-m-d H-i-s');
					$row_group_dir = $row_group_name."_TS_".$now."_TS".".json";
					$row_group_full_dir = $table_full_dir."/".$row_group_dir;
					file_put_contents($row_group_full_dir,$row_group_value_json);


					// $result[] = str_replace($dir,"",$row_group_full_dir);
				}	else {

					$row_group_dir = $dir_and_timestamp_row_group["dir"].".json";
					$row_group_full_dir = $table_full_dir."/".$row_group_dir;
					$row_group_value_json_current = file_get_contents($row_group_full_dir);

					if ($row_group_value_json_current !== $row_group_value_json) {
						$now = date('Y-m-d H-i-s');
						$row_group_dir_new = $row_group_name."_TS_".$now."_TS".".json";
						$row_group_full_dir_new = $table_full_dir."/".$row_group_dir_new;

						file_put_contents($row_group_full_dir,$row_group_value_json);
						rename($row_group_full_dir, $row_group_full_dir_new);
					}
				}

			}
		}


		$result = json_encode($result, JSON_PRETTY_PRINT);
		return $result;

	}



}
