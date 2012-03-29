<?php 

/**
 * class swenjaConnection \n
 * Connects automatically to DB, close Connection to DB and update the DB structure
 *  
 */

class swenjaConnection extends CI_Model
{
	
 	function __construct()
 	{
 		// Call the model Constructor
 		parent::__construct();	
 	 			
 		if($this->open())
 		{
 			// Call init_db Function
 			return $this->init_db();
 			
 		}
 		else
 		{
 			return false;
 		}
	}
	
	function __destruct()
	{
		if($this->close())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * open() \n
	 * connect to DB with the properties which are specified in /config/database.php \n
	 * false if connection failed or returns true if the connection was successful \n
	 */
	function open()
	{	
		if(!$this->db)
		{
			return false;			
		}
		else 
		{
			return true;
		}
	}
	
	/**
	 * close() \n
	 * close connection to DB which is specified in /config/database.php \n
	 * returns true after closing Connection
	 */
	function close()
	{
		$this->db->close();
		return true;
	}
	
	/**
	 * init_db() \n
	 * Update Database structure. \n
	 * return true if all DB update has working correctly \n
	 */
	function init_db() 
	{
		// Have set database to autoload but i don't no if we should or not
	
		// Create !here! variabels
		$version = array();
		$result = null;
		$result_sw = null;
				
		// Look for table
		$result = $this->db->simple_query("SELECT version FROM history order by version limit 1");
	
	
		// No version of the database structure is on DB server
		if($result == false)
			$version['version'] = 0;
		// Version is on DB save value for swichting right update level
		else
		{
			// Read Version of Database if no version can Read an DB connection is ok it is Version Zero ;-)
			$result = $this->db->query("SELECT version FROM history order by version limit 1");
	
			$version = $result->row_array();
		}
	
	
		/*
		 * Switch to update from on version to the other
		 * Last version has return true so we go Top down to update versiona fter version
		 */
		switch($version['version'])
		{
			// Version 0
			case 0:
				// Create history table without constraint
				$result_sw = $this->db->query("CREATE TABLE history (id int not null primary key auto_increment".
						",version int,changelog tinytext) ENGINE=INNODB");
				if($result_sw == false)
					return false;
	
				// Create table content without constraint
				$result_sw = $this->db->query("CREATE TABLE content (id int not null primary key auto_increment".
						", value text) ENGINE=INNODB");
				if($result_sw == false)
					return false;
	
				// Create table page without constraint
				$result_sw = $this->db->query("CREATE TABLE page (id int not null primary key auto_increment".
						",uri tinytext,title tinytext,keywords text,discription text) ENGINE=INNODB");
				if($result_sw == false)
					return false;
	
				// Create table page_content with constraint to ID of page and content table
				$result_sw = $this->db->query("CREATE TABLE page_content (f_id_content int,f_id_page int,INDEX (f_id_content), INDEX (f_id_page),".
						"FOREIGN KEY (f_id_content) REFERENCES content(id) ON UPDATE CASCADE ON DELETE RESTRICT".
						", FOREIGN KEY (f_id_page) REFERENCES page(id) ON UPDATE CASCADE ON DELETE RESTRICT) ENGINE=INNODB");
				if($result_sw == false)
					return false;
	
				// Insert version number into history table
				$result_sw = $this->db->query("INSERT INTO history VALUES('',1,'First version of database')");
			case 1:
				// Next update should placed here
				return true;
			default:
				return false;
		}
		
	}
	
}

?>