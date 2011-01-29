  
  function erroImg(el){     
    $(el).css('visibility','hidden');
    $(el).closest('.photo').removeClass('bg_loading');
  }
  
  $(function(){
    $('.last_published_ad .photo img').each(function() {
      loadImage(this);
    });
  });
  
  function loadImage(el){
    $(el).closest('.photo').addClass('bg_loading');
    
    $(el).error(function(){
      erroImg(el);
    });
    if ($(el).attr('complete')) {
      $(el).fadeIn('slow');
      $(el).closest('.photo').removeClass('bg_loading');
    }
    $(el).load(function(){
      $(el).fadeIn('slow');
      $(el).closest('.photo').removeClass('bg_loading');
    });
  
  }
  
  $(function(){
    
      var img_path = "images";
      
      jQuery.preloadImages = function(){
        
        for(var i = 0; i<arguments.length; i++){
          jQuery("<img>").attr("src", img_path + arguments[i]);
        }
      }
      
    $.preloadImages("blog-logo.jpg","submit-bg-hover.jpg");
    
    visibleAds = $('.last_published_ads_content>ul>li').not('.hidden');
    hiddenAds  = $('.last_published_ads_content>ul>li.hidden');

    rotateAds();

    function rotateAds() {
      if (!hiddenAds.size()) return;
      setTimeout(function(){
        $(visibleAds).eq(Math.floor(Math.random() * (visibleAds.length))).find('.last_published_ad>div').fadeOut('slow', function(){
          hiddenEl = $(hiddenAds).eq(Math.floor(Math.random() * (hiddenAds.length))).find('.last_published_ad>div');
          visibleElHtml = $(this).html();
          $(this).html($(hiddenEl).html());
          $(hiddenEl).html(visibleElHtml);
          $(this).fadeIn('slow', function(){rotateAds();});
        });
      }, 2 * 1000);
    }
    
    $("#s").val("pesquisar no blog");
    $("#s").blur(function(){
        if($(this).val() == ""){
          $(this).val('pesquisar no blog');
        }
    }).click(function(){
      if($(this).val() == "pesquisar no blog"){
          $(this).val('');
          
        }
    });
    
    $("#widget_twitter_vjck li:last").css("border","none");
    
    $(".share-icons img").hover(function(){
      $(this).animate({opacity: 0.7},100);
    },function(){
      $(this).animate({opacity: 1},100);
    })
    
  });
