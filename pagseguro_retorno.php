<?php
	/*
		Instalação
			- Configurando Pagseguro (Acessar https://pagseguro.uol.com.br/preferencias/integracoes.jhtml)
				- Notificação de Transação:
					- http://you-site/pagseguro_ipn.php
				- Página de redirecionamento:
					- A. Página fixa de redirecionamento
						- http://you-site/pagseguro_retorno.php
					- B. Redirecionamento com o código da transação
						- transaction
				- Gerar o Token e copiar para a próxima etapa

			- Configurando ZnoteACC
				- config.php
					- $config['pagseguro']['email']
						- Seu email da conta do pagseguro que irá receber o pagamento
					- $config['pagseguro']['token']
						- Preencher com o Token que pedi pra copiar na primeira etapa
					- $config['pagseguro']['product_name']
						- Nome do Produto

			- Instalando Tabelas
				CREATE TABLE IF NOT EXISTS `znote_pagseguro` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `transaction` varchar(36) NOT NULL,
				  `account` int(11) NOT NULL,
				  `price` decimal(11,2) NOT NULL,
				  `points` int(11) NOT NULL,
				  `payment_status` tinyint(1) NOT NULL,
				  `completed` tinyint(4) NOT NULL,
				  PRIMARY KEY (`id`),
				  FOREIGN KEY (account) REFERENCES accounts(id)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;

				CREATE TABLE IF NOT EXISTS `znote_pagseguro_notifications` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `notification_code` varchar(40) NOT NULL,
				  `details` text NOT NULL,
				  `receive_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	*/

	// Require the functions to fetch config values
	require 'config.php';

	// Require the functions to connect to database
	require 'engine/database/connect.php';

	$pagseguro = $config['pagseguro'];

	// Fetch and sanitize POST and GET values
	function getValue($value) {
		return (!empty($value)) ? sanitize($value) : false;
	}
	function sanitize($data) {
		return htmlentities(strip_tags(mysql_znote_escape_string($data)));
	}

	// Util function to insert log
	function report($code, $details = '') {
		$connectedIp = $_SERVER['REMOTE_ADDR'];
		$details = getValue($details);
		$details .= '\nConnection from IP: '. $connectedIp;
		mysql_insert('INSERT INTO `znote_pagseguro_notifications` VALUES (null, \'' . getValue($code) . '\', \'' . $details . '\', CURRENT_TIMESTAMP)');
	}

	function VerifyPagseguroIPN($code) {
		global $pagseguro;
		$url = $pagseguro['urls']['ws'];

		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($cURL, CURLOPT_URL, 'https://' . $url . '/v3/transactions/' . $code . '?email=' . $pagseguro['email'] . '&token=' . $pagseguro['token']);
		curl_setopt($cURL, CURLOPT_HEADER, false);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cURL, CURLOPT_FORBID_REUSE, true);
		curl_setopt($cURL, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($cURL, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($cURL, CURLOPT_TIMEOUT, 60);
		curl_setopt($cURL, CURLINFO_HEADER_OUT, true);
		curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
			'Connection: close',
			'Expect: ',
		));
		$Response = curl_exec($cURL);
		$Status = (int)curl_getinfo($cURL, CURLINFO_HTTP_CODE);
		curl_close($cURL);

		return trim($Response);
	}

	$transactionCode = getValue($_GET['transaction']);
	$rawTransaction = VerifyPagseguroIPN($transactionCode);
	$transaction = simplexml_load_string($rawTransaction);

	$transactionStatus = (int) $transaction->status;
	$completed = ($transactionStatus != 7) ? 0 : 1;

	$custom = (int) $transaction->reference;
	$item = $transaction->items->item[0];
	$points = $item->quantity;
	$price = $points * ($pagseguro['price'] / 100);
	mysql_insert('INSERT INTO `znote_pagseguro` VALUES (null, \'' . sanitize($transaction->code) . '\', ' . $custom . ', \'' . $price . '\', \'' . $points . '\', ' . $transactionStatus . ', ' . $completed . ')');

	header('Location: shop.php?callback=processing');
