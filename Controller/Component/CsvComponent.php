<?php
/**
 * CsvComponent
 *
 * Copyright (c) 2014 Mithun Das ( mithundas79 )
 *
 * 
 * Redistributions of files must retain the above copyright notice.
 *
 */
App::uses('Component', 'Controller');
/**
 * CsvComponent
 *
 * PHP 5.x / CakePHP 2.x
 *
 * Allows the importing and exporting of a standard $this->data formatted array to and from csv files. 
 * 
 * 
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * @author mithundas79
 *
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 *
 */
class CsvComponent extends Object {
	
	/**
	 * Allows the mapping of preg-compatible regular expressions to public or
	 * private methods in this class, where the array key is a /-delimited regular
	 * expression, and the value is a class method.  Similar to the functionality of
	 * the findBy* / findAllBy* magic methods.
	 *
	 * @public array
	 * @access public
	 */
	public $defaults = array(
		'length' => 0,
		'delimiter' => ',',
		'enclosure' => '"',
		'escape' => '\\',
		'headers' => true,
		'text' => false,
	);
	
	public function initialize(Controller $controller, $settings = array()) {
		// saving the controller reference for later use
		$this->controller = $controller;
		$this->defaults = array_merge($this->defaults, $settings);
	}

	public function startup(){
	}

	public function shutdown(){
	}

	public function beforeRender(){
	}

	public function beforeRedirect(){
	}
	
	/**
	 * Import function
	 *
	 * @param string $filename path to the file under webroot
	 * @return array of all data from the csv file in [Model][field] format
	 * @author Mithun Das
	 */
	public function import($filename, $fields = array(), $options = array()) {
		$options = array_merge($this->defaults, $options);
		$data = array();

		// open the file
		if ($file = @fopen(WWW_ROOT . $filename, 'r')) {
			if (empty($fields)) {
				// read the 1st row as headings
				$fields = fgetcsv($file, $options['length'], $options['delimiter'], $options['enclosure']);
			}
			// Row counter
			$r = 0; 
			// read each data row in the file
			while ($row = fgetcsv($file, $options['length'], $options['delimiter'], $options['enclosure'])) {
				// for each header field 
				foreach ($fields as $f => $field) {
					// get the data field from Model.field
					if(!empty($row[$f])){
						if (strpos($field,'.')) {
							$keys = explode('.',$field);
							if (isset($keys[2])) {
								$data[$r][$keys[0]][$keys[1]][$keys[2]] = $row[$f];
							} else {
								$data[$r][$keys[0]][$keys[1]] = $row[$f];
							}
						} else {
							$data[$r][$this->controller->modelClass][$field] = $row[$f];
						}
					}
				}
				$r++;
			}

			// close the file
			fclose($file);

			// return the messages
			return $data;
		} else {
			return false;
		}
	}
	
	/**
	 * Converts a data array into 
	 *
	 * @param string $filename 
	 * @param string $data 
	 * @return void
	 * @author Mithun Das
	 */
	public function export($filename, $data, $options = array()) {
		$options = array_merge($this->defaults, $options);
		
		// open the file
		if ($file = @fopen(WWW_ROOT . $filename, 'w')) {
			//pr($options); exit;
			// Iterate through and format data
			$firstRecord = true;
			foreach ($data as $record) {
				$row = array();
				foreach ($record as $model => $fields) {
					// TODO add parsing for HABTM
					foreach ($fields as $field => $value) {
						if (!is_array($value)) {
							if ($firstRecord) {
								$headers[] = $field;
							}
							$row[] = $value;
						} // TODO due to HABTM potentially being huge, creating an else might not be plausible
					}
				}
				$rows[] = $row;
				$firstRecord = false;
			}
			
			if ($options['headers']) {
				// write the 1st row as headings
				fputcsv($file, $headers, $options['delimiter'], $options['enclosure']);
			}
			// Row counter
			$r = 0; 
			foreach ($rows as $row) {
				fputcsv($file, $row, $options['delimiter'], $options['enclosure']);
				$r++;
			}

			// close the file
			fclose($file);

			return $r;
		} else {
			return false;
		}
	}
}
?>