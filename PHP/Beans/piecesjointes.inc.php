<?php
class PiecesJointes{
	private $nomfichier;
	private $pj;
	private $id_event;
	private $valeur_module;
	private $date_seance;
	private $id_user;
	private $type_seance;
	private $groupe;
	private $date;

	/**
	 * PiecesJointes constructor.
	 * @param $nomfichier
	 * @param $pj
	 * @param $id_event
	 * @param $valeur_module
	 * @param $date_seance
	 * @param $id_user
	 * @param $type_seance
	 * @param $groupe
	 * @param $date;
	 */
	public function __construct($nomfichier = "", $pj = "", $id_event = 0, $valeur_module = "", $date_seance = "", $id_user = "", $type_seance = "", $groupe = "", $date = "")
	{
		$this->nomfichier = $nomfichier;
		$this->pj = $pj;
		$this->id_event = $id_event;
		$this->valeur_module = $valeur_module;
		$this->date_seance = $date_seance;
		$this->id_user = $id_user;
		$this->type_seance = $type_seance;
		$this->groupe = $groupe;
		$this->date = $date;
	}

	/**
	 * @return mixed
	 */
	public function getNomfichier()
	{
		return $this->nomfichier;
	}

	/**
	 * @return mixed
	 */
	public function getPj()
	{
		return $this->pj;
	}

	/**
	 * @return mixed
	 */
	public function getIdEvent()
	{
		return $this->id_event;
	}

	/**
	 * @return mixed
	 */
	public function getValeurModule()
	{
		return $this->valeur_module;
	}

	/**
	 * @return mixed
	 */
	public function getDateSeance()
	{
		return $this->date_seance;
	}

	/**
	 * @return mixed
	 */
	public function getIdUser()
	{
		return $this->id_user;
	}

	/**
	 * @return mixed
	 */
	public function getTypeSeance()
	{
		return $this->type_seance;
	}

	/**
	 * @return mixed
	 */
	public function getGroupe()
	{
		return $this->groupe;
	}

	/**
	 * @return mixed
	 */
	public function getDate()
	{
		return $this->date;
	}
}