<?php

class FooterTemplate{
    function __construct() {
    }
	function display($parameters){
		return '
			<script type="text/javascript" src="js/index.js"></script>
		</body>
		</html>
		';
	}
}
