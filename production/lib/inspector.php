<?php

if (!$PROLOG_INCLUDED) {
	die();
}


/* ---------------------Inspector--------------------- */

class Inspector {
	
	# Получить список пользователей БД
	static function get_db_users() {
		$command = 'SELECT user_name from tdbusers order by user_name';
		$result = Database::query($command);
		
		return $result;
	}
	
	# Получить тарифы
	static function get_printerlist() {
		//Список принтеров
		return array ('Общий', 'Принтер 1', 'Принтер 2');
	}
	
	static function get_tariff($printer) {
		//Список тарифов
		$rand = rand(5, 15);
		if ($rand > 9)
			return array (10, 20, 300, 15, 4, 13);
		else	
			return array (455, 20, 310, 15, 4, 13);
	}
	
	//Сохранить тарифы
	static function save_tariff($printer, $items) {
		//Список тарифов
		return true;
	}
	
	# Получить списки пользователей
	static function get_userlist() {
		
		$command = 'SELECT DISTINCT user_name from tuserlocations order by user_name';
		$result = Database::query($command);
		
		return $result;
	}
	
	# Переиндексировать базу
	static function reindex_db () { //Стоит заглушка
		for($i = 0; $i < 1000000000; $i++) {
			$d = $i*2;
		}
	}
	
	# Получить записи журнала
	static function get_journal () {
		$command = "select SUBSTR(DATE_ADD('1899-12-31 00:00:00', INTERVAL a_time*24*3600 SECOND), 1, 16) `a_time`, user_name, src_info, a_id from tjournal order by a_time desc";
		$result = Database::query($command);
		
		return $result;
	}
	
	# Удалить старшие записи из журнала
	static function remove_old_recs_journal ($days) {
		if (!ctype_digit($days) || $days <0 )
			return false;
		$command = "DELETE FROM tjournal 
		WHERE DATEDIFF(NOW(), DATE_ADD('1899-12-31 00:00:00', INTERVAL a_time*24*3600 SECOND)) > " . $days;
		$result = Database::query($command);	
		if ($result) 
			return 'Удалено ' . Database::affected_rows() . ' строк';
		else
			return false;
	}
	
	# Работа с консолью
	static function execute_command ($command) {
		self::filter_command($command);
		$result = Database::query($command);
		self::filter_result($result);
		return $result;
	}
	
	# Фильтр комманд
	private static function filter_command (&$command) {
	}
	
	# Фильтр результатов
	private static function filter_result(&$result) {
		
		// Лимитировать вывод в 50 строк
		
		function get_column_width (&$table, $column_index) {
			$width = strlen($column_index);
			for ($i = 0; $i < count($table); $i++) {
				$cell_width = strlen($table[$i][$column_index]);
				if ($cell_width > $width)
					$width = $cell_width;
			}
			return $width;
		}
		function draw_line(&$columns) {
			$line = '';
			foreach($columns as $value) {
				$line .= str_repeat('-', $value+1);
			}
			return '\r' . $line ;
		}
		function align_center_cell(&$content, $cell_width) {
			$before_len = floor(($cell_width - strlen($content))/2);
			$after_len = ceil(($cell_width - strlen($content))/2);
			$content = str_repeat(' ', $before_len) . $content . str_repeat(' ', $after_len);
		}

		if (is_array($result)) {
			
			
			
			if (count($result)>0) {
				$limit = count($result);
				$is_limit_used = false;
				if (count($result)>50) {
					$limit = 50;
					$is_limit_used = true;
				}
				$tmp = '';
				$columns = array();
				//Максимальная ширина столбца
				foreach($result[0] as $key => $value) {
					$columns[$key] = get_column_width($result, $key);
				}
				//Строим шапку
				$tmp .= draw_line($columns);
				//Строим заголовки
				$tmp .='\r|';
				foreach($columns as $key => $value) {
					align_center_cell($key, $value);
					$tmp .= $key;
					//$tmp .= str_repeat(' ', $value-strlen($key));
					$tmp .='|';
				}
				$tmp .= draw_line($columns);
				//Строим построчно
				for ($i = 0; $i < $limit; $i++) {
					$tmp .= '\r';
					$tmp .='|';
					foreach($result[$i] as $key => $value) {
						
						align_center_cell($value, $columns[$key]);
						$tmp .=$value;
						//$tmp .= str_repeat(' ', $columns[$key]-strlen($value));
						
						$tmp .='|';
						
					}
					$tmp .= draw_line($columns);
					
				}
				if ($is_limit_used) {
					$tmp .= '\rQuery OK (' . $limit . ' rows only <USE LIMIT WITH OFFSET TO SHOW MORE ROWS>)';
				} else 
					$tmp .= '\rQuery OK (' . count($result) . ' rows)';
				
				$result = $tmp;
			} else {
				$result = 'Query OK (0 rows)';
			}
		} else  if (is_bool($result) && $result) {
			$result = 'Query OK (Affected ' . Database::affected_rows() . ' rows)';
		}
	}
	
	static function terminate() {
		Database::unload();
	}
}

?>