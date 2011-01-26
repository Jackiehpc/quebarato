jQuery(document).ready(function (){
	
	
	
	// <img src='https://www.pagamentodigital.com.br/site/imgs/pagamento_digital.gif' width='150' />
	
	
	
	jQuery("#cp_usar_o_pagamento_digital_1").parent().parent().parent().find('label:first').append("<img src='https://www.pagamentodigital.com.br/site/imgs/pagamento_digital.gif' width='150' />");
	
	
	jQuery("#cp_token_pagamentodigital").parent().hide();
	jQuery("#cp_id_pagamentodigital").parent().hide();
	
	jQuery("#cp_usar_o_pagamento_digital_1").change(function(){
    
    
    	//alert(jQuery(this).attr('checked'));
    	if(jQuery(this).attr('checked')){
    		jQuery("#cp_token_pagamentodigital").parent().show();
			jQuery("#cp_id_pagamentodigital").parent().show();
    	}else{
    		jQuery("#cp_token_pagamentodigital").parent().hide();
			jQuery("#cp_id_pagamentodigital").parent().hide();			
    	}
	
	});
	
	
});
