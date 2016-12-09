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

function gooSocial(social, appName, returnUrl, sid, mode, callbackUrl, services) {
    var homeUrl = '/',
    actionType = "login",
    openid_url = social;

    var strUrl = '{0}?app={1}&returnUrl={2}&actionType={3}&openid_url={4}&sid={5}&mode={6}&callbackUrl={7}&services={8}';
    var link = String.format(strUrl, homeUrl, appName, returnUrl, actionType, openid_url, sid, mode, callbackUrl, services);
    location.href = link;
}

function gooLocal(homeUrl, appName, returnUrl, sid, mode, callbackUrl, services) {
    var strUrl = '{0}?app={1}&sid={2}&returnUrl={3}&mode={4}&callbackUrl={5}&services={6}';
    var link = String.format(strUrl, homeUrl, appName, sid, returnUrl, mode, callbackUrl, services);
    location.href = link;
}

String.format = function () {
    // The string containing the format items (e.g. "{0}")
    // will and always has to be the first argument.
    var theString = arguments[0];

    // start with the second argument (i = 1)
    for (var i = 1; i < arguments.length; i++) {
        // "gm" = RegEx options for Global search (more than one instance)
        // and for Multiline search
        var regEx = new RegExp("\\{" + (i - 1) + "\\}", "gm");
        theString = theString.replace(regEx, arguments[i]);
    }

    return theString;
}