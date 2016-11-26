$(document).ready(function(){
    $("#closeBannerTop").click(function(){
        $("#bannerTop").hide();
    });

    $(".top-search-location").click(function(){    	
        $(".top-search-location .chosen-container").toggleClass("chosen-with-drop");
    });
    /*$(".top-search-location .chosen-container .chosen-drop .chosen-results li").mouseenter(function(){    	
    	alert("sdsd");
        $(this).toggleClass("highlighted");
    });*/
    
    $(".top-search-category").click(function(){    	
        $(".top-search-category .chosen-container").toggleClass("chosen-with-drop");
    });

    /*$(".chosen-drop").on("mouseleave", function(){
        // console.log($(this)[0]);
        $($(this)[0].parentNode).toggleClass("chosen-with-drop");
    });*/

    
    /*$(".active-results").hover(function(){
    	console.log("sdd");
        $(".active-results").toggleClass("highlighted");
    });*/
    
    
    /*$("#colMenu").mouseenter(function(){
    	var wcolmenu = $("#colMenu").width();
    	var wcolcontent = $("#colContent").width();
    	console.log($("#colContent").width());
        $("#colMenu").width(wcolmenu + 40);
        $("#colContent").width(wcolcontent - 40);
        console.log($("#colContent").width());
    });
    $("#colMenu").mouseleave(function(){
    	var wcolmenu = $("#colMenu").width();
    	var wcolcontent = $("#colContent").width();
    	console.log($("#colContent").width());
        $("#colMenu").width(wcolmenu - 40);
        $("#colContent").width(wcolcontent + 40);
        console.log($("#colContent").width());
    });*/
});