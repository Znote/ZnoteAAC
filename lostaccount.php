<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
$acceptedChars = '123456789ZXCVBNMASDFGHJKLQWERTYUIOPzxcvbnmasdfghjklqwertyuiop';
if (empty($_POST) === false) {
  if(isset($_POST['step']) && !empty($_POST['step']) && getValue($_POST['step'])>2){
    if (!Token::isValid($_POST['token'])) {
      $errors[] = 'Token is invalid.';
    }
  }
}
if(empty($_GET)){
$step = (isset($_POST['step']) && !empty($_POST['step'])) ? getValue($_POST['step']) : false;
$character = (isset($_POST['character']) && !empty($_POST['character'])) ? getValue($_POST['character']) : false;
$option = (isset($_POST['option']) && !empty($_POST['option'])) ? getValue($_POST['option']) : false;
$new_email = (isset($_POST['new_email']) && !empty($_POST['new_email'])) ? getValue($_POST['new_email']) : false;
$rec_key = (isset($_POST['rec_key']) && !empty($_POST['rec_key'])) ? getValue($_POST['rec_key']) : false;

  switch ($step) {
    case '1':
    {
      {
        ?>
        <form action="" method="post">
          <h2>Specify your problem</h2>
          <label><input type="radio" name="option" value=0> I have forgotten my password</label><br>
          <label><input type="radio" name="option" value=1> I have forgotten my account name</label><br>
            <?php
              if($config['recovery_key']['change_email_by_recovery'] && empty($errors))
                echo '<label><input type="radio" name="option" value=2> I don\'t have access to my e-mail</label><br>';
                else
                echo '<label><input type="radio" name="option" value=2> Recovery password by key</label><br>';
              echo '<input type="hidden" name="character" value="'.$character.'">';
            ?>
          <button type="submit" name="step" value=2>Submit</button>
        </form>
        <?php
      }
    }
	break;
    break;
    case '2':
        {
          switch ($option) {
            case '0':
              header('Location: recovery.php?mode=password');
              break;
            case '1':
              header('Location: recovery.php?mode=username');
              break;
            case '2':
              {
                  if($config['recovery_key']['change_email_by_recovery']){
                    if(user_character_exist($character)){
                    ?>
                    <h2>We'll send authentication code to your new e-mail and then new password</h2>
                    <form action="" method="post">
                      <label>New e-mail address<br><input type="text" placeholder="new e-mail address" name="new_email" autocomplete="off"></label><br><br>
                      <label>Recovery key<br><input type="text" placeholder="recovery key" name="rec_key" autocomplete="off"></label><br>
                      <?php echo '<input type="hidden" name="character" value="'.$character.'">'; Token::create();?>
                      <button type="submit" name="step" value=3>Submit</button>
                    </form>
                    <?php
                  }else {
                    echo 'We can\'t find that character';
                    }
                  }else{
                    ?>
                    <form action="" method="post">
                      <label>Recovery key<br><input type="text" placeholder="recovery key" name="rec_key" autocomplete="off"></label><br>
                      <?php echo '<input type="hidden" name="character" value="'.$character.'">'; Token::create();?>
                      <button type="submit" name="step" value=3>Submit</button>
                    </form>
                    <?php
                  }
              }
              break;
            default:
              echo "Something went wrong, please conact with administrator.";
              break;
          }
        }
        break;
    case '3':
        {

          if(user_character_exist($character) && empty($errors)){
            $query = mysql_select_single("SELECT `p`.`id` AS `player_id`, `a`.`name`, `a`.`key`, `a`.`email_new_time`, `a`.`email`, `a`.`id` AS `account_id` FROM `players` `p` INNER JOIN `accounts` `a` ON `p`.`account_id`=`a`.`id` WHERE `p`.`name` = '$character' LIMIT 1;");
          if($config['recovery_key']['change_email_by_recovery']){
            if($query['key']==$rec_key && filter_var($new_email, FILTER_VALIDATE_EMAIL) != false  && $query['email']!=$new_email){
              if((intval($query['email_new_time']) - time())>=7140)  //interval
                echo "Something went wrong";
              else {
          			$tempKey = NULL;
          			for($i=0; $i < 25; $i++) {
          				$cnum[$i] = $acceptedChars{mt_rand(0, 60)};
          				$tempKey .= $cnum[$i];
          			}
                mysql_update("UPDATE `accounts` SET `email_code` = '".$tempKey."', `email_new` = '".$new_email."', `email_new_time` = '".intval(time()+7200)."' WHERE `id` = '".$query['account_id']."';");


                $thisurl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $thisurl .= "?confirm&u=".$query['account_id']."&tempkey=".$tempKey;

              //Authenticate email
                $mailer = new Mail($config['mailserver']);
                $title = "Please authenticate your account at $_SERVER[HTTP_HOST].";
                $body = "<h1>Please click on the following link to authenticate your account:</h1>";
                $body .= "<p><a href='$thisurl'>$thisurl</a></p>";
                $body .= "<hr><p>I am an automatic no-reply e-mail. Any emails sent back to me will be ignored.</p>";
                $mailer->sendMail($_POST['new_email'], $title, $body, $query['name']);

                echo 'We have sent an e-mail to your new address with link to authenticate this e-mail, please check it.';

                }
              } else {
                  echo 'Incorrect e-mail or recovery key.<br>';
                }
              }else{
                if((intval($query['email_new_time']) - time())>=7140)  //interval
                  echo "Something went wrong";
                  else{
                    $newPassword = NULL;
                		for($i=0; $i < 10; $i++) {
                			$cnum[$i] = $acceptedChars{mt_rand(0, 60)};
                			$newPassword .= $cnum[$i];
                		}
                    $salt = '';
                    if ($config['TFSVersion'] != 'TFS_03') {
                      // TFS 0.2 and 1.0
                      $password = sha1($newPassword);
                    } else {
                      // TFS 0.3/4
                      if (config('salt') === true) {
                        $saltdata = mysql_select_single("SELECT `salt` FROM `accounts` WHERE `id` = $auid LIMIT 1;");
                        if ($saltdata !== false) $salt .= $saltdata['salt'];
                      }
                      $password = sha1($salt.$newPassword);
                    }
                    mysql_update("UPDATE `accounts` SET `password`='".$password."', `email_new_time` = '".intval(time()+7200)."' WHERE `id` = '".$query['account_id']."';");
                    echo "Your new password is: ".$newPassword."<br>Stay safe.";
                  }

              }

          } else {
              echo output_errors($errors);
              echo 'This character not exist.';
            }

        }
      break;
    default:
      ?>
      <h2>Welcome to the Lost Account Interface!</h2><br>

      <p>If you have lost access to your account, this interface can help you. Of course, you need to prove that your claim to the account is justified. Enter the requested data and follow the instructions carefully. Please understand there is no way to get access to your lost account if the interface cannot help you.</p>

      <form action="" method="post">
      Character name: <br>
        <input type="text" name="character"><br>
        <button type="submit" name="step" value=1>Submit</button>
      </form>
      <?php
      break;
    }
}elseif (isset($_GET['confirm']) && empty($_GET['confirm'])) {
  $auid = (isset($_GET['u']) && (int)$_GET['u'] > 0) ? (int)$_GET['u'] : false;
	$tempKey = (isset($_GET['tempkey'])) ? $_GET['tempkey'] : false;
  $tempKeyStatus = true;
  for($i = 0;$i<strlen($tempKey); $i++)
  {
    $homeStatus = false;
    for($j = 0; $j<strlen($acceptedChars); $j++)
    {
      $homeStatus = false;
      if($tempKey[$i] == $acceptedChars[$j]){
          $homeStatus = true;
          break;
        }
    }
    if($homeStatus===false)
    {
      $tempKeyStatus=false;
      break;
    }
  }
if($tempKeyStatus===false)
  return false;
  $query = mysql_select_single("SELECT `email_code`, `email_new`, `name`, `password` FROM `accounts` WHERE `id` = $auid LIMIT 1;");
  if($query!==false && $query['email_code']==$tempKey && $query['email_new']){
		$newPassword = NULL;
		for($i=0; $i < 10; $i++) {
			$cnum[$i] = $acceptedChars{mt_rand(0, 60)};
			$newPassword .= $cnum[$i];
		}
    $salt = '';
    if ($config['TFSVersion'] != 'TFS_03') {
      // TFS 0.2 and 1.0
      $password = sha1($newPassword);
    } else {
      // TFS 0.3/4
      if (config('salt') === true) {
        $saltdata = mysql_select_single("SELECT `salt` FROM `accounts` WHERE `id` = $auid LIMIT 1;");
        if ($saltdata !== false) $salt .= $saltdata['salt'];
      }
      $password = sha1($salt.$newPassword);
    }
    //Send new password
      $mailer = new Mail($config['mailserver']);
      $title = "This is your new password at $_SERVER[HTTP_HOST].";
      $body = "<p>Password: ".$newPassword."</p>";
      $body .= "<p>Stay safe at ".$config['mailserver']['fromName'].".</p>";
      $body .= "<hr><p>I am an automatic no-reply e-mail. Any emails sent back to me will be ignored.</p>";
      $mailer->sendMail($query['email_new'], $title, $body, $query['name']);

    mysql_update("UPDATE `accounts` SET `email` = '".$query['email_new']."', `email_new` = '0', `email_code` = '0', `password` = '$password' WHERE `id` = $auid LIMIT 1;");
    echo "We have sent new password to your new e-mail, have fun! :)";

  }else{
    echo 'Something went wrong';
  }
}
include 'layout/overall/footer.php'; ?>
