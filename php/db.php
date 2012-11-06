<?php



class Database {
	
	private $link = null;
	function __construct()
	{		
		include 'config.php';
		$db_dns = "mysql:dbname=" . $mysql_name . ";host=127.0.0.1";
		$this->link = new PDO($db_dns, $mysql_user, $mysql_pass);
	}
	
	function getJamList($user)
	{
		$sql = "SELECT DISTINCT sessid, `desc`
 			FROM `usersess` 
			LEFT JOIN (`sess`) ON (sess.id = usersess.sessid) WHERE userid = :userid";
		$stm = $this->link->prepare($sql);
		
		$stm->execute(Array(':userid' =>$user));
		
		return $stm->fetchAll(PDO::FETCH_CLASS);
	}
	
	function createJam($authorUser, $userList, $title)
	{
		$sql = "INSERT INTO sess (`desc`, `owner`) VALUES ( :title , :owner )";
		$stm = $this->link->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$stm->bindParam(':title', $title);
		$stm->bindParam(':owner', $authorUser, PDO::PARAM_STR);
		$stm->execute();

		$rowId = $this->link->lastInsertId();
		
		$sql = "INSERT INTO usersess (`userid`, `sessid`) VALUES ( :uid, :sid)";
		$stm = $this->link->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		
		$stm->bindParam(':sid', $rowId);
		foreach($userList as $user)
		{
			//echo $user . " ";
			$stm->bindParam(':uid', $user);
			$stm->execute();
		}
		
		return $rowId;
	}
	
	function createEmptySession($reqId, $userId, $title)
	{
		$sql = "INSERT INTO sess (`desc`, `owner`) VALUES (:title, :owner)";
		$stm = $this->link->prepare($sql, array(PDO::ATTR_CURSOR => PDO__CURSOR_FWDONLY));
		$stm->bindParam(':title', $title);
		$stm->bindParam(':owner', $userId, PDO::PARAM_STR);
		
		$stm->execute();
		
		$rowId = $this->link->lastInsertId();
		
		$sql = "INSERT INTO usersess (`userid`, `sessid`) VALUES ( :uid, :sid)";
		$stm = $this->link->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		
		$stm->bindParam(':sid', $rowId);
		
		$stm->bindParam(':uid', $userId);
		$stm->execute();				
		
		$zero = 0;
		$stm->bindParam(':uid', $zero);
		$stm->execute();
		
		$sql = "INSERT INTO reqs (`reqid`, `sessid`) VALUES ( :req, :sess )";
		$stm = $this->link->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		
		$stm->bindParam(':req', $reqId, PDO::PARAM_STR);
		$stm->bindParam(':sess', $rowId, PDO::PARAM_INT);
		
		$stm->execute();
		
		return $rowId;
	}
	
	function updateSession($req, $userId)
	{
		$sql = "SELECT sessid FROM reqs WHERE reqid = :reqid";
		$stm = $this->link->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		
		$stm->bindParam(':reqid', $req, PDO::PARAM_STR);
		$stm->execute();
		
		//print_r(); echo "hey";
		$rows = $stm->fetchAll(PDO::FETCH_CLASS);
		$sessid = 	$rows[0]->sessid;
		
		$sql = "UPDATE usersess SET `userid` = :uid WHERE sessid = :id AND `userid` = 0";
		$stm = $this->link->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		
		$stm->bindParam(':uid', $userId, PDO::PARAM_INT);
		$stm->bindParam(':id', $sessid, PDO::PARAM_INT);
		$stm->execute();		
	
		return $sessid;
	
	}


}






