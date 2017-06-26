<?php

class HeaderTemplate{
    function __construct() {
    }
	function display($parameters){
		return '
			<!DOCTYPE html>
			<html>
			<head>
				<meta charset="utf-8">
			    <meta http-equiv="X-UA-Compatible" content="IE=edge">
			    <meta name="viewport" content="width=device-width, initial-scale=1">
				<title>Drop-out students</title>
				<link rel="stylesheet" href="css/style.css" />
			</head>
			<body>
		';
	}
}
