<?php
/**
 * PHPSense Pagination Class & Converted to Wordpress By ONLYPS.com
 * 
 * PHP tutorials and scripts
 *
 * @package		PHPSense
 * @author		Jatinder Singh Thind
 * @copyright	Copyright (c) 2006, Jatinder Singh Thind
 * @link		http://www.phpsense.com
 */

// ------------------------------------------------------------------------


class PS_Pagination {
	var $php_self;
	var $rows_per_page = 10; //Number of records to display per page
	var $total_rows = 10; //Total number of rows returned by the query
	var $links_per_page = 5; //Number of links to display per page
	var $append = ""; //Paremeters to append to pagination links
	var $sql = "";
	var $debug = false;
	var $conn = false;
	var $page = 1;
	var $max_pages = 0;
	var $offset = 0;
	var $firstPagetext = 'First';
	var $LastPageText = 'Last';
	
	/**
	 * Constructor
	 *
	 * @param resource $connection Mysql connection link
	 * @param string $sql SQL query to paginate. Example : SELECT * FROM users
	 * @param integer $rows_per_page Number of records to display per page. Defaults to 10
	 * @param integer $links_per_page Number of links to display per page. Defaults to 5
	 * @param string $append Parameters to be appended to pagination links 
	 */
	
	function PS_Pagination($conn,$script, $sql, $rows_per_page = 10, $links_per_page = 5, $append = "") {
		$this->conn = $conn;
		$this->script = $script;
		$this->sql = $sql;
		$this->rows_per_page = (int)$rows_per_page;
		if (intval($links_per_page ) > 0) {
		
			$this->links_per_page = (int)$links_per_page;
		} else {
			$this->links_per_page = 5;
		}
		$this->append = $append;
		$this->php_self = htmlspecialchars($script);
		if (isset($_GET['wp-page'] )) {
			$this->page = intval($_GET['wp-page'] );
		}
	}
	
	/**
	 * Executes the SQL query and initializes internal variables
	 *
	 * @access public
	 * @return resource
	 */
	function paginate() {
		//Check for valid mysql connection
		if (! $this->conn) {
			if ($this->debug)
				echo "MySQL connection missing<br />";
			return false;
		}
		
		//Find total number of rows
		$all_rs = @$this->conn->get_results($this->sql);
		
		if (! $all_rs) {
			if ($this->debug)
				echo "SQL query failed. Check your query.<br /><br />Error Returned: " . $mysqli->error;
			return false;
		}
		$this->total_rows = @$this->conn->num_rows;
		@mysqli_close($all_rs );
		
		//Return FALSE if no rows found
		if ($this->total_rows == 0) {
			if ($this->debug)
				echo "Query returned zero rows.";
			return FALSE;
		}
		
		//Max number of pages
		$this->max_pages = ceil($this->total_rows / $this->rows_per_page );
		if ($this->links_per_page > $this->max_pages) {
			$this->links_per_page = $this->max_pages;
		}
		
		//Check the page value just in case someone is trying to input an aribitrary value
		if ($this->page > $this->max_pages || $this->page <= 0) {
			$this->page = 1;
		}
		
		//Calculate Offset
		$this->offset = $this->rows_per_page * ($this->page - 1);
		
		//Fetch the required result set
		$rs = @$this->conn->get_results($this->sql . " LIMIT {$this->offset}, {$this->rows_per_page}" );
		if (! $rs) {
			if ($this->debug)
				echo "Pagination query failed. Check your query.<br /><br />Error Returned: " . $mysqli->error;
			return false;
		}
		return $rs;
	}
	
	/**
	 * Display the link to the first page
	 *
	 * @access public
	 * @param string $tag Text string to be displayed as the link. Defaults to 'First'
	 * @return string
	 */
	function renderFirst($tag = '') {
		if ($this->total_rows == 0)
			return FALSE;
		
		if ($this->page == 1) {
			return "$tag ";
		} else {
			return '<a href="' . $this->php_self . '?wp-page=1' . $this->append . '"><span>'.$this->firstPagetext.'</span></a> ';
		}
	}
	
	/**
	 * Display the link to the last page
	 *
	 * @access public
	 * @param string $tag Text string to be displayed as the link. Defaults to 'Last'
	 * @return string
	 */
	function renderLast($tag = '') {
		if ($this->total_rows == 0)
			return FALSE;
		
		if ($this->page == $this->max_pages) {
			return $tag;
		} else {
			return ' <a href="' . $this->php_self . '?wp-page=' . $this->max_pages . '' . $this->append . '"><span>'.$this->LastPageText.'</span></a>';
		}
	}
	
	/**
	 * Display the next link
	 *
	 * @access public
	 * @param string $tag Text string to be displayed as the link. Defaults to '>>'
	 * @return string
	 */
	function renderNext($tag = '<span>&gt;&gt;</span>') {
		if ($this->total_rows == 0)
			return FALSE;
		
		if ($this->page < $this->max_pages) {
			return '<a href="' . $this->php_self . '?wp-page=' . ($this->page + 1) . '' . $this->append . '">' . $tag . '</a>';
		} else {
			return $tag;
		}
	}
	
	/**
	 * Display the previous link
	 *
	 * @access public
	 * @param string $tag Text string to be displayed as the link. Defaults to '<<'
	 * @return string
	 */
	function renderPrev($tag = '<span>&lt;&lt;</span>') {
		if ($this->total_rows == 0)
			return FALSE;
		
		if ($this->page > 1) {
			return ' <a href="' . $this->php_self . '?wp-page=' . ($this->page - 1) . '' . $this->append . '">' . $tag . '</a>';
		} else {
			return " $tag";
		}
	}
	
	/**
	 * Display the page links
	 *
	 * @access public
	 * @return string
	 */
	function renderNav($prefix = '<span class="page_link">', $suffix = '</span>') {
		if ($this->total_rows == 0)
			return FALSE;
		
		$batch = ceil($this->page / $this->links_per_page );
		$end = $batch * $this->links_per_page;
		if ($end == $this->page) {
			//$end = $end + $this->links_per_page - 1;
		//$end = $end + ceil($this->links_per_page/2);
		}
		if ($end > $this->max_pages) {
			$end = $this->max_pages;
		}
		$start = $end - $this->links_per_page + 1;
		$links = '';
		
		for($i = $start; $i <= $end; $i ++) {
			if ($i == $this->page) {
				$links .= $prefix . " $i " . $suffix;
			} else {
				$links .= ' ' . $prefix . '<a href="' . $this->php_self . '?wp-page=' . $i . '' . $this->append . '">' . $i . '</a>' . $suffix . ' ';
			}
		}
		
		return $links;
	}
	
	/**
	 * Display full pagination navigation
	 *
	 * @access public
	 * @return string
	 */
	function renderFullNav() {
		return $this->renderFirst() . '&nbsp;' . $this->renderPrev() . '&nbsp;' . $this->renderNav() . '&nbsp;' . $this->renderNext() . '&nbsp;' . $this->renderLast();
	}
	
	/**
	 * Set debug mode
	 *
	 * @access public
	 * @param bool $debug Set to TRUE to enable debug messages
	 * @return void
	 */
	function setDebug($debug) {
		$this->debug = $debug;
	}
}
?>
