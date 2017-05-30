<?php

register_shutdown_function('errorYa');

function errorYa(){
	print_r(error_get_last());
}

?>

<form action="">
	<a href="http://dark.dev.com/tests/trigger-related/trigger-related.php">Main link</a>
	<input type="submit" name="drop_trigger" value="drop_trigger"/>
	<input type="submit" name="show_triggers" value="Show triggers"/>
</form>

<?php
if (!defined('DB_NAME')) {
	define('DB_NAME', 'demos');
	define('DB_USER', 'root');
	define('DB_PASSWORD', '');
	define('DB_HOST', 'localhost');
	define('DB_CHARSET', 'utf8');
	define('DB_COLLATE', 'utf8_unicode_ci');
	define('WP_DEBUG', '');
}

include 'wp-functions.php';
include 'wp-db.php';

echo 'beginning';

global $wpdb;
$wpdb = new \wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
$wpdb->charset = null;

$obj = new Trigger_WPTC();

if(!empty($_REQUEST['drop_trigger'])){
	$obj->dropTrigger('before_rifai_insert');
	return;
}

if(!empty($_REQUEST['show_triggers'])){
	$obj->show_triggers();
	return;
}

$values = $obj->addTriggerForThisTable('rifai');

echo "<br>";

echo 'Trying to print column valuies';
print_r($values);
file_put_contents('DE_cl.php',"\n ----------------values------------ ".var_export($values,true)."\n",FILE_APPEND);

echo "<br>";

echo 'Nlasmdalksd';


class Trigger_WPTC{
	public static $table_structure;

	private $wpdb;

	function __construct($foo = null)
	{
		$this->foo = $foo;
		$this->wpdb = new \wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
		$this->wpdb->charset = null;
	}

	public function getCachedTableStructure($table_name)
	{
		if(!empty($this->table_structure[$table_name])){
			return $table_structure;
		}
		return false;
	}

	public function getTableStructure($table_name)
	{
		global $wpdb;

		$this_table_detail = $this->getCachedTableStructure($table_name);
		if($this_table_detail){
			return $this_table_detail;
		}

		$all_tables = $wpdb->query('SHOW TABLES');

		dark_debug($all_tables, "--------all_tables--------");
	}

	public function getColumnsDetailForThisTable($table_name)
	{
		global $wpdb;
		$columns_arr = $wpdb->get_results("SHOW columns FROM $table_name; ", ARRAY_A);

		return $columns_arr;
	}

	public function prepareReverseOfInsertQuery($table_name)
	{
		$columns_arr = $this->getColumnsDetailForThisTable($table_name);

		$where_stmts = '';
		$is_primary_key_registered = false;
		foreach($columns_arr as $k => $single_column){
			$where_stmts .= $single_column['Field'] . ' = ' . 'NEW.' . $single_column['Field'] . ' AND ';
			if($single_column['Key'] == 'PRI'){
				break;
			}
		}

		$where_stmts = rtrim($where_stmts, ' AND ');

		$rev_ins_query = "DELETE FROM `$table_name` WHERE " . $where_stmts;
		// $rev_ins_query = "DELETE FROM `$table_name` WHERE ";

		return $rev_ins_query;
	}

	public function prepareReverseOfDeleteQuery($table_name)
	{
		$columns_arr = $this->getColumnsDetailForThisTable($table_name);

		$where_stmts = '';
		$is_primary_key_registered = false;
		foreach($columns_arr as $k => $single_column){
			$where_stmts .= $single_column['Field'] . ' = ' . 'NEW.' . $single_column['Field'] . ' AND ';
			if($single_column['Key'] == 'PRI'){
				break;
			}
		}

		$where_stmts = rtrim($where_stmts, ' AND ');

		$rev_ins_query = "INSERT INTO `$table_name` WHERE " . $where_stmts;

		return $rev_ins_query;
	}

	public function addTriggerForThisTable($table_name)
	{
		$qry_to_write = $this->prepareReverseOfInsertQuery($table_name);
		//$qry_to_write = base64_encode($qry_to_write);

		file_put_contents('DE_cl.php',"\n ----------------qry_to_write------------ ".var_export($qry_to_write,true)."\n",FILE_APPEND);

		$extra_where = "id = ";

		$trigger_query = '
			CREATE TRIGGER before_rifai_insert
			    AFTER INSERT ON rifai
			    FOR EACH ROW 
					BEGIN
						DECLARE REV_INS_QRY TEXT DEFAULT "";
						DECLARE column_names_string TEXT;

						DECLARE o_pos INT(90);
						DECLARE c_name TEXT;
						DECLARE c_count INT(90);
						DECLARE done INT DEFAULT 0;

						DECLARE cur2 CURSOR FOR SELECT COUNT(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "'.DB_NAME.'" AND TABLE_NAME = "'.$table_name.'";

						DECLARE cur1 CURSOR FOR SELECT ORDINAL_POSITION, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "'.DB_NAME.'" AND TABLE_NAME = "'.$table_name.'";

						DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

							OPEN cur2;

							read_loop: LOOP
								FETCH cur2 INTO c_count;
								IF done THEN
							      LEAVE read_loop;
							    END IF;
							END LOOP;

							SET done = 0;

							OPEN cur1;

							read_loop: LOOP
								FETCH cur1 INTO o_pos, c_name;
								IF done THEN
							      LEAVE read_loop;
							    END IF;
								IF o_pos < c_count THEN
									SET REV_INS_QRY = CONCAT(REV_INS_QRY, "WHERE ", c_name, " = ", (NEW.c_name), " AND ");
								ELSE
									SET REV_INS_QRY = CONCAT(REV_INS_QRY, "WHERE ", c_name, " = ", (NEW.c_name), c_name);
								END IF;
							END LOOP;

							CLOSE cur1;

					    INSERT INTO rifai_audit (query) VALUES (REV_INS_QRY);
					END';

		file_put_contents('DE_cl.php',"\n ----------------trigger_query------------ ".var_export($trigger_query,true)."\n",FILE_APPEND);

		$trigger_result = $this->wpdb->query($trigger_query);

		if($trigger_result === false){
			file_put_contents('DE_cl.php',"\n ----------------mysqli_error------------ ".var_export(mysqli_error($this->wpdb->dbh),true)."\n",FILE_APPEND);
		}

		file_put_contents('DE_cl.php',"\n ----------------trigger_result------------ ".var_export($trigger_result,true)."\n",FILE_APPEND);
	}

	public function prepareInverseQry($table)
	{
		
	}

	public function dropTrigger($table_name)
	{
		$this->wpdb->query('DROP TRIGGER ' . $table_name);
	}

	public function show_triggers()
	{
		$results = $this->wpdb->get_results('SHOW TRIGGERS');
		file_put_contents('DE_cl.php',"\n ----------------show_triggers------------ ".var_export($results,true)."\n",FILE_APPEND);
		var_dump($results);
		return $results;
	}
}