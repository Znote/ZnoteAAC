<?php require_once 'engine/init.php'; include 'layout/overall/header.php';

// Loading spell list
$spellsCache = new Cache('engine/cache/spells');
if (user_logged_in() && is_admin($user_data)) {
	if (isset($_GET['update'])) {
		echo "<p><strong>Logged in as admin, loading engine/XML/spells.xml file and updating cache.</strong></p>";
		// SPELLS XML TO PHP ARRAY
		$spellsXML = simplexml_load_file("engine/XML/spells.xml");
		if ($spellsXML !== false) {
			$types = array();
			$type_attr = array();
			$groups = array();

			// This empty array will eventually contain all spells grouped by type and indexed by spell name
			$spells = array();

			// Loop through each XML spell object
			foreach ($spellsXML as $type => $spell) {
				// Get spell types
				if (!in_array($type, $types)) {
					$types[] = $type;
					$type_attr[$type] = array();
				}
				// Get spell attributes
				$attributes = array();
				// Extract attribute values from the XML object and store it in a more manage friendly way $attributes
				foreach ($spell->attributes() as $aName => $aValue)
					$attributes["$aName"] = "$aValue";
				// Remove unececsary attributes
				if (isset($attributes['script'])) unset($attributes['script']);
				if (isset($attributes['spellid'])) unset($attributes['spellid']);
				//if (isset($attributes['id'])) unset($attributes['id']);
				//if (isset($attributes['conjureId'])) unset($attributes['conjureId']);
				if (isset($attributes['function'])) unset($attributes['function']);
				// Populate type attributes
				foreach (array_keys($attributes) as $attr) {
					if (!in_array($attr, $type_attr[$type]))
						$type_attr[$type][] = $attr;
				}
				// Get spell groups
				if (isset($attributes['group'])) {
					if (!in_array($attributes['group'], $groups))
						$groups[] = $attributes['group'];
				}
				// Get spell vocations 
				$vocations = array();
				foreach ($spell->vocation as $vocation) {
					foreach ($vocation->attributes() as $attributeName => $attributeValue) {
						if ("$attributeName" == "name") {
							$vocId = vocation_name_to_id("$attributeValue");
							$vocations[] = ($vocId !== false) ? $vocId : "$attributeValue";
						} elseif ("$attributeName" == "id") {
							$vocations[] = (int)"$attributeValue";
						}
					}
				}
				// Exclude monster spells (Monster spells looks like this on the ORTS data pack)
				$words = (isset($attributes['words'])) ? $attributes['words'] : false;
				// Also exclude "house spells" such as aleta sio.
				$name = (isset($attributes['name'])) ? $attributes['name'] : false;
				if (substr($words, 0, 3) !== '###' && substr($name, 0, 5) !== 'House') {
					// Build full spell list where the spell name is the key to the spell array.
					$spells[$type][$name] = array('vocations' => $vocations);
					// Populate spell array with potential relevant attributes for the spell type
					foreach ($type_attr[$type] as $att)
						$spells[$type][$name][$att] = (isset($attributes[$att])) ? $attributes[$att] : false;
				}
			}
			// Sort the spell list properly
			foreach (array_keys($spells) as $type) {
				usort($spells[$type], function ($a, $b) {
					if (isset($a['lvl']))
						return $a['lvl'] - $b['lvl'];
					if (isset($a['maglv']))
						return $a['maglv'] - $b['maglv'];
					return -1;
				});
			}
			$spellsCache->setContent($spells);
			$spellsCache->save();
		} else {
			echo "<p><strong>Failed to load engine/XML/spells.xml file.</strong></p>";
		}
	} else {
		$spells = $spellsCache->load();
		?>
		<form action="">
			<input type="submit" name="update" value="Generate new cache">
		</form>
		<?php
	}
	// END SPELLS XML TO PHP ARRAY
} else {
	$spells = $spellsCache->load();
}
// End loading spell list

if ($spells) {
	// Preparing data
	$configVoc = $config['vocations'];
	$types = array_keys($spells);
	$itemServer = 'http://'.$config['shop']['imageServer'].'/';

	// Filter spells by vocation
	$getVoc = (isset($_GET['vocation'])) ? getValue($_GET['vocation']) : 'all';
	if ($getVoc !== 'all') {
		$getVoc = (int)$getVoc;
		foreach ($types as $type)
			foreach ($spells[$type] as $name => $spell)
				if (!empty($spell['vocations']))
					if (!in_array($getVoc, $spell['vocations']))
						unset($spells[$type][$name]);
	}

	// Render HTML
	?>

	<h1 id="spells">Spells<?php if ($getVoc !== 'all') echo ' ('.$configVoc[$getVoc]['name'].')';?></h1>

	<form action="#spells" class="filter_spells">
		<label for="vocation">Filter vocation:</label>
		<select id="vocation" name="vocation">
			<option value="all">All</option>
			<?php foreach ($config['vocations'] as $id => $vocation): ?>
				<option value="<?php echo $id; ?>" <?php if ($getVoc === $id) echo "selected"; ?>><?php echo $vocation['name']; ?></option>
			<?php endforeach; ?>
		</select>
		<input type="submit" value="Search">
	</form>

	<h2>Spell types:</h2>
	<ul>
		<?php foreach ($types as $type): ?>
		<li><a href="#spell_<?php echo $type; ?>"><?php echo ucfirst($type); ?></a></li>
		<?php endforeach; ?>
	</ul>

	<h2 id="spell_instant">Instant Spells</h2>
	<a href="#spells">Jump to top</a>
	<table class="table tbl-hover">
		<tbody>
			<tr class="yellow">
				<td>Name</td>
				<td>Words</td>
				<td>Level</td>
				<td>Mana</td>
				<td>Vocations</td>
			</tr>
			<?php foreach ($spells['instant'] as $spell): ?>
			<tr>
				<td><?php echo $spell['name']; ?></td>
				<td><?php echo $spell['words']; ?></td>
				<td><?php echo $spell['lvl']; ?></td>
				<td><?php echo $spell['mana']; ?></td>
				<td><?php
				if (!empty($spell['vocations'])) {
					$names = array();
					foreach ($spell['vocations'] as $id) {
						if (isset($configVoc[$id]))
							$names[] = $configVoc[$id]['name'];
					}
					echo implode(',<br>', $names);
				}
				?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<h2 id="spell_rune">Magical Runes</h2>
	<a href="#spells">Jump to top</a>
	<table class="table tbl-hover">
		<tbody>
			<tr class="yellow">
				<td>Name</td>
				<td>Level</td>
				<td>Magic Level</td>
				<td>Image</td>
				<td>Vocations</td>
			</tr>
			<?php foreach ($spells['rune'] as $spell): ?>
			<tr>
				<td><?php echo $spell['name']; ?></td>
				<td><?php echo $spell['lvl']; ?></td>
				<td><?php echo $spell['maglv']; ?></td>
				<td><img src="<?php echo $itemServer.$spell['id'].'.gif'; ?>" alt="Rune image"></td>
				<td><?php
				if (!empty($spell['vocations'])) {
					$names = array();
					foreach ($spell['vocations'] as $id) {
						if (isset($configVoc[$id]))
							$names[] = $configVoc[$id]['name'];
					}
					echo implode(',<br>', $names);
				}
				?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<h2 id="spell_conjure">Conjure Spells</h2>
	<a href="#spells">Jump to top</a>
	<table class="table tbl-hover">
		<tbody>
			<tr class="yellow">
				<td>Name</td>
				<td>Words</td>
				<td>Level</td>
				<td>Mana</td>
				<td>Soul</td>
				<td>Charges</td>
				<td>Image</td>
				<td>Vocations</td>
			</tr>
			<?php foreach ($spells['conjure'] as $spell): ?>
			<tr>
				<td><?php echo $spell['name']; ?></td>
				<td><?php echo $spell['words']; ?></td>
				<td><?php echo $spell['lvl']; ?></td>
				<td><?php echo $spell['mana']; ?></td>
				<td><?php echo $spell['soul']; ?></td>
				<td><?php echo $spell['conjureCount']; ?></td>
				<td><img src="<?php echo $itemServer.$spell['conjureId'].'.gif'; ?>" alt="Rune image"></td>
				<td><?php
				if (!empty($spell['vocations'])) {
					$names = array();
					foreach ($spell['vocations'] as $id) {
						if (isset($configVoc[$id]))
							$names[] = $configVoc[$id]['name'];
					}
					echo implode(',<br>', $names);
				}
				?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<a href="#spells">Jump to top</a>
	<?php
} else {
	?>
	<h1>Spells</h1>
	<p>Spells have currently not been loaded into the website by the server admin.</p>
	<?php
}

/* Debug tests
foreach ($spells as $type => $spells) {
	data_dump($spells, false, "Type: $type");
}

// All spell attributes?
'group', 'words', 'lvl', 'maglv', 'charges', 'allowfaruse', 'blocktype', 'mana', 'soul', 'prem', 'aggressive', 'range', 'selftarget', 'needtarget', 'blockwalls', 'needweapon', 'exhaustion', 'groupcooldown', 'needlearn', 'casterTargetOrDirection', 'direction', 'params', 'playernameparam', 'conjureId', 'reagentId', 'conjureCount', 'vocations'
*/
include 'layout/overall/footer.php'; ?>