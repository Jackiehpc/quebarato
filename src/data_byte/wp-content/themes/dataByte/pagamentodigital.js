jQuery(document).ready(function (){
	
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
