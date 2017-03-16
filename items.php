<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
 
 if($config['items'] == true) {
 
// Loading equipable items list
$itemsCache = new Cache('engine/cache/items');
if (user_logged_in() && is_admin($user_data)) {
	if (isset($_GET['update'])) {
		echo "<p><strong>Logged in as admin, loading engine/XML/items.xml file and updating cache.</strong></p>";
		// ITEMS XML TO PHP ARRAY
		$itemsXML = simplexml_load_file("engine/XML/items.xml");
		if ($itemsXML !== false) {
			$types = array();
			$type_attr = array();
			$groups = array();
 
			// This empty array will eventually contain all items grouped by type and indexed by item type
			$items = array();
 
			// Loop through each XML item object
			foreach ($itemsXML as $type => $item) {
				// Get item types
				if (!in_array($type, $types)) {
					$types[] = $type;
					$type_attr[$type] = array();
				}
				// Get item attributes
				$attributes = array();
				// Extract attribute values from the XML object and store it in a more manage friendly way $attributes
				foreach ($item->attributes() as $aName => $aValue)
					$attributes["$aName"] = "$aValue";
				// Remove unececsary attributes
				if (isset($attributes['plural'])) unset($attributes['plural']);
				//if (isset($attributes['id'])) unset($attributes['id']);
				//if (isset($attributes['fromid'])) unset($attributes['fromid']);
				//if (isset($attributes['toid'])) unset($attributes['toid']);
				if (isset($attributes['editorsuffix'])) unset($attributes['editorsuffix']);
				if (isset($attributes['article'])) unset($attributes['article']);
				// Populate type attributes
				foreach (array_keys($attributes) as $attr) {
					if (!in_array($attr, $type_attr[$type]))
						$type_attr[$type][] = $attr;
				}
 
				// Loop through every <attribute> object inside the <item> object
				$item_attributes = array();
				$iai = array();
				
				foreach ($item as $attribute) {
					foreach ($attribute->attributes() as $aName => $aValue) {
						if($aName == 'key') {
							$attribute_attributes["$aName"] = "$aValue";
							$iai[] = $attribute_attributes[$aName];
						}
					}
				}
				foreach ($item as $attribute) {
					foreach ($attribute->attributes() as $aName => $aValue) {
						$attribute_attributes["$aName"] = "$aValue";
						if(in_array($attribute_attributes[$aName], $iai)) {
							$whatis = $attribute_attributes[$aName];
						} else {
							$item_attributes[$whatis] = (isset($attribute_attributes[$aName])) ? $attribute_attributes[$aName] : false;
						}
					}
				}
				foreach (array_keys($attributes) as $attr) {
					if (!in_array($attr, $type_attr[$type]))
						$type_attr[$type][] = $attr;
				}
				
				// Add items with slotType or weaponType (TFS 1.x default)
				if(isset($attributes['id'])) $id = (isset($attributes['id'])) ? $attributes['id'] : false;
				if(isset($attributes['fromid'])) $id = (isset($attributes['name'])) ? $attributes['name'] : false;
				if (isset($item_attributes['slotType']) || isset($item_attributes['weaponType'])) {
					$items[$type][$id] = array('attributes' => $item_attributes);
					
					// Populate item array with potential relevant attributes for the item type
					foreach ($type_attr[$type] as $att)
						$items[$type][$id][$att] = (isset($attributes[$att])) ? $attributes[$att] : false;
				}
						
						
				$save = array($items);
				
				
			}
			$itemsCache->setContent($items);
			$itemsCache->save();
		} else {
			echo "<p><strong>Failed to load engine/XML/items.xml file.</strong></p>";
		}
	} else {
		$items = $itemsCache->load();
		?>
		<form action="">
			<input type="submit" name="update" value="Generate new cache">
		</form>
		<?php
	}
	// END ITEMS XML TO PHP ARRAY
} else {
	$items = $itemsCache->load();
}
// End loading items list
 
if ($items) {
	// Preparing data
	$types = array_keys($items);
	$itemServer = 'http://'.$config['shop']['imageServer'].'/';
 
	//slotType values and names
	if(isset($_GET['slot'])) {
		switch($_GET['slot']) {
			case 'helmet':
				$slottype = 'head';
				$slottype_name = 'Helmets';
				break;
			case 'sword':
				$slottype = 'sword';
				$slottype_name = 'Swords';
				break;
			case 'distance':
				$slottype = 'distance';
				$slottype_name = 'Distance Weapons';
				break;
			case 'wand':
				$slottype = 'wand';
				$slottype_name = 'Wands & Rods';
				break;
			case 'armor':
				$slottype = 'body';
				$slottype_name = 'Armors';
				break;
			case 'club':
				$slottype = 'club';
				$slottype_name = 'Clubs';
				break;
			case 'ammunition':
				$slottype = 'ammunition';
				$slottype_name = 'Ammunition';
				break;
			case 'book':
				$slottype = 'shield';
				$slottype_name = 'Spellbooks';
				break;
			case 'legs':
				$slottype = 'legs';
				$slottype_name = 'Legs';
				break;
			case 'axe':
				$slottype = 'axe';
				$slottype_name = 'Axes';
				break;
			case 'necklace':
				$slottype = 'necklace';
				$slottype_name = 'Amulets & Necklaces';
				break;
			case 'feet':
				$slottype = 'feet';
				$slottype_name = 'Boots';
				break;
			case 'shield':
				$slottype = 'shield';
				$slottype_name = 'Shields & Spellbooks';
				break;
			case 'backpack':
				$slottype = 'backpack';
				$slottype_name = 'Backpacks';
				break;
			case 'ring':
				$slottype = 'ring';
				$slottype_name = 'Rings';
				break;
			default:
				$slottype_name = 'null';
				break;
		}
	}
 
	// Render HTML
	if(isset($_GET['slot']) && ($slottype_name == 'null')) header("Location:items.php");
	?>
 
		<h1 id="items">Items<?php if (isset($_GET['slot'])) echo ' ('.$slottype_name.')';?></h1>
	<?php if(empty($_GET['slot'])) { ?>
	<table>
		<tbody>
			<tr> 
				<td style="text-align:center;"><a href="?slot=helmet">Helmets<br><img src="<?php echo $itemServer.'2471.gif'; ?>" /></a></td>
				<td style="text-align:center;"><a href="?slot=sword">Swords<br><img src="<?php echo $itemServer.'8931.gif'; ?>" /></a></td>
				<td style="text-align:center;"><a href="?slot=shield">Shields & Spellbooks<br><img src="<?php echo $itemServer.'2523.gif'; ?>" /></a></td>
				<td style="text-align:center;"><a href="?slot=necklace">Amulets<br><img src="<?php echo $itemServer.'2173.gif'; ?>" /></a></td>
			</tr>
			<tr>
				<td style="text-align:center;"><a href="?slot=armor">Armors<br><img src="<?php echo $itemServer.'2466.gif'; ?>" /></a></td>
				<td style="text-align:center;"><a href="?slot=club">Clubs<br><img src="<?php echo $itemServer.'2444.gif'; ?>" /></a></td>
				<td style="text-align:center;"><a href="?slot=wand">Wands & Rods<br><img src="<?php echo $itemServer.'2190.gif'; ?>" /></a></td>
				<td style="text-align:center;"><a href="?slot=ammunition">Ammunition<br><img src="<?php echo $itemServer.'6529.gif'; ?>" /></a></td>
			</tr>
			<tr>
				<td style="text-align:center;"><a href="?slot=legs">Legs<br><img src="<?php echo $itemServer.'2470.gif'; ?>" /></a></td>
				<td style="text-align:center;"><a href="?slot=axe">Axes<br><img src="<?php echo $itemServer.'8925.gif'; ?>" /></a></td>
				<td style="text-align:center;"><a href="?slot=ring">Rings<br><img src="<?php echo $itemServer.'6093.gif'; ?>" /></a></td>
			</tr>
			<tr>
				<td style="text-align:center;"><a href="?slot=feet">Boots<br><img src="<?php echo $itemServer.'2646.gif'; ?>" /></a></td>
				<td style="text-align:center;"><a href="?slot=distance">Distance<br><img src="<?php echo $itemServer.'5803.gif'; ?>" /></a></td>
				<td style="text-align:center;"><a href="?slot=backpack">Backpacks<br><img src="<?php echo $itemServer.'9774.gif'; ?>" /></a></td>
			</tr>
		</tbody>
	</table>
	<?php } else {  ?>
	<table>
		<tbody>
			<tr>
				<td></td>
				<td>Name</td>
				<td>Attributes</td>
			</tr>
	
<?php	foreach ($items['item'] as $select) { 
			$attributes = array();
			$extradef = NULL;
			$element = NULL;
				if (!empty($select['id'])) $itemid = $select['id'];
				else $itemid = $select['fromid'];
			
			if (!empty($select['attributes'])) {
				foreach ($select['attributes'] as $att => $value) {
					if($att == 'slotType' || $att == 'weaponType') $slotType = $value;
						if(!empty($slotType) && $slotType == $slottype) $show = true;
						else $show = false;
				}
			}
				
			if($show == true) { ?>
			<tr>
				<td><img src="<?php echo $itemServer.$itemid.'.gif'; ?>" /></td>
				<td><?php echo ucwords($select['name']); ?></td>
				<td><?php
				foreach ($select['attributes'] as $array => $value) {
		
					$extra = NULL;
					if($value > 0) $extra = '+'; 
						switch ($array) {
						case 'weight':
							echo ucwords($array).': '.intval($value/100).'.'.substr($value, -2).' oz<br>';
						break;
						case 'containerSize':
							echo 'Slots: '.$value.'<br>';
						break;
						case 'armor':
							echo ucwords($array).': '.$value.'<br>';
						break;
						case 'attack':
							echo ucwords($array).': '.$value;
							if($element != NULL) echo ' ('.$element.')';
							echo '<br>';
						break;
						case 'defense':
							echo ucwords($array).': '.$value;
							if($extradef != NULL) echo ' ('.$extradef.')';
							echo '<br>';
						break;
						case 'skillFist':
							echo 'Fist Fighting: '.$extra.$value.'<br>';
						break;
						case 'skillAxe':
							echo 'Axe Fighting: '.$extra.$value.'<br>';
						break;
						case 'skillSword':
							echo 'Sword Fighting: '.$extra.$value.'<br>';
						break;
						case 'skillClub':
							echo 'Club Fighting: '.$extra.$value.'<br>';
						break;
						case 'skillAxe':
							echo 'Axe Fighting: '.$extra.$value.'<br>';
						break;
						case 'skillDist':
							echo 'Distance Fighting: '.$extra.$value.'<br>';
						break;
						case 'skillShield':
							echo 'Shielding: '.$extra.$value.'<br>';
						break;
						case 'range':
							echo ucwords($array).': '.$value.'<br>';
						break;
						case 'shootType':
							echo 'Shoot Type: '.ucwords($value).'<br>';
						break;
						case 'hitChance':
							echo 'Hit: '.$extra.$value.'%<br>';
						break;
						case 'magiclevelpoints':
							echo 'Magic Level: '.$extra.$value.'<br>';
						break;
						case 'absorbPercentEnergy':
							echo 'Energy Protection: '.$extra.$value.'%<br>';
						break;
						case 'absorbPercentFire':
							echo 'Fire Protection: '.$extra.$value.'%<br>';
						break;
						case 'absorbPercentEarth':
							echo 'Earth Protection: '.$extra.$value.'%<br>';
						break;
						case 'absorbPercentPoison':
							echo 'Poison Protection: '.$extra.$value.'%<br>';
						break;
						case 'absorbPercentIce':
							echo 'Ice Protection: '.$extra.$value.'%<br>';
						break;
						case 'absorbPercentHoly':
							echo 'Holy Protection: '.$extra.$value.'%<br>';
						break;
						case 'absorbPercentDeath':
							echo 'Death Protection: '.$extra.$value.'%<br>';
						break;
						case 'absorbPercentLifeDrain':
							echo 'Life Drain Protection: '.$extra.$value.'%<br>';
						break;
						case 'absorbPercentManaDrain':
							echo 'Mana Drain Protection: '.$extra.$value.'%<br>';
						break;
						case 'absorbPercentDrown':
							echo 'Drown Protection: '.$extra.$value.'%<br>';
						break;
						case 'absorbPercentPhysical':
							echo 'Physical Protection: '.$extra.$value.'%<br>';
						break;
						case 'absorbPercentIce':
							echo 'Ice Protection: '.$extra.$value.'%<br>';
						break;
						/**case 'suppressDrunk':
							echo 'Suppress Drunk: Yes<br>';
						break;
						case 'suppressEnergy':
							echo 'Suppress Energy: Yes<br>';
						break;
						case 'suppressFire':
							echo 'Suppress Fire: Yes<br>';
						break;
						case 'suppressPoison':
							echo 'Suppress Poison: Yes<br>';
						break;
						case 'suppressDrown':
							echo 'Suppress Drown: Yes<br>';
						break;
						case 'suppressPhysical':
							echo 'Suppress Bleeding: Yes<br>';
						break;
						case 'suppressFreeze':
							echo 'Suppress Freeze: Yes<br>';
						break;
						case 'suppressDazzle':
							echo 'Suppress Dazzle: Yes<br>';
						break;
						case 'suppressCurse':
							echo 'Suppress Curse: Yes<br>';
						break; 
						Those are not necessary in my opinion, but if you want to show
						**/
						case 'speed':
							echo 'Speed: '.$extra.($value/2).'<br>';
						break;
						case 'charges':
							echo 'Charges: '.$value.'<br>';
						break;
					}
				}
			?>				
				</td>
			</tr>
					
					
<?php
			}
		} ?>
	
		</tbody>
	</table>
				
	<?php
	}
} else { ?>
	<h1>Items</h1>
	<p>Items have currently not been loaded into the website by the server admin.</p>
<?php }
} else {
	echo 'Items\' page not enabled.';
}
include 'layout/overall/footer.php'; 
?>
