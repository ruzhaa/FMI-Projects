<?php

class FooterTemplate{
    function __construct() {
    }
	function display($parameters){
		return '
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
			<script type="text/javascript" src="js/index.js"></script>
		</body>
		</html>
		';
	}
}
