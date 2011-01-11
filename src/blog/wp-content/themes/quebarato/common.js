function initForm(elId, uniqueSelection){
	$(document.getElementById(elId)).find(':checkbox').change(function(){
		form = $(this).closest('form');
		checkboxes = $(form).find(':checkbox');

		if (uniqueSelection) {
			if (!$(checkboxes).filter('not(:checked)').length) {
				this.checked = true;
			}

			$(checkboxes).not(this).each(function(){this.checked = false});
		}

		$(form).submit();
	});

	$(document.getElementById(elId)).find('select, :radio').change(function(){
		form = $(this).closest('form');
		$(form).submit();
	});

}

//Botao Ações
function clickOutsideActionsBt(e){
	var $nav = $('.bt_actions_wrapper');
	var $sub = $nav.find('.sub');
	
	var $target = $(e.target);
		if ($target.closest('.bt_actions_wrapper').length<1) {
			$sub.hide().prev().removeClass('bt_active');
			$('body').unbind('click');
				return;
		}
}

function toggleDropdown(el) {
	if($(el).hasClass('bt_active')){
		$(el).removeClass('bt_active').next().hide();
	} else {
		$(el).addClass('bt_active').next().show();
		$('body').click(function(e){
			clickOutsideActionsBt(e);
		});
	}

	return false;
}

function initCollapsible() {
	$('.collapsed .collapse_activator').next().hide();

	$heading = $('.collapsible .collapse_activator');
	$heading.click(function(){
		el = this;
		if ($(el).parent().hasClass('collapsed')) {
			$(el).next().slideDown('fast', function(){$(el).parent().removeClass('collapsed')});
		}
		else {
			$(el).next().slideUp('fast', function(){$(el).parent().addClass('collapsed')});
		}
		return false;
	});
}

function initSearch(searchBar) {
	if (!$(searchBar).val())
		$(searchBar).addClass('text_busca_off');
	$(searchBar).focus(function(){
		$(searchBar).removeClass('text_busca_off');
	}).blur(function(){
		if (!$(searchBar).val()) $(searchBar).addClass('text_busca_off');
	});

	$(searchBar).closest('form').submit(function(){
		if ($.trim($(searchBar).val()).length < 2) return false; 
	});
}
function initCategoryMenu() {
	$('.busca_lista_categorias:last').click(function(){

		if ($(this).next().is(':visible')) {
			$(this).removeClass('busca_lista_categorias_on');
			$(this).next().fadeOut();
		}
		else {
			$(this).addClass('busca_lista_categorias_on');
			$(this).next().fadeIn();
		}
	});
	$('.category_menu:last li').click(function(){
		form = $(this).closest('form.busca');
		list = $('.busca_lista_categorias', form);
		if (!$(this).hasClass('prefs')) {
			$('span', list).text($(this).text().toLowerCase());
			$(list).attr('title', $(list).eq(0).text());
			$(form).attr('action', $(this).attr('rel'));
		}
		$(this).parent().prev().trigger('click');
	});
}

function addReturnParam(el) {
	document.location.href = el.href + '?direct_to=' + document.location.href;	
	return false;
}

function initPasswordField(el) {
	strengthMeterContainer = $('.strength_meter_container');
	strengthMeterEl = $('.strength_meter', strengthMeterContainer);
	errorMessage = $('.sidevalidation_msg', $(el).closest('.middleCol'));
	
	$(el).keyup(function(){
		$(strengthMeterEl).attr('class', 'strength_meter');
		$(errorMessage).hide();
		$(strengthMeterContainer).show();
		password = $(el).val();
		if (validatePassword(password)) {
			strength = passwordStrengthMeter(password);
			if (strength < 20) $(strengthMeterEl).addClass('strength_meter_very_weak');
			else if (strength < 40) $(strengthMeterEl).addClass('strength_meter_weak');
			else if (strength < 60) $(strengthMeterEl).addClass('strength_meter_regular');
			else if (strength < 80) $(strengthMeterEl).addClass('strength_meter_strong');
			else $(strengthMeterEl).addClass('strength_meter_very_strong');
		}
	});
}

function validatePassword(password) {
	if (password.length < 6 || password.length > 30) return false;
	if (password.indexOf(' ') != -1) return false;
	
	return true;
}

function prepareLightbox(href, trigger) {
	Boxy.load(href, {modal: true, afterShow: function(){if (!$('.boxy-inner *').length) Boxy.get('*').hideAndUnload(); if (typeof trigger == 'function') trigger();}, fixed: false});
	return false;
}

$('.boxy-modal-blackout').live('click', function(e){
	Boxy.get('*').hideAndUnload();
});

function triggerPaymentMethods() {
	$('.cards_menu li div').hover(function(){
		if ($.browser.msie && $.browser.version < 7) {
			$('.body>div').hide().eq($(this).parent().index()).show().find('.box_content ul').hide().show();
		} else {
			$('.body>div').hide().eq($(this).parent().index()).show().find('.box_content ul').hide().fadeIn(200);
		}
	});
}

function triggerAutocomplete() {
	$('#locale_search').result(function(event, data){
		$('#locale_id').val(data[1]);
		$('#locale_search').closest('form').find(':submit').each(function(){this.focus()});
	}).focus(function(){
		if (!$(this).data('ac_on'))
			$(this).data('ac_on', true).autocomplete($(this).closest('form').attr('action'), {selectFirst: true});
	}).each(function(){
		this.focus(); $(this).trigger('focus');
	});
}

function triggerAvatarUpload() {
	endLoading();
	$('div.ajaxupload').remove();
	$(':hidden[name="urlReturn"]').each(function(){
		$(this).val($(this).val() + '&direct_to=' + document.location.pathname + document.location.search);
	});
	$(':hidden[name="urlFailure"]').val(document.location.href);

	bbUpload();
}

jQuery.unparam = function(query) {
	if (query && query[0] == '?') query = query.substr(1);
	var query_string = {};
	if (!query) return query_string;
	var vars = query.split("&");
	for (var i=0;i<vars.length;i++) {
		var pair = vars[i].split("=");
		pair[0] = decodeURIComponent(pair[0]);
		pair[1] = decodeURIComponent(pair[1]);
		// If first entry with this name
		if (typeof query_string[pair[0]] === "undefined") {
			query_string[pair[0]] = pair[1];
			// If second entry with this name
		} else if (typeof query_string[pair[0]] === "string") {
			var arr = [ query_string[pair[0]], pair[1] ];
			query_string[pair[0]] = arr;
			// If third or later entry with this name
		} else {
			query_string[pair[0]].push(pair[1]);
		}
	} 
	return query_string;
};

jQuery.parametrize = function(obj) {
	if (typeof obj != 'object') return obj;
	var queryString = '';
	jQuery.each(obj, function(name, value) {
		if (!name) return;
		if (queryString) queryString += '&';
		queryString += name + '=' + value;
	});
	
	return queryString;
};

//addMethod - By John Resig (MIT Licensed)
var addMethod = function(object, name, fn){
    var old = object[name];
    if (old)
        object[name] = function(){
            if (fn.length == arguments.length)
                return fn.apply(this, arguments);
            else if (typeof old == 'function')
                return old.apply(this, arguments);
        };
    else
        object[name] = fn;
};

$('.close_lightbox_report_ad').live('click', function(){
	Boxy.get(this).hide('veryfast').hideAndUnload();
});

$('.close_lightbox').live('click', function(){
	Boxy.get(this).hideAndUnload();
	return false;
});

$('.reset_form').live('click', function(){
	$(this).closest('form').each(function(){this.reset()});
	return false;
});

//FECHAR ITEM
$('.closeThis').live('click', function(){
	$(this).closest('.closable').fadeOut('slow');
	return false;
});

$('#lightbox form.ajax').live('submit', function(){
	$.ajax({
		url: $(this).attr('action'),
		data: $(this).serialize(),
		dataType: 'json',
		error: error,
		success: success,
		type: 'POST'
	});

	function success(data) {
		if (data.success) {
			directTo = $('#lightbox input[name="direct_to"]').val();
			if (directTo)
				window.location.href=directTo;
			else
				window.location.reload();
		}
		else {
			error(data.messages);
		}
	}

	function error(messages) {
		error = $('#lightbox .warning');
		if (messages && messages.length > 0)
			$('p.small', error).text(messages[0]);

		$(error).fadeIn();
	}

	return false;
});

//PARA IE6 SOMENTE
if ($.browser.msie && $.browser.version < 7) {
	$(function(){
		
		// HOVER ULTIMOS ANUNCIOS PUBLICADOS
		$('.last_published_ad').hover(
				function(){
					$(this).addClass('hover')
				},function(){
					$(this).removeClass('hover')
				}
		);
		
		// HOVER ANUNCIOS DO RESULTADO DE BUSCA
		$('.ads_grid li, .ads_list li').hover(
				function(){
					$(this).addClass('hover');
				},function(){
					$(this).removeClass('hover');
				}
		);
		
		// HOVER BOTÃO CANCELAR
		$('.bt_cancelar').hover(
				function(){
					$(this).addClass('hover')
				},function(){
					$(this).removeClass('hover')
				}
		);
		
		// HOVER ULTIMOS ANUNCIOS PUBLICADOS
		$('.filter_default .default_list label').hover(
				function(){
					$(this).addClass('hover')
				},function(){
					$(this).removeClass('hover')
				}
		);
		
		//HOVER "EM TODAS AS CATEGORIAS"
		$('.busca_lista_categorias').hover(function(){
			$(this).addClass('busca_lista_categorias_on');
		},function(){
			if (!$(this).next().is(':visible'))
				$(this).removeClass('busca_lista_categorias_on');
		});


		// HOVER BOTAO BUSCA ANUNCIO HEADER/FOOTER
		$('.button_busca').hover(
				function(){
					$(this).addClass('hover')
				},function(){
					$(this).removeClass('hover')
				}
		);

		//FOOTER BOTÃO GO TO TOP
		$('.gototop_bt').hover(
				function(){
					$(this).removeClass('gototop_bt')
					$(this).addClass('gototop_bt_hover')
				},function(){
					$(this).removeClass('gototop_bt_hover')
					$(this).addClass('gototop_bt')
				}
		);

		// HOVER LINKS PATROCINADOS
		$('.lp_wide ul li, .lp_narrow ul li').hover(
				function(){
					$(this).addClass('lp_hover')
				},function(){
					$(this).removeClass('lp_hover')
				}
		);

		// HOVER LINKS PATROCINADOS OFERTAS BUSCAPE
		$('.lp_BPoffer .BPoffer').hover(
				function(){
					$(this).addClass('hover')
				},function(){
					$(this).removeClass('hover')
				}
		);

		//BOTOES PADRAO AZUL PRATA E LARANJA

			//TIPO 1
			$('.bt_qb_azul.asizetype1,.bt_qb_prata.psizetype1,.bt_qb_laranja.lsizetype1,').hover(
					function(){
						$(this).addClass('action_hover_250')
					},function(){
						$(this).removeClass('action_hover_250')
					}
			).mousedown(function(){
				$(this).removeClass('action_hover_250');
				$(this).addClass('action_down_250');
			}
			).mouseup(function(){
				$(this).removeClass('action_down_250');
				$(this).addClass('action_hover_250');
			}
			).mouseout(function(){
				$(this).removeClass('action_down_250');
			}
			);

			//TIPO 2, TIPO 3, TIPO 4, TIPO 5
			$('.bt_qb_azul.asizetype2,.bt_qb_azul.asizetype3,.bt_qb_azul.asizetype4,.bt_qb_azul.asizetype5,.bt_qb_prata.psizetype2,.bt_qb_prata.psizetype3,.bt_qb_prata.psizetype4,.bt_qb_prata.psizetype5,.bt_qb_laranja.lsizetype2,.bt_qb_laranja.lsizetype3,.bt_qb_laranja.lsizetype4,.bt_qb_laranja.lsizetype5').hover(
					function(){
						$(this).addClass('action_hover_2345')
					},function(){
						$(this).removeClass('action_hover_2345')
					}
			).mousedown(function(){
				$(this).removeClass('action_hover_2345');
				$(this).addClass('action_down_2345');
			}
			).mouseup(function(){
				$(this).removeClass('action_down_2345');
				$(this).addClass('action_hover_2345');
			}
			).mouseout(function(){
				$(this).removeClass('action_down_2345');
			});

			//TIPO 6
			$('.bt_qb_azul.asizetype6,.bt_qb_prata.psizetype6,.bt_qb_laranja.lsizetype6,').hover(
					function(){
						$(this).addClass('action_hover_26')
					},function(){
						$(this).removeClass('action_hover_26')
					}
			).mousedown(function(){
				$(this).removeClass('action_hover_26');
				$(this).addClass('action_down_26');
			}
			).mouseup(function(){
				$(this).removeClass('action_down_26');
				$(this).addClass('action_hover_26');
			}
			).mouseout(function(){
				$(this).removeClass('action_down_26');
			});
		

		// BOTAO PD 

			// TIPO 1
			$('.bt_qb_pd.pdsizetype1').hover(
					function(){
						$(this).addClass('action_hover_250')
					},function(){
						$(this).removeClass('action_hover_250')
					}
			).mousedown(function(){
				$(this).removeClass('action_hover_250');
				$(this).addClass('action_down_250');
			}
			).mouseup(function(){
				$(this).removeClass('action_down_250');
				$(this).addClass('action_hover_250');
			}
			).mouseout(function(){
				$(this).removeClass('action_down_250');
			});

		// BOTAO PD COMPRE JÁ
		$('.bt_qbpd_compreja').hover(
				function(){
					$(this).addClass('pd_cj_action_hover');
				},function(){
					$(this).removeClass('pd_cj_action_hover');
				}
		).mousedown(function(){
			$(this).removeClass('pd_cj_action_hover');
			$(this).addClass('pd_cj_action_down');
		}
		).mouseup(function(){
			$(this).removeClass('pd_cj_action_down');
			$(this).addClass('pd_cj_action_hover');
		}
		).mouseout(function(){
			$(this).removeClass('pd_cj_action_down');
		});
		

		//HOVER ANUNCIOS LIST/GRID
		$('.ads_list .ad,.ads_grid .ad').hover(
				function(){
					$(this).addClass('hover');
				},function(){
					$(this).removeClass('hover');
				}
		);

		//BOTAO PUBLICAR ANUNCIO HEADER
		$('#hd .publicaranuncio')		
		.hover(
				function(){
					$(this).addClass('hd_bt_hover');
				},function(){
					$(this).removeClass('hd_bt_hover');
				}
		)
		.mousedown(
				function(){
					$(this).removeClass('hd_bt_hover');
					$(this).addClass('hd_bt_down');
				}
		)
		.mouseup(
				function(){
					$(this).removeClass('hd_bt_down');
					$(this).addClass('hd_bt_hover');
				}
		)
		.mouseout(
				function(){
					$(this).removeClass('hd_bt_down');
				}
		);
		
		// AVATAR
		$('.avatar_area').hover(function(){
			$(this).addClass('avatar_area_hover');
		},function(){
			$(this).removeClass('avatar_area_hover');
		}).each(function(){
			$(this).find('div.edit_avatar').height($(this).height()-2);
		});

		$('div.dashboard_sidemenu .titled li').hover(
				function(){
					$(this).addClass('df_hover');
				},function(){
					$(this).removeClass('df_hover');
				}
		);

	});
}

//CAIXA DE TEXTO E SELECT COM FOCO
function initActivateText(context){
	var ACTIVE_CLASS = 'active_text';
	var FILLED_CLASS = 'active_text_filled';
	if (typeof context == 'undefined') context = $(document);
	$("input[type=text]:not('.text_busca'), input[type=password], textarea, select", context).each(function(){
		if($(this).attr('tagName')=="SELECT" && $(this).val()) $(this).addClass(FILLED_CLASS);
	}).focus(function(){
		$this = $(this);

		if($this.attr('tagName')=="SELECT"){
			$this.addClass(ACTIVE_CLASS);
			$this.change(function(){
				$selected = $this.find('option:selected');
				if($selected.index()==0 && $this.val()) $this.removeClass(FILLED_CLASS);
				else $this.addClass(FILLED_CLASS);
			});
		} else {
			$this.addClass(ACTIVE_CLASS);
		}
	})

	.blur(function(){
		$this.removeClass(ACTIVE_CLASS);
	});
}

$(function(){
	initActivateText();
});


function showWorld(el){
	var pais = $(el).find('option:selected').val();
	window.location.href=pais;
}

//COLOCAR NEGRITO NA OPÇÃO SELECIONADA
function changeBoldLabel($el){
	//init
	var selectedIdList = new Array();
	$('input[type=radio]:checked,input[type=checkbox]:checked',$el).each(function(){
		selectedIdList.push([$(this).attr('id')]);
	});
	$(selectedIdList).each(function(){
		$('label', $el).filter('label[for='+$(this)[0]+']').addClass('bold_label');
	});


	//click
	$el.each(function(){
		var $nextLabel;
		var ev='';
		if('\v'=='v') ev = 'click';
		else ev = 'change';

		$(this).find('input[type=radio]').bind(ev,function(){
			var $closestBoldParent = $(this).closest('.bold_labels');
			var checkedId = $closestBoldParent.find('input[type=radio]:checked').attr('id');
			$closestBoldParent.find('label').removeClass('bold_label');
			$closestBoldParent.find('label[for='+checkedId+']').addClass('bold_label');
		});

		$(this).find('input[type=checkbox]').bind(ev,function(){
			var $thisBox = $(this);
			var $closestBoldParent = $(this).closest('.bold_labels');
			var thisId = $thisBox.attr('id');

			$thisLabel = $closestBoldParent.find('label[for='+thisId+']');
			if($thisBox.attr('checked')) $thisLabel.addClass('bold_label');
			else $thisLabel.removeClass('bold_label');
		});
	});
}

// MEU PERFIL FORM EDITAVEL / NAO EDITAVEL
function initFormEditable(title){
	$(".not_editable .head .edit").live('click', function(){
		$(this).closest(".not_editable").hide().next().show();
	});

	$(".editable .bt_cancelar").live('click', function(){
		$(this).closest(".editable").hide().prev().show();
	});
	
}

//VOLTAR PARA O TOPO
function scrollUp() {
	$.scrollTo(0, 2000);
	return false;
}

function scrollLocaleLightbox(option) {
	el_to = '.to_'+$(option).attr('class');
	$('.index_by_letter').scrollTo(el_to, 2000);
	return false;
}

//EDITOR DE TEXTO - DESCRICAO DO ANUNCIO
function initTinyMce(lang){
	tinyMCE.init({
		language: lang, 
	
		mode:"textareas",
		theme:"advanced",
		editor_selector:"mceEditor",
		strict_loading_mode:true,
		document_base_url:"/26924/",
		oninit: function(){	
			if ($('.ad_content').children().hasClass('has_error')) {
				$('#description_ifr').contents().find('html').css("background","#FFECE8");
			};
		},
		valid_elements: "span[!style],strike,u,strong/b/h1/h2/h3/h4/h5/h6,p,div,em/i,ul,ol,li,br",
		theme_advanced_toolbar_location: "top",
		theme_advanced_toolbar_align: "center",
		theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,undo,redo,|,bullist,numlist",
		theme_advanced_buttons2: "",
		theme_advanced_buttons3: "",
		remove_redundant_brs: true,
		
		handle_event_callback : function(e) {
			if (e.type == 'keydown') {
				$('#description_ifr').closest('.has_error').removeClass('has_error');
				$('#description_ifr').contents().find('html').css("background","#fff")
			}
		},
		
		plugins: "paste",
		paste_auto_cleanup_on_paste: true,
		paste_block_drop: true,
		paste_remove_styles: true,
		paste_remove_styles_if_webkit: true,

		paste_preprocess: function(pl, o) { o.content = $('<div>' +o.content+ '</div>').text(); }
	});
}

// TABS

function toggleTabs(el) {
	el = $(el).closest('li');
	if ( $(el).parent().hasClass('tabs') || $(el).parent().hasClass('tabs2') || $(el).parent().hasClass('tabs_mode3') ) {
		if ($(el).hasClass('tab_selected')) return false;
		$(el).siblings().removeClass('tab_selected').addClass('tab_deselected');
		$(el).removeClass('tab_deselected tab_deselected_hover').addClass('tab_selected');
		$(el).closest('.tabs_wrapper').find('.tabs_content').hide().eq($(el).index()).show();
	}
	else {
		if ($(el).find('a').hasClass('lbh_active')) return false;
		$(el).find('a').addClass('lbh_active');
		$(el).siblings().find('a').removeClass('lbh_active');
		className = $(el).find('em').attr('class');
		$('.ads_grid').each(function(){
			if ($(this).hasClass(className)) $(this).show();
			else $(this).hide();
		});
	}
}

if($.address){
	$.address.change(function(event) {  
		$($.address.pathNames()).each(function(num){
			toggleTabs($('.' + $.address.pathNames()[num]));
		})
	});
}

//EXIBICAO GOOGLE DISPLAY ADS
function show_gda(){
	
	$('#ad_top').html($('#google_ad_top ins:first').clone());
	$('#google_ad_top ins:first').remove();
	
	$('#ad_bottom').html($('#google_ad_bottom ins:first').clone());
	$('#google_ad_bottom ins:first').remove();
	
	$('#ad_arroba').html($('#google_ad_arroba ins:first').clone());
	$('#google_ad_arroba ins:first').remove();	
	
	$('#ad_sky').html($('#google_ad_sky ins:first').clone());
	$('#google_ad_sky ins:first').remove();
}

function initError(context){

	if (typeof context == 'undefined') context = $(document);
	
	//RETIRAR CLASSES DE ERRO DE FORMULARIO

	// INPUT TEXT E TEXTAREA
	$('.field_validation.has_error .textboxG, .field_validation.has_error .textboxP, .field_validation.has_error .textarea').keydown(
			function(){$(this).closest('.has_error').removeClass('has_error');}
	);

	// SELECT, RADIO E CHECKBOX
	$('.field_validation.has_error .selectG, .field_validation.has_error .selectP, .field_validation.has_error .checkbox_bt, .field_validation.has_error .radio_bt').change(
			function(){$(this).closest('.has_error').removeClass('has_error');}
	);
	
	//RETIRAR MENSAGEM DE ERRO DE FORMULARIO LATERAL
	
	//INPUT TEXT E TEXTAREA
	
	$('.textboxP,.textboxG').keyup(function(){
		$(this).closest('.line').find('.sidevalidation_msg').empty();
	})
}

$(document).ready(function(){
	
	show_gda();

	initError();
	
	//SEARCH BOX FOCUS
	$('.text_search').focus(function(){
		$('.search_box').addClass('sb_active');
	}).blur(function(){
		$('.search_box').removeClass('sb_active');
	});
	
	//BOTAO COMPARTILHE
	$('.sharethis_wrapper .bt_share_wrapper').mouseover(
		function(){
			$(this).find('.bt_share').addClass('share_active').next('.sub').show();
		}).mouseout(
		function(){
			$(this).find('.bt_share').removeClass('share_active').next('.sub').hide();
		});
	
	
	//HOVER ANUNCIOS MODO LISTA
	$('li .ad').hover(function(){
		$(this).parent().prev().addClass('nobg');
	}, function(){
		$(this).parent().prev().removeClass('nobg');
	});

	//TABS 2 E TABS MODE 3
	$('ul.tabs2 li, ul.tabs_mode3 li').hover(function(){
		var thisSelClass="";
		if($(this).hasClass('tab_selected')) thisSelClass = 'selected';
		else thisSelClass = 'deselected';
		$(this).addClass('tab_'+thisSelClass+'_hover');
	}, function(){
		$(this).removeClass('tab_selected_hover').removeClass('tab_deselected_hover');
	}).click(function(){
		value = $('p', this).attr("class");
		$('.tab_selected p, .lbh_active em').not($(this).closest('ul').find('p')).each(function(){
			if ($(this).closest('a[href!="#"]').length) return;
			if (!value.length)
				value = $(this).attr('class');
			else
				value += '+' + $(this).attr('class');
		});
		if($.address)$.address.value(value);
	});	
	
	//LINK BLUE HOVER
	$('a.linkbluehover').click(function(){
		if ($(this).attr('href') != '#') return true;
		value = $('em', this).attr("class");
		$('.tab_selected p, .lbh_active em').not($(this).closest('ul').find('em')).each(function(){
			if (!value.length)
				value = $(this).attr('class');
			else
				value += '+' + $(this).attr('class');
		})
		if($.address)$.address.value(value);

		return false;
	});

	// CLONAR ELEMENTO
	$('a.clone').live('click', function(){
		parent = $(this).closest('.add_container');
		cloned = $(parent).prev().clone();
		$(':text', cloned).val('');
		$('select', cloned).removeAttr('selected');
		$(':radio, :checkbox', cloned).removeAttr('checked').each(function(){
			var name = $(this).attr('id');
			var newName = name.substring(0, name.lastIndexOf('_') + 1);
			var number = name.substring(name.lastIndexOf('_') + 1, name.length);
			$('[for=' + name + ']', cloned).attr('for', newName + (number + 1));
			$(this).attr('id', newName + (number + 1));
		});
		$('.deleteThis', cloned).css('display', 'block');
		changeBoldLabel($('.bold_labels', cloned));
		$(parent).before(cloned);

		return false;
	});

	$('textarea.elastic').elastic();
	$('.has_tooltip').tooltip();
});

/**
 * Ação de foco na textarea de perguntas e respostas.
 * 
 * @param el O elemento alvo
 * @param minimized true se deve exibir botões ocultos
 * @return
 */
function questionFocusAction(el, minimized) {

	$(el).addClass('focus');

	if (!$(el).hasClass('active_text_filled'))
		if (minimized) $(el).parent().parent().next().show();
}

/**
 * Ação de blur no textarea de perguntas e respostas
 * 
 * @param el O elemento alvo
 * @param minimized true se deve coultas botões
 * @return
 */
function questionBlurAction(el, minimized) {
	if (!$(el).val()) {
		$(el).removeClass('active_text_filled focus');

		if(!$(el).val()) $(el).height(15);

		if (minimized) $(el).parent().parent().next().hide();
	}
}

function cancelForm(el) {
	$(el).closest('form').find(':text, :password, .textarea').each(function(){
		$(this).val('');
		$(this).trigger('blur').trigger('keyup');
	});

	return false;
}

function fixAvatarDimensions(el) {
	if (!$(el).data('fixed')) {
		$editAvatar = $(el).find('.edit_avatar');
		$image = $(el).find('img:first');
		$editAvatar.height($image.height() - 2);
		$editAvatar.width($image.width() - 2);
		$(el).data('fixed', true);
	}
}

(function($){
	$.fn.tooltip = function(options){
		var settings =	$.extend({
							upleftClass: 'goes_up_left',
							uprightClass: 'goes_up_right',
							upcenterClass: 'goes_up_center',
							downleftClass: 'goes_down_left',
							downrightClass: 'goes_down_right',
							downcenterClass: 'goes_down_center',
							tooltipClass: 'tooltip',
							animationSpeed: 'fast',
							delay: 500
						}, options);
		
		var timeout;
		
		function fadeIn(el) {
			clearTimeout(timeout);
			el.stop(true, true).fadeIn(settings.animationSpeed);
		}
		
		function fadeOut(el) {
			el.stop(true, true);
			clearTimeout(timeout);
			timeout = setTimeout(function(){el.fadeOut(settings.animationSpeed);}, settings.delay);
		}
		
		return this.each(function(){
			var tooltip = $('.' + $(this).attr('rel'));
			var offset = {};
			
			tooltip.hover(function(){clearTimeout(timeout);this.stop(true, true);}, function(){fadeOut(tooltip);});
			
			$(this).hover(
				function(){
					$('.' + settings.tooltipClass).hide();
					if (tooltip.hasClass(settings.upleftClass)) {
						offset.left = Math.round($(this).offset().left + ($(this).innerWidth() / 2) - 13);
						offset.top = Math.round($(this).offset().top + $(this).innerHeight() + 1);
						//alert('UP LEFT');
					} else if (tooltip.hasClass(settings.uprightClass)) {
						offset.left = Math.round($(this).offset().left + ($(this).innerWidth() / 2) - tooltip.innerWidth() + 13);
						offset.top = Math.round($(this).offset().top + $(this).innerHeight() + 1);
						//alert('UP RIGHT');
					} else if (tooltip.hasClass(settings.upcenterClass)) {
						offset.left = Math.round($(this).offset().left + ($(this).innerWidth() / 2) - (tooltip.innerWidth() / 2));
						offset.top = Math.round($(this).offset().top + $(this).innerHeight() + 1);
						//alert('UP CENTER');
					}	else if (tooltip.hasClass(settings.downrightClass)) {
						offset.left = Math.round($(this).offset().left + ($(this).innerWidth() / 2) - tooltip.innerWidth() + 13 );
						offset.top = Math.round($(this).offset().top - tooltip.innerHeight() - 1);
						//alert('DOWN RIGHT');
					} else if (tooltip.hasClass(settings.downleftClass)) {
						offset.left = Math.round($(this).offset().left + ($(this).innerWidth() / 2) - 13);
						offset.top = Math.round($(this).offset().top - tooltip.innerHeight() - 1);
						//alert('DOWN LEFT');
					} else if (tooltip.hasClass(settings.downcenterClass)) {
						offset.left = Math.round($(this).offset().left + ($(this).innerWidth() / 2) - (tooltip.innerWidth() / 2));
						offset.top = Math.round($(this).offset().top - tooltip.innerHeight() - 1);
						//alert('DOWN CENTER');
					}					
					fadeIn(tooltip.css({left: offset.left + 'px', top: offset.top + 'px'}));
				},
				function(){
					fadeOut(tooltip);
				}
			);
		});
	};
})(jQuery);

/**
 * jQuery.ScrollTo - Easy element scrolling using jQuery.
 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * Date: 5/25/2009
 * @author Ariel Flesler
 * @version 1.4.2
 *
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 */

(function(d){var k=d.scrollTo=function(a,i,e){d(window).scrollTo(a,i,e)};k.defaults={axis:'xy',duration:parseFloat(d.fn.jquery)>=1.3?0:1};k.window=function(a){return d(window)._scrollable()};d.fn._scrollable=function(){return this.map(function(){var a=this,i=!a.nodeName||d.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!i)return a;var e=(a.contentWindow||a).document||a.ownerDocument||a;return d.browser.safari||e.compatMode=='BackCompat'?e.body:e.documentElement})};d.fn.scrollTo=function(n,j,b){if(typeof j=='object'){b=j;j=0}if(typeof b=='function')b={onAfter:b};if(n=='max')n=9e9;b=d.extend({},k.defaults,b);j=j||b.speed||b.duration;b.queue=b.queue&&b.axis.length>1;if(b.queue)j/=2;b.offset=p(b.offset);b.over=p(b.over);return this._scrollable().each(function(){var q=this,r=d(q),f=n,s,g={},u=r.is('html,body');switch(typeof f){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(f)){f=p(f);break}f=d(f,this);case'object':if(f.is||f.style)s=(f=d(f)).offset()}d.each(b.axis.split(''),function(a,i){var e=i=='x'?'Left':'Top',h=e.toLowerCase(),c='scroll'+e,l=q[c],m=k.max(q,i);if(s){g[c]=s[h]+(u?0:l-r.offset()[h]);if(b.margin){g[c]-=parseInt(f.css('margin'+e))||0;g[c]-=parseInt(f.css('border'+e+'Width'))||0}g[c]+=b.offset[h]||0;if(b.over[h])g[c]+=f[i=='x'?'width':'height']()*b.over[h]}else{var o=f[h];g[c]=o.slice&&o.slice(-1)=='%'?parseFloat(o)/100*m:o}if(/^\d+$/.test(g[c]))g[c]=g[c]<=0?0:Math.min(g[c],m);if(!a&&b.queue){if(l!=g[c])t(b.onAfterFirst);delete g[c]}});t(b.onAfter);function t(a){r.animate(g,j,b.easing,a&&function(){a.call(this,n,b)})}}).end()};k.max=function(a,i){var e=i=='x'?'Width':'Height',h='scroll'+e;if(!d(a).is('html,body'))return a[h]-d(a)[e.toLowerCase()]();var c='client'+e,l=a.ownerDocument.documentElement,m=a.ownerDocument.body;return Math.max(l[h],m[h])-Math.min(l[c],m[c])};function p(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);

/**
 * Autocomplete - jQuery plugin 1.0 Beta
 *
 * Copyright (c) 2007 Dylan Verheul, Dan G. Switzer, Anjesh Tuladhar, Jörn Zaefferer
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Revision: $Id: jquery.autocomplete.js 4485 2008-01-20 13:52:47Z joern.zaefferer $
 *
 */
(function(e){e.fn.extend({autocomplete:function(a,c){var u=typeof a=="string";c=e.extend({},e.Autocompleter.defaults,{url:u?a:null,data:u?null:a,delay:u?e.Autocompleter.defaults.delay:10,max:c&&!c.scroll?10:150},c);c.highlight=c.highlight||function(o){return o};return this.each(function(){new e.Autocompleter(this,c)})},result:function(a){return this.bind("result",a)},search:function(a){return this.trigger("search",[a])},flushCache:function(){return this.trigger("flushCache")},setOptions:function(a){return this.trigger("setOptions", [a])},unautocomplete:function(){return this.trigger("unautocomplete")}});e.Autocompleter=function(a,c){function u(){var b=r.selected();if(!b)return false;var h=b.result;s=h;if(c.multiple){var w=A(l.val());if(w.length>1)h=w.slice(0,w.length-1).join(c.multipleSeparator)+c.multipleSeparator+h;h+=c.multipleSeparator}l.val(h);p();l.trigger("result",[b.data,b.value]);return true}function o(b,h){if(j==n.DEL)r.hide();else{b=l.val();if(!(!h&&b==s)){s=b;b=q(b);if(b.length>=c.minChars){l.addClass(c.loadingClass); c.matchCase||(b=b.toLowerCase());t(b,v,p)}else{f();r.hide()}}}}function A(b){if(!b)return[""];b=b.split(e.trim(c.multipleSeparator));var h=[];e.each(b,function(w,k){if(e.trim(k))h[w]=e.trim(k)});return h}function q(b){if(!c.multiple)return b;b=A(b);return b[b.length-1]}function x(b,h){if(c.autoFill&&q(l.val()).toLowerCase()==b.toLowerCase()&&j!=8){l.val(l.val()+h.substring(q(s).length));e.Autocompleter.Selection(a,s.length,s.length+h.length)}}function i(){clearTimeout(z);z=setTimeout(p,200)}function p(){r.hide(); clearTimeout(z);f();c.mustMatch&&l.search(function(b){b||l.val("")})}function v(b,h){if(h&&h.length&&d){f();r.display(h,b);x(b,h[0].value);r.show()}else p()}function t(b,h,w){c.matchCase||(b=b.toLowerCase());var k=m.load(b);if(k&&k.length)h(b,k);else if(typeof c.url=="string"&&c.url.length>0){var B={};e.each(c.extraParams,function(y,D){B[y]=typeof D=="function"?D():D});e.ajax({mode:"abort",port:"autocomplete"+a.name,dataType:c.dataType,url:c.url,data:e.extend({q:q(b),limit:c.max},B),success:function(y){y= c.parse&&c.parse(y)||g(y);m.add(b,y);h(b,y)}})}else w(b)}function g(b){var h=[];b=b.split("\n");for(var w=0;w<b.length;w++){var k=e.trim(b[w]);if(k){k=k.split("|");h[h.length]={data:k,value:k[0],result:c.formatResult&&c.formatResult(k,k[0])||k[0]}}}return h}function f(){l.removeClass(c.loadingClass)}var n={UP:38,DOWN:40,DEL:46,TAB:9,RETURN:13,ESC:27,COMMA:188,PAGEUP:33,PAGEDOWN:34},l=e(a).attr("autocomplete","off").addClass(c.inputClass),z,s="",m=e.Autocompleter.Cache(c),d=0,j,C={mouseDownOnSelect:false}, r=e.Autocompleter.Select(c,a,u,C);l.keydown(function(b){j=b.keyCode;switch(b.keyCode){case n.UP:b.preventDefault();r.visible()?r.prev():o(0,true);break;case n.DOWN:b.preventDefault();r.visible()?r.next():o(0,true);break;case n.PAGEUP:b.preventDefault();r.visible()?r.pageUp():o(0,true);break;case n.PAGEDOWN:b.preventDefault();r.visible()?r.pageDown():o(0,true);break;case c.multiple&&e.trim(c.multipleSeparator)==","&&n.COMMA:case n.TAB:case n.RETURN:if(u()){c.multiple||l.blur();b.preventDefault()}break; case n.ESC:r.hide();break;default:clearTimeout(z);z=setTimeout(o,c.delay);break}}).keypress(function(){}).focus(function(){d++}).blur(function(){d=0;C.mouseDownOnSelect||i()}).click(function(){d++>1&&!r.visible()&&o(0,true)}).bind("search",function(){function b(w,k){var B;if(k&&k.length)for(var y=0;y<k.length;y++)if(k[y].result.toLowerCase()==w.toLowerCase()){B=k[y];break}typeof h=="function"?h(B):l.trigger("result",B&&[B.data,B.value])}var h=arguments.length>1?arguments[1]:null;e.each(A(l.val()), function(w,k){t(k,b,b)})}).bind("flushCache",function(){m.flush()}).bind("setOptions",function(b,h){e.extend(c,h);"data"in h&&m.populate()}).bind("unautocomplete",function(){r.unbind();l.unbind()})};e.Autocompleter.defaults={inputClass:"ac_input",resultsClass:"ac_results",loadingClass:"ac_loading",minChars:1,delay:400,matchCase:false,matchSubset:true,matchContains:false,cacheLength:10,max:100,mustMatch:false,extraParams:{},selectFirst:true,formatItem:function(a){return a[0]},autoFill:false,width:0, multiple:false,multipleSeparator:", ",highlight:function(a,c){return a.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)("+c.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi,"\\$1")+")(?![^<>]*>)(?![^&;]+;)","gi"),"<strong>$1</strong>")},scroll:true,scrollHeight:180,attachTo:"body"};e.Autocompleter.Cache=function(a){function c(i,p){a.matchCase||(i=i.toLowerCase());i=i.indexOf(p);if(i==-1)return false;return i==0||a.matchContains}function u(i,p){x>a.cacheLength&&A();q[i]||x++;q[i]=p}function o(){if(!a.data)return false; var i={},p=0;if(!a.url)a.cacheLength=1;i[""]=[];for(var v=0,t=a.data.length;v<t;v++){var g=a.data[v];g=typeof g=="string"?[g]:g;var f=a.formatItem(g,v+1,a.data.length);if(f!==false){var n=f.charAt(0).toLowerCase();i[n]||(i[n]=[]);g={value:f,data:g,result:a.formatResult&&a.formatResult(g)||f};i[n].push(g);p++<a.max&&i[""].push(g)}}e.each(i,function(l,z){a.cacheLength++;u(l,z)})}function A(){q={};x=0}var q={},x=0;setTimeout(o,25);return{flush:A,add:u,populate:o,load:function(i){if(!a.cacheLength||!x)return null; if(!a.url&&a.matchContains){var p=[];for(var v in q)if(v.length>0){var t=q[v];e.each(t,function(g,f){c(f.value,i)&&p.push(f)})}return p}else if(q[i])return q[i];else if(a.matchSubset)for(v=i.length-1;v>=a.minChars;v--)if(t=q[i.substr(0,v)]){p=[];e.each(t,function(g,f){if(c(f.value,i))p[p.length]=f});return p}return null}}};e.Autocompleter.Select=function(a,c,u,o){function A(){if(z){s=e("<div/>").hide().addClass(a.resultsClass).css("position","absolute").appendTo(a.attachTo);m=e("<ul>").appendTo(s).mouseover(function(d){if(q(d).nodeName&& q(d).nodeName.toUpperCase()=="LI"){f=e("li",m).removeClass(t.ACTIVE).index(q(d));e(q(d)).addClass(t.ACTIVE)}}).click(function(d){e(q(d)).addClass(t.ACTIVE);u();c.focus();return false}).mousedown(function(){o.mouseDownOnSelect=true}).mouseup(function(){o.mouseDownOnSelect=false});a.width>0&&s.css("width",a.width);z=false}}function q(d){for(d=d.target;d&&d.tagName!="LI";)d=d.parentNode;if(!d)return[];return d}function x(d){g.slice(f,f+1).removeClass();i(d);d=g.slice(f,f+1).addClass(t.ACTIVE);if(a.scroll){var j= 0;g.slice(0,f).each(function(){j+=this.offsetHeight});if(j+d[0].offsetHeight-m.scrollTop()>m[0].clientHeight)m.scrollTop(j+d[0].offsetHeight-m.innerHeight());else j<m.scrollTop()&&m.scrollTop(j)}}function i(d){f+=d;if(f<0)f=g.size()-1;else if(f>=g.size())f=0}function p(d){return a.max&&a.max<d?a.max:d}function v(){m.empty();for(var d=p(n.length),j=0;j<d;j++)if(n[j]){var C=a.formatItem(n[j].data,j+1,d,n[j].value,l);if(C!==false){C=e("<li>").html(a.highlight(C,l)).addClass(j%2==0?"ac_event":"ac_odd").appendTo(m)[0]; e.data(C,"ac_data",n[j])}}g=m.find("li");if(a.selectFirst){g.slice(0,1).addClass(t.ACTIVE);f=0}m.bgiframe()}var t={ACTIVE:"ac_over"},g,f=-1,n,l="",z=true,s,m;return{display:function(d,j){A();n=d;l=j;v()},next:function(){x(1)},prev:function(){x(-1)},pageUp:function(){f!=0&&f-8<0?x(-f):x(-8)},pageDown:function(){f!=g.size()-1&&f+8>g.size()?x(g.size()-1-f):x(8)},hide:function(){s&&s.hide();f=-1},visible:function(){return s&&s.is(":visible")},current:function(){return this.visible()&&(g.filter("."+t.ACTIVE)[0]|| a.selectFirst&&g[0])},show:function(){var d=e(c).offset();s.css({width:typeof a.width=="string"||a.width>0?a.width:e(c).width(),top:d.top+c.offsetHeight,left:d.left}).show();if(a.scroll){m.scrollTop(0);m.css({maxHeight:a.scrollHeight,overflow:"auto"});if(e.browser.msie&&typeof document.body.style.maxHeight==="undefined"){var j=0;g.each(function(){j+=this.offsetHeight});d=j>a.scrollHeight;m.css("height",d?a.scrollHeight:j);d||g.width(m.width()-parseInt(g.css("padding-left"))-parseInt(g.css("padding-right")))}}}, selected:function(){var d=g&&g.filter("."+t.ACTIVE).removeClass(t.ACTIVE);return d&&d.length&&e.data(d[0],"ac_data")},unbind:function(){s&&s.remove()}}};e.Autocompleter.Selection=function(a,c,u){if(a.createTextRange){var o=a.createTextRange();o.collapse(true);o.moveStart("character",c);o.moveEnd("character",u);o.select()}else if(a.setSelectionRange)a.setSelectionRange(c,u);else if(a.selectionStart){a.selectionStart=c;a.selectionEnd=u}a.focus()}})(jQuery);

/**
 * Boxy 0.1.4 - Facebook-style dialog, with frills
 *
 * (c) 2008 Jason Frame
 * Licensed under the MIT License (LICENSE)
 */
jQuery.fn.boxy=function(d){d=d||{};return this.each(function(){var e=this.nodeName.toLowerCase(),f=this;if(e=="a")jQuery(this).click(function(){var a=Boxy.linkedTo(this),b=this.getAttribute("href"),c=jQuery.extend({actuator:this,title:this.title},d);if(a)a.show();else if(b.indexOf("#")>=0){a=jQuery(b.substr(b.indexOf("#")));b=a.clone(true);a.remove();c.unloadOnHide=false;new Boxy(b,c)}else{if(!c.cache)c.unloadOnHide=true;Boxy.load(this.href,c)}return false});else e=="form"&&jQuery(this).bind("submit.boxy",function(){Boxy.confirm(d.message||"Please confirm:",function(){jQuery(f).unbind("submit.boxy").submit()});return false})})};
function Boxy(a,b){this.boxy=jQuery(Boxy.WRAPPER);jQuery.data(this.boxy[0],"boxy",this);this.visible=false;this.options=jQuery.extend({},Boxy.DEFAULTS,b||{});if(this.options.modal)this.options=jQuery.extend(this.options,{center:true,draggable:false});this.options.actuator&&jQuery.data(this.options.actuator,"active.boxy",this);this.setContent(a||"<div></div>");this._setupTitleBar();this.boxy.css("display","none").appendTo(document.body);this.toTop();if(this.options.fixed)if(jQuery.browser.msie&&jQuery.browser.version< 7)this.options.fixed=false;else this.boxy.addClass("fixed");this.options.center&&Boxy._u(this.options.x,this.options.y)?this.center():this.moveTo(Boxy._u(this.options.x)?this.options.x:Boxy.DEFAULT_X,Boxy._u(this.options.y)?this.options.y:Boxy.DEFAULT_Y);this.options.show&&this.show()}Boxy.EF=function(){}; jQuery.extend(Boxy,{WRAPPER:"<table cellspacing='0' cellpadding='0' border='0' class='boxy-wrapper'><tr><td class='boxy-top-left'></td><td class='boxy-top'></td><td class='boxy-top-right'></td></tr><tr><td class='boxy-left'></td><td class='boxy-inner'></td><td class='boxy-right'></td></tr><tr><td class='boxy-bottom-left'></td><td class='boxy-bottom'></td><td class='boxy-bottom-right'></td></tr></table>",DEFAULTS:{title:null,closeable:true,draggable:true,clone:false,actuator:null,center:true,show:true, modal:false,fixed:true,closeText:"[close]",unloadOnHide:false,clickToFront:false,behaviours:Boxy.EF,afterDrop:Boxy.EF,afterShow:Boxy.EF,afterHide:Boxy.EF,beforeUnload:Boxy.EF},DEFAULT_X:50,DEFAULT_Y:50,zIndex:1337,dragConfigured:false,resizeConfigured:false,dragging:null,load:function(a,b){b=b||{};var c={url:a,type:"GET",dataType:"html",cache:false,success:function(d){d=jQuery(d);if(b.filter)d=jQuery(b.filter,d);new Boxy(d,b)}};jQuery.each(["type","cache"],function(){if(this in b){c[this]=b[this]; delete b[this]}});jQuery.ajax(c)},get:function(a){a=jQuery(a).parents(".boxy-wrapper");return a.length?jQuery.data(a[0],"boxy"):null},linkedTo:function(a){return jQuery.data(a,"active.boxy")},alert:function(a,b,c){return Boxy.ask(a,["OK"],b,c)},confirm:function(a,b,c){return Boxy.ask(a,["OK","Cancel"],function(d){d=="OK"&&b()},c)},ask:function(a,b,c,d){d=jQuery.extend({modal:true,closeable:false},d||{},{show:true,unloadOnHide:true});a=jQuery("<div></div>").append(jQuery('<div class="boxy-question"></div>').html(a)); var f={},g=[];if(b instanceof Array)for(var e=0;e<b.length;e++){f[b[e]]=b[e];g.push(b[e])}else for(e in b){f[b[e]]=e;g.push(b[e])}b=jQuery('<form class="boxy-answers"></form>');b.html(jQuery.map(g,function(h){return"<input type='button' value='"+h+"' />"}).join(" "));jQuery("input[type=button]",b).click(function(){var h=this;Boxy.get(this).hide(function(){c&&c(f[h.value])})});a.append(b);new Boxy(a,d)},isModalVisible:function(){return jQuery(".boxy-modal-blackout").length>0},_u:function(){for(var a= 0;a<arguments.length;a++)if(typeof arguments[a]!="undefined")return false;return true},_handleResize:function(){var a=jQuery(document);jQuery(".boxy-modal-blackout").css("display","none").css({width:a.width(),height:a.height()}).css("display","block")},_handleDrag:function(a){var b;if(b=Boxy.dragging)b[0].boxy.css({left:a.pageX-b[1],top:a.pageY-b[2]})},_nextZ:function(){return Boxy.zIndex++},_viewport:function(){var a=document.documentElement,b=document.body,c=window;return jQuery.extend(jQuery.browser.msie? {left:b.scrollLeft||a.scrollLeft,top:b.scrollTop||a.scrollTop}:{left:c.pageXOffset,top:c.pageYOffset},!Boxy._u(c.innerWidth)?{width:c.innerWidth,height:c.innerHeight}:!Boxy._u(a)&&!Boxy._u(a.clientWidth)&&a.clientWidth!=0?{width:a.clientWidth,height:a.clientHeight}:{width:b.clientWidth,height:b.clientHeight})}}); Boxy.prototype={estimateSize:function(){this.boxy.css({visibility:"hidden",display:"block"});var a=this.getSize();this.boxy.css("display","none").css("visibility","visible");return a},getSize:function(){return[this.boxy.width(),this.boxy.height()]},getContentSize:function(){var a=this.getContent();return[a.width(),a.height()]},getPosition:function(){var a=this.boxy[0];return[a.offsetLeft,a.offsetTop]},getCenter:function(){var a=this.getPosition(),b=this.getSize();return[Math.floor(a[0]+b[0]/2),Math.floor(a[1]+ b[1]/2)]},getInner:function(){return jQuery(".boxy-inner",this.boxy)},getContent:function(){return jQuery(".boxy-content",this.boxy)},setContent:function(a){a=jQuery(a).css({display:"block"}).addClass("boxy-content");if(this.options.clone)a=a.clone(true);this.getContent().remove();this.getInner().append(a);this._setupDefaultBehaviours(a);this.options.behaviours.call(this,a);return this},moveTo:function(a,b){this.moveToX(a).moveToY(b);return this},moveToX:function(a){typeof a=="number"?this.boxy.css({left:a}): this.centerX();return this},moveToY:function(a){typeof a=="number"?this.boxy.css({top:a}):this.centerY();return this},centerAt:function(a,b){var c=this[this.visible?"getSize":"estimateSize"]();typeof a=="number"&&this.moveToX(a-c[0]/2);typeof b=="number"&&this.moveToY(b-c[1]/2);return this},centerAtX:function(a){return this.centerAt(a,null)},centerAtY:function(a){return this.centerAt(null,a)},center:function(a){var b=Boxy._viewport(),c=this.options.fixed?[0,0]:[b.left,b.top];if(!a||a=="x")this.centerAt(c[0]+ b.width/2,null);if(!a||a=="y")this.centerAt(null,c[1]+b.height/2);return this},centerX:function(){return this.center("x")},centerY:function(){return this.center("y")},resize:function(a,b,c){if(this.visible){a=this._getBoundsForResize(a,b);this.boxy.css({left:a[0],top:a[1]});this.getContent().css({width:a[2],height:a[3]});c&&c(this);return this}},tween:function(a,b,c){if(this.visible){a=this._getBoundsForResize(a,b);var d=this;this.boxy.stop().animate({left:a[0],top:a[1]});this.getContent().stop().animate({width:a[2], height:a[3]},function(){c&&c(d)});return this}},isVisible:function(){return this.visible},show:function(){if(!this.visible){if(this.options.modal){var a=this;if(!Boxy.resizeConfigured){Boxy.resizeConfigured=true;jQuery(window).resize(function(){Boxy._handleResize()})}this.modalBlackout=jQuery('<div class="boxy-modal-blackout"></div>').css({zIndex:Boxy._nextZ(),opacity:0,width:jQuery(document).width(),height:jQuery(document).height()}).appendTo(document.body);this.toTop();this.options.closeable&&jQuery(document.body).bind("keypress.boxy", function(b){if((b.which||b.keyCode)==27){a.hide();jQuery(document.body).unbind("keypress.boxy")}})}this.boxy.stop().css({opacity:1}).show();this.visible=true;this._fire("afterShow");return this}},hide:function(a){if(this.visible){var b=this;if(this.options.modal){jQuery(document.body).unbind("keypress.boxy");this.modalBlackout.animate({opacity:0},function(){jQuery(this).remove()})}this.boxy.stop().animate({opacity:0},300,function(){b.boxy.css({display:"none"});b.visible=false;b._fire("afterHide"); a&&a(b);b.options.unloadOnHide&&b.unload()});return this}},toggle:function(){this[this.visible?"hide":"show"]();return this},hideAndUnload:function(a){this.options.unloadOnHide=true;this.hide(a);return this},unload:function(){this._fire("beforeUnload");this.boxy.remove();this.options.actuator&&jQuery.data(this.options.actuator,"active.boxy",false)},toTop:function(){this.boxy.css({zIndex:Boxy._nextZ()});return this},getTitle:function(){return jQuery("> .title-bar h2",this.getInner()).html()},setTitle:function(a){jQuery("> .title-bar h2", this.getInner()).html(a);return this},_getBoundsForResize:function(a,b){var c=this.getContentSize();c=[a-c[0],b-c[1]];var d=this.getPosition();return[Math.max(d[0]-c[0]/2,0),Math.max(d[1]-c[1]/2,0),a,b]},_setupTitleBar:function(){if(this.options.title){var a=this,b=jQuery("<div class='title-bar'></div>").html("<h2>"+this.options.title+"</h2>");this.options.closeable&&b.append(jQuery("<a href='#' class='boxy-close'></a>").html(this.options.closeText));if(this.options.draggable){b[0].onselectstart= function(){return false};b[0].unselectable="on";b[0].style.MozUserSelect="none";if(!Boxy.dragConfigured){jQuery(document).mousemove(Boxy._handleDrag);Boxy.dragConfigured=true}b.mousedown(function(c){a.toTop();Boxy.dragging=[a,c.pageX-a.boxy[0].offsetLeft,c.pageY-a.boxy[0].offsetTop];jQuery(this).addClass("dragging")}).mouseup(function(){jQuery(this).removeClass("dragging");Boxy.dragging=null;a._fire("afterDrop")})}this.getInner().prepend(b);this._setupDefaultBehaviours(b)}},_setupDefaultBehaviours:function(a){var b= this;this.options.clickToFront&&a.click(function(){b.toTop()});jQuery(".close",a).click(function(){b.hide();return false}).mousedown(function(c){c.stopPropagation()})},_fire:function(a){this.options[a].call(this)}}; 


/**
 * Copyright (c) 2006 Brandon Aaron (http://brandonaaron.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) 
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * $LastChangedDate: 2007-07-22 01:45:56 +0200 (Son, 22 Jul 2007) $
 * $Rev: 2447 $
 *
 * Version 2.1.1
 */
(function($){$.fn.bgIframe=$.fn.bgiframe=function(s){if($.browser.msie&&/6.0/.test(navigator.userAgent)){s=$.extend({top:'auto',left:'auto',width:'auto',height:'auto',opacity:true,src:'javascript:false;'},s||{});var prop=function(n){return n&&n.constructor==Number?n+'px':n;},html='<iframe class="bgiframe"frameborder="0"tabindex="-1"src="'+s.src+'"'+'style="display:block;position:absolute;z-index:-1;'+(s.opacity!==false?'filter:Alpha(Opacity=\'0\');':'')+'top:'+(s.top=='auto'?'expression(((parseInt(this.parentNode.currentStyle.borderTopWidth)||0)*-1)+\'px\')':prop(s.top))+';'+'left:'+(s.left=='auto'?'expression(((parseInt(this.parentNode.currentStyle.borderLeftWidth)||0)*-1)+\'px\')':prop(s.left))+';'+'width:'+(s.width=='auto'?'expression(this.parentNode.offsetWidth+\'px\')':prop(s.width))+';'+'height:'+(s.height=='auto'?'expression(this.parentNode.offsetHeight+\'px\')':prop(s.height))+';'+'"/>';return this.each(function(){if($('> iframe.bgiframe',this).length==0)this.insertBefore(document.createElement(html),this.firstChild);});}return this;};})(jQuery);

//"Elastic": Facebook-like textarea by Jan Jarfalk - http://www.unwrongest.com/projects/elastic/
(function(jQuery){jQuery.fn.extend({elastic:function(){var mimics=['paddingTop','paddingRight','paddingBottom','paddingLeft','fontSize','lineHeight','fontFamily','width','fontWeight'];return this.each(function(){if(this.type!='textarea'){return false}var $textarea=jQuery(this),$twin=jQuery('<div />').css({'position':'absolute','display':'none','word-wrap':'break-word'}),lineHeight=parseInt($textarea.css('line-height'),10)||parseInt($textarea.css('font-size'),'10'),minheight=parseInt($textarea.css('height'),10)||lineHeight*3,maxheight=parseInt($textarea.css('max-height'),10)||Number.MAX_VALUE,goalheight=0,i=0;if(maxheight<0){maxheight=Number.MAX_VALUE}$twin.appendTo($textarea.parent());var i=mimics.length;while(i--){$twin.css(mimics[i].toString(),$textarea.css(mimics[i].toString()))}function setHeightAndOverflow(height,overflow){curratedHeight=Math.floor(parseInt(height,10));if($textarea.height()!=curratedHeight){$textarea.css({'height':curratedHeight+'px','overflow':overflow})}}function update(){var textareaContent=$textarea.val().replace(/&/g,'&amp;').replace(/  /g,'&nbsp;').replace(/<|>/g,'&gt;').replace(/\n/g,'<br />');var twinContent=$twin.html();if(textareaContent+'&nbsp;'!=twinContent){$twin.html(textareaContent+'&nbsp;');if(Math.abs($twin.height()+lineHeight-$textarea.height())>3){var goalheight=$twin.height()+lineHeight;if(goalheight>=maxheight){setHeightAndOverflow(maxheight,'auto')}else if(goalheight<=minheight){setHeightAndOverflow(minheight,'hidden')}else{setHeightAndOverflow(goalheight,'hidden')}}}}$textarea.css({'overflow':'hidden'});$textarea.keyup(function(){update()});$textarea.live('input paste',function(e){setTimeout(update,250)});update()})}})})(jQuery);

// Password Strength Meter
function passwordStrengthMeter(a){function p(){if(b>2)return a.substring(1,a.length-1).replace(/[a-zA-Z ]/g,"").length;return 0}function q(){var e=(h?1:0)+(j?1:0)+(g?1:0)+(m?1:0);if(b>=8&&e>=3)return e+1;return 0}function r(){var e=0;if(b<2)return 0;var c=a;for(i=0;c.length;){var f=c.length;c=c.replace(new RegExp("["+c.charAt(0)+"]","gi"),"");f=f-c.length;if(f>1)e+=f*(f-1);i++}return e}function k(e){var c=0;if(b<2)return 0;for(i=0;i<b-1;i++)a.charAt(i).match(e)&&a.charAt(i+1).match(e)&&c++;return c}function n(e){var c=0;if(b<3)return 0;for(i=0;i<b-2;i++)c+=o(a.charAt(i),a.charAt(i+1),a.charAt(i+2),e);for(i=b-1;i>1;i--)c+=o(a.charAt(i),a.charAt(i-1),a.charAt(i-2),e);return c}function o(e,c,f,l){if(e.match(l)&&c.match(l)&&f.match(l))if(String.fromCharCode(e.charCodeAt(0)+1)==c&&String.fromCharCode(e.charCodeAt(0)+2)==f)return 1;return 0}var b=a.length,h=a.replace(/[^A-Z]/g,"").length,j=a.replace(/[^a-z]/g,"").length,g=a.replace(/[^0-9]/g,"").length,m=a.replace(/[a-zA-Z0-9 ]/g,"").length,s=p(),t=q(),u=a.replace(/[^a-zA-Z]/g,"").length==b?b:0,v=g==b?b:0,w=r(),x=k(/[A-Z]/),y=k(/[a-z]/),z=k(/[0-9]/),A=n(/[a-zA-Z]/),B=n(/[0-9]/),d=b*4;d=h?d+(b-h)*2:d;d=j?d+(b-j)*2:d;d=g!=b?d+g*4:d;d=d+m*6+s*2+t*2-u-v-w-x*2-y*2-z*2-A*3-B*3;if(d<0)d=0;else if(d>100)d=100;return d};
/**
 * jQuery (character and word) counter
 * Copyright (C) 2009  Wilkins Fernandez
 */
(function(b){b.fn.extend({counter:function(a){a=b.extend({},{type:"char",count:"down",goal:140},a);var d=false;return this.each(function(){function e(c){if(typeof a.type==="string")switch(a.type){case "char":if(a.count==="down")return a.goal-c;else if(a.count==="up")return c;break;case "word":if(a.count==="down")return a.goal-c;else if(a.count==="up")return c;break;default:}}var f=b(this),h=b("."+this.name+"_counter",b(this).closest("form")).html(e(b(f).val().length));f.bind("keyup click blur focus change paste",function(c){switch(a.type){case "char":c=b(f).val().length;break;case "word":c=f.val()===""?0:b.trim(f.val()).replace(/\s+/g," ").split(" ").length;break;default:}switch(a.count){case "up":if(e(c)>=a.goal&&a.type==="char"){b(this).val(b(this).val().substring(0,a.goal));d=true;break}if(e(c)===a.goal&&a.type==="word"){d=true;break}else if(e(c)>a.goal&&a.type==="word"){b(this).val("");h.text("0");d=true;break}break;case "down":if(e(c)<=0&&a.type==="char"){b(this).val(b(this).val().substring(0,a.goal));d=true;break}if(e(c)===0&&a.type==="word")d=true;else if(e(c)<0&&a.type==="word"){b(this).val("");d=true;break}break;default:}f.keydown(function(g){if(d){this.focus();if(g.keyCode!==46&&g.keyCode!==8)if(b(this).val().length>a.goal&&a.type==="char"){b(this).val(b(this).val().substring(0,a.goal));return false}else return g.keyCode!==32&&g.keyCode!==8&&a.type==="word"?true:false;else{d=false;return true}}});h.text(e(c))})})}})})(jQuery);

/**
 * jQuery Constrain
 */
(function(b){b.fn.constrain=function(e){function k(a,g){var l=false;b.each(e.limit,function(f){var d=this;if(f.charCodeAt(0)==g.which){l=d<0?false:d<b(a).val().split(f).length;return false}});return l}function m(a){return a.chars.length>0||a.regex&&a.regex.length>0}function j(a,g,l){g=a.chars.split("");for(var f in g)if(g[f].charCodeAt(0)==l.which)return true;if(a.regex)if((new RegExp(a.regex)).test(String.fromCharCode(l.which)))return true;return false}function p(a,g){if(g.which==0||g.which==8||g.which==27)return false;var l=m(e.prohibit)?j(e.prohibit,a,g):false,f=m(e.allow)?j(e.allow,a,g):true;a=k(a,g);return l||!f||a}e=b.extend(true,{},{limit:{},prohibit:{chars:"",regex:false},allow:{chars:"",regex:false}},e);return this.each(function(){b(this).keypress(function(a){p(this,a)&&a.preventDefault()})})};b.fn.numeric=function(e){e=b.extend(true,{},{onblur:true,format:""},e);var k=e.format.split("."),m=k.length>1?k[1].length:false;return this.each(function(){var j="\\d";if(e.format.indexOf(".")>-1)j+="\\.";if(e.format.indexOf(",")>-1)j+=",";j={allow:{regex:"["+j+"]"},limit:{".":1}};b(this).constrain(j);if(m){b(this).blur(function(){var a=parseFloat(b(this).val());if(!isNaN(a)){a=b(this).val();b(this).val(b.formatNumber(a,e.format))}});if(!e.onblur){var p=new RegExp("\\d+\\.*\\d{0,"+m+"}");b(this).keyup(function(a){if(!(a.which<48&&a.which>57||a.which<96&&a.which>105)){a=b(this).val();b(this).val(a.match(p))}})}}})}})(jQuery);(function(b){b.numericFormat=b.numericFormat||{};b.numericFormat.formats=b.numericFormat.formats||[];b.extend({formatNumber:function(e,k){function m(j,p){function a(d,c){c="var "+c+" = function(num){\n";c+="num = num.replace(/,/,'');";var h=d.split(";");switch(h.length){case 1:c+=g(d);break;case 2:c+='return (num < 0) ? _numberFormat(num,"'+f(h[1])+'", 1) : _numberFormat(num,"'+f(h[0])+'", 2);';break;case 3:c+='return (num < 0) ? _numberFormat(num,"'+f(h[1])+'", 1) : ((num == 0) ? _numberFormat(num,"'+f(h[2])+'", 2) : _numberFormat(num,"'+f(h[0])+'", 3));';break;default:c+="throw 'Too many semicolons in format string';";break}return c+"};"}function g(d){if(d.length>0&&d.search(/[0#?]/)==-1)return"return '"+f(d)+"';\n";var c="var val = (context == null) ? new Number(num) : Math.abs(num);\n",h=false,n=d,r="",s=0,o=0,q=0,t=false,u="";if(i=d.match(/\..*(e)([+-]?)(0+)/i)){u=i[1];t=i[2]=="+";q=i[3].length;d=d.replace(/(e)([+-]?)(0+)/i,"")}var i=d.match(/^([^.]*)\.(.*)$/);if(i){n=i[1].replace(/\./g,"");r=i[2].replace(/\./g,"")}if(d.indexOf("%")>=0)c+="val *= 100;\n";if(i=n.match(/(,+)(?:$|[^0#?,])/))c+="val /= "+Math.pow(1E3,i[1].length)+"\n;";if(n.search(/[0#?],[0#?]/)>=0)h=true;if(i||h)n=n.replace(/,/g,"");if(i=n.match(/0[0#?]*/))s=i[0].length;if(i=r.match(/[0#?]*/))o=i[0].length;if(q>0)c+="var sci = toScientific(num,val,"+s+", "+o+", "+q+", "+t+");\nvar arr = [sci.l, sci.r];\n";else{if(d.indexOf(".")<0)c+="val = (val > 0) ? Math.ceil(val) : Math.floor(val);\n";c+="var arr = round(val,"+o+").toFixed("+o+").split('.');\n";c+="arr[0] = (val < 0 ? '-' : '') + leftPad((val < 0 ? arr[0].substring(1) : arr[0]), "+s+", '0');\n"}if(h)c+="arr[0] = addSeparators(arr[0]);\n";c+="arr[0] = reverse(injectIntoFormat(reverse(arr[0]), '"+f(l(n))+"', true));\n";if(o>0)c+="arr[1] = injectIntoFormat(arr[1], '"+f(r)+"', false);\n";if(q>0)c+="arr[1] = arr[1].replace(/(\\d{"+o+"})/, '$1"+u+"' + sci.s);\n";return c+"return arr.join('.');\n"}function l(d){for(var c="",h=d.length;h>0;--h)c+=d.charAt(h-1);return c}function f(d){return d.replace(/('|\\)/g,"\\$1")}j="numFormat"+b.numericFormat.formats.length++;eval(a(p,j));return eval(j)}b.numericFormat.formats[k]||(b.numericFormat.formats[k]=m(e,k));return b.numericFormat.formats[k](e)}})})(jQuery);