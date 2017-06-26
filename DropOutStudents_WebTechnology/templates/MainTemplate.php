<?php

class MainTemplate{
    function __construct() {
    }
	function display($parameters){

		$html = '<div class="menu">
			<a href="" class="menu-item active">View all</a>
			<a href="?page=students" class="menu-item">Add</a>
			<a href="" class="menu-item">Edit</a>
			<a href="" class="menu-item">Delete</a>
			<a href="?page=import" class="menu-item">Import</a>
		</div>
		<div class="container">
			<table>
				<tbody>
				<tr>
					<td>id</td>
					<td>name</td>
					<td>fn</td>
					<td>subject</td>';



		foreach ($parameters['categories'] as $cat) {
			$html .= '<td>'.$cat['title'].'</td>';
		}

		$html .= '</tr>';
				

		foreach($parameters['students'] as $student){
			$html .= '<tr><td>'. $student['id'] . "</td><td>". 
					 $student['name'] . "</td><td>".
					 $student['fk_number'] . "</td><td>".
					 $student['title'] . "</td><td>".
					 $student['category'] . "</td><td>".
					 $student['score'] .
					 "</td></tr>";
		}

		$html .= '</table></div>';



		return $html;
	}
}
