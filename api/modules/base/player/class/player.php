<?php

class Player {

	protected $_playerdata = array(
		'id' => null,
		'name' => null,
		'world_id' => null,
		'group_id' => null,
		'account_id' => null,
		'level' => null,
		'vocation' => null,
		'health' => null,
		'healthmax' => null,
		'experience' => null,
		'lookbody' => null,
		'lookfeet' => null,
		'lookhead' => null,
		'looklegs' => null,
		'looktype' => null,
		'lookaddons' => null,
		'maglevel' => null,
		'mana' => null,
		'manamax' => null,
		'manaspent' => null,
		'soul' => null,
		'town_id' => null,
		'posx' => null,
		'posy' => null,
		'posz' => null,
		'conditions' => null,
		'cap' => null,
		'sex' => null,
		'lastlogin' => null,
		'lastip' => null,
		'save' => null,
		'skull' => null,
		'skulltime' => null,
		'rank_id' => null,
		'guildnick' => null,
		'lastlogout' => null,
		'blessings' => null,
		'balance' => null,
		'stamina' => null,
		'direction' => null,
		'loss_experience' => null,
		'loss_mana' => null,
		'loss_skills' => null,
		'loss_containers' => null,
		'loss_items' => null,
		'premend' => null,
		'online' => null,
		'marriage' => null,
		'promotion' => null,
		'deleted' => null,
		'description' => null,
		'onlinetime' => null,
		'deletion' => null,
		'offlinetraining_time' => null,
		'offlinetraining_skill' => null,
		'skill_fist' => null,
		'skill_fist_tries' => null,
		'skill_club' => null,
		'skill_club_tries' => null,
		'skill_sword' => null,
		'skill_sword_tries' => null,
		'skill_axe' => null,
		'skill_axe_tries' => null,
		'skill_dist' => null,
		'skill_dist_tries' => null,
		'skill_shielding' => null,
		'skill_shielding_tries' => null,
		'skill_fishing' => null,
		'skill_fishing_tries' => null,
	);
	protected $_znotedata = array(
		'comment' => null,
		'created' => null,
		'hide_char' => null,
	);
	protected $_name_id = false;
	protected $_querylog = array();
	protected $_errors = array();

	public function __construct($name_id_array, $fields = false, $query = true) {
		
		if (!is_array($name_id_array)) $this->_name_id = $name_id_array;

		if ($name_id_array !== false) {
			// Fetch player by name or id
			if (is_string($name_id_array) || is_integer($name_id_array)) {
				if ($query) {
					$this->update($this->mysql_select($name_id_array, $fields));
				}
			}

			// Load these player data.
			if (is_array($name_id_array)) {
				if (isset($name_id_array['id'])) $this->_name_id = $name_id_array['id'];
				elseif (isset($name_id_array['name'])) $this->_name_id = $name_id_array['name'];

				$this->update($name_id_array);
			}
		} else die("Player construct takes arguments: string or id for fetch, array for load.");
	}

	/**
	 * Return all player data, or the fields specified in param $fields.
	 *
	 * @param  array $fields
	 * @access public
	 * @return mixed (array 'field' => 'value', or false (bool))
	**/
	public function fetch($fields = false) {
		if (is_string($fields)) $fields = array($fields);
		// Return all data that is not null.
		if (!$fields) {
			$returndata = array();
			foreach ($this->_playerdata as $field => $value) {
				if (!is_null($value)) $returndata[$field] = $value;
			}
			foreach ($this->_znotedata as $field => $value) {
				if (!is_null($value)) $returndata[$field] = $value;
			}
			return $returndata;

		} else {
			// The return array
			$returndata = array();

			// Array containing null fields, we need to fetch these from db later on.
			$missingValues = array();

			// Populate the two above arrays
			foreach ($fields as $field) {

				if (array_key_exists($field, $this->_playerdata)) {
					if (is_null($this->_playerdata[$field])) $missingValues[] = $field;
					else $returndata[$field] = $this->_playerdata[$field];

				} elseif (array_key_exists($field, $this->_znotedata)) {
					if (is_null($this->_znotedata[$field])) $missingValues[] = $field;
					else $returndata[$field] = $this->_znotedata[$field];
				}
			}

			// See if we are missing any values
			if (!empty($missingValues)) {
				// Query for this data
				$data = $this->mysql_select($this->_name_id, $missingValues);
				// Update this object
				$this->update($data);
				foreach ($data as $field => $value) {
					$returndata[$field] = $value;
				}
			}
			return $returndata;
		}
		return false;
	}

	/**
	 * Update player data.
	 *
	 * @param  array $fields
	 * @access public
	 * @return mixed (array, boolean)
	**/
	public function update($data) {
		if (is_array($data) && !empty($data)) {
			foreach ($data as $field => $value) {

				if (array_key_exists($field, $this->_playerdata)) {
					$this->_playerdata[$field] = $value;
				
				} elseif (array_key_exists($field, $this->_znotedata)) {
					$this->_znotedata[$field] = $value;
				}
			}
			return true;
		}
		return false;
	}

	public function getErrors() {
		return (!empty($this->_errors)) ? $this->_errors : false;
	}
	public function dumpErrors() {
		if ($this->getErrors() !== false) 
			data_dump($this->getErrors(), false, "Errors detected in player class:");
	}

	/**
	 * Select player data from mysql. 
	 *
	 * @param  mixed (int, string) $name_id, array $fields
	 * @access private
	 * @return mixed (array, boolean)
	**/
	private function mysql_select($name_id, $fields = false) {
		$table = 'players';
		$znote_table = 'znote_players';
		$znote_fields = array();
		
		// Dynamic fields logic
		switch (gettype($fields)) {
			case 'boolean':
				$field_elements = '*';
				$znote_fields = array('comment', 'created', 'hide_char');
				break;
			
			case 'string':
				$fields = array($fields);

			case 'array':
				// Get rid of fields related to znote_
				foreach ($fields as $key => $field) {
					if (!array_key_exists($field, $this->_playerdata)) {
						$znote_fields[] = $field;
						unset($fields[$key]);
					}
				}

				//Since we use for loop later, we need to reindex the array if we unset something.
				if (!empty($znote_fields)) $fields = array_values($fields);

				// Add 'id' field if its not already there.
				if (!in_array('id', $fields)) $fields[] = 'id';

				// Loop through every field and generate the sql string
				for ($i = 0; $i < count($fields); $i++) {
					if ($i === 0) $field_elements = "`". getValue($fields[$i]) ."`";
					else $field_elements .= ", `". getValue($fields[$i]) ."`";
				}
			break;
		}

		// Value logic
		if (is_integer($name_id)) {
			$name_id = (int)$name_id;
			$where = "`id` = '{$name_id}'";
		} else {
			$name_id = getValue($name_id);
			$where = "`name` = '{$name_id}'";
		}

		$query = "SELECT {$field_elements} FROM `{$table}` WHERE {$where} LIMIT 1;";

		// Log query to player object
		$this->_querylog[] = $query;
		// Fetch from players table
		$data = mysql_select_single($query);
		if (isset($data['conditions'])) unset($data['conditions']);

		// Fetch from znote_players table if neccesary
		if (!empty($znote_fields)) {
			// Loop through every field and generate the sql string
			for ($i = 0; $i < count($znote_fields); $i++) {
				if ($i === 0) $field_elements = "`". getValue($znote_fields[$i]) ."`";
				else $field_elements .= ", `". getValue($znote_fields[$i]) ."`";
			}

			$query = "SELECT {$field_elements} FROM `{$znote_table}` WHERE `player_id`='".$data['id']."' LIMIT 1;";
			$this->_querylog[] = $query;
			$zdata = mysql_select_single($query);
			foreach ($zdata as $field => $value) $data[$field] = $value;
		}
		return $data;
	}

	/**
	 * Create player.
	 *
	 * @param  none
	 * @access public
	 * @return bool $status
	**/
	public function create() {
		// If player already have an id, the player already exist.
		if (is_null($this->_playerdata['id']) && is_string($this->_playerdata['name'])) {
			
			// Confirm player does not exist
			$name = format_character_name($this->_playerdata['name']);
			$name = validate_name($name);
			$name = sanitize($name);
			$exist = mysql_select_single("SELECT `id` FROM `players` WHERE `name`='{$name}' LIMIT 1;");
			if ($exist !== false) {
				$this->errors[] = "A player with the name [{$name}] already exist.";
				return false;
			}
			$config = fullConfig();

			if (user_character_exist($_POST['name']) !== false) {
				$errors[] = 'Sorry, that character name already exist.';
			}
			if (!preg_match("/^[a-zA-Z_ ]+$/", $_POST['name'])) {
				$errors[] = 'Your name may only contain a-z, A-Z and spaces.';
			}
			if (strlen($_POST['name']) < $config['minL'] || strlen($_POST['name']) > $config['maxL']) {
				$errors[] = 'Your character name must be between ' . $config['minL'] . ' - ' . $config['maxL'] . ' characters long.';
			}
			// name restriction
			$resname = explode(" ", $_POST['name']);
			foreach($resname as $res) {
				if(in_array(strtolower($res), $config['invalidNameTags'])) {
					$errors[] = 'Your username contains a restricted word.';
				}
				else if(strlen($res) == 1) {
					$errors[] = 'Too short words in your name.';
				}
			}
			// Validate vocation id
			if (!in_array((int)$_POST['selected_vocation'], $config['available_vocations'])) {
				$errors[] = 'Permission Denied. Wrong vocation.';
			}
			// Validate town id
			if (!in_array((int)$_POST['selected_town'], $config['available_towns'])) {
				$errors[] = 'Permission Denied. Wrong town.';
			}
			// Validate gender id
			if (!in_array((int)$_POST['selected_gender'], array(0, 1))) {
				$errors[] = 'Permission Denied. Wrong gender.';
			}
			if (vocation_id_to_name($_POST['selected_vocation']) === false) {
				$errors[] = 'Failed to recognize that vocation, does it exist?';
			}
			if (town_id_to_name($_POST['selected_town']) === false) {
				$errors[] = 'Failed to recognize that town, does it exist?';
			}
			if (gender_exist($_POST['selected_gender']) === false) {
				$errors[] = 'Failed to recognize that gender, does it exist?';
			}
			// Char count
			$char_count = user_character_list_count($session_user_id);
			if ($char_count >= $config['max_characters']) {
				$errors[] = 'Your account is not allowed to have more than '. $config['max_characters'] .' characters.';
			}
			if (validate_ip(getIP()) === false && $config['validate_IP'] === true) {
				$errors[] = 'Failed to recognize your IP address. (Not a valid IPv4 address).';
			}

			echo "create player";
			// Make sure all neccesary values are set
			//Register
			$character_data = array(
				'name'		=>	format_character_name($_POST['name']),
				'account_id'=>	$session_user_id,
				'vocation'	=>	$_POST['selected_vocation'],
				'town_id'	=>	$_POST['selected_town'],
				'sex'		=>	$_POST['selected_gender'],
				'lastip'	=>	getIPLong(),
				'created'	=>	time()
			);

			array_walk($character_data, 'array_sanitize');
			$cnf = fullConfig();
			
			if ($character_data['sex'] == 1) {
				$outfit_type = $cnf['maleOutfitId'];
			} else {
				$outfit_type = $cnf['femaleOutfitId'];
			}
			// Create the player

		} else {
			echo "Player already exist.";
			return false;
		}
	}
}

/*
$this->_file = $file . self::EXT;
$this->setExpiration(config('cache_lifespan'));
$this->_lifespan = $span;
*/