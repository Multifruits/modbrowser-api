<?php
	// +-------------------------------------------------------------------------+
	// |                       MODBROWSER by Multifruits                         |
	// |                             api v2.0                                    |
	// +-------------------------------------------------------------------------+

	// Edit what you want under this line ----------------------------------------
	$host = ''; // Database host
	$name = ''; // Database name
	$id = ''; // Database ID
	$pwd = ''; // Database password

	$modtable = 'mb_mods'; // Mods table name
	$versiontable = 'mb_version'; // Versions table name

	// DON'T EDIT ANYTHING under this line ---------------------------------------

	// Connection to the database
	$db = new PDO('mysql:host='.$host.';dbname='.$name, $id, $pwd);

	$g = htmlentities($_GET["mode"]);
	
	if($g == "install")
	{
		// If not exist, create modbrowser tables
		$db->query("CREATE TABLE IF NOT EXISTS `". $modtable ."` (`id` int(11) NOT NULL,`author` varchar(30) CHARACTER SET utf8 NOT NULL,`name` varchar(30) CHARACTER SET utf8 NOT NULL,`image_url` varchar(255) CHARACTER SET utf8 DEFAULT NULL,`description` text CHARACTER SET utf8 NOT NULL,`note` int(11) DEFAULT NULL,`category` varchar(30) CHARACTER SET utf8 NOT NULL,`prerequisites` varchar(60) CHARACTER SET utf8 DEFAULT NULL) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf-8 COMMENT='modbrowser mods table'");
		$db->query("CREATE TABLE IF NOT EXISTS `". $versiontable ."` (`id` int(11) NOT NULL DEFAULT '0', `name` varchar(30) CHARACTER SET utf8 NOT NULL, `version` varchar(30) CHARACTER SET utf8 NOT NULL, `url` varchar(255) CHARACTER SET utf8 NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf-8 COMMENT='modbrowser versions table'");

	}
	elseif($g == "info")
	{
		// Retrieve client variables
		$n = htmlentities($_GET["n"]);
		$v = htmlentities($_GET["v"]);

		$request = $db->prepare("SELECT url FROM `". $versiontable ."` WHERE name='".$n."' AND version='".$v."'");
		$request->execute();
		$vdata = $request->fetch();
		$request->closeCursor();

		$request = $db->prepare("SELECT * FROM `". $modtable ."` WHERE name='".$n."'");
		$request->execute();
		header('Content-Type: application/json; Charset=UTF-8');
		while($mdata = $request->fetch()) {
			echo json_encode(array("name" => $n, "author" => mb_convert_encoding($mdata["author"], "UTF-8", "HTML-ENTITIES"), "category" => mb_convert_encoding($mdata["category"], "UTF-8", "HTML-ENTITIES"), "version" => $v, "description" => mb_convert_encoding($mdata['description'], "UTF-8", "HTML-ENTITIES"), "prerequisites" => $mdata["prerequisites"], "image_url" => mb_convert_encoding($mdata['image_url'], "UTF-8", "HTML-ENTITIES"), "jar" => array("url" => mb_convert_encoding($vdata['url'], "UTF-8", "HTML-ENTITIES"), "path" => "modpathgoeshere")), JSON_FORCE_OBJECT);
		}
		$request->closeCursor();
	}
	elseif($g == "version")
	{
		$id = htmlentities($_GET["n"]);
		$request = $db->prepare("SELECT * FROM ". $modtable ." WHERE name='$id'");
		$request->execute();

		while ($data = $request->fetch())
		{
			$name = $data['name'];
			$request2 = $db->prepare("SELECT * FROM ". $versiontable ." WHERE name='$name'");
			$request2->execute();
			while ($data = $request2->fetch())
			{
				echo $data['version'] . "\n";
			}
			$request2->closeCursor();
		}
		$request->closeCursor();
	}
	elseif($g == "list")
	{
		if(!empty($_GET["s"]))
		{
			$request = $db->prepare("SELECT * FROM `". $modtable ."` WHERE name LIKE '" . htmlentities($_GET["s"]) . "%'");
			$request->execute();
		}
		else
		{
			$request = $db->prepare("SELECT name FROM `". $modtable ."` LIMIT 50");
			$request->execute();
		}
		while ($data = $request->fetch())
		{
			echo $data['name'] . "\n";
		}
		$request->closeCursor();
	}
?>
