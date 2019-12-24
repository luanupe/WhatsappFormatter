<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<style>
			body {
				background:url('./assets/img/background.jpg');
				margin:0;
				padding:0;
			}
			#conteudo {
				margin: 0px 50px 50px 50px;
			}
			.conversa {
				display:block;
				background:#FFFFFF;
				margin:10px 100px 10px 0px;
				padding: 5px 5px 5px 5px;
			}
			.separador {
				display:block;
				text-align:center;
			}
			.mensagem {
				padding: 5px 5px 5px 5px;
			}
			.mensagemA {
				background:#DCF8C7;
				margin:10px 0px 10px 100px;
			}
			.mensagemB {
				background:#FFFFFF;
				margin:10px 100px 10px 0px;
			}
			.azul {
				background:#5DBCD2;
				margin:15px 10px 5px 10px;
				padding: 5px 5px 5px 5px;
			}
		</style>
	</head>
	
	<body onload="renderizarConversas()">
		<table>
			<tbody>
				<tr>
					<td width="30%" valign="top">
						<div id="conversas"></div>
					</td>
					
					<td width="70%">
						<div id="conteudo"></div>
						<div style="text-align:center;">
							<button id="carregarMais" onclick="carregarMais()" style="display:none;">Carregar Mais</button>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
	
	<script>
		var historico = {};
		
		function selecionarConversa(arquivo) {
			// Setar valores padrões
			historico.pagina = 0;
			historico.data = '';
			historico.participantes = [];
			historico.arquivo = arquivo;
			document.title = arquivo;
			
			// Limpar as mensagens de outras conversas
			$("#conteudo").html('');
			
			// Renderizar a primeira página
			renderizarMensagens(historico.arquivo, historico.pagina);
			
			// Exibir o botão de carregar mais
			$("#carregarMais").show();
		}
		
		function carregarMais() {
			// Avança uma página
			historico.pagina += 1;
			
			// Renderizar mensagens da página atual
			renderizarMensagens(historico.arquivo, historico.pagina);
		}
		
		function renderizarConversas() {
			var request = new XMLHttpRequest();
			request.open('GET', 'acao.php?acao=index', true);
			
			request.onload = function (e) {
				if ((request.readyState === 4)) {
					var conteudo = '';
					if ((request.status === 200)) {
						var response = JSON.parse(request.response);
						for (var k in response) {
							conteudo += '<div class="conversa"><a onclick="selecionarConversa(\'' + response[k] + '\')" style="cursor:pointer;">' + response[k] + '</a></div>';
						}
					}
					$("#conversas").html(conteudo);
				}
			};
			
			request.onerror = function (e) {
				alert('Não foi possível carregar a lista de conversas...');
			};
			
			request.send(null); 
		}
		
		function renderizarMensagens(arquivo, pagina) {
			var request = new XMLHttpRequest();
			request.open('GET', 'acao.php?acao=mensagens&arquivo=' + arquivo + '&pagina=' + pagina, true);
			
			request.onload = function (e) {
				if ((request.readyState === 4)) {
					if ((request.status === 200)) {
						var response = JSON.parse(request.response);
						for (var k in response) {
							var mensagem = response[k];
							renderizarMensagem(mensagem.trim());
						}
					}
					else {
						if ((historico.pagina > 0)) historico.pagina -= 1;
						alert('Não foi possível carregar mais mensagens do arquivo: ' + historico.arquivo);
					}
				}
			};
			
			request.onerror = function (e) {
				if ((historico.pagina > 0)) historico.pagina -= 1;
				alert('Não foi possível carregar mais mensagens do arquivo: ' + historico.arquivo);
			};
			
			request.send(null);
		}
		
		function renderizarMensagem(mensagem) {
			var random = Math.floor(Math.random() * 100);
			
			//(?=^\d{1,2}\/\d{1,2}\/\d{2},\s\d{1,2}:\d{2}\s[AP]M)
			var regex = new RegExp(/(?=^(\d{1,2}\/\d{1,2}\/\d{2}),\s(\d{1,2}:\d{2}\s[AP]M)\s-\s([^\:]+):\s([^\n]+))/m);
			var matches = regex.exec(mensagem);
			
			if ((matches)) {
				var conteudo = '';
				matches[4] = matches[4].trim();
				
				// Insere o novo divisor de tempo
				if (!(historico.data == matches[1])) {
					historico.data = matches[1];
					conteudo += '<div class="separador"><span class="azul">' + matches[1] + '</div>';
				}
				
				// Insere a nova mensagem
				var style = getStylePeloParticipante(matches[3]);
				conteudo += '<div class="mensagem ' + style + '">' + matches[4] + '</div>';
				$("#conteudo").html($("#conteudo").html() + conteudo);
			}
			else {
				var last = $('#conteudo').children().last();
				if ((last)) last.html(last.html() + '<br />' + mensagem);
			}
		}
		
		function getStylePeloParticipante(participante) {
			// Se o participante ainda não tiver na lista, joga pra lá
			if ((!historico.participantes.includes(participante))) {
				historico.participantes.push(participante);
			}
			
			// Pega qual o index do participante...
			var index = historico.participantes.indexOf(participante);
			return (index == 0) ? 'mensagemA' : 'mensagemB';
		}
	</script>
</html>