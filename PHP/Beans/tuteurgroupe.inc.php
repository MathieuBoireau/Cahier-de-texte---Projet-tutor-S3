<?php

class TuteurGroupe {

	private $id_user;
	private $groupe;
	private static $db;


	public function __construct($id_user = '', $groupe = '') {
		$this->id_user = $id_user;
        $this->groupe  = $groupe;
        
		if (is_null(TuteurGroupe::$db)) {
			TuteurGroupe::$db = DB::getInstance();
		}
    }
    
    public function getId()       { return $this->id_user; }
    public function getGroupe()   { return $this->groupe; }
}
?>
