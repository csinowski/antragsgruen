<?php

/**
 * @property integer $id
 * @property string $subdomain
 * @property string $name
 * @property string $name_kurz
 * @property int $offiziell
 * @property string $einstellungen
 * @property int $aktuelle_veranstaltung_id
 *
 * @property Veranstaltung[] $veranstaltungen
 * @property Veranstaltung $aktuelle_veranstaltung
 * @property Person[] $admins
 */
class VeranstaltungsReihe extends CActiveRecord
{

	/** @var null|VeranstaltungsReihenEinstellungen */
	private $einstellungen_object = null;

	/**
	 * @return VeranstaltungsreihenEinstellungen
	 */
	public function getEinstellungen()
	{
		if (!is_object($this->einstellungen_object)) $this->einstellungen_object = new VeranstaltungsreihenEinstellungen($this->einstellungen);
		return $this->einstellungen_object;
	}

	/**
	 * @param VeranstaltungsreihenEinstellungen $einstellungen
	 */
	public function setEinstellungen($einstellungen)
	{
		$this->einstellungen_object = $einstellungen;
		$this->einstellungen        = $einstellungen->toJSON();
	}


	/**
	 * @param Person $person
	 * @return bool
	 */
	public function isAdmin($person)
	{
		foreach ($this->admins as $e) if ($e->id == $person->id) return true;
		return false;
	}

	/**
	 * @return bool
	 */
	public function isAdminCurUser()
	{
		$user = Yii::app()->user;
		if ($user->isGuest) return false;
		if ($user->getState("role") === "admin") return true;
		$ich = Person::model()->findByAttributes(array("auth" => $user->id));
		/** @var Person $ich */
		if ($ich == null) return false;
		return $this->isAdmin($ich);
	}

	/**
	 * @var string $className
	 * @return GxActiveRecord
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}


	public function tableName()
	{
		return 'veranstaltungsreihe';
	}

	public static function label($n = 1)
	{
		return Yii::t('app', 'Veranstaltungsreihe|Veranstaltungsreihen', $n);
	}

	public static function representingColumn()
	{
		return 'name';
	}

	public function rules()
	{
		return array(
			array('id, subdomain, name, name_kurz, offiziell', 'required'),
			array('name', 'length', 'max' => 200),
			array('subdomain', 'length', 'max' => 45),
			array('name', 'length', 'max' => 100),
			array('name, name_kurz', 'safe'),
			array('id, name, subdomain, name_kurz, offiziell', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
	{
		return array(
			'veranstaltungen'        => array(self::HAS_MANY, 'Veranstaltung', 'veranstaltungsreihe_id'),
			'admins'                 => array(self::MANY_MANY, 'Person', 'veranstaltungsreihen_admins(veranstaltungsreihe_id, person_id)'),
			'aktuelle_veranstaltung' => array(self::BELONGS_TO, 'Veranstaltung', 'aktuelle_veranstaltung_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id'                     => Yii::t('app', 'ID'),
			'name'                   => Yii::t('app', 'Name'),
			'name_kurz'              => Yii::t('app', 'Name Kurz'),
			'subdomain'              => Yii::t('app', 'Subdomain'),
			'einstellungen'          => "Einstellungen",
			'offiziell'              => 'Offizielle Veranstaltungsreihe',
			'veranstaltungen'        => "Veranstaltungen",
			'aktuelle_veranstaltung' => "Aktuelle Veranstaltung",
			'admins'                 => null,
		);
	}

	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('name_kurz', $this->name_kurz, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}


	public function save($runValidation = true, $attributes = null)
	{
		Yii::app()->cache->delete("pdf_" . $this->id);
		return parent::save($runValidation, $attributes);
	}
}