<?php
// meta cp_id_pagamentodigital
// meta cp_token_pagamentodigital



			
			

include_once '../../../wp-load.php' ;
// 
// 	$token =  get_post_meta( (int) 49 , 'cp_token_pagamentodigital' );
// $args = array(  "meta_key"=>'cp_city' );
// 			$lastposts = get_posts( $args );
// 			/*foreach($lastposts as $post) : setup_postdata($post); 
// 				the_title();	
// 			
// 			endforeach;
// 			*/
// 			 
// 			
// 			var_dump($lastposts,$token);
// 			die;
// 			
			
			ob_start();
			
			var_dump($_REQUEST);
			
			var_dump($_SERVER);
			

// $post_id = 10;

			
			

			$id_transacao = $_POST['id_transacao'];
			$data_transacao = $_POST['data_transacao'];
			$data_credito = $_POST['data_credito'];
			$valor_original = $_POST['valor_original'];
			$valor_loja = $_POST['valor_loja'];
			$valor_total = $_POST['valor_total'];
			$desconto = $_POST['desconto'];
			$acrescimo = $_POST['acrescimo'];
			$tipo_pagamento = $_POST['tipo_pagamento'];
			$parcelas = $_POST['parcelas'];
			$cliente_nome = $_POST['cliente_nome'];
			$cliente_email = $_POST['cliente_email'];
			$cliente_rg = $_POST['cliente_rg'];
			$cliente_data_emissao_rg = $_POST['cliente_data_emissao_rg'];
			$cliente_orgao_emissor_rg = $_POST['cliente_orgao_emissor_rg'];
			$cliente_estado_emissor_rg = $_POST['cliente_estado_emissor_rg'];
			$cliente_cpf = $_POST['cliente_cpf'];
			$cliente_sexo = $_POST['cliente_sexo'];
			$cliente_data_nascimento = $_POST['cliente_data_nascimento'];
			$cliente_endereco = $_POST['cliente_endereco'];
			$cliente_complemento = $_POST['cliente_complemento'];
			$status = $_POST['status'];
			$cod_status = $_POST['cod_status'];
			$cliente_bairro = $_POST['cliente_bairro'];
			$cliente_cidade = $_POST['cliente_cidade'];
			$cliente_estado = $_POST['cliente_estado'];
			$cliente_cep = $_POST['cliente_cep'];
			$frete = $_POST['frete'];
			$tipo_frete = $_POST['tipo_frete'];
			$informacoes_loja = $_POST['informacoes_loja'];
			$id_pedido = $_POST['id_pedido'];
			
			
			$post_id = $free = $_REQUEST['free'];


			
			$token =  get_post_meta( (int) $post_id , 'cp_token_pagamentodigital' , true);
			
			
			
			if($token && $post_id ){
				
				/* Essa variável indica a quantidade de produtos retornados */
				$qtde_produtos 	= $_POST['qtde_produtos'];
				
				/* Verificando ID da transação */
				/* Verificando status da transação */
				/* Verificando valor original */
				/* Verificando valor da loja */
				
				// /*
				$id_transacao = "4748181";
				$cod_status = 1;
				$valor_original = "1.00";
				
				$valor_loja = "0.93";
				// */
				
				$post = "transacao=$id_transacao" .
				"&cod_status=$cod_status" .
				"&valor_original=$valor_original" .
				"&valor_loja=$valor_loja" .
				"&token=$token".
				"&status=".urlencode("$status");
				
				
				
				$enderecoPost = "https://www.pagamentodigital.com.br/checkout/verify/";
				
				$enderecoPost .= "?".$post;
				
				include_once "SendCurl.class.php";
				$retorno_completo = SendCurl::open_https_url($enderecoPost);
				
				$retorno = explode("
	", $retorno_completo);
				$resposta = $retorno[count($retorno)-1];
				
				
				if(preg_match("/VERIFICADO/mi",$resposta)){
					
					// echo 'tá pago > ' . $post_id;
					
					// die;
					
					// update_post_meta($post_id, "cp_sys_expire_date", date("0/d/Y", time() -100));
					
					$my_post = array();
					$my_post['ID'] = $post_id;
					$my_post['post_status'] = 'draft';
					wp_update_post( $my_post );
					
				}
			}	
			$content_OB = ob_get_contents();
			
			ob_end_clean();
			
			$content = "\n\n--------TUDO NO FINAL DAS CONTAS------" . date("d/m/Y H:i:s") . "---------------\n\n";
			$content.= $content_OB;
			$content.= "\n\n------------------------------\n\n";

			$fh = fopen('log.txt', "r+");
			fwrite($fh, $content );
			fclose($fh);			
			
die;


