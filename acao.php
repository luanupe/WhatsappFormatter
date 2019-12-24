<?php
	function lerArquivos($path, $arquivos = []) {
		$diretorio = dir($path);
		while($arquivo = $diretorio->read()) {
			$ext = pathinfo($arquivo, PATHINFO_EXTENSION);
			$ext = trim(strtolower($ext));
			if (($ext == 'txt')) array_push($arquivos, $arquivo);
		}
		$diretorio->close();
		return $arquivos;
	}
	
	function lerMensagens($arquivo, $pagina = 0, $limite = 10, $fim = 0, $mensagens = []) {
		$linhas = file($arquivo);
		try {
			$inicio = ($pagina * $limite);
			$fim = ($inicio + $limite);
			
			for ($i = $inicio; $i < $fim; ++$i) {
				if ((!isset($linhas[$i]))) break;
				array_push($mensagens, htmlspecialchars($linhas[$i]));
			}
		} catch (Exception $e) {
			// Ignorar Exceptions...
		}
		return $mensagens;
	}
	
	function abortar($code = 404) {
		http_response_code(404);
		die();
	}
	
	// Define a ação padrão
	if ((!isset($_GET['acao']))) $_GET['acao'] = 'index';
	
	//echo var_dump($_GET);
	
	// Executar uma determinada ação
	if (($_GET['acao'] == 'index'))
	{
		header('Content-Type: application/json');
		echo json_encode(lerArquivos('./conversas'));
	}
	else if (($_GET['acao'] == 'mensagens'))
	{
		if ((isset($_GET['arquivo']))) {
			$pagina = ($_GET['pagina'] ?? 0);
			$arquivo = ('./conversas/' . $_GET['arquivo']);
			header('Content-Type: application/json');
			echo json_encode(lerMensagens($arquivo, $pagina));
		}
		else {
			return abortar(404);
		}
	}
	else {
		abortar();
	}
?>