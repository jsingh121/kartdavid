require(['jquery'], function($) {
    $.migrateMute = true;
});

requirejs(['jquery', 'jquery/ui'], function($) {
$(document).ready(function() {
    
    "use strict";
/*--qty-functions--*/

   $('.up_btn').on('click', function() {
       var $qty = $(this).closest('.count_quanity').find('.qty');
       var currentVal = parseInt($qty.val());
       if (!isNaN(currentVal)) {
           $qty.val(currentVal + 1);
       }
   });
   $('.down_btn').on('click', function() {
       var $qty = $(this).closest('.count_quanity').find('.qty');
       var currentVal = parseInt($qty.val());
       if (!isNaN(currentVal) && currentVal > 1) {
           $qty.val(currentVal - 1);
       }
   });

    


    //FOR Top Search bar
    $(".srch-btn").click(function() {
        $(".srch-fild").toggleClass("open");
    });
        


//FOR STICKY HEADER
//  $(window).load(function(){
//      $(".header1").sticky({ topSpacing: 0 });
//    });



//FOR MENU

    $(".toggleMenu").click(function(e) {
        e.preventDefault();
        $(this).toggleClass("active");
        $(".navi").slideToggle();
        
    });

$('.parent > span').click(function(e) {
        $(this).parent('.parent').siblings().removeClass('openSub');
        $(this).parent('.parent').toggleClass('openSub');
        $(this).parent('.parent').siblings().children('.submenu').slideUp();
        $(this).parent('.parent').children('.submenu').slideToggle();
    });


    $('.owl-ser').owlCarousel({
    loop:true,
    margin:0,
    nav:false,
    autoplayTimeout:2000,
    mouseDrag:false,
    responsive:{
        0:{
            items:1,
            autoplay:true,
            touchDrag:true
        },
        665:{
            items:2,
            autoplay:true,
            touchDrag:true
        },
        1000:{
            items:4
        }
    }
});








    $('.owl-max').owlCarousel({
    loop:true,
    margin:0,
    nav:true,
    navText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
    autoplay:true,
    autoplayTimeout:1500,
    autoplayHoverPause:false,
    responsive:{
        0:{
            items:1,
            
        },
        480:{
            items:2
        },
        
        
        640:{
            items:4
        },
        1024:{
            items:6
        }
    }
});






    $('.owl-one').owlCarousel({
    loop:true,
    margin:0,
    nav:false,
	autoplay:true,
    autoplayTimeout:3000,	
    mouseDrag:true,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:1
        }
    }
});



$('.owl-review').owlCarousel({
    loop:true,
    margin:50,
    nav:true,
    navText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:3
        }
    }
});




$('.owl-gall').owlCarousel({
    loop:true,
    margin:0,
    nav:true,
    navText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
    responsive:{
        0:{
            items:1,
			 autoplay:true,
            autoplayTimeout:1500
        },
        600:{
            items:1
        },
        1000:{
            items:1
        }
    }
});



$('.owl-prod').owlCarousel({
    loop:true,
    margin:0,
    nav:true,
    navText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
    autoplay:true,
    autoplayTimeout:3000,
    autoplayHoverPause:false,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:1
        }
    }
});


$('.owl-service').owlCarousel({
    loop:true,
    margin:0,
    nav:false,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:1
        }
    }
});


	
    $('.owl-acce').owlCarousel({
    loop:true,
    margin:0,
    nav:true,
    navText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
    autoplay:false,
    autoplayTimeout:1500,
    autoplayHoverPause:false,
    responsive:{
        0:{
            items:1,
            
        },
		
		
		480:{
            items:2,
        },
		
        768:{
            items:3,
        },
        
     
        1024:{
            items:4
        }
    }
});	
	
	
	
	
	
	
	


//$('body').addClass('fadeIn animated wow');
//$('.owl-ser .item , .graphics ,.mx-team ,.review-sec ,.gall-sec ,.ser-cell ,.footer ').addClass('fadeIn  animated wow');

//new WOW().init();

    $('.dtl-title').click(function() {
    $(this).parents('.dtl-wrap').siblings().children('.dtl-title').removeClass('active');
        $(this).addClass('active');
        $(this).parents('.dtl-wrap').siblings().children('.dtl-content').slideUp();
        $(this).next('.dtl-content').slideDown(); 
    });

    $('#thumb-carousel').owlCarousel({
        items:1,
        navText: [ '<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>' ],
        //loop:true,
        nav:true,
        center:true,
        margin:0,
        URLhashListener:false,
        autoplayHoverPause:true,
        startPosition: 'URLHash'
    });
    
    
    $(".red-btn").click(function(){
       
        //this will find the selected website from the dropdown
        var go_to_url = $("#websites").find(":selected").val();

        console.log(go_to_url);
     
        //this will redirect us in same window
        document.location.href = go_to_url;
    });



});



});





