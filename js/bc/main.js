
// JavaScript Document
$.support.cors = true;
var pictureSource;   // picture source
var destinationType; // sets the format of returned value
var serviceUrl = "http://localhost/vtt/samsung_care/php/";
//var serviceUrl = "https://dev.phonebuy.com/mobile/samsung_care/php/";
var bckKeyCount=0;
var attendanceArray = [];
var version = '2.8.3';
//var serviceUrl = "http://salesplay.in/Salesplay_App_Source/php/";

var geoOptionsHigh = {timeout:5000, enableHighAccuracy: true};
var geoOptionsLow = {timeout:5000, enableHighAccuracy: false};

// Wait for Cordova to connect with the device
document.addEventListener("deviceready",onDeviceReady,true);
document.addEventListener("backbutton", onBackKeyDown);
function onDeviceReady()
{
	//navigator.splashscreen.hide();
	//console.log("inside device ready");
	
	//setTimeout(function(){ $('#restBtn').click(); }, 1000);
	
	//navigator.geolocation.getCurrentPosition(onSuccessFirst, onErr, geoOptions);
	getCurrentPosition();
}

function onSuccessFirst(position)
{
	var lat  = position.coords.latitude;
	var long = position.coords.longitude;
	alert("lat : " + position.coords.latitude + " lng : " + position.coords.longitude);
	//window.plugins.toast.showShortBottom('GPS okay!')
}

 function getCurrentPosition(){
	navigator.geolocation.getCurrentPosition(
		onSuccessFirst,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					onSuccessFirst, 
					function(error){
						switch(error.code){
							case error.PERMISSION_DENIED: alert("user did not share geolocation data");
							break;
				
							case error.POSITION_UNAVAILABLE: alert("could not detect current position");
							break;
				
							case error.TIMEOUT: alert("retrieving position timed out");
							break;
				
							case error.UNKNOWN_ERROR: alert("unknown GPS error");
							break;
							}
			
						},
					geoOptionsLow);
			 	return;
				}
			switch(error.code){
				case error.PERMISSION_DENIED: alert("user did not share geolocation data");
				break;
	
				case error.POSITION_UNAVAILABLE: alert("could not detect current position");
				break;
	
				case error.TIMEOUT: alert("retrieving position timed out");
				break;
	
				case error.UNKNOWN_ERROR: alert("unknown GPS error");
				break;
				}

			}, 
		geoOptionsHigh
	); 


}
 
function onBackKeyDown(e) {
    e.preventDefault();
	bckKeyCount++;
	setTimeout(function(){ bckKeyCount=0;}, 2000);
	
	if(bckKeyCount!=2)
	{
		if(localStorage.getItem('app_user'))
			window.location.replace('#page_home');
		//window.plugins.toast.showLongCenter('Press Back button again to Exit the App!');
        return;
    }else{
        navigator.app.exitApp();
    }
}
$(document).on('click','.menuLinkBtn',function()
{
	var pageId = $(this).attr('href');
	var un_fos = localStorage.getItem('un_fos');
	var fos_name = localStorage.getItem('fos_name');
	if(un_fos==0 || un_fos==2)
	{
		var menus_script = '';
		menus_script += '<ul style="padding: 0;font-size: 15px;height:550px;overflow: auto;" class="menu">';
			menus_script += '<a href="#page_home"><li style="font-size:19px;border-bottom: 3px solid lightgrey !important;">HOME<i class="fas fa-home" style="float:right;margin-right:20px;font-size: 23px;color:#1a8cff;"></i></li></a>';
			menus_script += '<a href="#page2" ><li onclick="timer_entry()" ><i class="fa fa-check" style="color:#3ADF00;"></i> &nbsp;<span class="page2">Store Visit</span></li></a>';
			menus_script += '<a href="#page3"><li onclick="attendance_log()"><i class="fa fa-clock" style="color:#F7BE81;"></i> &nbsp;<span class="page3">Attendance Log</span></li></a>';

			menus_script += '<a href="#"><li onclick="logoutApp()"><i class="fa fa-power-off	"  aria-hidden="true" style="color:#10e4ac;"></i> &nbsp;<span >Log Out</span></li></a>';	
			// menus_script += '<a href="#page3"><li><i class="fa fa-cart-plus" style="color:#F7BE81;"></i> &nbsp;<span class="page3">Get Orders</span></li></a>';

			// menus_script += '<a href="#page4"><li><i class="fas fa-rupee-sign" style="color:#0B3B17;"></i> &nbsp;&nbsp;&nbsp;<span class="page4">Get Payments</span></li></a>';
			// if(localStorage.getItem('app_admin')==1 && fos_name!='ADMIN')
			// 	menus_script += '<a href="#page24"><li><i class="fas fa-thumbs-up" style="color:#0174DF;"></i> &nbsp;<span class="page24">Approval Requests</span></li></a>';
			// menus_script += '<a href="#page18"><li><i class="fa fa-list-alt" style="color:#FE642E;"></i> &nbsp;<span class="page18">Counter Stock</span></li></a>';
			// menus_script += '<a href="#page9"><li><i class="fa fa fa-truck" style="color:#DF0101;"></i> &nbsp;<span class="page9">Delivery</span></li></a>';
			// menus_script += '<a href="#page14"><li><i class="fa fa fa-tasks" style="color:#FACC2E;"></i> &nbsp;<span class="page14">Today Task</span></li></a>';
			// menus_script += '<a href="#page1"><li><i class="fa fa-building" style="color:#A4A4A4;"></i> &nbsp;<span class="page1">Add Shops</span></li></a>';
			// menus_script += '<a href="#page21"><li><i class="far fa-chart-bar" style="color:#0B2161;"></i> &nbsp;<span class="page21">Market Statistics</span></li></a>';
			// if(localStorage.getItem('app_admin')==1 && fos_name!='ADMIN')
			// 	menus_script += '<a href="#page26"><li><i class="fa fa-plus" style="color:#0000FF;"></i> &nbsp;<span class="page26">Get Stocks</span></li></a>';
			// menus_script += '<li style="background: floralwhite;" class="reprtLink" style="color:#;"><i class="fa fa-file" style="color:#A9F5E1;"></i> &nbsp;<span>Reports</span>';
			// menus_script += '<i class="fas fa-caret-down downArrow" style="float: right;margin-right: 20px;"></i>';
			// menus_script += '<i class="fas fa-caret-left upArrow" style="float: right;margin-right: 20px;display:none;"></i>';
			// 	menus_script += '<ul class="reportsUlId" style="padding-top: 10px;padding-left: 0px;display:none;">';
			// 		menus_script += '<a href="#page11"><li><i class="fas fa-cubes" style="color:#61210B;"></i> &nbsp;<span class="page11">Stock in Hand</span></li></a>';
			// 		menus_script += '<a href="#"><li><i class="fab fa-product-hunt" style="color:#610B4B;"></i> &nbsp;<span>Product Features</span></li></a>';
			// 		menus_script += '<a href="#page20"><li><i class="fas fa-clipboard-list" style="color:#848484;"></i> &nbsp;<span class="page20">Schemes & Pricelist</span></li></a>';
			// 		menus_script += '<a href="#page7"><li><i class="fas fa-balance-scale" style="color:#B45F04;width: 25px;"></i> &nbsp;<span class="page7">Market Outstandings</span></li></a>';
			// 		menus_script += '<a href="#page8"><li><i class="fas fa-chart-area" style="color:#5882FA;"></i> &nbsp;<span class="page8">Sales Report</span></li></a>';
			// 		menus_script += '<a href="#page10"><li><i class="fas fa-search" style="color:#2E2EFE;"></i> &nbsp;<span class="page10">Sales Review</span></li></a>';
			// 		menus_script += '<a href="#page13"><li><i class="fas fa-flag-checkered" style="color:black;"></i> &nbsp;<span class="page13">Orders Report</span></li></a>';
			// 		menus_script += '<a href="#page19"><li><i class="fas fa-money-bill-alt" style="color:#21610B;"></i> &nbsp;<span class="page19">Payment Collections</span></li></a>';
			// 		menus_script += '<a href="#page12"><li><i class="fas fa-book" style="color:#FF0000;"></i> &nbsp;<span class="page12">Pending Cheques</span></li></a>';
			// 		menus_script += '<a href="#page6"><li><i class="fas fa-road" style="color:#A4A4A4;"></i> &nbsp;<span class="page6">Calculate Distance</span></li></a>';
			// 		menus_script += '<a href="#page23"><li><i class="fas fa-bullseye" style="color:black;"></i> &nbsp;<span class="page23">Value Target</span></li></a>';
				menus_script += '</ul>';
			menus_script += '</li>';
		menus_script += '</ul>';
	}
	else if(un_fos==1)
	{
		var menus_script = '';
		menus_script += '<ul style="padding: 0;font-size: 15px;" class="menu">';
			menus_script += '<a href="#page_home"><li>&nbsp;HOME<i class="fas fa-home" style="float:right;margin-right:20px;color:#1a8cff;"></i></li></a>';
			// menus_script += '<a href="#page2"><li><i class="fa fa-check" style="color:#3ADF00;"></i> &nbsp;<span class="page2">Store Visit</span></li></a>';
			// menus_script += '<a href="#page4"><li><i class="fas fa-rupee-sign" style="color:#0B3B17;"></i> &nbsp;&nbsp;&nbsp;<span class="page4">Get Payments</span></li></a>';
			// menus_script += '<a href="#page9"><li><i class="fa fa fa-truck" style="color:#DF0101;"></i> &nbsp;<span class="page9">Delivery</span></li></a>';
			// menus_script += '<a href="#page14"><li><i class="fa fa fa-tasks" style="color:#FACC2E;"></i> &nbsp;<span class="page14">Today Task</span></li></a>';
			// menus_script += '<li style="background: aliceblue;" class="reprtLink"><i class="fa fa-file" style="color:#A9F5E1;"></i> &nbsp;<span>Reports</span>';
			// menus_script += '<i class="fas fa-caret-down downArrow" style="float: right;margin-right: 20px;"></i>';
			// menus_script += '<i class="fas fa-caret-left upArrow" style="float: right;margin-right: 20px;display:none;"></i>';
			// 	menus_script += '<ul class="reportsUlId" style="padding-top: 10px;padding-left: 12px;display:none;">';
			// 		menus_script += '<a href="#page11"><li><i class="fas fa-cubes" style="color:#61210B;"></i> &nbsp;<span class="page11">Stock in Hand</span></li></a>';
			// 		menus_script += '<a href="#"><li><i class="fab fa-product-hunt" style="color:#610B4B;"></i> &nbsp;<span class="page2">Product Features</span></li></a>';
			// 		menus_script += '<a href="#page20"><li><i class="fas fa-clipboard-list" style="color:#848484;"></i> &nbsp;<span class="page20">Schemes & Price List</span></li></a>';
			// 		menus_script += '<a href="#page6"><li><i class="fas fa-road" style="color:#A4A4A4;"></i> &nbsp;<span class="page6">Calculate Distance</span></li></a>';
			// 	menus_script += '</ul>';
			// menus_script += '</li>';
		menus_script += '</ul>';
	}
	$(pageId).html(menus_script);
});

$(document).on('click','.scrollBottomImg',function()
{
	$( "#mypanel" ).trigger( "updatelayout" );
});
$(document).on('click','#addShops',function(e)
{
	window.location.replace('#page1');
});
$('#page1').on('pageshow',function()
{
	getLatLong();
	$('#addShop')[0].reset();
	if(localStorage.getItem('app_userId'))
	{
		var app_userId = localStorage.getItem('app_userId');
		$('#userId').val(app_userId);
	}
	if($('.recIcon').hasClass("expanded"))
		$('.recIcon').removeClass("expanded");	
	$('#addShop').css('visibility','visible');
	$('.menu_shp').hide();
	getCurrentPosition2();
});


function getCurrentPosition2(){
	navigator.geolocation.getCurrentPosition(
		onSuccess,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					onSuccess, 
					function(error){
							console.log('gps error');
						},
					geoOptionsLow);
			 	return;
				}
				console.log('gps error');	
			}, 
		geoOptionsHigh
	); 
}


$(document).on('keyup','#shopName',function()
{
	if($(this).val()!='')
	{
		var srchShopName = $('#shopName').val();
		var srchStr = srchShopName.includes("&");
		var srchStr1 = srchShopName.includes("#");
		if(srchStr)
			var srchShopName = srchShopName.replace("&","!!");
		if(srchStr1)
			var srchShopName = srchShopName.replace("#","@@");
		var appUsrIdLcl = localStorage.getItem('app_userId');	
		$.getJSON(serviceUrl+'searchShops.php?appUsrIdLcl='+appUsrIdLcl+'&SrchShopTxt='+srchShopName,function(data)
		{
			$('#srchShpList').html('');
			$('#srchShpList').html('<p style="text-align:right;margin: 0;"><img src="images/ic_action.png" id="srchClsP"></p>');
			var jres = data.Result;
			$.each(jres,function(index,objct)
			{
				if(objct.Status=='Success')
					$('#srchShpList').append('<p style="text-align:center;" srchSId="'+objct.id+'" class="srchShpNameP">'+ objct.shopName +'</p>');
				if(objct.Status=='NoRows')
					$('#srchShpList').html('<p style="color:red;text-align:center;font-weight:bold;">:: No Records ::</p>');
			});
			$('#srchShpList').fadeIn('fast');
		});
	}
	else
		$('#srchShpList').fadeOut('fast');
});
$(document).on('click','#srchClsP',function()
{
	$('#srchShpList').fadeOut('fast');
});
$(document).on('click','.srchShpNameP',function()
{
	$('#shopName').val($(this).text());
	$('#srchShpList').fadeOut('fast');
	
});

$(document).on('click','#shop_attendance',function(e)
{
	 timer_entry();
	//window.location.replace('#page2');
});
$(document).on('click','.bckImg',function(e)
{
	if(localStorage.getItem('un_fos'))
	{
		var un_fos = localStorage.getItem('un_fos');
		if(un_fos=='0' || un_fos=='3' || un_fos=='2')
			window.location.replace('#page_home2');
		if(un_fos=='1')
			window.location.replace('#page_home');
	}
});
$(document).on('click','.bckImg2',function(e)
{
	if(localStorage.getItem('un_fos'))
	{
		var un_fos = localStorage.getItem('un_fos');
		if(un_fos=='0' || un_fos=='3' || un_fos=='2')
			window.location.replace('#page_home');
		if(un_fos=='1')
			window.location.replace('#page_home');
	}
});

$(document).on('click','#getOrder',function(e)
{
		attendance_log();
	// window.location.replace('#page3');
});
$(document).on('click','#paymentInvoice',function(e)
{
	window.location.replace('#page4');
});

$('#loginPage').on('pageshow',function()
{
	getCompanyPrfl('login_page');
	if(localStorage.getItem('app_user'))
	{
		var app_user = localStorage.getItem('app_user');
		$.getJSON(serviceUrl+'pswdReset.php?app_user='+app_user,function(data)
		{
			var jres = data.Result;
			if(jres.status=='success')
			{
				window.location.replace('#page_home');
			}
			if(jres.status=='error')
			{
				window.location.replace('#loginPage');
				$('.loginPageLw').html('Please create login account!!');
				$('.loginPageLw').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.loginPageLw').fadeIn('slow');
			}
			if(jres.status=='failed')
			{
				window.location.replace('#loginPage');
				$('.loginPageLw').html('Error occure. Try after some times !!');
				$('.loginPageLw').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.loginPageLw').fadeIn('slow');
			}
		});
	}
});

$('#page_home').on('pageshow',function()
{
	$('.overlayCls').hide();
	$('.menu_lgt').hide();
	var app_admin = localStorage.getItem('app_admin');
	if(app_admin=='1')
	{
		$('#otpDetailsLink').parent('div').show();
	}
	else
		$('#otpDetailsLink').parent('div').fadeOut('fast');
	getLatLong();
	getCompanyPrfl('home_page');
	/* Last Login Date & Time */
	var loginDate = localStorage.getItem('loginDate').split("-");
	loginDate = loginDate[2]+'-'+loginDate[1]+'-'+loginDate[0];
	var loginTime = localStorage.getItem('loginTime');
	var loginTime = format24to12(loginTime);
	var lastLgnDateTime = loginDate+' ('+loginTime+')';
	$('.last_lgnDateTime').html('&nbsp; - &nbsp;<strong>'+lastLgnDateTime+'</strong>');
	/* Last Login End */
	$('#nextPageBtn').parent().css({'background-color':'rgb(49, 136, 203)','color':'white','text-shadow':'none'});
	$('.loginPageLw').fadeOut('slow');
	if(localStorage['app_user'])
	{
		var fos_name = localStorage['fos_name'];
		$('#accountHolder').html('<strong style="color:darkgreen;">'+fos_name+'</strong><span id="designation" style="display:none;"> - </span>');
		if(localStorage.getItem('un_fos'))
		{	
			var un_fos = localStorage.getItem('un_fos');
			var app_admin = localStorage.getItem('app_admin');
			var app_userId = localStorage.getItem('app_userId');
			var app_userId_admin = localStorage.getItem('app_userId_admin');
			
			if(un_fos=='1')
			{
				$('.hideDivCls').parent().hide();
				$('.hideDivCls1').hide();
				$('.shwDivCls').parent().show();
				$('#designation').html('');
				$('#home_fos').hide();
				$('#home_delivery').show();
				home_delivery_Info(app_userId);
			}
			else
			{
				$('#home_delivery').hide();
				$('#home_fos').show();
				if(app_admin=='1')
					$('#switch_userLink').show();
				if(app_admin=='0')
					$('#switch_userLink').hide();
				$('#lgtIcon').show();
				$('.shwDivCls').parent().hide();
				$('.hideDivCls1').show();
				$('.hideDivCls').parent().show();
				var status = 'notification_bounceCheque';
				getBounceCheque(status);
						
				var statusTrgt = 'notification_trgtAch';
				getTrgtAchvmt(statusTrgt);
						
				var status = 'notification_outstnds';
				getOutstndsNtfy(status);
						
				var status = 'notification';
				unvstAndBilling(status);
			}
		}
	}
	else
	{
		$('.loginPageLw').fadeOut('slow');
		$('.lgtDiv img').hide();
	}
});

function getBounceCheque(status)
{
	var app_userId = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl+'modelwiseTarget.php?fosId='+app_userId+'&ChequeBounce_status='+status,function(data)
	{
		console.log(data);
		$('#chequeBounceNtfy').html('');
		var jres = data.result;
		if(jres.length!=0)
		{
			$('#chequeBounceNtfy').html('<p class="homePageHeader">Cheque Bounce Record</p><table id="chequeBndsTbl" width="100%" border="1" style="border-collapse:collapse;border-color:rgb(49, 136, 203);"><tr style="background: bisque;color: rgb(49, 136, 203);text-shadow: none;font-size: 13px;font-weight: bold;"><th style="padding: 2px;">CB Date</th><th>Inv No</th><th>Party</th><th>Amt</th></tr></table>');
			if(jres.status!='failed')
			{
				$.each(jres,function(index,obj)
				{
					if(obj.status=='success')
					{
						var outstanding_date = obj.outstanding_date;
						var datePart = outstanding_date.match(/\d+/g),
						year = datePart[0].substring(2), // get only two digits
						month = datePart[1], day = datePart[2];
						outstanding_date = day+'-'+month; 
						$('#chequeBndsTbl').append('<tr style="font-size: 12px;"><td style="padding:5px">'+outstanding_date+'</td><td>'+obj.ref_no+'</td><td style="word-break: break-all;">'+obj.party_name+'</td><td>'+obj.pending_amount+'</td></tr>');
					}
				});
			}
			else
			{
				$('#chequeBndsTbl').append('<tr><td colspan="5" style="text-align:center">No Bounce Cheques!</td></tr>');
			}
		}
		else
			$('#chequeBounceNtfy').hide();
	});
}

function getOutstndsNtfy(status)
{
	var app_userId = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl+'modelwiseTarget.php?user='+app_userId+'&status='+status,function(data)
	{
		console.log(data);
		var jres = data.result;
		if(jres.length!=0)
		{
			var totalOutstndsNtfy = 0;
			if(status=='notification_outstnds')
				$('#outstndsNtfy').html('<p class="homePageHeader">Outstandings Record</p><table id="outstndsNtfyTbl" width="99.9%" height="auto" border="1" style="border-collapse:collapse;border-color: rgb(49, 136, 203);"><tr style="text-align:center;font-weight:bold;text-shadow:none;background: beige;color: rgb(49, 136, 203);font-size: 14px;"><th>Shops</th><th>Invoice</th><th>Amt</th><th>Inv date</th><th>Days</th></tr></table>');
			if(status=='pageshow')
				$('#outstndsNtfyList').html('<table id="outstndsNtfyListTbl" width="99.9%" height="auto" border="1" style="border-collapse:collapse;border-color: rgb(49, 136, 203);"><tr style="text-align:center;font-weight:bold;text-shadow:none;background: beige;color: rgb(49, 136, 203);font-size: 14px;"><th>Shops</th><th>Invoice</th><th>Amt</th><th>Inv_date</th><th>Days</th></tr></table>');
			$.each(jres,function(index,obj)
			{
				if(obj.status=='success')
				{
					var outstanding_date = obj.outstanding_date;
					var datePart = outstanding_date.match(/\d+/g),
					year = datePart[0].substring(2), // get only two digits
					month = datePart[1], day = datePart[2];
					outstanding_date = day+'-'+month;    
					if(status=='notification_outstnds')
						$('#outstndsNtfyTbl').append('<tr style="font-size: 12px;line-height: 20px;"><td style="text-align:center;word-break: break-all;">'+obj.party_name+'</td><td style="text-align:center;">'+obj.ref_no+'</td><td style="text-align:center;">'+obj.pending_amount+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center;">'+obj.overdue+'</td></tr>');
					if(status=='pageshow')
					{
						totalOutstndsNtfy = parseInt(totalOutstndsNtfy)+parseInt(obj.pending_amount);
						$('#outstndsNtfyListTbl').append('<tr style="font-size: 12px;line-height: 20px;"><td style="text-align:center;word-break: break-all;">'+obj.party_name+'</td><td style="text-align:center;">'+obj.ref_no+'</td><td style="text-align:center;">'+obj.pending_amount+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center;">'+obj.overdue+'</td></tr>');
					}
				}
				else
				{
					if(status=='notification_outstnds')
						$('#outstndsNtfyTbl').html('<tr><td colspan="4" style="color:red;text-align:center;">No Outstandings!</td></tr>');
					if(status=='pageshow')
						$('#outstndsNtfyListTbl').html('<tr><td colspan="4" style="color:red;text-align:center;">No Outstandings!</td></tr>');
				}
			});
			$('.totalOutstndsNtfy').html(totalOutstndsNtfy);
		}
		else
			$('#outstndsNtfy').hide();
	});
}

$(document).on('click','#outstndsNtfy',function()
{
	window.location.replace('#page17');
});

$('#page17').on('pageshow',function()
{
	getLatLong();
	var status = 'pageshow';
	getOutstndsNtfy(status);
});

$(document).on('click','#nextPageBtn',function()
{
	window.location.replace('#page_home2');
});

$(document).on('click','#allReports',function()
{
	$('#allRptsBtn').fadeToggle('slow');
	$('body').animate({
    	scrollTop: $('#homeDiv').get(0).scrollHeight+300
	}, 500);
});

$(document).scroll(function() {
	var scrollHeight = $("body").scrollTop();
    if(scrollHeight==0) {
        $('#allRptsBtn').fadeOut('slow');
    }
});

$(document).on('click','.lgtClsIcon',function()
{
	if($(this).hasClass("expanded"))
	{
		$(this).removeClass("expanded");	
		setPrflData();
		$('.menu_lgt').slideToggle('fast');
	}
	else
	{
		$(this).addClass("expanded");
		setPrflData();
		$('.menu_lgt').slideToggle('fast');
	}
});

function setPrflData()
{
	var fos_name = localStorage.getItem('fos_name');
	var app_mbl = localStorage.getItem('app_user');
	var un_fos = localStorage.getItem('un_fos');
	var app_admin = localStorage.getItem('app_admin');
	if(app_admin=='1')
	{
		$('#switch_userLink').show();
		$('.prflHrLine').hide();	
	}
	else
	{
		$('#switch_userLink').hide();
		$('.prflHrLine').show();
	}
	$('#appUser').html(fos_name+' - '+app_mbl);
}

$('#app_usr').on('keyup',function()
{
	var cnt = $(this).val();
	if(cnt.length==10)
		$('#app_psw').focus();
});

$(document).on('click','.lgt',function(e)
{
	logoutApp();
});

function logoutApp()
{
	if(localStorage.getItem('app_admin_user'))
		localStorage.removeItem('app_admin_user');
	if(localStorage.getItem('app_user'))
		localStorage.removeItem('app_user');
	$('.loginPageLw').fadeOut('slow');
	window.location.replace('#loginPage');
	location.reload();

	$('.lgtDiv img').hide();
	$('.menu_lgt').hide();
	$('#app_usr').val('').css('border','none');
	$('#app_psw').val('').css('border','none');
	if(localStorage.getItem('app_userId_admin'))
		localStorage.removeItem('app_userId_admin');
	if(localStorage.getItem('app_user_admin'))
		localStorage.removeItem('app_user_admin');
	if(localStorage.getItem('fos_name_admin'))
		localStorage.removeItem('fos_name_admin');
}

$(document).on('click','#bckLgt',function()
{
	$('.menu_lgt').slideUp('slow');
});

$(document).on('click','#app_lgn_btn',function(e)
{	
	var app_user = $('#app_usr').val();
	var app_pass = $('#app_psw').val();
	var flag = 1;
	if(app_user == '' || app_pass == '')
	{
		$('#app_usr').css('border','2px solid red');
		$('#app_psw').css('border','2px solid red');
		flag = 0;
	}
	if(flag == 1)
	{	
		$.getJSON(serviceUrl+'app_login.php?app_user='+app_user+'&app_pass='+app_pass+'&version='+version,function(data)
		{
			console.log(data);
			var rs = data.Result;
			if(rs.status=='success')
			{
				window.location.replace('#page_home');
				$('#homeDiv').css('visibility','visible');
				$('.lgtDiv img').show();	
				localStorage['app_user']=app_user;
				localStorage['app_userId']=rs.lgnId;
				localStorage['fos_name']=rs.fos_name;
				localStorage['un_fos']=rs.un_fos;		
				localStorage['loginDate']=rs.loginDate;
				localStorage['loginTime']=rs.loginTime;
				localStorage['app_admin']=rs.app_admin;
					
				localStorage.setItem('app_userId_admin',rs.lgnId);
				localStorage.setItem('app_user_admin',app_user);
				localStorage.setItem('fos_name_admin',rs.fos_name);
				localStorage.setItem('un_fos_admin',rs.un_fos);
				if(rs.AdminId!='NoAmin')
					localStorage.setItem('AdminId',rs.AdminId);
				else
					localStorage.removeItem('AdminId');
			}	
			if(rs.status=='failed')
			{
				$('.loginPageLw').html('Please create login account!!');
				$('.loginPageLw').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.loginPageLw').fadeOut('slow');
				$('.loginPageLw').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
			}
			if(rs.status=='error')
			{	
				$('.loginPageLw').html('Error occure. Try after some times !!');
				$('.loginPageLw').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.loginPageLw').fadeOut('slow');
				$('.loginPageLw').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
			}
			if(rs.status=='expired')
			{	
				$('.loginPageLw').html('This app is expired. Please use latest app!!');
				$('.loginPageLw').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.loginPageLw').fadeOut('slow');
				$('.loginPageLw').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
			}
		});	
	}
});

$(document).on('click','#capImg',function(e)
{
	navigator.camera.getPicture(onPhotoURISuccess, onFail, { quality: 10, destinationType: Camera.DestinationType.DATA_URL });
});

function onPhotoURISuccess(imageData) 
{
	var imgRes = "data:image/jpeg;base64," + imageData;
	$('#capImg').attr('src',imgRes);
	$('#ImgSource').val(imageData);
	$('#closeIcon').css('display','inline-block');
	
}

function onFail(message) {alert(message);}

function onSuccess(position)
{
		var lat  = position.coords.latitude;
		var long = position.coords.longitude;
		$('#slatitude').val(lat);
		$('#slongitude').val(long);
		$('#latP').html(lat);
		$('#longP').html(long);
		var geolocation = lat+','+long;
		
		$.getJSON('http://maps.googleapis.com/maps/api/geocode/json?latlng='+geolocation+'&sensor=true',function(data)
		{
			console.log(data);
		   	var fullAddress = data.results[0].formatted_address;
		   	var addrs = fullAddress.split(',');
			var halfAddrs = addrs.length/2;
			var addrs1Val = '';
			var addrs2Val = ''
			for(var i=0;i<addrs.length;i++)
			{
				if(i<halfAddrs)
					addrs1Val += addrs[i]+',';
				else
					if(i==addrs.length-1)
						addrs2Val += addrs[i]+'.';
					else
						addrs2Val += addrs[i]+',';
			}
			$('#shopAddr1').val(addrs1Val);
			$('#shopAddr2').val(addrs2Val);
			for (var i = 0; i < data.results[0].address_components.length; i++) 
		   	{	
                var types = data.results[0].address_components[i].types;
                for (var typeIdx = 0; typeIdx < types.length; typeIdx++) 
				{
					if (types[typeIdx] == 'political' && types[typeIdx+1] == 'sublocality' && types[typeIdx+2] == 'sublocality_level_1') {
                        var shopArea = data.results[0].address_components[i].short_name;
						$('#shopArea').val(shopArea);
                    }
                    if (types[typeIdx] == 'postal_code') {
                        var zipcode = data.results[0].address_components[i].short_name;
						$('#pincode').val(zipcode);
                    }
                }
            }
        });	
}
function onError(error)
{
	console.log('error gps');
}

	/* Page4 function start */
	
$('#page4').on('pageshow',function()
{
	getLatLong();
	var todayDateFull = new Date();
	todayDateFull.setDate(todayDateFull.getDate() - 30);
	
	var year = todayDateFull.getFullYear();
	var mnth = todayDateFull.getMonth()+1;
	mnth = mnth<10?'0'+mnth:mnth;
	var day  = todayDateFull.getDate();
	day = day<10?'0'+day:day;
	var todayDate = year+'-'+mnth+'-'+day;
	$('#chequeDate').attr('min',todayDate);
	$('#page4ShpSelectLbl').fadeOut('slow');
	$('#page4ShpSelectDiv').fadeIn('slow');
	$('.pymntPage4w').fadeOut('slow');
	$('#getPmntForm')[0].reset();
	$('.cashTypeRadio').prop('disabled',false);
	$('.c_rd').prop('disabled',false);
	$('#pymntTotalDiv').html('0');
	$('#cashType_cash').click();
	$('#addedPAmt').fadeOut('fast');
	$('#getPymnt_btn').parent().css({"background-color":"#3188cb","color":"white","text-shadow":"none"});
	$('#infoRadiosDiv').css('margin-top','0px');
	$('#cashType_cash').attr('checked','checked');
	$('#getPymnt_btn').prop('disabled',true);
	$('#Pymnt_Amnt').prop('disabled',false);
	
	$('#otpDiv').show();
	$('#otpCnfrmDiv').hide();
	
	$('#AddedPymntDiv').hide();
	$('#shpNamePymnt').html('<option value=""><-- : select shop : --></option>');
	var isOff = 'onLine' in navigator && !navigator.onLine;

	if ( isOff ) {
    	onFailedPmnts();
	}
	else {
		getCurrentPosition3();
	}
	if(localStorage.getItem('pymntGroup'))
		localStorage.removeItem('pymntGroup');
	if(localStorage.getItem('OutstandingsArr'))
		localStorage.removeItem('OutstandingsArr');
	if(localStorage.getItem('AddDuplicate'))
		localStorage.removeItem('AddDuplicate')	
	
});

function getCurrentPosition3(){
	navigator.geolocation.getCurrentPosition(
		onSuccessPymnts,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					onSuccessPymnts, 
					function(error){
							console.log('gps error');
						},
					geoOptionsLow);
			 	return;
				}
				console.log('gps error');	
			}, 
		geoOptionsHigh
	); 
}

function onSuccessPymnts(position)
{
	var Shp_lat  = position.coords.latitude;
	var Shp_long = position.coords.longitude;
	$('#pmyntLat').val(Shp_lat);
	$('#pmyntLong').val(Shp_long);
	var appUsrIdLcl = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl +'NearestShop.php?appUsrIdLcl='+appUsrIdLcl+'&lat='+Shp_lat+'&long='+Shp_long,function(data)
	{
		$('#shopNamePymnt').html('<option value=""><-- : select shop : --></option>');
		var jres = data.Result;
		$.each(jres,function(index,obj)
		{
			if(obj.Status=='success')
			{
				$('#shpNamePymnt').append('<option value="'+obj.shopId+'">'+obj.shopName+'</option>');
			}
			if(obj.shopName=='emptySet')
			{
				$('#shpNamePymnt').html('<option value=""><---: Select current shop :---></option><option>No shops found !!</option>');
			}
		});
	});
}
function onFailedPmnts()
{
	window.plugins.toast.showLongCenter(':: No internet connectivity ::');
}
$(document).on('change','#shpNamePymnt',function()
{
	if($(this).val()=='')
		$('#shopNamePymnt').val('');	
	$('#Pymnt_InvoiceNo').val('');
	$('#chequeDate').val('');
	localStorage.removeItem('pymntGroup');
	localStorage.removeItem('OutstandingsArr');
	localStorage.removeItem('OutstandingsArrBackup');
	$('#AddedPymntDiv').fadeOut('slow');
	$('#addedPAmt').fadeOut('slow');
	$('#pymntTotalDiv').html('0');
	$('.cashTypeRadio').prop('disabled',false);
	var shpNamePymnt = $(this).val();
	if(shpNamePymnt!='')
	{
	$('#shopNamePymntView').val('');	
	var userId = localStorage.getItem('app_userId');
	var InfoVal = $('#infoRdBtn').val();
	if(InfoVal!='')
		$('#'+InfoVal).attr('checked',false);
	$('#ownerEmail').val('');
	$('#ownerName').val('');
	$('#otpMblHidd').val('');
	$('#ownrEmail').val('');
	$('#ownrName').val('');
	$('#ownrMbl').val('');
	var shpId = $(this).val();
	var shpTxt = $('#shpNamePymnt option:selected').text();
	$('#page4ShpSelectLbl').html(shpTxt);
	$('#page4ShpSelectDiv').fadeOut('slow');
	$('#page4ShpSelectLbl').fadeIn('slow');
	
	$('#currentShpName').val(shpTxt);
	var srchStr = shpTxt.includes("&");
	var srchStr1 = shpTxt.includes("#");
	if(srchStr)
		var shpTxt = shpTxt.replace("&","!!");
	if(srchStr1)
		var shpTxt = shpTxt.replace("#","@@");
	$('#shopNamePymnt').val(shpId);
	var OutstandingsArr = [];
	getOwnerInfo(shpId);
	$.getJSON(serviceUrl+'searchShops.php?shpTxt='+shpTxt+'&userId='+userId,function(data)
	{
		console.log(data);
		var jres = data.Result;
		localStorage.removeItem('OutstandingsArr');
		$.each(jres,function(index,objt)
		{
			if(objt.Status=='Success')
			{
				var outstanding_date = objt.outstanding_date;
				var pending_amount = objt.pending_amount;
				var ref_no = objt.ref_no;
				var dueDate = objt.dueDate;
				OutstandingsArr.push({'ref_no':ref_no,'outstanding_date':outstanding_date,'pending_amount':pending_amount,'dueDate':dueDate,'overdue':objt.overdue});
				localStorage.setItem('OutstandingsArr',JSON.stringify(OutstandingsArr));
				localStorage.setItem('OutstandingsArrBackup',JSON.stringify(OutstandingsArr));
			}
		});
		if(localStorage.getItem('OutstandingsArr'))
		{
			var OutstandingsArrLcl = JSON.parse(localStorage.getItem('OutstandingsArr'));
			if(OutstandingsArrLcl.length!=0)
			{
				$('#AddedPymntDiv').html('<table id="AddedPymntTbl" width="100%" height="auto"><tr style="text-align: center;background: cornflowerblue;color: antiquewhite;"><td colspan="4" style="padding: 4px;">Total : <span class="ttlOutstnds"></span></td></tr><tr style="font-weight:bold;text-align:left;background: gainsboro;font-size:13px"><th>&nbsp;&nbsp;&nbsp;Invoice No</th><th style="text-align:center;">Date</th><th style="text-align:center;">Due Date</th><th style="text-align:right">Amt&nbsp;</th></tr>');
				var ttlOutstnds = 0;
				$.each(OutstandingsArrLcl,function(index,objt)
				{
					var outstanding_date = objt.outstanding_date;
					var datePart = outstanding_date.match(/\d+/g),
					year = datePart[0].substring(2), // get only two digits
				 	month = datePart[1], day = datePart[2];
					outstanding_date = day+'-'+month+'-'+year;
			
					var pending_amount = objt.pending_amount;
					ttlOutstnds = parseInt(ttlOutstnds)+parseInt(pending_amount);
					var ref_no = objt.ref_no;
					var dueDate = objt.dueDate;
					var indx = index+1;
					$('#AddedPymntTbl').append('<tr><td style="text-align:left">&nbsp;<input type="radio" ref_no="'+ref_no+'" amt="'+objt.pending_amount+'" class="p_rd" id="pyId'+indx+'" name="pChbox" unic="RdB'+index+'" overdue="'+objt.overdue+'" style="width: 15px !important;"><label for="pyId'+indx+'" class="RdB'+index+' cls" style="display:inline-block;font-size: 13px;">'+ ref_no+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center">'+dueDate+'</td><td style="text-align:right;">'+pending_amount+'&nbsp;</td></tr>');
				});
				$('#AddedPymntTbl').append('</table>');
				$('.ttlOutstnds').html(ttlOutstnds);
			}
		}
		else
		{
			$('#AddedPymntTbl').html('<tr style="text-align:center;color:red;font-weight:bold;"><td colspan="3" style="padding: 5px;">No Outstandings!<td></tr>');
		}
		$('#AddedPymntDiv').fadeIn('slow');
	});
	}
	else
	{
		localStorage.removeItem('pymntGroup');
		localStorage.removeItem('OutstandingsArr');
		localStorage.removeItem('OutstandingsArrBackup');
		$('#AddedPymntDiv').fadeOut('slow');
		$('#addedPAmt').fadeOut('slow');
		$('#pymntTotalDiv').html('0');
		$('.cashTypeRadio').prop('disabled',false);
	}
});

$(document).on('change','.p_rd',function()
{
	$('.pymntPage4w').fadeOut('slow');
	var oustngId = $(this).attr('id');
	$('#addPymnt').attr('oustngId',oustngId);
	$('.cashTypeRadio').prop('disabled',false);
	var attrVal = $(this).attr('unic');
	$('.cls').css({'background':'','color':'','padding': ''});
	$('.'+attrVal).css({'background':'cadetblue','color':'white','padding': '0 3px 0 3px'});
	var ref_no = $(this).attr('ref_no');
	var amt = $(this).attr('amt');
	$('#Pymnt_InvoiceNo').val(ref_no);
	$('#Pymnt_overdue').val($(this).attr('overdue'));
	$('#Pymnt_Amnt').val(amt);
	$('#Pymnt_Amt2').val(amt);
	$('#cashType_cash').click();
	$('#chequeNmbr').val('');
	$('#chequeDate').val('');
	if(localStorage.getItem('pymntGroup'))
	{
		var pyGroup = JSON.parse(localStorage.getItem('pymntGroup'));
		if(pyGroup.length!=0)
		{
			$.each(pyGroup,function(index,objj)
			{
				if(objj.inv_no==ref_no && objj.cashType=='cash')
				{
					$('#cashType_cash').prop('disabled',true);
					$('#cashType_cheque').click();
					$('#chequeNmbr').focus();
				}
			});
		}
	}
});
$(document).on('click','#addPymnt',function()
{
	var inv_no = $('#Pymnt_InvoiceNo').val();
	var amt = $('#Pymnt_Amnt').val();
	var test_amt = $('#Pymnt_Amt2').val();
	var pymntGroupArr = [];
	var pymntGroupArr1 = [];
	var cashTypeHidTxt = $('#cashTypeHidTxt').val();
	var oustngid = $(this).attr('oustngid');
	var cash_type = $(this).attr('cash_type');
	var flag = 1;
	if(cashTypeHidTxt=='cheque')
	{
		var chequeNmbr = $('#chequeNmbr').val();
		var chequeDate = $('#chequeDate').val();
		var chequeNoLen = chequeNmbr.length;
		if(chequeNmbr == '' || chequeDate=='')
		{
			alert('Required : Cheque Number & Date');
			flag = 0;
		}
		
		if(chequeNmbr=='000000' || chequeNoLen!=6)
		{
			alert('Invalid Cheque Number!');
			flag = 0;
		}
	}
	if(cashTypeHidTxt=='neft')
	{
		var refNmbr = $('#refNmbr').val();
		var neftDate = $('#neftDate').val();
		if(refNmbr == '' || neftDate=='')
		{
			alert('Required : Ref Number & Date');
			flag = 0;
		}
	}
	if(cashTypeHidTxt=='cn')
	{
		var cnNo = $('#cnNmbr').val();
		if(cnNo == '')
		{
			alert('Required : CN Number');
			flag = 0;
		}
	}
	if(inv_no=='' || parseInt(amt)=='')
	{
		alert('Require : Invoice No & Amount!');
		flag = 0;
	}
	if(parseInt(amt)==0 || amt=='')
	{	
		alert('Please give correct amount!');
		flag = 0;
	}
	if(parseInt(test_amt)<parseInt(amt))
	{
		alert('Amount Invalid!');
		flag = 0;
	}
	if(flag==1)
	{
		if(parseInt(test_amt)>parseInt(amt))
		{
			localStorage.removeItem('AddDuplicate');
			var newOutstndArr = [];
			$('#cash_fullPartHidTxt').val('partial');
			var OutstandingsArr = JSON.parse(localStorage.getItem('OutstandingsArr'));
			if(OutstandingsArr.length!=0)
			{
				$.each(OutstandingsArr,function(index,objt)
				{
					if(objt.ref_no==inv_no)
					{
						if(parseInt(objt.pending_amount)>parseInt(amt))
						{
							var finalAmt = parseInt(objt.pending_amount)-parseInt(amt);
							newOutstndArr.push({'ref_no':objt.ref_no,'outstanding_date':objt.outstanding_date,'pending_amount':finalAmt,'dueDate':objt.dueDate,'overdue':objt.overdue});
						}
					}
					else
						newOutstndArr.push({'ref_no':objt.ref_no,'outstanding_date':objt.outstanding_date,'pending_amount':objt.pending_amount,'dueDate':objt.dueDate,'overdue':objt.overdue});
				});
				localStorage.setItem('AddDuplicate','yes');
				localStorage.setItem("OutstandingsArr",JSON.stringify(newOutstndArr));
				var OutstandingsArrLcl = JSON.parse(localStorage.getItem('OutstandingsArr'));
				if(OutstandingsArrLcl.length!=0)
				{
					$('#AddedPymntDiv').html('<table id="AddedPymntTbl" width="100%" height="auto"><tr style="text-align: center;background: cornflowerblue;color: antiquewhite;"><td colspan="4" style="padding: 4px;">Total : <span class="ttlOutstnds"></span></td></tr><tr style="font-weight:bold;text-align:left;background: gainsboro;font-size:13px;"><th>&nbsp;&nbsp;&nbsp;Invoice No</th><th style="text-align:center;">Date</th><th style="text-align:center;">Due Date</th><th style="text-align:right">Amt&nbsp;</th></tr>');
					var ttlOutstnds = 0;
					$.each(OutstandingsArrLcl,function(index,objt)
					{
						var outstanding_date = objt.outstanding_date;
						var datePart = outstanding_date.match(/\d+/g),
						year = datePart[0].substring(2), // get only two digits
						month = datePart[1], day = datePart[2];
						outstanding_date = day+'-'+month+'-'+year;		
						
						var pending_amount = objt.pending_amount;
						ttlOutstnds = parseInt(ttlOutstnds)+parseInt(pending_amount);
						var ref_no = objt.ref_no;
						var dueDate = objt.dueDate;
						var indx = index+1;
						
						if(pending_amount==0)	
						{
							$('#AddedPymntTbl').append('<tr><td style="text-align:left">&nbsp;<input type="radio" disabled ref_no="'+ref_no+'" amt="'+objt.pending_amount+'" class="p_rd" id="pyId'+indx+'" name="pChbox" unic="RdB'+index+'" overdue="'+objt.overdue+'" style="width: 15px !important;"><label for="pyId'+indx+'" class="RdB'+index+' cls" style="display:inline-block;font-size: 13px;">'+ ref_no+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center;">'+dueDate+'</td><td style="text-align:right;">'+pending_amount+'&nbsp;</td></tr>');
						}
						else
						{
							$('#AddedPymntTbl').append('<tr><td style="text-align:left">&nbsp;<input type="radio" ref_no="'+ref_no+'" amt="'+objt.pending_amount+'" class="p_rd" id="pyId'+indx+'" name="pChbox" unic="RdB'+index+'" overdue="'+objt.overdue+'" style="width: 15px !important;"><label for="pyId'+indx+'" class="RdB'+index+' cls" style="display:inline-block;font-size: 13px;">'+ ref_no+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center;">'+dueDate+'</td><td style="text-align:right;">'+pending_amount+'&nbsp;</td></tr>');
						}
					});
					$('#AddedPymntTbl').append('</table>');
					$('.ttlOutstnds').html(ttlOutstnds);
				}
				else
				{
					$('#AddedPymntTbl').html('<tr style="text-align:center;color:red;font-weight:bold;"><td colspan="3" style="padding: 5px;">No Outstandings!<td></tr>');
				}
				$('#AddedPymntDiv').fadeIn('slow');		
			}
		}
		if(parseInt(test_amt)==parseInt(amt))
		{
			var newOutstndArr = [];
			$('#cash_fullPartHidTxt').val('fullPayment');
			var samePymntAddValArr = [];
			var cashFullPartHidTxt = $('#cash_fullPartHidTxt').val();
			if(localStorage.getItem('pymntGroup'))
			{
				var pyGroup = JSON.parse(localStorage.getItem('pymntGroup'));
				if(pyGroup.length!=0)
				{
					$.each(pyGroup,function(index,objj)
					{
						if(objj.inv_no==inv_no && objj.cashType==cashTypeHidTxt && objj.pymntType!=cashFullPartHidTxt)
						{
							var finalAmt = parseInt(objj.amt)+parseInt(amt);
							if(objj.cashType=='cash')
								samePymntAddValArr.push({'inv_no':objj.inv_no,'amt':objj.amt,'cashType':objj.cashType,'pymntType':objj.pymntType,'originalAmt':objj.originalAmt});
							if(objj.cashType=='cheque')
								samePymntAddValArr.push({'inv_no':objj.inv_no,'amt':objj.amt,'chequeNo':objj.chequeNo,'chequeDate':objj.chequeDate,'cashType':objj.cashType,'pymntType':objj.pymntType,'originalAmt':objj.originalAmt});
							if(objj.cashType=='neft')
								samePymntAddValArr.push({'inv_no':objj.inv_no,'amt':objj.amt,'refNo':objj.refNo,'neftDate':objj.neftDate,'cashType':objj.cashType,'pymntType':objj.pymntType,'originalAmt':objj.originalAmt});
							if(objj.cashType=='cn')
								samePymntAddValArr.push({'inv_no':objj.inv_no,'amt':objj.amt,'cnNo':objj.cnNo,'cashType':objj.cashType,'pymntType':objj.pymntType,'originalAmt':objj.originalAmt});		
						}
						else
						{
							if(objj.cashType=='cash')
								samePymntAddValArr.push({'inv_no':objj.inv_no,'amt':objj.amt,'cashType':objj.cashType,'pymntType':objj.pymntType,'originalAmt':objj.originalAmt});
							if(objj.cashType=='cheque')
								samePymntAddValArr.push({'inv_no':objj.inv_no,'amt':objj.amt,'chequeNo':objj.chequeNo,'chequeDate':objj.chequeDate,'cashType':objj.cashType,'pymntType':objj.pymntType,'originalAmt':objj.originalAmt});
							if(objj.cashType=='neft')
								samePymntAddValArr.push({'inv_no':objj.inv_no,'amt':objj.amt,'refNo':objj.refNo,'neftDate':objj.neftDate,'cashType':objj.cashType,'pymntType':objj.pymntType,'originalAmt':objj.originalAmt});
							if(objj.cashType=='cn')
								samePymntAddValArr.push({'inv_no':objj.inv_no,'amt':objj.amt,'cnNo':objj.cnNo,'cashType':objj.cashType,'pymntType':objj.pymntType,'originalAmt':objj.originalAmt});
						}
					});
					localStorage.setItem("pymntGroup",JSON.stringify(samePymntAddValArr));
					var getFinal = JSON.parse(localStorage.getItem('pymntGroup'));
					
					$('#addedPAmt').html('<p style="text-align:center;"><button type="button" class="clearPymtLocal" style="padding:5px 10px;">Clear</button></p>');
					$.each(getFinal,function(index,objj)
					{	
						var pymntType = objj.pymntType;
						if(pymntType=='partial')
							pymntType = 'PARTIAL PAYMENT';
						if(pymntType=='fullPayment')
							pymntType = 'FULL PAYMENT';
						if(objj.cashType=='cash')
						{
							$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel'+index+'" pydelunic="'+index+'" inv_no="'+objj.inv_no+'" amt="'+objj.amt+'">'+objj.inv_no+'</span></th><th style="font-weight:normal;">'+objj.originalAmt+'</th></tr><tr><td style="text-align:center;">CASH RECEIVED</td><td style="text-align:center;font-weight:bold;">'+objj.amt+'</td></tr><tr><td colspan="2"  style="text-align:center;background: antiquewhite;">'+pymntType+'</td></tr></table>');
						}
						if(objj.cashType=='cheque')
						{
							$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;" colspan="2"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel'+index+'" pydelunic="'+index+'" inv_no="'+objj.inv_no+'" amt="'+objj.amt+'">'+objj.inv_no+'</span></th><th style="font-weight:normal;">'+objj.originalAmt+'</th></tr><tr><td style="text-align:center;">CHEQUE RECEIVED</td><td style="text-align:center;font-weight:bold;">'+objj.amt+'</td><td style="text-align:center;">'+objj.chequeDate+'</td></tr><tr><td colspan="3"  style="text-align:center;background: antiquewhite;">'+pymntType+'</td></tr></table>');
						}
						if(objj.cashType=='neft')
						{
							$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;" colspan="2"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel'+index+'" pydelunic="'+index+'" inv_no="'+objj.inv_no+'" amt="'+objj.amt+'">'+objj.inv_no+'</span></th><th style="font-weight:normal;">'+objj.originalAmt+'</th></tr><tr><td style="text-align:center;">NEFT RECEIVED</td><td style="text-align:center;font-weight:bold;">'+objj.amt+'</td><td style="text-align:center;">'+objj.neftDate+'</td></tr><tr><td colspan="3"  style="text-align:center;background: antiquewhite;">'+pymntType+'</td></tr></table>');
						}
						if(objj.cashType=='cn')
						{
							$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;" colspan="2"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel'+index+'" pydelunic="'+index+'" inv_no="'+objj.inv_no+'" amt="'+objj.amt+'">'+objj.inv_no+'</span></th><th style="font-weight:normal;">'+objj.originalAmt+'</th></tr><tr><td style="text-align:center;">CN RECEIVED</td><td style="text-align:center;font-weight:bold;">'+objj.amt+'</td><td style="text-align:center;">'+objj.cnNo+'</td></tr><tr><td colspan="3"  style="text-align:center;background: antiquewhite;">'+pymntType+'</td></tr></table>');
						}
					});
					$('#addedPAmt').fadeIn('slow');
					$('.c_rd').prop('disabled',false);
				}
			}
			/* Outstandings value reduce code */
			
			var OutstandingsArr = JSON.parse(localStorage.getItem('OutstandingsArr'));
			if(OutstandingsArr.length!=0)
			{
				$.each(OutstandingsArr,function(index,objt)
				{
					if(objt.ref_no==inv_no)
					{
						if(parseInt(objt.pending_amount)==parseInt(amt))
						{
							var finalAmt = parseInt(objt.pending_amount)-parseInt(amt);
							newOutstndArr.push({'ref_no':objt.ref_no,'outstanding_date':objt.outstanding_date,'pending_amount':finalAmt,'dueDate':objt.dueDate,'overdue':objt.overdue});
						}
					}
					else
						newOutstndArr.push({'ref_no':objt.ref_no,'outstanding_date':objt.outstanding_date,'pending_amount':objt.pending_amount,'dueDate':objt.dueDate,'overdue':objt.overdue});
				});
				localStorage.setItem("OutstandingsArr",JSON.stringify(newOutstndArr));
				var OutstandingsArrLcl = JSON.parse(localStorage.getItem('OutstandingsArr'));
				if(OutstandingsArrLcl.length!=0)
				{
					$('#AddedPymntDiv').html('<table id="AddedPymntTbl" width="100%" height="auto"><tr style="text-align: center;background: cornflowerblue;color: antiquewhite;"><td colspan="4" style="padding: 4px;">Total : <span class="ttlOutstnds"></span></td></tr><tr style="font-weight:bold;text-align:left;background: gainsboro;font-size:13px;"><th>&nbsp;&nbsp;&nbsp;Invoice No</th><th style="text-align:center;">Date</th><th style="text-align:center;">Due Date</th><th style="text-align:right">Amt&nbsp;</th></tr>');
					var ttlOutstnds = 0;
					$.each(OutstandingsArrLcl,function(index,objt)
					{
						var outstanding_date = objt.outstanding_date;
						var datePart = outstanding_date.match(/\d+/g),
						year = datePart[0].substring(2), // get only two digits
						month = datePart[1], day = datePart[2];
						outstanding_date = day+'-'+month+'-'+year;
						
						var pending_amount = objt.pending_amount;
						ttlOutstnds = parseInt(ttlOutstnds)+parseInt(pending_amount);
						var ref_no = objt.ref_no;
						var dueDate = objt.dueDate;
						var indx = index+1;
							
						if(pending_amount==0)
						{
							$('#AddedPymntTbl').append('<tr><td style="text-align:left">&nbsp;<input type="radio" disabled ref_no="'+ref_no+'" amt="'+objt.pending_amount+'" class="p_rd" id="pyId'+indx+'" name="pChbox" unic="RdB'+index+'" overdue="'+objt.overdue+'" style="width: 15px !important;"><label for="pyId'+indx+'" class="RdB'+index+' cls" style="display:inline-block;font-size: 13px;">'+ ref_no+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center;">'+dueDate+'</td><td style="text-align:right;">'+pending_amount+'&nbsp;</td></tr>');
						}
						else
						{
							$('#AddedPymntTbl').append('<tr><td style="text-align:left">&nbsp;<input type="radio" ref_no="'+ref_no+'" amt="'+objt.pending_amount+'" class="p_rd" id="pyId'+indx+'" name="pChbox" unic="RdB'+index+'" overdue="'+objt.overdue+'" style="width: 15px !important;"><label for="pyId'+indx+'" class="RdB'+index+' cls" style="display:inline-block;font-size: 13px;">'+ ref_no+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center;">'+dueDate+'</td><td style="text-align:right;">'+pending_amount+'&nbsp;</td></tr>');
						}
					});
					$('#AddedPymntTbl').append('</table>');
					$('.ttlOutstnds').html(ttlOutstnds);
				}
				else
				{
					$('#AddedPymntTbl').html('<tr style="text-align:center;color:red;font-weight:bold;"><td colspan="3" style="padding: 5px;">No Outstandings!<td></tr>');
				}
				$('#AddedPymntDiv').fadeIn('slow');		
			}
			
			/* Outstandings value reduce end */
			
			$('#'+oustngid).prop('disabled',true);
			$('.cashTypeRadio').prop('disabled',true);
		}
		var cashFullPartHidTxt = $('#cash_fullPartHidTxt').val();
		if(cashTypeHidTxt=='cash')
			pymntGroupArr.push({'inv_no':inv_no,'amt':amt,'cashType':cashTypeHidTxt,'pymntType':cashFullPartHidTxt,'originalAmt':test_amt});
		if(cashTypeHidTxt=='cheque')
			pymntGroupArr.push({'inv_no':inv_no,'amt':amt,'chequeNo':chequeNmbr,'chequeDate':chequeDate,'cashType':cashTypeHidTxt,'pymntType':cashFullPartHidTxt,'originalAmt':test_amt});
		if(cashTypeHidTxt=='neft')
			pymntGroupArr.push({'inv_no':inv_no,'amt':amt,'refNo':refNmbr,'neftDate':neftDate,'cashType':cashTypeHidTxt,'pymntType':cashFullPartHidTxt,'originalAmt':test_amt});	
		if(cashTypeHidTxt=='cn')
			pymntGroupArr.push({'inv_no':inv_no,'amt':amt,'cnNo':cnNo,'cashType':cashTypeHidTxt,'pymntType':cashFullPartHidTxt,'originalAmt':test_amt});
			
		if(localStorage.getItem('pymntGroup'))
		{
			var getRes = JSON.parse(localStorage.getItem('pymntGroup'));
			if(getRes.length!=0)
			{
				localStorage.setItem('inv_no','no');
				$.each(getRes,function(index,objj)
				{
					if(inv_no==objj.inv_no)
					{
						if(localStorage.getItem('AddDuplicate'))
							localStorage.setItem('inv_no','no');
						else
							localStorage.setItem('inv_no','yes');
					}
				});
				if(localStorage.getItem('inv_no')=='no')
				{
						$.each(getRes,function(index,obj)
						{
							if(obj.cashType=='cash')	
								pymntGroupArr.push({'inv_no':obj.inv_no,'amt':obj.amt,'cashType':obj.cashType,'pymntType':obj.pymntType,'originalAmt':obj.originalAmt});
							if(obj.cashType=='cheque')
								pymntGroupArr.push({'inv_no':obj.inv_no,'amt':obj.amt,'chequeNo':obj.chequeNo,'chequeDate':obj.chequeDate,'cashType':obj.cashType,'pymntType':obj.pymntType,'originalAmt':obj.originalAmt});
							if(obj.cashType=='neft')
								pymntGroupArr.push({'inv_no':obj.inv_no,'amt':obj.amt,'refNo':obj.refNo,'neftDate':obj.neftDate,'cashType':obj.cashType,'pymntType':obj.pymntType,'originalAmt':obj.originalAmt});
							if(obj.cashType=='cn')
								pymntGroupArr.push({'inv_no':obj.inv_no,'amt':obj.amt,'cnNo':obj.cnNo,'cashType':obj.cashType,'pymntType':obj.pymntType,'originalAmt':obj.originalAmt});
						});
						localStorage.setItem("pymntGroup",JSON.stringify(pymntGroupArr))
				}
				var getFinal = JSON.parse(localStorage.getItem('pymntGroup'));
				$('#addedPAmt').html('<p style="text-align:center;"><button type="button" class="clearPymtLocal" style="padding:5px 10px;">Clear</button></p>');
				$.each(getFinal,function(index,objj)
				{	
					var pymntType = objj.pymntType;
					if(pymntType=='partial')
						pymntType = 'PARTIAL PAYMENT';
					if(pymntType=='fullPayment')
						pymntType = 'FULL PAYMENT';
					if(objj.cashType=='cash')
					{
						$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel'+index+'" pydelunic="'+index+'" inv_no="'+objj.inv_no+'" amt="'+objj.amt+'">'+objj.inv_no+'</span></th><th style="font-weight:normal;">'+objj.originalAmt+'</th></tr><tr><td style="text-align:center;">CASH RECEIVED</td><td style="text-align:center;font-weight:bold;">'+objj.amt+'</td></tr><tr><td colspan="2"  style="text-align:center;background: antiquewhite;">'+pymntType+'</td></tr></table>');
					}
					if(objj.cashType=='cheque')
					{
						$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;" colspan="2"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel'+index+'" pydelunic="'+index+'" inv_no="'+objj.inv_no+'" amt="'+objj.amt+'">'+objj.inv_no+'</span></th><th style="font-weight:normal;">'+objj.originalAmt+'</th></tr><tr><td style="text-align:center;">CHEQUE RECEIVED</td><td style="text-align:center;font-weight:bold;">'+objj.amt+'</td><td style="text-align:center;">'+objj.chequeDate+'</td></tr><tr><td colspan="3"  style="text-align:center;background: antiquewhite;">'+pymntType+'</td></tr></table>');
					}
					if(objj.cashType=='neft')
					{
						$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;" colspan="2"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel'+index+'" pydelunic="'+index+'" inv_no="'+objj.inv_no+'" amt="'+objj.amt+'">'+objj.inv_no+'</span></th><th style="font-weight:normal;">'+objj.originalAmt+'</th></tr><tr><td style="text-align:center;">NEFT RECEIVED</td><td style="text-align:center;font-weight:bold;">'+objj.amt+'</td><td style="text-align:center;">'+objj.neftDate+'</td></tr><tr><td colspan="3"  style="text-align:center;background: antiquewhite;">'+pymntType+'</td></tr></table>');
					}
					if(objj.cashType=='cn')
					{
						$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;" colspan="2"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel'+index+'" pydelunic="'+index+'" inv_no="'+objj.inv_no+'" amt="'+objj.amt+'">'+objj.inv_no+'</span></th><th style="font-weight:normal;">'+objj.originalAmt+'</th></tr><tr><td style="text-align:center;">CN RECEIVED</td><td style="text-align:center;font-weight:bold;">'+objj.amt+'</td><td style="text-align:center;">'+objj.cnNo+'</td></tr><tr><td colspan="3"  style="text-align:center;background: antiquewhite;">'+pymntType+'</td></tr></table>');
					}
				});
				$('#addedPAmt').fadeIn('slow');	
				$('.c_rd').prop('disabled',false);				
			}
		}
		else
		{
			localStorage.setItem("pymntGroup",JSON.stringify(pymntGroupArr))
			$('#addedPAmt').html('<p style="text-align:center;"><button type="button" class="clearPymtLocal" style="padding:5px 10px;">Clear</button></p>');
			var pymntType = cashFullPartHidTxt;
			if(pymntType=='partial')
				cashFullPartHidTxt = 'PARTIAL PAYMENT';
			if(pymntType=='fullPayment')
				cashFullPartHidTxt = 'FULL PAYMENT';
			if(cashTypeHidTxt=='cash')
			{
				$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel0" pydelunic="0" inv_no="'+inv_no+'" amt="'+amt+'">'+inv_no+'</span></th><th style="font-weight:normal;">'+test_amt+'</th></tr><tr><td style="text-align:center;">CASH RECEIVED</td><td style="text-align:center;font-weight:bold;">'+amt+'</td></tr><tr><td colspan="2"  style="text-align:center;background: antiquewhite;">'+cashFullPartHidTxt+'</td></tr></table>');
			}
			if(cashTypeHidTxt=='cheque')
			{
				$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;" colspan="2"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel0" pydelunic="0" inv_no="'+inv_no+'" amt="'+amt+'">'+inv_no+'</span></th><th style="font-weight:normal;">'+test_amt+'</th></tr><tr><td style="text-align:center;">CHEQUE RECEIVED</td><td style="text-align:center;font-weight:bold;">'+amt+'</td><td style="text-align:center;">'+chequeDate+'</td></tr><tr><td colspan="3"  style="text-align:center;background: antiquewhite;">'+cashFullPartHidTxt+'</td></tr></table>');
			}
			if(cashTypeHidTxt=='neft')
			{
				$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;" colspan="2"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel0" pydelunic="0" inv_no="'+inv_no+'" amt="'+amt+'">'+inv_no+'</span></th><th style="font-weight:normal;">'+test_amt+'</th></tr><tr><td style="text-align:center;">NEFT RECEIVED</td><td style="text-align:center;font-weight:bold;">'+amt+'</td><td style="text-align:center;">'+neftDate+'</td></tr><tr><td colspan="3"  style="text-align:center;background: antiquewhite;">'+cashFullPartHidTxt+'</td></tr></table>');
			}
			if(cashTypeHidTxt=='cn')
			{
				$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;" colspan="2"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel0" pydelunic="0" inv_no="'+inv_no+'" amt="'+amt+'">'+inv_no+'</span></th><th style="font-weight:normal;">'+test_amt+'</th></tr><tr><td style="text-align:center;">CN RECEIVED</td><td style="text-align:center;font-weight:bold;">'+amt+'</td><td style="text-align:center;">'+cnNo+'</td></tr><tr><td colspan="3"  style="text-align:center;background: antiquewhite;">'+cashFullPartHidTxt+'</td></tr></table>');
			}
			$('#addedPAmt').fadeIn('slow');
			$('.c_rd').prop('disabled',false);
		}
		if(localStorage.getItem('pymntGroup'))
		{
			var pymntGroup = JSON.parse(localStorage.getItem('pymntGroup'));
			var totalPymnt = 0;
			$.each(pymntGroup,function(index,object)
			{
				totalPymnt = parseInt(totalPymnt)+parseInt(object.amt);
			});
			$('#pymntTotalDiv').html(totalPymnt);
			$('#pymntTotal').val(totalPymnt);
		}
	$('#Pymnt_InvoiceNo').val('');
	$('#Pymnt_Amnt').val('');
	$('#Pymnt_Amt2').val('');
	}
});

$(document).on('click','.clearPymtLocal',function()
{
	localStorage.removeItem('pymntGroup');
	$('#addedPAmt').html('<p style="color:red;text-align:center;">No Invoices!</p>');
	$('#pymntTotalDiv').html(0);
	var OutstandingsArr = [];
	var userId = localStorage.getItem('app_userId');
	var shpTxt = $('#currentShpName').val();
	var srchStr = shpTxt.includes("&");
	var srchStr1 = shpTxt.includes("#");
	if(srchStr)
		var shpTxt = shpTxt.replace("&","!!");
	if(srchStr1)
		var shpTxt = shpTxt.replace("#","@@");
	$.getJSON(serviceUrl+'searchShops.php?shpTxt='+shpTxt+'&userId='+userId,function(data)
	{
		var jres = data.Result;
		localStorage.removeItem('OutstandingsArr');
		$.each(jres,function(index,objt)
		{
			if(objt.Status=='Success')
			{
				var outstanding_date = objt.outstanding_date;
				var pending_amount = objt.pending_amount;
				var ref_no = objt.ref_no;
				var dueDate = objt.dueDate
				OutstandingsArr.push({'ref_no':ref_no,'outstanding_date':outstanding_date,'pending_amount':pending_amount,'dueDate':dueDate,'overdue':objt.overdue});
				localStorage.setItem('OutstandingsArr',JSON.stringify(OutstandingsArr));
				localStorage.setItem('OutstandingsArrBackup',JSON.stringify(OutstandingsArr));
			}
		});
		if(localStorage.getItem('OutstandingsArr'))
		{
			var OutstandingsArrLcl = JSON.parse(localStorage.getItem('OutstandingsArr'));
			if(OutstandingsArrLcl.length!=0)
			{
				$('#AddedPymntDiv').html('<table id="AddedPymntTbl" width="100%" height="auto"><tr style="text-align: center;background: cornflowerblue;color: antiquewhite;"><td colspan="4" style="padding: 4px;">Total : <span class="ttlOutstnds"></span></td></tr><tr style="font-weight:bold;text-align:left;background: gainsboro;font-size:13px;"><th>&nbsp;&nbsp;&nbsp;Invoice No</th><th style="text-align:center;">Date</th><th style="text-align:center;">Due Date</th><th style="text-align:right">Amt&nbsp;</th></tr>');
				var ttlOutstnds = 0;
				$.each(OutstandingsArrLcl,function(index,objt)
				{
					var outstanding_date = objt.outstanding_date;
					var datePart = outstanding_date.match(/\d+/g),
				  	year = datePart[0].substring(2), // get only two digits
				 	month = datePart[1], day = datePart[2];
					outstanding_date = day+'-'+month+'-'+year;
							
					var pending_amount = objt.pending_amount;
					ttlOutstnds = parseInt(ttlOutstnds)+parseInt(pending_amount);
					var ref_no = objt.ref_no;
					var dueDate = objt.dueDate;
					var indx = index+1;
					if(pending_amount==0)
					{
						$('#AddedPymntTbl').append('<tr><td style="text-align:left">&nbsp;<input type="radio" disabled ref_no="'+ref_no+'" amt="'+objt.pending_amount+'" class="p_rd" id="pyId'+indx+'" name="pChbox" unic="RdB'+index+'" overdue="'+objt.overdue+'" style="width: 15px !important;"><label for="pyId'+indx+'" class="RdB'+index+' cls" style="display:inline-block;font-size: 13px;">'+ ref_no+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center;">'+dueDate+'</td><td style="text-align:right;">'+pending_amount+'&nbsp;</td></tr>');
					}
					else
					{
						$('#AddedPymntTbl').append('<tr><td style="text-align:left">&nbsp;<input type="radio" ref_no="'+ref_no+'" amt="'+objt.pending_amount+'" class="p_rd" id="pyId'+indx+'" name="pChbox" unic="RdB'+index+'" overdue="'+objt.overdue+'" style="width: 15px !important;"><label for="pyId'+indx+'" class="RdB'+index+' cls" style="display:inline-block;font-size: 13px;">'+ ref_no+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center;">'+dueDate+'</td><td style="text-align:right;">'+pending_amount+'&nbsp;</td></tr>');
					}
				});
				$('#AddedPymntTbl').append('</table>');
				$('.ttlOutstnds').html(ttlOutstnds);
			}
			else
			{
				$('#AddedPymntTbl').html('<tr style="text-align:center;color:red;font-weight:bold;"><td colspan="3" style="padding: 5px;">No Outstandings!<td></tr>');
			}
			$('#AddedPymntDiv').fadeIn('slow');
		}
		else
		{
			$('#AddedPymntDiv').html('<p style="color:red;font-weight:bold;text-align:center;">No Outstandings!<p>');
		}
		$('#AddedPymntDiv').fadeIn('slow');
	});
});

$(document).on('click','.pymntDelCls',function()
{
	var pymntArr = [];
	var prdctDelId = $(this).attr('pydelunic');
	var inv_no = $(this).attr('inv_no');
	var amt = $(this).attr('amt');
	if(localStorage.getItem('pymntGroup'))
	{
		var pymntFinal = JSON.parse(localStorage.getItem('pymntGroup'));
		if(pymntFinal.length==1)
		{
			localStorage.removeItem('pymntGroup');
			$('#viewInvoiceBtn').click();
			$('#viewInvoiceBtn').click();
			$('#pymntTotalDiv').html(0);
			deleteAddedInvoices(inv_no,amt);
		}
		if(pymntFinal.length!=1)
		{
			pymntFinal.splice(prdctDelId,1);
			$.each(pymntFinal,function(index,obh)
			{
				if(inv_no==obh.inv_no)
					var pymntType = 'partial';
				else
					var pymntType = obh.pymntType;
					
					
				if(obh.cashType=='cash')
					pymntArr.push({'inv_no':obh.inv_no,'amt':obh.amt,'cashType':obh.cashType,'pymntType':pymntType,'originalAmt':obh.originalAmt});
				if(obh.cashType=='cheque')
					pymntArr.push({'inv_no':obh.inv_no,'amt':obh.amt,'chequeNo':obh.chequeNo,'chequeDate':obh.chequeDate,'cashType':obh.cashType,'pymntType':pymntType,'originalAmt':obh.originalAmt});
				if(obh.cashType=='neft')
					pymntArr.push({'inv_no':obh.inv_no,'amt':obh.amt,'refNo':obh.refNo,'neftDate':obh.neftDate,'cashType':obh.cashType,'pymntType':pymntType,'originalAmt':obh.originalAmt});
				if(obh.cashType=='cn')
					pymntArr.push({'inv_no':obh.inv_no,'amt':obh.amt,'cnNo':obh.cnNo,'cashType':obh.cashType,'pymntType':pymntType,'originalAmt':obh.originalAmt});
			});
			localStorage.setItem('pymntGroup',JSON.stringify(pymntArr));
			$('#viewInvoiceBtn').click();
			$('#viewInvoiceBtn').click();
			if(localStorage.getItem('pymntGroup'))
			{
				var pymntGroup = JSON.parse(localStorage.getItem('pymntGroup'));
				var totalPymnt = 0;
				$.each(pymntGroup,function(index,object)
				{
					totalPymnt = parseInt(totalPymnt)+parseInt(object.amt);
				});
				$('#pymntTotalDiv').html(totalPymnt);
				$('#pymntTotal').val(totalPymnt);
			}
			deleteAddedInvoices(inv_no,amt);
		}
	}
});

function deleteAddedInvoices(inv_no,amt)
{
	var newOutstndArr = [];
	var inv_no = inv_no;
	var amt = amt;
	if(localStorage.getItem('OutstandingsArr'))
			{
				var OutstandingsArr = JSON.parse(localStorage.getItem('OutstandingsArr'));
				if(OutstandingsArr.length!=0)
				{
					$.each(OutstandingsArr,function(index,objt)
					{
						if(objt.ref_no==inv_no)
						{
							var finalAmt = parseInt(objt.pending_amount)+parseInt(amt);
							newOutstndArr.push({'ref_no':objt.ref_no,'outstanding_date':objt.outstanding_date,'pending_amount':finalAmt,'dueDate':objt.dueDate,'overdue':objt.overdue});
						}
						else
							newOutstndArr.push({'ref_no':objt.ref_no,'outstanding_date':objt.outstanding_date,'pending_amount':objt.pending_amount,'dueDate':objt.dueDate,'overdue':objt.overdue});
					});
					localStorage.setItem("OutstandingsArr",JSON.stringify(newOutstndArr));
					var OutstandingsArrLcl = JSON.parse(localStorage.getItem('OutstandingsArr'));
					if(OutstandingsArrLcl.length!=0)
					{
						$('#AddedPymntDiv').html('<table id="AddedPymntTbl" width="100%" height="auto"><tr style="text-align: center;background: cornflowerblue;color: antiquewhite;"><td colspan="4" style="padding: 4px;">Total : <span class="ttlOutstnds"></span></td></tr><tr style="font-weight:bold;text-align:left;background: gainsboro;font-size:13px;"><th>&nbsp;&nbsp;&nbsp;Invoice No</th><th style="text-align:center;">Date</th><th style="text-align:center;">Due Date</th><th style="text-align:right">Amt&nbsp;</th></tr>');
						var ttlOutstnds = 0;
						$.each(OutstandingsArrLcl,function(index,objt)
						{
							var outstanding_date = objt.outstanding_date;
							var datePart = outstanding_date.match(/\d+/g),
						  	year = datePart[0].substring(2), // get only two digits
						 	month = datePart[1], day = datePart[2];
							outstanding_date = day+'-'+month+'-'+year;
							
							var pending_amount = objt.pending_amount;
							ttlOutstnds = parseInt(ttlOutstnds)+parseInt(pending_amount);
							var ref_no = objt.ref_no;
							var dueDate = objt.dueDate;
							var indx = index+1;
							if(pending_amount==0)
							{	
								$('#AddedPymntTbl').append('<tr><td style="text-align:left">&nbsp;<input type="radio" disabled ref_no="'+ref_no+'" amt="'+objt.pending_amount+'" class="p_rd" id="pyId'+indx+'" name="pChbox" unic="RdB'+index+'" overdue="'+objt.overdue+'" style="width: 15px !important;"><label for="pyId'+indx+'" class="RdB'+index+' cls" style="display:inline-block;font-size: 13px;">'+ ref_no+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center;">'+dueDate+'</td><td style="text-align:right;">'+pending_amount+'&nbsp;</td></tr>');
							}
							else
							{
								$('#AddedPymntTbl').append('<tr><td style="text-align:left">&nbsp;<input type="radio" ref_no="'+ref_no+'" amt="'+objt.pending_amount+'" class="p_rd" id="pyId'+indx+'" name="pChbox" unic="RdB'+index+'" overdue="'+objt.overdue+'" style="width: 15px !important;"><label for="pyId'+indx+'" class="RdB'+index+' cls" style="display:inline-block;font-size: 13px;">'+ ref_no+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center;">'+dueDate+'</td><td style="text-align:right;">'+pending_amount+'&nbsp;</td></tr>');
							}
						});
						$('#AddedPymntTbl').append('</table>');
						$('.ttlOutstnds').html(ttlOutstnds);
					}
					else
					{
						$('#AddedPymntTbl').html('<tr style="text-align:center;color:red;font-weight:bold;"><td colspan="3" style="padding: 5px;">No Outstandings!<td></tr>');
					}
					$('#AddedPymntDiv').fadeIn('slow');		
				}
			
			}
}

$(document).on('click','#viewInvoiceBtn',function()
{
	if(localStorage.getItem('pymntGroup'))
	{
		var pymntLcl = JSON.parse(localStorage.getItem('pymntGroup'));
		if(pymntLcl.length==0)
		{	
			$('#addedPAmt').html('<p style="color:red;text-align:center;">No Invoices!</p>');
			$('#addedPAmt').fadeToggle('slow');
		}
		else
		{	
			$('#addedPAmt').html('<p style="text-align:center;"><button type="button" class="clearPymtLocal" style="padding:5px 10px;">Clear</button></p>');
			$.each(pymntLcl,function(index,objj)
			{	
				var pymntType = objj.pymntType;
				if(pymntType=='partial')
					pymntType = 'PARTIAL PAYMENT';
				if(pymntType=='fullPayment')
					pymntType = 'FULL PAYMENT';
				if(objj.cashType=='cash')
				{
					$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel'+index+'" pydelunic="'+index+'" inv_no="'+objj.inv_no+'" amt="'+objj.amt+'">'+objj.inv_no+'</span></th><th style="font-weight:normal;">'+objj.originalAmt+'</th></tr><tr><td style="text-align:center;">CASH RECEIVED</td><td style="text-align:center;font-weight:bold;">'+objj.amt+'</td></tr><tr><td colspan="2"  style="text-align:center;background: antiquewhite;">'+pymntType+'</td></tr></table>');
				}
				if(objj.cashType=='cheque')
				{
					$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;" colspan="2"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel'+index+'" pydelunic="'+index+'" inv_no="'+objj.inv_no+'" amt="'+objj.amt+'">'+objj.inv_no+'</span></th><th style="font-weight:normal;">'+objj.originalAmt+'</th></tr><tr><td style="text-align:center;">CHEQUE RECEIVED</td><td style="text-align:center;font-weight:bold;">'+objj.amt+'</td><td style="text-align:center;">'+objj.chequeDate+'</td></tr><tr><td colspan="3"  style="text-align:center;background: antiquewhite;">'+pymntType+'</td></tr></table>');
				}
				if(objj.cashType=='neft')
				{
					$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;" colspan="2"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel'+index+'" pydelunic="'+index+'" inv_no="'+objj.inv_no+'" amt="'+objj.amt+'">'+objj.inv_no+'</span></th><th style="font-weight:normal;">'+objj.originalAmt+'</th></tr><tr><td style="text-align:center;">NEFT RECEIVED</td><td style="text-align:center;font-weight:bold;">'+objj.amt+'</td><td style="text-align:center;">'+objj.neftDate+'</td></tr><tr><td colspan="3"  style="text-align:center;background: antiquewhite;">'+pymntType+'</td></tr></table>');
				}
				if(objj.cashType=='cn')
				{
					$('#addedPAmt').append('<table width="97%" border="1" style="border-collapse:collapse;margin:auto;margin-bottom:5px;" height="auto"><tr><th style="text-align: left;" colspan="2"><img src="images/ic_action.png" style="height:25px;" class="pymntDelCls" id="pymntDel'+index+'" pydelunic="'+index+'" inv_no="'+objj.inv_no+'" amt="'+objj.amt+'">'+objj.inv_no+'</span></th><th style="font-weight:normal;">'+objj.originalAmt+'</th></tr><tr><td style="text-align:center;">CN RECEIVED</td><td style="text-align:center;font-weight:bold;">'+objj.amt+'</td><td style="text-align:center;">'+objj.cnNo+'</td></tr><tr><td colspan="3"  style="text-align:center;background: antiquewhite;">'+pymntType+'</td></tr></table>');
				}
				
			});
			$('#addedPAmt').fadeToggle('slow');
		}
	}
	else
	{
		$('#addedPAmt').html('<p style="color:red;text-align:center;">No Invoices!</p>');
		$('#addedPAmt').fadeToggle('slow');
	}
});

$(document).on('keyup','#shopNamePymntView',function()
{
	if($(this).val()!='')
	{
		var srchShopName = $('#shopNamePymntView').val();
		var srchStr = srchShopName.includes("&");
		var srchStr1 = srchShopName.includes("#");
		if(srchStr)
			var srchShopName = srchShopName.replace("&","!!");
		if(srchStr1)
			var srchShopName = srchShopName.replace("#","@@");
		var appUsrIdLcl = localStorage.getItem('app_userId');	
		$.getJSON(serviceUrl+'searchShops.php?appUsrIdLcl='+appUsrIdLcl+'&SrchShopTxt='+srchShopName,function(data)
		{
			$('#srchPmyntList').html('');
			$('#srchPmyntList').html('<p style="text-align:right;margin: 0;"><img src="images/ic_action.png" id="srchCls1"></p>');
			var jres = data.Result;
			$.each(jres,function(index,objct)
			{
				if(objct.Status=='Success')
					$('#srchPmyntList').append('<p style="text-align:center;" srchSId="'+objct.id+'" class="srchShpName1">'+ objct.shopName +'</p>');
				if(objct.Status=='NoRows')
					$('#srchPmyntList').html('<p style="color:red;text-align:center;font-weight:bold;">:: No Records ::</p>');
			});
			$('#srchPmyntList').fadeIn('fast');
		});
	}
	else
	{
		$('#srchPmyntList').fadeOut('fast');
		localStorage.removeItem('pymntGroup');
		localStorage.removeItem('OutstandingsArr');
		localStorage.removeItem('OutstandingsArrBackup');
		$('#AddedPymntDiv').fadeOut('slow');
		$('#addedPAmt').fadeOut('slow');
		$('#pymntTotalDiv').html('0');
		$('.cashTypeRadio').prop('disabled',false);
	}
});
$(document).on('click','#srchCls1',function()
{
	$('#srchPmyntList').fadeOut('fast');
	$('#shopNamePymnt').val('');
});

$(document).on('click','.srchShpName1',function()
{
	$('#chequeDate').val('');
	$('#Pymnt_InvoiceNo').val('');
	localStorage.removeItem('pymntGroup');
	localStorage.removeItem('OutstandingsArr');
	localStorage.removeItem('OutstandingsArrBackup');
	$('#AddedPymntDiv').fadeOut('slow');
	$('#addedPAmt').fadeOut('slow');
	$('#pymntTotalDiv').html('0');
	$('.cashTypeRadio').prop('disabled',false);
	var InfoVal = $('#infoRdBtn').val();
	if(InfoVal!='')
		$('#'+InfoVal).attr('checked',false);
	$('#shpNamePymnt').val('');
	$('#ownerEmail').val('');
	$('#ownerName').val('');
	$('#otpMblHidd').val('');
	$('#ownrEmail').val('');
	$('#ownrName').val('');
	$('#ownrMbl').val('');
	var srchsid = $(this).attr('srchsid');
	$('#shopNamePymnt').val(srchsid);
	$('#shopNamePymntView').val($(this).text());
	$('#currentShpName').val($(this).text());
	$('#page4ShpSelectLbl').html($(this).text());
	$('#page4ShpSelectDiv').fadeOut('slow');
	$('#page4ShpSelectLbl').fadeIn('slow');
	
	$('#srchPmyntList').fadeOut('fast');
	var shpId = $('#shopNamePymnt').val();
	var shpTxt = $(this).text();
	var srchStr = shpTxt.includes("&");
	var srchStr1 = shpTxt.includes("#");
	if(srchStr)
		var shpTxt = shpTxt.replace("&","!!");
	if(srchStr1)
		var shpTxt = shpTxt.replace("#","@@");
	var userId = localStorage.getItem('app_userId');
	var OutstandingsArr = [];
	getOwnerInfo(shpId);
	$.getJSON(serviceUrl+'searchShops.php?shpTxt='+shpTxt+'&userId='+userId,function(data)
	{
		var jres = data.Result;
		console.log(jres);
		localStorage.removeItem('OutstandingsArr');
		$.each(jres,function(index,objt)
		{
			if(objt.Status=='Success')
			{
				var outstanding_date = objt.outstanding_date;
				var pending_amount = objt.pending_amount;
				var ref_no = objt.ref_no;
				var dueDate = objt.dueDate;
				OutstandingsArr.push({'ref_no':ref_no,'outstanding_date':outstanding_date,'pending_amount':pending_amount,'dueDate':dueDate,'overdue':objt.overdue});
				localStorage.setItem('OutstandingsArr',JSON.stringify(OutstandingsArr));
				localStorage.setItem('OutstandingsArrBackup',JSON.stringify(OutstandingsArr));
			}
		});
		if(localStorage.getItem('OutstandingsArr'))
		{
			var OutstandingsArrLcl = JSON.parse(localStorage.getItem('OutstandingsArr'));
			if(OutstandingsArrLcl.length!=0)
			{
				$('#AddedPymntDiv').html('<table id="AddedPymntTbl" width="100%" height="auto"><tr style="text-align: center;background: cornflowerblue;color: antiquewhite;"><td colspan="4" style="padding: 4px;">Total : <span class="ttlOutstnds"></span></td></tr><tr style="font-weight:bold;text-align:left;background: gainsboro;font-size:13px;"><th>&nbsp;&nbsp;&nbsp;Invoice No</th><th style="text-align:center;">Date</th><th style="text-align:center;">Due Date</th><th style="text-align:right">Amt&nbsp;</th></tr>');
				var ttlOutstnds = 0;
				$.each(OutstandingsArrLcl,function(index,objt)
				{
					var outstanding_date = objt.outstanding_date;
					var datePart = outstanding_date.match(/\d+/g),
					year = datePart[0].substring(2), // get only two digits
				 	month = datePart[1], day = datePart[2];
					outstanding_date = day+'-'+month+'-'+year;
			
					var pending_amount = objt.pending_amount;
					ttlOutstnds = parseInt(ttlOutstnds)+parseInt(pending_amount);
					var ref_no = objt.ref_no;
					var dueDate = objt.dueDate;
					var indx = index+1;
					if(pending_amount==0)
					{
						$('#AddedPymntTbl').append('<tr><td style="text-align:left">&nbsp;<input type="radio" disabled ref_no="'+ref_no+'" amt="'+objt.pending_amount+'" class="p_rd" id="pyId'+indx+'" name="pChbox" unic="RdB'+index+'" overdue="'+objt.overdue+'" style="width: 15px !important;"><label for="pyId'+indx+'" class="RdB'+index+' cls" style="display:inline-block;font-size: 13px;">'+ ref_no+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center;">'+dueDate+'</td><td style="text-align:right;">'+pending_amount+'&nbsp;</td></tr>');
					}
					else
					{
						$('#AddedPymntTbl').append('<tr><td style="text-align:left">&nbsp;<input type="radio" ref_no="'+ref_no+'" amt="'+objt.pending_amount+'" class="p_rd" id="pyId'+indx+'" name="pChbox" unic="RdB'+index+'" overdue="'+objt.overdue+'" style="width: 15px !important;"><label for="pyId'+indx+'" class="RdB'+index+' cls" style="display:inline-block;font-size: 13px;">'+ ref_no+'</td><td style="text-align:center;">'+outstanding_date+'</td><td style="text-align:center;">'+dueDate+'</td><td style="text-align:right;">'+pending_amount+'&nbsp;</td></tr>');
					}
				});
				$('#AddedPymntTbl').append('</table>');
				$('.ttlOutstnds').html(ttlOutstnds);
			}
			else
			{
				$('#AddedPymntTbl').html('<tr style="text-align:center;color:red;font-weight:bold;"><td colspan="3" style="padding: 5px;">No Outstandings!<td></tr>');
			}
			$('#AddedPymntDiv').fadeIn('slow');
		}
		else
		{
			$('#AddedPymntDiv').html('<p style="color:red;font-weight:bold;text-align:center;">No Outstandings!<p>');
		}
		$('#AddedPymntDiv').fadeIn('slow');
	});
});

$(document).on('click','#clearLocal1',function()
{
	localStorage.removeItem('invoiceData');
	$('#RecentPmntIcon').click();
	$('#RecentPmntIcon').click();
});

$(document).on('click','#RecentPmntIcon',function(e)
{
	$('#todayCollectedPymnt').html('');
	var app_user = localStorage['app_userId'];
	var status = 'normal';
	$('.menu_payment').slideToggle('fast');
	var d = new Date();
	var day = d.getDate()>10? d.getDate():0+''+d.getDate();
	var mnth = d.getMonth()+1;
	var fmnth = mnth>10? mnth:0+''+mnth;
	var currDate = d.getFullYear()+'-'+fmnth+'-'+day;
	$('#dayWisePymnt').val(currDate);
	var dateVal = '0';
	getPymntData(app_user,status,dateVal);
});

$(document).on('change','#dayWisePymnt',function()
{
	$('#todayCollectedPymnt').html('');
	var app_user = localStorage['app_userId'];
	var status = 'dayWise';
	var dateVal = $(this).val();
	getPymntData(app_user,status,dateVal);
});

function getPymntData(app_user,status,dateVal)
{
	if(status=='normal')
		var dataUrl = serviceUrl+'delivery.php?appUserId='+app_user+'&getCollectedPymntShops=1&status=normal';
	if(status=='dayWise')
		var dataUrl = serviceUrl+'delivery.php?appUserId='+app_user+'&getCollectedPymntShops=1&status=dayWise&dateVal='+dateVal;
	$.getJSON(dataUrl,function(data)
	{
		var jres = data.Result;
		if(jres.status=='failed')
		{
			$('#todayCollectedPymnt').append('<p style="text-align:center;color:red;">No Payments Found!</p>');
		}
		else
		{
			$('#todayCollectedPymnt').append('<p style="color:green;text-align: center;"><strong>Amount Received - Rs.</strong><span id="totalPymnt" style="color:black;background:gainsboro;padding: 5px;"></span></p>')
			$('#todayCollectedPymnt').append('<div id="pymntClctDivCash" style="display:none;margin-top:20px;"><p style="padding:10px;background:cornflowerblue;color:azure">&nbsp;&nbsp;Payment Type - Cash</p><table width="100%" border="1" style="border-collapse:collapse;" id="pymntCollectedTbl"><tr style="font-size:13px;font-weight:bold;"><th style="padding:4px;">Fos</th><th>Invoice</th><th>Party</th><th>Amt</th></tr></table></div><div id="pymntClctDivCheque" style="display:none;margin-top:20px;"><p style="padding:10px;background:cornflowerblue;color:azure">&nbsp;&nbsp;Payment Type - Cheque</p><table width="100%" border="1" style="border-collapse:collapse;" id="pymntCollectedTbl1"><tr style="font-size:13px;font-weight:bold;"><th style="padding:4px;">Fos</th><th>Invoice</th><th>Party</th><th>Cheque</th><th>Amt</th></tr></table></div></div><div id="pymntClctDivNeft" style="display:none;margin-top:20px;"><p style="padding:10px;background:cornflowerblue;color:azure">&nbsp;&nbsp;Payment Type - NEFT</p><table width="100%" border="1" style="border-collapse:collapse;" id="pymntCollectedTbl2"><tr style="font-size:13px;font-weight:bold;"><th style="padding:4px;">Fos</th><th>Invoice</th><th>Party</th><th>Ref No</th><th>Amt</th></tr></table></div><div id="pymntClctDivCn" style="display:none;margin-top:20px;"><p style="padding:10px;background:cornflowerblue;color:azure">&nbsp;&nbsp;Payment Type - Credit Note</p><table width="100%" border="1" style="border-collapse:collapse;" id="pymntCollectedTbl3"><tr style="font-size:13px;font-weight:bold;"><th style="padding:4px;">Fos</th><th>Invoice</th><th>Party</th><th>CN No</th><th>Amt</th></tr></table></div>');

			var totalPymnt = 0;
			$.each(jres,function(index,objj)
			{
				var indx = index+1;
				totalPymnt += parseInt(objj.amount);
				fos_name = objj.fos_name.substring(0,4);
				if(objj.cash_type=='cash')
				{
					$('#pymntCollectedTbl').append('<tr style="font-size:13px;"><td style="text-align:center;padding:3px;">'+fos_name+'</td><td>'+objj.invoice_no+'</td><td class="lowercase cntr" style="word-break: break-all;">'+ objj.Name+'</td><td style="text-align:center;">'+objj.amount+'</td></tr>');
					$('#pymntClctDivCash').fadeIn('slow');	
				}
				if(objj.cash_type=='cheque')
				{
					$('#pymntCollectedTbl1').append('<tr style="font-size:13px;"><td style="text-align:center;padding:3px;">'+fos_name+'</td><td>'+objj.invoice_no+'</td><td class="lowercase cntr" style="word-break: break-all;">'+ objj.Name+'</td><td style="text-align:center;">'+objj.cheque_no+'</td><td style="text-align:center;">'+objj.amount+'</td></tr>');
					$('#pymntClctDivCheque').fadeIn('slow');
				}
				if(objj.cash_type=='neft')
				{
					$('#pymntCollectedTbl2').append('<tr style="font-size:13px;"><td style="text-align:center;padding:3px;">'+fos_name+'</td><td>'+objj.invoice_no+'</td><td class="lowercase cntr" style="word-break: break-all;">'+ objj.Name+'</td><td style="text-align:center;">'+objj.cheque_no+'</td><td style="text-align:center;">'+objj.amount+'</td></tr>');
					$('#pymntClctDivNeft').fadeIn('slow');
				}
				if(objj.cash_type=='cn')
				{
					$('#pymntCollectedTbl3').append('<tr style="font-size:13px;"><td style="text-align:center;padding:3px;">'+fos_name+'</td><td>'+objj.invoice_no+'</td><td class="lowercase cntr" style="word-break: break-all;">'+ objj.Name+'</td><td style="text-align:center;">'+objj.cheque_no+'</td><td style="text-align:center;">'+objj.amount+'</td></tr>');
					$('#pymntClctDivCn').fadeIn('slow');
				}
			});
			$('#totalPymnt').html('<strong>&nbsp;'+totalPymnt+'</strong>');	
		}
	});
}

$(document).on('click','.prdctDelCls1',function()
{
	var secoundPArr = [];
	var pymntDelId = $(this).attr('pDelUnic');
	if(localStorage.getItem('invoiceData'))
	{
		var pymntFinal = JSON.parse(localStorage.getItem('invoiceData'));
		if(pymntFinal.length==1)
		{
			localStorage.removeItem('invoiceData');
			$('#RecentPmntIcon').click();
			$('#RecentPmntIcon').click();
		}
		if(pymntFinal.length!=1)
		{
			pymntFinal.splice(pymntDelId,1);
			$.each(pymntFinal,function(index,obh)
			{
				secoundPArr.push({'invoiceNo':obh.invoiceNo,'Amount':obh.Amount});
			});
			localStorage.setItem('invoiceData',JSON.stringify(secoundPArr));
			$('#RecentPmntIcon').click();
			$('#RecentPmntIcon').click();
		}
	}
});

$(document).on('click','.c_rd',function()
{
	if(localStorage.getItem('pymntGroup'))
	{
		var pymntGroup = localStorage.getItem('pymntGroup');
		if(pymntGroup.length!=0)
		{
			var info = $(this).attr('info');
			$('#infoRdBtn').val(info);
			if(info=='Owner')
			{
				$('#mblTxtBox').fadeOut();
				$('#otpMblDiv').fadeIn();
				var shpId = $('#shopNamePymnt').val();
				if(shpId!='')
					getOwnerInfo(shpId,info);
				else
					alert('Please select shop!');
			}
			if(info=='Shop')
			{
				$('#mblTxtBox').fadeOut();
				$('#otpMblDiv').fadeIn();
				var shpId = $('#shopNamePymnt').val();
				if(shpId!='')
					getOwnerInfo(shpId,info);
				else
					alert('Please select shop!');
			}
			if(info=='Staff')	
			{
				$('#otpMblDiv').fadeOut();
				$('#mblTxtBox').fadeIn();
				$('#ownrMbl').attr('placeholder','Staff Mobile..');
				$('#ownrMbl').val('');
				$('#ownerName').val('');
				var shpId = $('#shopNamePymnt').val();
				if(shpId!='')
					getOwnerInfo(shpId,info);
				else
					alert('Please select shop!');
			}
		}
		else
		{
			$('.c_rd').prop('disabled',true);
			$(this).attr('checked',false);
			alert('Please add Invoices!');
		}
	}
	else
	{
		$('.c_rd').prop('disabled',true);
		$(this).attr('checked',false);
		alert('Please add Invoices!');
	}
});

function getOwnerInfo(shpId,info)
{
	$('#ownrName').val('');
	$('#ownrMbl').val('');
	$('#ownrEmail').val('');
	$('#ownerEmail').val('');
	$('#ownrMbl1').html('');
	$('#ownrMbl1').html('<option value="000">Select Mobile</option>');
	$.getJSON(serviceUrl+'delivery.php?getOwnerInfo=yes&shpId='+shpId,function(data)
	{
		console.log(data);
		var jres = data.Result;
		if(jres.status=='success')
		{
			if(info=='Owner')
			{
				if(jres.primary_mobile!='')
				{	
					$('#mblTxtBox').fadeOut();
					$('#otpMblDiv').fadeIn();
					$('#ownrMbl1').html('<option value="000">Select Owner Mobile</option>');
					$('#ownrMbl1').append('<option value="'+jres.primary_mobile+'"><strong>P - </strong>'+jres.primary_mobile+'</option>');
					$('#otpMblHidd').val(jres.primary_mobile);
				}
				else
				{
					alert('Owner Primary number not found!');
				}
				if(jres.Name!='')
					$('#shpFullName').val(jres.Name);
					
				if(jres.secondary_mobile!='')
						$('#ownrMbl1').append('<option value="'+jres.secondary_mobile+'"><strong>S - </strong>'+jres.secondary_mobile+'</option>');
				else
					alert('Owner Secondary number not found!');	
			}
			if(info=='Shop')
			{
				if(jres.shop_PMobile!='')
				{
					$('#mblTxtBox').fadeOut();
					$('#otpMblDiv').fadeIn();
					$('#ownrMbl1').html('<option value="000">Select Shop Mobile</option>');
					$('#ownrMbl1').append('<option value="'+jres.shop_PMobile+'"><strong>P - </strong>'+jres.shop_PMobile+'</option>');
					$('#otpMblHidd').val(jres.shop_PMobile);
				}
				else
				{
					alert('Shop Primary number not found!')
					$('#ownrMbl').prop('disabled',false);
				}
				if(jres.Name!='')
					$('#shpFullName').val(jres.Name);
				
				if(jres.shop_SMobile!='')	
					$('#ownrMbl1').append('<option value="'+jres.shop_SMobile+'"><strong>S - </strong>'+jres.shop_SMobile+'</option>');
				else
					alert('Owner Secondary number not found!');
			}
			if(info=='Staff')
			{
				$('#otpMblDiv').fadeOut();
				$('#mblTxtBox').fadeIn();
				if(jres.Name!='')
					$('#shpFullName').val(jres.Name);
				
				$('#ownrMbl').prop('disabled',false);
			}			
		}
		else
		{
			alert('Partner Info fetch error!');
		}
	});
}
/* Page4 end */
	
/* Page3 function start */
//$('#page3').on('pageshow',function(e){
function attendance_log(){
	$('.overlayCls').hide();
	$('#srchShopList').hide();
	$('#orderVal_p').hide();
	$('#prdctNameList').hide();
	$('#colorList').hide();
	$('.quantityArea').hide();
	$('#prdct_quantity').val('');
	if(localStorage.getItem('orderDisabled'))
		localStorage.removeItem('orderDisabled');
	getLatLong();
	if(!localStorage.getItem('stckOrder'))
	{
		$('#shpFullNameTxt').val('');
		$('#shopNameTxt').val('');
		$('.orderDisabledInfo').fadeOut('fast');
		//$('#getPrdctForm')[0].reset();
		$('#addPrdct').prop('disabled',false);
		if(localStorage.getItem('productData'))
			localStorage.removeItem('productData')
		$('#page3ShpSelectLbl').fadeOut('slow');
		$('#page3ShpSelectDiv').fadeIn('slow');
		$('.getOrderPage3w').fadeOut('slow');
		$('#AddedPrdctDiv').hide();
		$('#shpNamePrdct').html('<option value=""><-- : select shop : --></option>');
	}
	var isOff = 'onLine' in navigator && !navigator.onLine;

	if ( isOff ) {
    	onFailedOrders();
	}
	else {
		getCurrentPosition4();
	}
	$.getJSON(serviceUrl+'getProducts.php?getAllProducts=yes',function(data)
	{
		var jres = data.Result;
		$('#productsName').html('');
		if(jres.status=='error')
		{
			$('.getOrderPage3w').html('No Models Available!');
			$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.getOrderPage3w').fadeIn('slow');
		}
		if(jres.status=='failed')
		{
			$('.getOrderPage3w').html('Some error occure! Please try later.');
			$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.getOrderPage3w').fadeIn('slow');
		}
		if(jres.status!='error' && jres.status!='failed')
		{
			if(!localStorage.getItem('stckOrder'))
				$('.getOrderPage3w').fadeOut('slow');
			$.each(jres,function(index,objj)
			{
				$('#productsName').append('<p style="width:50%;float:left;cursor:pointer;margin:10px 0;text-align:left;"><input type="radio" name="prdctType" data-role="none" uId="'+index+'" id="prdctNameAll'+index+'" class="radioBtn" value="'+objj.product_name+'"><label for="prdctNameAll'+index+'" style="display:inline-block">'+objj.product_name+'</label></p>');
			});
			if(localStorage.getItem('stckOrder'))
			{
				checkOrderDisabled();
				getStckOrderInOrderPage();
				localStorage.removeItem('stckOrder');
			}
		}
	});
}
//});
function getCurrentPosition4(){
	navigator.geolocation.getCurrentPosition(
		onSuccessOrders,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					onSuccessOrders, 
					function(error){
							console.log('gps error');
						},
					geoOptionsLow);
			 	return;
				}
				console.log('gps error');	
			}, 
		geoOptionsHigh
	); 
}


function onSuccessOrders(position)
{
	var Shp_lat  = position.coords.latitude;
	var Shp_long = position.coords.longitude;
	$('#orderLat').val(Shp_lat);
	$('#orderLong').val(Shp_long);
	var appUsrIdLcl = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl +'NearestShop.php?appUsrIdLcl='+appUsrIdLcl+'&lat='+Shp_lat+'&long='+Shp_long,function(data)
	{
		var jres = data.Result;
		$.each(jres,function(index,obj)
		{
			if(obj.Status=='success')
			{
				$('#shpNamePrdct').append('<option value="'+obj.shopId+'">'+obj.shopName+'</option>');
			}
			if(obj.shopName=='emptySet')
			{
				$('#shpNamePrdct').html('<option value=""><---: Select current shop :---></option><option>No shops found !!</option>');
			}
		});
	});
}
function onFailedOrders()
{
	window.plugins.toast.showLongCenter(':: No internet connectivity ::');
}

$(document).on('change','#shpNamePrdct',function()
{
	if(localStorage.getItem('stckOrder'))
		localStorage.removeItem('stckOrder');
	if(localStorage.getItem('stckOrdersDataLcl'))
		localStorage.removeItem('stckOrdersDataLcl');
	$('#orderRprtTbl').hide('slow');
	var shpId = $(this).val();
	$('#shopNameTxt').val(shpId);
	var shpName = $(this).find("option:selected").text();
	$('#shpFullNameTxt').val(shpName);
	$('#page3ShpSelectLbl').html(shpName);
	$('#page3ShpSelectDiv').fadeOut('slow');
	$('#page3ShpSelectLbl').fadeIn('slow');
	checkOrderDisabled();
});
$(document).on('click','.orderGetApproval',function()
{
	$('#rejectTxtId').hide();
	$('#otpPopupInside').css('height','160px');
	var app_admin = localStorage.getItem('app_admin');
	if(app_admin==1)
	{
		$('#getApprvlBtnDiv').html('<button type="button" id="sendOtpBtn_orderAdmin" style="background-color:dodgerblue;color: white;font-size: 15px;text-shadow: none;padding: 7px;width: 80%;margin: auto;" class=" ui-btn ui-shadow ui-corner-all">Approve</button>');
	}
	else
	{
		$('#getApprvlBtnDiv').html('<button type="button" id="sendOtpBtn_order" style="background-color:dodgerblue;color: white;font-size: 15px;text-shadow: none;padding: 7px;width: 80%;margin: auto;" class=" ui-btn ui-shadow ui-corner-all">Get Approval</button>');
	}
	$('.otpPopup_order').show('slow');
});
$(document).on('click','#sendOtpCnclBtn_order',function()
{
	$('.otpPopup_order').hide('slow');
	
});
$(document).on('click','#sendOtpCnclBtn_order2',function()
{
	$('#cnfrmOtpPopupDiv_order').fadeIn('slow');
	$('#cnfrmOtpPopupDiv2_order').fadeOut('slow');
	getStckOrderInOrderPage();	
	$('.otpPopup_order').hide('slow');
});
$(document).on('click','#sendOtpBtn_orderAdmin',function()
{
	$('.otpPopup_order').fadeOut('slow');
	$('.getOrderPage3w').fadeOut('slow');
	$('.orderDisabledInfo').fadeOut('slow');
	$('#addPrdct').prop('disabled',false);
	$('#getOrdrs_btn').prop('disabled',false);
	$('#cnfrmOtpPopupDiv_order').fadeIn('slow');
	$('#cnfrmOtpPopupDiv2_order').fadeOut('slow');
	localStorage.removeItem('orderDisabled');
	$('#getOrdrs_btn').click();
});
$(document).on('click','#sendOtpBtn_order',function()
{
	$('#ldrIconId').show('fast');
	$('#otpCnclBtnId').show('fast');
	$('#apprvdTxtId').hide('fast');
	$('#rejectTxtId').hide('fast');
	var app_userId = localStorage.getItem('app_userId');
	var currentShpName =  $('#shpFullNameTxt').val();
	var fos_name = localStorage.getItem('fos_name')+'!!'+currentShpName;
	var shpId = $('#shopNameTxt').val();
	var invPeriod = '';
	var invPeriod_o = 'no_data';
	var invPeriod_cv = 'no_data';
	var invPeriod_PndngCP = 'no_data';
	var invPeriod_PndngCV = 'no_data';
	var invPeriodLen = $('#orderInvTbl tr').length;
	var crdPrd = $('.crdPrd').text();
	var orderVal = $('#orderVal_span').html();
	if(localStorage.getItem('disable_overdue'))
	{
		invPeriod_o = '';
		$('#orderInvTbl tr').each(function(index, element) {
			if(index!=0)
			{
				if(invPeriodLen-1==index)
					invPeriod_o += $(this).find('td:nth-child(2)').html()+'!'+crdPrd+'!'+$(this).find('td:nth-child(4)').html()+'!'+$(this).find('td:nth-child(3)').html()+'!'+orderVal;
				else
					invPeriod_o += $(this).find('td:nth-child(2)').html()+'!'+crdPrd+'!'+$(this).find('td:nth-child(4)').html()+'!'+$(this).find('td:nth-child(3)').html()+'!'+orderVal+',';
			}
		});
	}
	if(localStorage.getItem('disable_cv'))
	{
		invPeriod_cv = '';
		$('#orderInvTbl_cv tr').each(function(index, element) {
			if(index!=0)
			{
				invPeriod_cv = $(this).find('td:nth-child(1)').html()+'!'+$(this).find('td:nth-child(2)').html()+'!'+$(this).find('td:nth-child(3)').html()+'!'+orderVal;
			}
		});
	}
	if(localStorage.getItem('disable_unprsnt'))
	{
		invPeriod_PndngCP = '';
		invPeriod_PndngCV = '';
		if($('#pndngChqDaysExcdDiv_tbl tr').length>0)
		{
			$('#pndngChqDaysExcdDiv_tbl tr').each(function(index, element) 
			{
				if($('#pndngChqDaysExcdDiv_tbl tr').length-1==index)
					invPeriod_PndngCP += $(this).find('td:nth-child(1)').html()+'!'+$(this).find('td:nth-child(2)').html()+'!'+$(this).find('td:nth-child(3)').html()+'!'+$(this).find('td:nth-child(4)').html()+'!'+$(this).find('td:nth-child(5)').html()+'!'+orderVal;
				else
					invPeriod_PndngCP += $(this).find('td:nth-child(1)').html()+'!'+$(this).find('td:nth-child(2)').html()+'!'+$(this).find('td:nth-child(3)').html()+'!'+$(this).find('td:nth-child(4)').html()+'!'+$(this).find('td:nth-child(5)').html()+'!'+orderVal+',';
			});
		}
		if($('#pndngChqAmtExcdDiv_tbl tr').length>0)
		{
			$('#pndngChqAmtExcdDiv_tbl tr').each(function(index, element) 
			{
				if($('#pndngChqAmtExcdDiv_tbl tr').length-1==index)
					invPeriod_PndngCV += $(this).find('td:nth-child(1)').html()+'!'+$(this).find('td:nth-child(2)').html()+'!'+$(this).find('td:nth-child(3)').html()+'!'+$(this).find('td:nth-child(4)').html()+'!'+$(this).find('td:nth-child(5)').html()+'!'+orderVal;
				else
					invPeriod_PndngCV += $(this).find('td:nth-child(1)').html()+'!'+$(this).find('td:nth-child(2)').html()+'!'+$(this).find('td:nth-child(3)').html()+'!'+$(this).find('td:nth-child(4)').html()+'!'+$(this).find('td:nth-child(5)').html()+'!'+orderVal+',';
			});
		}
	}
	if(invPeriod_o=='')
		invPeriod_o = 'no_data';
	if(invPeriod_cv=='')
		invPeriod_cv = 'no_data';
	if(invPeriod_PndngCP=='')
		invPeriod_PndngCP = 'no_data';
	if(invPeriod_PndngCV=='')
		invPeriod_PndngCV = 'no_data';
	invPeriod = invPeriod_o+'@'+invPeriod_cv+'@'+invPeriod_PndngCP+'@'+invPeriod_PndngCV;
	console.log(invPeriod);
	$.post(serviceUrl+'verifyCode.php',{pymntPopupOtp:'yes',app_userId:app_userId,fos_name:fos_name,currentShpName:currentShpName,shpId:shpId,invPeriod:invPeriod,tt:(Date.now()/1000|0)},function(data)
	{
		console.log(data);
		var jres = $.parseJSON(data).result;
		if(jres.status=='success')
		{
			$('#otpPopupInside').css('height','190px');
			
			$('#cnfrmOtpPopupDiv_order').fadeOut('slow');
			$('#cnfrmOtpPopupDiv2_order').fadeIn('slow');
			var currentDateTime = jres.currentDateTime;
			var myVar = setInterval(function()
			{
				$.ajax(
				{
					url : serviceUrl +"verifyCode.php",
					type:"GET",
					data:'getOTPResponse=yes&currentDateTime='+currentDateTime+'&app_userId='+app_userId+'&shpId='+shpId+'&tt='+(Date.now()/1000|0),
					contentType:false,
					cache:false,
					processData:false,
					success:function(data)
					{
						console.log(data);
						var jres = $.parseJSON(data).result;
						if(jres.status=='success')
						{
							if(jres.response_code=='Approved')
							{
								$('#ldrIconId').fadeOut('fast');
								$('#apprvdTxtId').fadeIn('fast');
								$('#otpCnclBtnId').hide();
								$('#otpPopupInside').css('height','190px');
								clearInterval(myVar);
								setTimeout(function(){
									$('.otpPopup_order').fadeOut('slow');
									$('.getOrderPage3w').fadeOut('slow');
									$('.orderDisabledInfo').fadeOut('slow');
									$('#cnfrmOtpPopupDiv2_order').fadeOut('slow');
									$('#cnfrmOtpPopupDiv_order').fadeIn('slow');
									getStckOrderInOrderPage();
									localStorage.removeItem('orderDisabled');
									if(!localStorage.getItem('orderDisabled'))
										$('#getOrdrs_btn').click();
								},1500);
							}
							if(jres.response_code=='Rejected')
							{
								$('#ldrIconId').fadeOut('fast');
								$('#rejectTxtId').fadeIn('fast');
								$('#otpCnclBtnId').hide();
								$('#otpPopupInside').css('height','190px');
								$('#sendOtpCnclBtn_order').fadeIn();
								clearInterval(myVar);
							}
						}//success
					}//success:function(data)
				});//ajax
			},3000);	
		}
		else
		{
			alert('Please try again!');
		}
	});
});
$(document).on('click','#cnfrmPopupOkBtn_order',function()
{
	var popupOtpTxt = $('#popupOtpTxt_order').val();
	var flag = 1;
	if(popupOtpTxt=='')
	{
		alert('Please enter OTP!');
		flag = 0;
	}
	if(popupOtpTxt.length<4)
	{
		alert('OTP must be a 4 digit!');
		flag = 0;
	}
	if(flag==1)
	{
		var app_userId = localStorage.getItem('app_userId');
		$.getJSON(serviceUrl+'verifyCode.php?cnfrmCodePopupOtp=yes&cnfrmOtpPopupCode='+popupOtpTxt+'&app_userId='+app_userId,function(data)
		{
			var jres = data.result;
			if(jres.status=='success')
			{
				$('.otpPopup_order').fadeOut('slow');
				$('.getOrderPage3w').fadeOut('slow');
				$('.orderDisabledInfo').fadeOut('slow');
				$('#addPrdct').prop('disabled',false);
				$('#getOrdrs_btn').prop('disabled',false);
				$('#cnfrmOtpPopupDiv_order').fadeIn('slow');
				$('#cnfrmOtpPopupDiv2_order').fadeOut('slow');
				getStckOrderInOrderPage();
			}
			else
			{
				alert('Please give correct OTP!');
			}
		});
	}
});
    /* Page3 function end */

// $('#page2').on('pageshow',function(e){
function timer_entry(){
	//$('#PopupDiv_attendance').fadeOut();
	getLatLong();
	$('#homePageForm')[0].reset();
	$('.attndsPage2w').fadeOut('slow');
	$('.menu_attnds').hide();
	$('#homePageForm').css('visibility','visible');
		if($('.recIcon').hasClass("expanded"))
		$('.recIcon').removeClass("expanded");	
	$('.recIcon').attr('src','images/recentIcon.png').css('width','28px');
	$('#shpName').html('<option value=""><-- : select shop : --></option>');
	var isOff = 'onLine' in navigator && !navigator.onLine;

	if ( isOff ) {
    	onFailedFunction();
	}
	else {
		getCurrentPosition5();
	}
	//window.plugins.uniqueDeviceID.get(success, fail);
}
//});
document.addEventListener("online", onfunction, false);
function onfunction()
{
	getCurrentPosition5();
}
function getCurrentPosition5(){
	navigator.geolocation.getCurrentPosition(
		onSuccessFunction,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					onSuccessFunction, 
					function(error){
							console.log('gps error');
						},
					geoOptionsLow);
			 	return;
				}
				console.log('gps error');	
			}, 
		geoOptionsHigh
	); 
}
function onSuccessFunction(position)
{
	if(localStorage['app_user'])
		$('#workerName').val(localStorage['app_user']);
	var Shp_lat  = position.coords.latitude;
	var Shp_long = position.coords.longitude;
	$('#attndsLat').val(Shp_lat);
	$('#attndsLong').val(Shp_long);
	var appUsrIdLcl = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl +'NearestShop.php?appUsrIdLcl='+appUsrIdLcl+'&lat='+Shp_lat+'&long='+Shp_long,function(data)
	{
		$('#shpName').html('');
		$('#shpName').html('<option value=""><-- : select shop : --></option>');
		var jres = data.Result;
		$.each(jres,function(index,obj)
		{
			if(obj.Status=='success')
			{
				$('#shpName').append('<option value="'+obj.shopId+'" shpName="'+obj.shopName+'">'+obj.shopName+'</option>');
			}
			if(obj.shopName=='emptySet')
			{
				$('#shpName').html('<option value=""><---: Select current shop :---></option><option>No shops found !!</option>');
			}
		});
	});
}
function onFailedFunction()
{
	window.plugins.toast.showLongCenter(':: No internet connectivity ::');
	if(localStorage['app_user'])
		$('#workerName').val(localStorage['app_user']);
}

function success(uuid)
{
	localStorage['deviceUUID']=uuid;
}
function fail()
{
	localStorage['deviceUUID']='DeviceError';
}
	
$(document).on('change','#shpName',function()
{
	$('#PopupDiv_attendance').fadeOut();
	var shpId = $(this).val();
	$('#shopAttendsTxt').val(shpId);
	var app_userId = localStorage.getItem('app_userId');
	var shpName = $('#shpName option:selected').text();
	var srchStr = shpName.includes("&");
	var srchStr1 = shpName.includes("#");
	if(srchStr)
		var shpName = shpName.replace("&","!!");
	if(srchStr1)
		var shpName = shpName.replace("#","@@");
	$.getJSON(serviceUrl+'attendance.php?version='+version+'&getOutstnds=yes&app_userId='+app_userId+'&shpName='+shpName,function(data)
	{
		var jres = data.Result;
		if(jres.status=='emptySet')
			$('#outstndsDiv').html('');
		else
		{
			var o_Amt = 0;
			$('#outstndsDiv').html('<p>Total Outstandings : <span class="o_Amt"></span></p>');
			$('#outstndsDiv').append('<table width="100%" border="1" id="outstndsTbl" style="border-collapse:collapse;"><tr><th>Date</th><th>INVOICE No</th><th>P_Amt</th><th>Value</th></tr></table>');
			$.each(jres,function(index,objj)
			{
				if(objj.status=='success')
				{
					$('#outstndsTbl').append('<tr style="text-align:center;"><td>'+objj.outstanding_date+'</td><td>'+objj.ref_no+'</td><td>'+objj.pending_amount+'</td><td>'+objj.overdue+'</td></tr>');
					o_Amt += parseInt(objj.pending_amount);				
				}
			});
			$('.o_Amt').html(o_Amt);
			$('#outstndsDiv').slideDown('slow');
		}
	});
});

$('#addShopDetails').click(function(e)
{
	var shopName = $('#shopName').val();
	var shopArea = $('#shopArea').val();
	var latVal = $('#slatitude').val(); 
	var longVal = $('#slongitude').val();
	var flag = 1;
	$('.spinner').css('display','block');	
		
	if(latVal == '' || longVal == '')
	{
		$('#slatitude').addClass("errorTxt");
		$('#slongitude').addClass("errorTxt");
		flag = 0;
		setTimeout(function(){ $('.spinner').css('display','none'); }, 1000);
	}	
	
	if(shopName=='')
	{
		$('#shopName').addClass("errorTxt").attr('placeholder','Please fill Shop Name');
		flag = 0;
		setTimeout(function(){ $('.spinner').css('display','none'); }, 1000);
	}
	else
		$('#shopName').removeClass("errorTxt")
		
		
	if(shopArea=='')
	{
		$('#shopArea').addClass("errorTxt").attr('placeholder','Please fill Area');
		flag = 0;
		setTimeout(function(){ $('.spinner').css('display','none'); }, 1000);
	}
	else
		$('#shopArea').removeClass("errorTxt");
	
	
		
	if(flag==1)
	{
		$('#demo').html('');
		$('#shopName').removeClass("errorTxt");
		$('#shopArea').removeClass("errorTxt");
		var fData = new FormData($('#addShop')[0]);
		$.ajax(
		{
			url : serviceUrl +"addNewShop.php",
			type:"POST",
			data:fData,
			contentType:false,
			cache:false,
			processData:false,
			success:function(data)
			{
				var Jres = $.parseJSON(data);
				$.each(Jres,function(index,objj)
				{
					if(objj.Status=='Saved')
					{
						$('.spinner').css('display','none');
						$('#shopName').val('');
						$('#shopAddr1').val('');
						$('#shopAddr2').val('');
						$('#shopArea').val('');
						$('#pincode').val('');
						$('.addShpPage1w').html('Data saved successfully..');
						$('.addShpPage1w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
						$('.addShpPage1w').fadeIn('slow');
						$('html,body').animate({scrollTop:0},500);
					}
					if(objj.Status=='exists')
					{
						$('.addShpPage1w').html('Shop Name Already Exists!');
						$('.addShpPage1w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
						$('.addShpPage1w').fadeIn('slow');
						$('html,body').animate({scrollTop:0},500);
						setTimeout(function(){ $('.spinner').css('display','none'); }, 1000);
					}
					if(objj.Status=='Failed')
					{
						$('.addShpPage1w').html('Please try after some times..');
						$('.addShpPage1w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
						$('.addShpPage1w').fadeIn('slow');
						$('html,body').animate({scrollTop:0},500);
						setTimeout(function(){ $('.spinner').css('display','none'); }, 1000);
					}
				});
			}
		});
	}
});

$(document).on('click','#closeIcon',function(e)
{
	$('#capImg').attr('src','images/cameraIcon.png');
	$('#ImgSource').val('');
	$(this).css('display','none');
});

$(document).on('click','#restBtn',function()
{
	$('#capImg').attr('src','images/cameraIcon.png');
	$('#closeIcon').css('display','none');
});


$(document).on('click','#RecentShops',function(e)
{
	$('.menu_shp').slideToggle('fast');
	$.getJSON(serviceUrl +'ShopList.php?',function(data)
	{
		$('.menuUl').html('');
		$Jres = data.Result;
		$.each($Jres,function(index,objj)
		{
			if(objj.Status=='success')
			{
				var htm='';
				htm += '<li class="shopLi">'+objj.shopName+'</li>';
				$('.menuUl').append(htm);
			}
			if(objj.shopName=='emptySet')
			{
				$('.menuUl').html('<li style="list-style:none;">Empty Shops Found!</li>');
			}
		});
	});
});

$(document).on('click','#AtndsRecentIcon',function(e)
{
	var app_user = localStorage['app_user'];
	$('.menu_attnds').slideToggle('fast');
	$.getJSON(serviceUrl +'VisitedShops.php?app_user='+app_user,function(data)
	{
		$('.menuUl_attnds').html('');
		$Jres = data.Result;
		$.each($Jres,function(index,objj)
		{
			if(objj.Status=='success')
			{
				var htm='';
				htm += '<li class="shopLi">'+objj.shopName+'<span style="font-size:12px;"> ( '+objj.created+' ) - '+objj.purpose+'</span></li>';
				$('.menuUl_attnds').append(htm);
			}
			if(objj.shopName=='emptySet')
			{
				$('.menuUl_attnds').append('<li style="list-style:none;">No Shops Visited!</li>');
			}
		});
	});

});

$(document).on('click','#attendance_btn',function(e)
{
	if(localStorage.getItem('attendanceTime'))
	{
		var track_Time = localStorage.getItem('attendanceTime');
		var attnds = check15MinAtten(track_Time);
		if(attnds==1)
		{
			var parseVal = '1';
			atndsFunction(parseVal);
		}
	}
	else
	{
		var parseVal = '1';
		atndsFunction(parseVal);
	}
});
$(document).on('click','#cnfrmOk',function(e)
{
	var parseVal = '2';
	atndsFunction(parseVal);
	$('.alertDiv').hide();
});

$(document).on('keyup','#shopAttendsView',function()
{
	if($(this).val()!='')
	{
		$('#srchAttndsList').html('<p style="text-align:right;margin: 0;"><img src="images/ic_action.png" id="srchCls2"></p>');
		var srchShopName = $('#shopAttendsView').val();
		var srchStr = srchShopName.includes("&");
		var srchStr1 = srchShopName.includes("#");
		if(srchStr)
			var srchShopName = srchShopName.replace("&","!!");
		if(srchStr1)
			var srchShopName = srchShopName.replace("#","@@");
		var appUsrIdLcl = localStorage.getItem('app_userId');
		$.getJSON(serviceUrl+'searchShops.php?appUsrIdLcl='+appUsrIdLcl+'&SrchShopTxt='+srchShopName,function(data)
		{
			var jres = data.Result;
			$.each(jres,function(index,objct)
			{
				if(objct.Status=='Success')
					$('#srchAttndsList').append('<p style="text-align:center;" srchSId="'+objct.id+'" class="srchShpName2">'+ objct.shopName +'</p>');
				if(objct.Status=='NoRows')
					$('#srchAttndsList').html('<p style="color:red;text-align:center;font-weight:bold;">:: No Records ::</p>');
			});
			$('#srchAttndsList').fadeIn('fast');
		});
	}
	else
		$('#srchAttndsList').fadeOut('fast');
});

$(document).on('click','#srchCls2',function()
{
	$('#srchAttndsList').fadeOut('fast');
	$('#shopAttendsTxt').val('');
});
$(document).on('click','.srchShpName2',function()
{
	var srchsid = $(this).attr('srchsid');
	$('#shopAttendsTxt').val(srchsid);
	$('#shopAttendsView').val($(this).text());
	$('#srchAttndsList').fadeOut('fast');
});

function atndsFunction(parseVal)
{
		$('#PopupDiv_attendance').fadeOut();
		var functionParse = parseVal;
		var shp_id = $('#shopAttendsTxt').val();
		var attndsRdVal = $('#attndsRdVal').val();
		if(localStorage.getItem('app_user'))
			var fos = localStorage['app_user'];
		var shpLat = $('#attndsLat').val();
		var shpLong = $('#attndsLong').val();
		var flag = 1;
		if(shp_id=='000' || shp_id=='' || shp_id=='null')
		{
			$('.attndsPage2w').html('Please Select Current Shop.');
			$('.attndsPage2w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.attndsPage2w').fadeIn('slow');
			$('html,body').animate({scrollTop:0},500);
			flag = 0;
		}
		if(shp_id=='0000')
		{
			$('.attndsPage2w').html('Please connect network');
			$('.attndsPage2w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.attndsPage2w').fadeIn('slow');
			$('html,body').animate({scrollTop:0},500);
			flag = 0;
		}
		if(flag==1)
		{
			var isOffline = 'onLine' in navigator && !navigator.onLine;
			if ( isOffline ) 
			{
				var shp_id = $('#shopAttendsTxt').val();
				if(localStorage.getItem('app_user'))
					var fos = localStorage['app_user'];
				if(shp_id!='000' || shp_id!='0000' || shp_id!='' || shp_id!='null')
				{
						attendanceArray.push({'shp_id':shp_id,'attndsRdVal':attndsRdVal});
						localStorage['Pending_Attendance']= JSON.stringify(attendanceArray);
						$('.attndsPage2w').html('No internet connection.Your data saved to local');
						$('.attndsPage2w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
						$('.attndsPage2w').fadeIn('slow');
						$('html,body').animate({scrollTop:0},500);
						$('#PopupDiv_attendance').fadeIn();
				}
				else
					alert('Please select shop(offline)');
			}
			else 
			{
				var shp_id = $('#shopAttendsTxt').val();
				if(localStorage.getItem('app_user'))
					var fos = localStorage['app_user'];
				var deviceUUID = localStorage['deviceUUID'];
				if(shp_id!='000' || shp_id!='0000' || shp_id!='' || shp_id!='null' || deviceUUID!='')
				{
					$.getJSON(serviceUrl+'attendance.php?shp_id='+shp_id+'&fos='+fos+'&deviceUUID='+deviceUUID+'&functionParse='+functionParse+'&shpLat='+shpLat+'&shpLong='+shpLong+'&attndsRdVal='+attndsRdVal,function(data)
					{
						var res = data.Result;
						if(res.status=='success')
						{
							$('.attndsPage2w').html(':: Visit Recorded ::');
							$('.attndsPage2w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
							$('.attndsPage2w').fadeIn('slow');
							$('html,body').animate({scrollTop:0},500);
							var getCurrTime = currentTimeData();
							if(getCurrTime)
								localStorage.setItem('attendanceTime',getCurrTime);
							$('#shpName').val('');
							$('#outstndsDiv').fadeOut('slow');
							$('#PopupDiv_attendance').fadeIn();
						}
						if(res.status=='failed')
						{	
							$('.attndsPage2w').html('Please try later!!');
							$('.attndsPage2w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
							$('.attndsPage2w').fadeIn('slow');
							$('html,body').animate({scrollTop:0},500);
						}
						if(res.status=='error')
						{	
							$('.alertDiv').show();
						}
					});
				}
			}
		}
}
$(document).on('click','#cnfrmCncl',function(e)
{
	$('.alertDiv').hide();
});

document.addEventListener("online", onOnlineAttnds, false);
function onOnlineAttnds()
{
	if(localStorage.getItem('app_user'))
		var fos = localStorage['app_user'];
	var deviceUUID = localStorage['deviceUUID']; 
	var functionParse = '2';
	var pendingAttnds = JSON.parse(localStorage.getItem('Pending_Attendance'));
	var shpLat = $('#attndsLat').val();
	var shpLong = $('#attndsLong').val();
	if(pendingAttnds.length!=0)
	{
		$.each(pendingAttnds,function(index,objt)
		{
			$.getJSON(serviceUrl+'attendance.php?shp_id='+objt.shp_id+'&fos='+fos+'&deviceUUID='+deviceUUID+'&functionParse='+functionParse+'&shpLat='+shpLat+'&shpLong='+shpLong+'&attndsRdVal='+objt.attndsRdVal,function(data)
			{
				var res = data.Result;
				if(res.status=='success')
				{
					var rs = pendingAttnds.indexOf(objt.shp_id);
					pendingAttnds.splice(rs,1);
					localStorage['Pending_Attendance']= JSON.stringify(pendingAttnds);
				}
				if(res.status=='error')
				{
					var rs1 = pendingAttnds.indexOf(objt.shp_id);
					pendingAttnds.splice(rs1,1);
					localStorage['Pending_Attendance']= JSON.stringify(pendingAttnds);
				}
			});
		});
	}
}

$(document).on('click','.radioAttnds',function()
{
	$('#attndsRdVal').val($(this).val());
});

$(document).on('click','#prdct_name',function()
{
	var prdcttypeid = $('#prdctTypeBtn').attr('prdcttypeid');	
	$('#'+prdcttypeid).attr('checked','checked');
	$('#prdctNameList').html('');
	var prdctType = $('#prdctTypeBtn').attr('prdctTypeVal');
	$.getJSON(serviceUrl+'getProducts.php?getModels=yes&prdctTypeBtn='+prdctType,function(data)
	{
		console.log(data);
		var jres = data.Result;
		if(jres.status=='error')
		{
			$('.getOrderPage3w').html('No results');
			$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.getOrderPage3w').fadeIn('slow');
			$('html,body').animate({scrollTop:0},500);
		}
		if(jres.status=='failed')
		{
			$('.getOrderPage3w').html('Please try later!!');
			$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.getOrderPage3w').fadeIn('slow');
			$('html,body').animate({scrollTop:0},500);
		}
		if(jres.status!='error' && jres.status!='failed')
		{
			$.each(jres,function(index,objc)
			{
				var htm = '';
				htm += '<p style="width:50%;float:left;cursor:pointer;margin:5px 0;"><input type="radio" name="prdctType2" data-role="none" uId="'+index+'" id="prdctName'+index+'" class="PrdctNameRadio" value="'+objc.product_name+'" dp="'+objc.dp+'"><label for="prdctName'+index+'" style="display:inline-block" id="PrdctNameRadio'+index+'">'+objc.product_name+'</label></p>';
				$('#prdctNameList').append(htm);
			});
			var prdctnameIds = $('#prdct_name').attr('prdctnameid');
			$('#prdctNameList').fadeIn('slow');
			$('#prdctName'+prdctnameIds).attr('checked','checked');
		}
	});
});

$(document).on('click','#prdctTypeBtn',function()
{
	var prdctTypeId = $(this).attr('prdctTypeId');
	$('#'+prdctTypeId).attr('checked','checked');
	$('.prdctTypeListDiv').fadeToggle('slow');
});
$(document).on('click','.radioBtn',function()
{
	$('.getOrderPage3w').fadeOut('slow');
	$('.radioBtn').prop('disabled',false);
	$(this).prop('disabled',true);
	$('.quantityInrArea').html('');
	$('#prdct_name').attr('prdctnameval','');
	$('#prdct_name').attr('prdctnameid','');
	$('#prdct_N').val('');
	$('#prdct_C').val('');
	$('#colorList').fadeOut('slow');
	$('#prdct_color').text('Select Color');
	
	var prdcttypeid = $('#prdctTypeBtn').attr('prdcttypeid');
	$('#'+prdcttypeid).attr('checked','checked');
	$('#prdct_name').text('Select Product Name');
	var prdctTypeId = $(this).attr('id');
	var prdctType = $(this).val();
	if(prdctType)
	{
		$('#prdctTypeBtn').text(prdctType);
		$('#prdct_T').val(prdctType);
		$('#prdctTypeBtn').attr('prdctTypeId',prdctTypeId);
		$('#prdctTypeBtn').attr('prdctTypeVal',prdctType);
	}
	$('#prdct_name').click();
});

$(document).on('click','.PrdctNameRadio',function()
{
	$('#orderRprtTbl').hide('slow');
	$('#prdct_C').val('');
	$('.PrdctNameRadio').prop('disabled',false);
	$(this).prop('disabled',true);
	$('.quantityInrArea').html('');
	var prdcttypeid = $('#prdctTypeBtn').attr('prdcttypeid');
	$('#'+prdcttypeid).attr('checked','checked');
	var uId = $(this).attr('uId');
	var prdctName = $('#PrdctNameRadio'+uId).html();
	if(prdctName)
	{
		$('#prdct_name').text(prdctName);
		$('#prdct_N').val(prdctName);
		$('#prdct_N').attr('dp',$(this).attr('dp'));
		$('#prdct_name').attr('prdctNameId',uId);
		$('#prdct_name').attr('prdctNameVal',prdctName);
	}
	$('#prdct_color').text('Select Color');
	$('#prdct_color').click();
});

$(document).on('click','#prdct_color',function()
{
	$('#colorList').html('');
	var prdctName = $('#prdctTypeBtn').attr('prdctTypeVal');
	var prdct_model = $('#prdct_name').attr('prdctnameval');
	$.getJSON(serviceUrl+'getProducts.php?req=prdctColor&prdctTypeBtn='+prdctName+'&prdct_model='+prdct_model,function(data)
	{
		var jres = data.Result;
		if(jres.status=='error')
		{
			$('.getOrderPage3w').html('No results!');
			$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.getOrderPage3w').fadeIn('slow');
			$('html,body').animate({scrollTop:0},500);
		}
		if(jres.status=='failed')
		{
			$('.getOrderPage3w').html('Please try later!!');
			$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.getOrderPage3w').fadeIn('slow');
			$('html,body').animate({scrollTop:0},500);
		}
		if(jres.status!='error' && jres.status!='failed')
		{
			$.each(jres,function(index,objc)
			{
				if(objc.color)
				{
					htm = '';
					htm += '<p style="width:50%;float:left;cursor:pointer;margin:5px 0;"><input type="radio" name="prdctType3" data-role="none" uId="C'+index+'" id="prdctColor'+index+'" class="PrdctColorRadio" value="'+objc.color+'"><label for="prdctColor'+index+'" style="display:inline-block" id="PrdctColorRadio'+index+'">'+objc.color+'</label></p>';
					$('#colorList').append(htm);
					$('#prdct_color').attr('color','yes');
				}
				else
				{
					$('#prdct_color').html('No Color');
					$('#prdct_C').val('no color');
					$('#prdct_color').attr('color','');
					$('#colorList').fadeOut('fast');
					/* get quantity section */
					var prdct_Type = $('#prdct_T').val();
					var prdct_name = $('#prdct_N').val();
					var prdct_color = $('#prdct_C').val();
					$.getJSON(serviceUrl+'getProducts.php?getquantity=yes&prdct_Type='+prdct_Type+'&prdct_name='+prdct_name+'&prdct_color='+prdct_color,function(data)
					{
						var jres = data.Result;
						if(jres.status=='error')
						{
							$('.quantityArea').slideDown('slow');
							$('.quantityInrArea').html('Empty stock').css('color','red');
						}
						if(jres.status=='failed')
						{
							$('.quantityArea').slideDown('slow');
							$('.quantityInrArea').html('Error').css('color','red');
						}
						if(jres.status!='error' && jres.status!='failed')
						{
							$('.quantityArea').slideDown('slow');
							$('.quantityInrArea').html(jres.quantity).css('color','green');
						}
					});
					/* end quantity */
				}
			});
			var prdctnameIds = $('#prdct_color').attr('prdctnameid');
			$('#prdctColor'+prdctnameIds).attr('checked','checked');
			if($('#prdct_color').attr('color')=='yes')
				$('#colorList').fadeIn('slow');
			if(!$('#prdct_color').attr('color'))
				$('#colorList').hide();
		}
	});
});

$(document).on('click','.PrdctColorRadio',function()
{
	$('#orderRprtTbl').hide('slow');
	var shpId = $('#shopNameTxt').val();
	var fosId = localStorage['app_userId'];
	if(shpId!='')
	{
		$('#orderRprtTbl').html('<tr><th>Target</th><th>Achieved</th><th>Pending</th></tr>');
		$('.PrdctColorRadio').prop('disabled',false);
		$(this).prop('disabled',true);
		var colorVal = $(this).val();
		$('#prdct_color').text(colorVal);
		$('#prdct_C').val(colorVal);
		var prdct_Type = $('#prdct_T').val();
		var prdct_name = $('#prdct_N').val();
		var prdct_color = $('#prdct_C').val();
		$.getJSON(serviceUrl+'getProducts.php?getquantity=yes&prdct_Type='+prdct_Type+'&prdct_name='+prdct_name+'&prdct_color='+prdct_color+'&shpId='+shpId+'&fosId='+fosId,function(data)
		{
			console.log(data);
			var jres = data.Result;
			if(jres.status=='error')
			{
				$('.quantityArea').slideDown('slow');
				$('.quantityInrArea').html('Empty stock').css('color','red');
			}
			if(jres.status=='failed')
			{
				$('.quantityArea').slideDown('slow');
				$('.quantityInrArea').html('Error').css('color','red');
			}
			if(jres.status!='error' && jres.status!='failed')
			{
				$('.quantityArea').html('Stock in Hand : <span class="quantityInrArea"></span>');
				$('.quantityArea').slideDown('slow');
				$('.quantityInrArea').html(jres.quantity).css('color','green');
				if(jres.target!='empty')
				{
					var pending = parseInt(jres.target)-parseInt(jres.achievement);
					$('#orderRprtTbl').append('<tr style="text-align:center;"><td>'+jres.target+'</td><td>'+jres.achievement+'</td><td>'+pending+'</td></tr>');
					$('#orderRprtTbl').show('slow');
				}
			}
		});
	}
	else
	{
		alert('Please select shop!');
		$('.PrdctColorRadio').prop('checked',false);
		$('#orderRprtTbl').hide('slow');
	}
});

$(document).on('click','#addPrdct',function()
{
	var prdct_Type = $('#prdct_T').val();
	var prdct_name = $('#prdct_N').val();
	var dp = $('#prdct_N').attr('dp');
	var prdct_color = $('#prdct_C').val();
	var prdct_quantity = $('#prdct_quantity').val();
	var orderTotalVal = 0;
	var newArr = [];
	var Condition = prdct_Type!='' && prdct_name!='' && prdct_color!='' && prdct_quantity!='';
	if(Condition && prdct_quantity>0)
	{
		orderTotalVal = parseInt(orderTotalVal)+(parseInt(dp)*parseInt(prdct_quantity));
		$('#orderVal_span').html(orderTotalVal);
		$('#orderVal_p').show();
		newArr.push({'prdctType':prdct_Type,'prdctName':prdct_name,'prdctColor':prdct_color,'prdctQuantity':prdct_quantity,'orderVal':orderTotalVal});
		if(localStorage.getItem('productData'))
		{
			var prdctInLocal = JSON.parse(localStorage.getItem('productData'));
			if(prdctInLocal.length!=0)
			{
				$.each(prdctInLocal,function(index,objt)
				{
					if(prdct_Type==objt.prdctType && prdct_name==objt.prdctName && prdct_color==objt.prdctColor)
					{
						localStorage.setItem('productData',JSON.stringify(newArr));
					}
					else
					{
						newArr.push({'prdctType':objt.prdctType,'prdctName':objt.prdctName,'prdctColor':objt.prdctColor,'prdctQuantity':objt.prdctQuantity,'orderVal':objt.orderVal});	
						localStorage.setItem('productData',JSON.stringify(newArr));
					}
				});
				var prdctFinal = JSON.parse(localStorage.getItem('productData'));
				$('#AddedPrdctDiv').html('<p style="text-align:center;"><button type="button" id="clearLocal" style="padding:5px 10px;">Clear</button></p>');
				var ordrVal1 = 0;
				$.each(prdctFinal,function(index,objj)
				{
					ordrVal1 = parseInt(ordrVal1)+parseInt(objj.orderVal);
					var indexData = index+1;
				$('#AddedPrdctDiv').append('<p style="color:green;text-align:left;margin-left:10px;"><span><img src="images/ic_action.png" style="height:25px;" class="prdctDelCls" id="prdtDel'+index+'" pDelUnic="'+index+'"></span><span style="margin-left:10px;">'+ indexData +' .'+objj.prdctType+', '+objj.prdctName+', '+objj.prdctColor+', '+objj.prdctQuantity+'</span></p>');			
					$('#AddedPrdctDiv').fadeIn('slow');
					$('.getOrderPage3w').fadeOut('slow');
				});
				$('#orderVal_span').html(ordrVal1);
				$('#orderVal_p').show();
				$('#prdct_quantity').val('');
				$('.quantityArea').html('<span style="color:green;"><strong>Added!</strong>, '+prdct_name+' ('+prdct_color+') - '+prdct_quantity+'.</span>');
			}
		}
		else
		{
			localStorage.setItem('productData',JSON.stringify(newArr));
			var prdctFinal = JSON.parse(localStorage.getItem('productData'));
			$('#AddedPrdctDiv').html('<p style="text-align:center;"><button type="button" id="clearLocal" style="padding:5px 10px;">Clear</button></p>');
			var ordrVal = 0;
			$.each(prdctFinal,function(index,objj)
			{
				ordrVal = parseInt(ordrVal)+parseInt(objj.orderVal);
				var indexValData = index+1;
				$('#AddedPrdctDiv').append('<p style="color:green;text-align:left;margin-left:10px;"><span><img src="images/ic_action.png" style="height:25px;" class="prdctDelCls" id="prdtDel'+index+'" pDelUnic="'+index+'"></span><span style="margin-left:10px;">'+ indexValData+' .'+objj.prdctType+', '+objj.prdctName+', '+objj.prdctColor+', '+objj.prdctQuantity+'</span></p>');			
				$('#AddedPrdctDiv').fadeIn('slow');
				$('.getOrderPage3w').fadeOut('slow');
			});
			$('#orderVal_span').html(ordrVal);
			$('#orderVal_p').show();
			$('#prdct_quantity').val('');
			$('.quantityArea').html('<span style="color:green;"><strong>Added!</strong>, '+prdct_name+' ('+prdct_color+') - '+prdct_quantity+'.</span>');
		}
	}
	else
	{	
		$('.getOrderPage3w').html('Required:Product Name & Model & Color & Qty');
		$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.getOrderPage3w').fadeIn('slow');
		$('html,body').animate({scrollTop:0},500);
	}
});

$(document).on('click','#RecentOrdersIcon',function(e)
{
	if(localStorage.getItem('productData'))
	{
		var prdctFinal = JSON.parse(localStorage.getItem('productData'));
		if(prdctFinal.length!=0)
		{
			$('#AddedPrdctDiv').html('<p style="text-align:center;"><button type="button" id="clearLocal" style="padding:5px 10px;">Clear</button></p>');
			var orderVal2 = 0;
			$.each(prdctFinal,function(index,objj)
			{
				orderVal2 = parseInt(orderVal2)+(parseInt(objj.orderVal)*parseInt(objj.prdctQuantity));
				var indexVal = index+1;
				$('#AddedPrdctDiv').append('<p style="color:green;text-align:left;margin-left:10px;"><span><img src="images/ic_action.png" style="height:25px;" class="prdctDelCls" id="prdtDel'+index+'" pDelUnic="'+index+'"></span><span style="margin-left:10px;">'+ indexVal+' . '+objj.prdctType+', '+objj.prdctName+', '+objj.prdctColor+', '+objj.prdctQuantity+'</span></p>');			
			});
			$('#orderVal_span').html(orderVal2);
			$('#orderVal_p').show();
			$('#AddedPrdctDiv').slideToggle('slow');
		}
	}
	if(!localStorage.getItem('productData'))
	{
		$('#AddedPrdctDiv').html('<p style="color:red;text-align:center;margin-left:10px;">:: No Products::</p>');
		$('#AddedPrdctDiv').slideToggle('slow');
	}
});

$(document).on('click','.prdctDelCls',function()
{
	var secoundArr = [];
	var prdctDelId = $(this).attr('pDelUnic');
	if(localStorage.getItem('productData'))
	{
		var prdctFinal = JSON.parse(localStorage.getItem('productData'));
		console.log(prdctFinal);
		if(prdctFinal.length==1)
		{
			localStorage.removeItem('productData');
			$('#RecentOrdersIcon').click();
			$('#RecentOrdersIcon').click();
		}
		if(prdctFinal.length!=1)
		{
			prdctFinal.splice(prdctDelId,1);
			console.log(prdctFinal);
			$.each(prdctFinal,function(index,obh)
			{
				secoundArr.push({'prdctType':obh.prdctType,'prdctName':obh.prdctName,'prdctColor':obh.prdctColor,'prdctQuantity':obh.prdctQuantity,'orderVal':obh.orderVal});
			});
			localStorage.setItem('productData',JSON.stringify(secoundArr));
			$('#RecentOrdersIcon').click();
			$('#RecentOrdersIcon').click();
		}
	}
});

/*$(document).on('click','#getOrdrs_btn',function()
{
	var shopNameTxt = $('#shopNameTxt').val();
	var app_user = localStorage.getItem('app_user');
	var app_userId = localStorage.getItem('app_userId');
	var shpLat = $('#orderLat').val();
	var shpLong = $('#orderLong').val();
	var lastInsertedIds = '';
	var lastInsertedIds_all = '';
	var app_admin = localStorage.getItem('app_admin');

	if(shopNameTxt!='')
	{
		if(localStorage.getItem('productData'))
		{
			if(localStorage.getItem('orderDisabled'))
			{
				$('.orderGetApproval').click();
			}
			else
			{
				var prdctFinal = JSON.parse(localStorage.getItem('productData'));
				if(prdctFinal.length!=0)
				{		
				  $.each(prdctFinal,function(index,obh)
				  {
						$.getJSON(serviceUrl+'addOrders_test.php?shopNameTxt='+shopNameTxt+'&app_user='+app_user+'&prdctType='+obh.prdctType+'&prdctName='+obh.prdctName+'&prdctColor='+obh.prdctColor+'&prdctQuantity='+obh.prdctQuantity+'&shpLat='+shpLat+'&shpLong='+shpLong,function(data)
						{
							var jres = data.Result;
							if(jres.status=='success' && index+2>prdctFinal.length)
							{
								lastInsertedIds = jres.lastInsertedIds;
								var length = lastInsertedIds.length;
								$.each(lastInsertedIds,function(ind,a)
								{
									if(length!=ind+1)
										lastInsertedIds_all += a+'!';
									else
										lastInsertedIds_all += a;
								});
								console.log(lastInsertedIds_all);
								
								var otpMbl = jres.primary_mobile;
								var Pymnt_Amnt = jres.dateUnique;
								var pwd = 'orderCnfrm';
								var otpDetails = app_userId+','+shopNameTxt+'@'+lastInsertedIds_all;
								sendPassword(otpMbl,pwd,Pymnt_Amnt,otpDetails);
								$('.getOrderPage3w').html('Your Orders Saved');
								$('.getOrderPage3w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
								$('.getOrderPage3w').fadeIn('slow');
								$('html,body').animate({scrollTop:0},500);
								localStorage.removeItem('productData');
								$.getJSON(serviceUrl+'ordersMail.php?mailSts=orders&shopNameTxt='+shopNameTxt+'&app_user='+app_user,function(data)			
								{
									var dres = data.result;
									if(dres.mail=='sent')
									{
										location.reload();
									}
									if(dres.status=='norows')
									{	
										$('.getOrderPage3w').html('Try After Some Times!');
										$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
										$('.getOrderPage3w').fadeIn('slow');
										$('html,body').animate({scrollTop:0},500);
									}
								});
							}
							if(jres.status=='failed')
							{	
								$('.getOrderPage3w').html('Try After Some Times!');
								$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
								$('.getOrderPage3w').fadeIn('slow');
								$('html,body').animate({scrollTop:0},500);
							}
							if(jres.status=='OrdersExist')
							{
								$('.getOrderPage3w').html('Exists Orders!');
								$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
								$('.getOrderPage3w').fadeIn('slow');
								$('html,body').animate({scrollTop:0},500);
							}
						});
				  });
				}
			}
		}
		if(!localStorage.getItem('productData'))
		{
			$('.getOrderPage3w').html('Please Add Orders!!');
			$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.getOrderPage3w').fadeIn('slow');
			$('html,body').animate({scrollTop:0},500);
		}
	}
	if(shopNameTxt=='')
	{	
		$('.getOrderPage3w').html('Please select or Give shop');
		$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.getOrderPage3w').fadeIn('slow');
		$('html,body').animate({scrollTop:0},500);
	}
});	*/

$(document).on('click','#getOrdrs_btn',function()
{
	var shopNameTxt = $('#shopNameTxt').val();
	var app_user = localStorage.getItem('app_user');
	var app_userId = localStorage.getItem('app_userId');
	var shpLat = $('#orderLat').val();
	var shpLong = $('#orderLong').val();
	var lastInsertedIds = '';
	var lastInsertedIds_all = '';
	var app_admin = localStorage.getItem('app_admin');
	var alrtDivCls = 'getOrderPage3w';
	
	if(shopNameTxt!='')
	{
		if(localStorage.getItem('productData'))
		{
			if(localStorage.getItem('orderDisabled'))
			{
				$('.orderGetApproval').click();
			}
			else
			{
				//var prdctFinal = JSON.parse(localStorage.getItem('productData'));
				var prdctFinal = localStorage.getItem('productData');
				if(prdctFinal.length!=0)
				{
					var jsonString_orders = prdctFinal;
					console.log(jsonString_orders);					
					//$.post(serviceUrl+'addOrders.php',{add_orders:'yes',shopNameTxt:shopNameTxt,app_user:app_user,app_userId:app_userId,shpLat:shpLat,shpLong:shpLong,jsonString_orders:jsonString_orders},function(data)
					$.ajax(
					{
						url:serviceUrl+"addOrders.php",
						type:"POST",
						data:{add_orders:'yes',shopNameTxt:shopNameTxt,app_user:app_user,app_userId:app_userId,shpLat:shpLat,shpLong:shpLong,jsonString_orders:jsonString_orders},
						cache:false,
						async: false,
						success:function(data)
						{
							console.log(data);
							var jres = $.parseJSON(data);
							var res_final = jres.Result;
							if(res_final.status=='success')
							{
								lastInsertedIds = res_final.lastInsertedIds;
								var length = lastInsertedIds.length;
								$.each(lastInsertedIds,function(ind,a)
								{
									if(length!=ind+1)
										lastInsertedIds_all += a+'!';
									else
										lastInsertedIds_all += a;
								});
								console.log(lastInsertedIds_all);
								
								var otpMbl = res_final.primary_mobile;
								var Pymnt_Amnt = res_final.dateUnique;
								var pwd = 'orderCnfrm';
								var otpDetails = app_userId+','+shopNameTxt+'@'+lastInsertedIds_all;
								sendPassword(otpMbl,pwd,Pymnt_Amnt,otpDetails);
								var msg = 'Your Orders Saved';
								successStyles(msg,alrtDivCls);
								localStorage.removeItem('productData');
								$.getJSON(serviceUrl+'ordersMail.php?mailSts=orders&shopNameTxt='+shopNameTxt+'&app_user='+app_user,function(data)			
								{
									var dres = data.result;
									if(dres.mail=='sent')
									{
										location.reload();
									}
									if(dres.status=='norows')
									{	
										var msg = 'Failed! Try after some times.';
										errStyles(msg,alrtDivCls);
									}
								});
							}
							else
							{
								var msg = 'Failed! Try after some times.';
								errStyles(msg,alrtDivCls);
							}
						}
					});
				}
			}
		}
		else
		{
			var msg = 'Please Add Orders!!';
			errStyles(msg,alrtDivCls);
		}
	}//if(shopNameTxt!='')
	else
	{	
		var msg = 'Please select or Give shop';
		errStyles(msg,alrtDivCls);
	}
});

function errStyles(msg,alrtDivCls)
{
	$('.'+alrtDivCls).html(msg);
	$('.'+alrtDivCls).css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
	$('.'+alrtDivCls).fadeIn('slow');
	$('html,body').animate({scrollTop:0},500);
}

function successStyles(msg,alrtDivCls)
{
	$('.'+alrtDivCls).html(msg);
	$('.'+alrtDivCls).css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
	$('.'+alrtDivCls).fadeIn('slow');
	$('html,body').animate({scrollTop:0},500);
}


$(document).on('click','#clearLocal',function()
{
	localStorage.removeItem('productData');
	$('#RecentOrdersIcon').click();
	$('#RecentOrdersIcon').click();
});

$(document).on('keyup','#shopNameView',function()
{
	if($(this).val()!='')
	{
		var srchShopName = $('#shopNameView').val();
		var srchStr = srchShopName.includes("&");
		var srchStr1 = srchShopName.includes("#");
		if(srchStr)
			var srchShopName = srchShopName.replace("&","!!");
		if(srchStr1)
			var srchShopName = srchShopName.replace("#","@@");
		var appUsrIdLcl = localStorage.getItem('app_userId');	
		$.getJSON(serviceUrl+'searchShops.php?appUsrIdLcl='+appUsrIdLcl+'&SrchShopTxt='+srchShopName,function(data)
		{
			$('#srchShopList').html('');
			$('#srchShopList').html('<p style="text-align:right;margin: 0;"><img src="images/ic_action.png" id="srchCls"></p>');
			var jres = data.Result;
			$.each(jres,function(index,objct)
			{
				if(objct.Status=='Success')
					$('#srchShopList').append('<p style="text-align:center;" srchSId="'+objct.id+'" class="srchShpName">'+ objct.shopName +'</p>');
				if(objct.Status=='NoRows')
					$('#srchShopList').html('<p style="color:red;text-align:center;font-weight:bold;">:: No Records ::</p>');
			});
			$('#srchShopList').fadeIn('fast');
		});
	}
	else
		$('#srchShopList').fadeOut('fast');
});

$(document).on('click','#srchCls',function()
{
	$('#srchShopList').fadeOut('fast');
	$('#shopNameTxt').val('');
});
$(document).on('click','.srchShpName',function()
{
	if(localStorage.getItem('stckOrder'))
		localStorage.removeItem('stckOrder');
	if(localStorage.getItem('stckOrdersDataLcl'))
		localStorage.removeItem('stckOrdersDataLcl');
	$('#orderRprtTbl').hide('slow');
	var srchsid = $(this).attr('srchsid');
	$('#shopNameTxt').val(srchsid);
	$('#shopNameView').val($(this).text());
	$('#shpFullNameTxt').val($(this).text());
	$('#page3ShpSelectLbl').html($(this).text());
	$('#page3ShpSelectDiv').fadeOut('slow');
	$('#page3ShpSelectLbl').fadeIn('slow');
	$('#srchShopList').fadeOut('fast');
	checkOrderDisabled();
});

<!-- Reset password block start -->

$(document).on('click','#rstPswd',function()
{	
	window.location.replace('#page5');
});

$('#page5').on('pageshow',function()
{
	getLatLong();
	$('.resetPwdPage5w').fadeOut('slow');
	$('#getRstPswdForm')[0].reset();
	var userId = localStorage.getItem('app_userId');
	var userMbl = localStorage.getItem('app_user');
	$('#userIdPswdRst').val(userId);
	$('#userMblPswdRst').val(userMbl);
	$('#existPswdRstTxt').val('');
	$('#newPswdRstTxt').val('');
});

$(document).on('click','#rstSbmtBtn',function()
{
	
	var fData = new FormData($('#getRstPswdForm')[0]);
	$.ajax(
	{
		url : serviceUrl +"pswdReset.php",
		type:"POST",
		data:fData,
		contentType:false,
		cache:false,
		processData:false,
		success:function(data)
		{
			var jres = $.parseJSON(data);
			var res = jres.Result;
			if(res.status=='success')
			{
				$('.resetPwdPage5w').html('Your password is changed');
				$('.resetPwdPage5w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.resetPwdPage5w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
				window.location.replace('#page_home');
			}
			if(res.status=='error')
			{	
				$('.resetPwdPage5w').html('Please try later!');
				$('.resetPwdPage5w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.resetPwdPage5w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
			}
			if(res.status=='failed')
			{
				$('.resetPwdPage5w').html('Require : correct exists password!');
				$('.resetPwdPage5w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.resetPwdPage5w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
			}
		}
	});
});
<!-- Reset password block end -->

<!-- Calculate Distance block start -->
$(document).on('click','#calctDist',function()
{
	window.location.replace('#page6');
});
$(document).on('click','#calctDist_unfos',function()
{
	window.location.replace('#page6');
});

$('#page6').on('pageshow',function()
{
	getLatLong();
	$('.distancePage6w').fadeOut('slow');
	$('#calctDistForm')[0].reset();
	var dat = new Date();
	var mnth = dat.getMonth()+1<10?'0'+(dat.getMonth()+1):dat.getMonth()+1;
	var dy = dat.getDate()<10?'0'+dat.getDate():dat.getDate();
	var today_d = dat.getFullYear()+'-'+mnth+'-'+dy;
	$('.srchDateWise').val(today_d);
	$('#srchDate').val(today_d);
	var app_admin = localStorage.getItem('app_admin');
	if(app_admin==1)
	{
		$('#distance_admin').show();
		$('#distance_user').hide();
		$.ajax(
		{
			url : serviceUrl +"getFosList.php",
			type:"GET",
			data:'getFosName=yes',
			contentType:false,
			cache:false,
			processData:false,
			success:function(data)
			{
				console.log(data);
				var jres = $.parseJSON(data).Result;
				$('#distance_fos').html('');
				var flag = 1;
				$.each(jres,function(index,c)
				{
					if(c.status=='success')
						$('#distance_fos').append('<option value="'+c.id+'">&nbsp;'+c.fos_name+'</option>');
					else
					{
						$('#distance_fos').append('<option value="000"> Empty Users</option>');
						flag = 0;
					}
				});
				if(flag==1)
					getShops('admin');
			}
		});		
	}
	else
	{
		$('#distance_admin').hide();
		$('#distance_user').show();
		getShops('user');
	}
});

$(document).on('change','.srchDateWise',function()
{
	$('#srchDate').val($(this).val());
	if($('#distance_fos').val()!='000' && $('#distance_fos').val()!='')
	{
		var dateVal = $(this).val();
		if(dateVal!='')
		{
			var app_admin = localStorage.getItem('app_admin');
			if(app_admin==1)
				getShops('admin');
			else
				getShops('user');
		}
		else
		{	
			$('.distancePage6w').html('Required : Date!');
			$('.distancePage6w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.distancePage6w').fadeIn('slow');
			$('html,body').animate({scrollTop:0},500);
		}
	}
	else
	{
		alert('Please select user!');
	}
});

$(document).on('change','#distance_fos',function()
{
	var app_admin = localStorage.getItem('app_admin');
	if(app_admin==1)
		getShops('admin');
	else
		getShops('user');
});

///////////////////cpk changes///////////////////////
function getShops(b)
{
	var userMbl = localStorage.getItem('app_user'); 
	var app_userId = localStorage.getItem('app_userId');
	var srchDateWise = $('#srchDate').val();
	if(b=='admin')
	{
		app_userId = $('#distance_fos').val();
	}
	var urlData = serviceUrl+'getShops.php?userMbl='+userMbl+'&srchDateWise='+srchDateWise+'&app_userId='+app_userId;

	$.getJSON(urlData,function(data)
	{
		console.log(data);
		var jres = data.Result;
		var cnt = jres.length;
		var kmTotal = 0;
		var runningtottalKM=0;
		var fromTime = 0;
		var toTime = 0;
		var spentTime = 0;
		var mileage = 0;
		var Price = 0;
		if(jres.shopName=='emptySet')
		{
			$('#calcDistDiv').html('<p style="color:red;text-align:center;">No Records!</p>');
		}
		else
		{
			$('#calcDistDiv').html('<p style="width:100%;font-size: 14px;color: white;font-weight: bold;background: cornflowerblue;line-height: 30px;"><span>&nbsp;Party Name</span><span style="float:right;margin-right:10px;padding-left:50px;">Time</span><span style="float:right;margin-right:-20px;">km</span></p>');
			console.log(data);
			$.each(jres,function(index,obb)
			{
				if(index==cnt-1)
				{
					fromTime = obb.fromTime;
					toTime = obb.toTime;
					spentTime = obb.spentTime;
					spentTime = spentTime.split(":");
					spentTime = spentTime[0]+' hrs, '+spentTime[1]+' min';
					mileage = obb.mileage;
					Price = obb.Price;
					$('#calcDistDiv').append('<div style="background: cornflowerblue;padding: 1px 0;color: floralwhite;text-shadow: none;font-weight: bold;border-top: 3px solid white;font-size: 14px;box-shadow: 0px 1px 1px 1px darkgrey;"><table width="100%"><thead><tr><th></th><th></th></tr></thead><tbody id="summeryBody"></tbody></table></div>');
				}
				else
				{
					var indx = index+1;
					if(index!=cnt-1)
					{
						if(obb.attnds_time)
						{
							var attnds_time = obb.attnds_time;
							var splitData = attnds_time.split(":");
						}
						var httm = '';
						httm += '<p style="width:100%;" id="'+obb.shopId+'"><span><strong>'+obb.shopName+'</strong></span>';
						if(index==0)
							httm += '<span style="float:right;margin-right:10px;padding-left:50px;"><strong>'+splitData[0]+':'+splitData[1]+'</strong></span><span style="float:right;margin-right:-20px;">-</span></p>';
						if(index!=0) 	
						{
							var km = obb.km; 
							kmTotal += parseFloat(km);
							runningtottalKM += parseFloat(km);
							if(obb.attnds_time)
								httm += '<span style="float:right;margin-right:10px;"><strong>'+splitData[0]+':'+splitData[1]+'</strong></span>';
							else
								httm += '<span style="float:right;margin-right:10px;"><strong>00:00</strong></span>';
							httm += '<span style="float:right;margin-right:5px;color:blue" id="'+(runningtottalKM).toFixed(1)+'"><strong>'+(runningtottalKM).toFixed(1)+'</strong></span>';
							httm += '<span style="float:right;margin-right:10px;" id="'+km+'"><strong>'+km+'</strong></span>';
						} 	
						httm += '</p>'
					}
					$('#calcDistDiv').append(httm);
				}
			});
			var summery = '';
			summery += '<tr><td>Total Distance Travelled</td><td class="finalTotalKm">0</td></tr>';
			summery += '<tr><td>1st Visit</td><td>'+fromTime+'</td></tr>';
			summery += '<tr><td>Last Visit</td><td>'+toTime+'</td></tr>';
			summery += '<tr><td>Time Spent in Market</td><td>'+spentTime+'</td></tr>';
			summery += '<tr><td>Petrol Allowance</td><td class="finalPetrolAlowns">0</td></tr>';	
			$('#summeryBody').html(summery);
			$('.finalTotalKm').html(Math.round(kmTotal)+' km');
			var totalKm = Math.round(kmTotal);
			console.log(totalKm);
			var petrolTotal = totalKm;
			petrolTotal = totalKm/mileage;
			petrolTotal = Math.round(petrolTotal*Price);
			console.log(petrolTotal+'*'+Price);
			$('.finalPetrolAlowns').html('Rs. '+petrolTotal);
		}
	});
}
///////////////////////////////cpk changes///////////////////////////////

$(document).on('click','.cashTypeRadio',function()
{
	var cashType = $(this).val();
	var rdAttrSts = $(this).attr('bgAttr');
	$('.rls').css({'background':'','color':'','padding': ''});
	$('.'+rdAttrSts).css({'background':'cadetblue','color':'white','padding': '0 3px 0 3px'});
	$('#cashTypeHidTxt').val(cashType);
	var cash_type = $(this).attr('id');
	$('#addPymnt').attr('cash_type',cash_type);
	if(cashType=='cheque')
	{
		$('#neftHidDiv').slideUp('slow');
		$('#cnHidDiv').slideUp('slow');
		$('#chequeHidDiv').slideDown('slow');
		$('#chequeFull').css({"background":"white","height":"165px","padding":"10px","border":"1px solid gray","border-radius":"5px"});
		$('#infoRadiosDiv').css('margin-top','5px');
	}
	else if(cashType=='neft')
	{
		$('#refNmbr').val('');
		$('#neftDate').val('');
		$('#chequeHidDiv').slideUp('slow');
		$('#cnHidDiv').slideUp('slow');
		$('#neftHidDiv').slideDown('slow');
		$('#chequeFull').css({"background":"white","height":"165px","padding":"10px","border":"1px solid gray","border-radius":"5px"});
		$('#infoRadiosDiv').css('margin-top','5px');
	}
	else if(cashType=='cn')
	{
		$('#cnNmbr').val('');
		$('#chequeHidDiv').slideUp('slow');
		$('#neftHidDiv').slideUp('slow');
		$('#cnHidDiv').slideDown('slow');
		$('#chequeFull').css({"background":"white","height":"100px","padding":"10px","border":"1px solid gray","border-radius":"5px"});
		$('#infoRadiosDiv').css('margin-top','5px');
	}
	else
	{
		$('#chequeHidDiv').slideUp('slow');
		$('#neftHidDiv').slideUp('slow');
		$('#cnHidDiv').slideUp('slow');
		$('#chequeFull').css({"background":"","height":"0px","padding":"0","border":"none","border-radius":"0"});
	}
});

/* OTP process in Payment-collection page start*/

$(document).on('click','#otpBtn',function()
{
	var app_user = localStorage.getItem('app_user');
	var app_userId = localStorage.getItem('app_userId');
	var shopNameTxt = $('#shopNamePymnt').val();
	if($('#infoRdBtn').val()=='Staff')
		var otpMbl = $('#ownrMbl').val();
	else
		var otpMbl = $('#ownrMbl1').val();
	$('#otpMblHidd').val(otpMbl);
	var Pymnt_Amnt = $('#pymntTotal').val();
	var infoRdBtn = $('#infoRdBtn').val();
	var flag = 1;
	if(Pymnt_Amnt=='')
	{
		$('.pymntPage4w').html('Required : Amount!');
		$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.pymntPage4w').fadeIn('slow');
		$('html,body').animate({scrollTop:0},500);
		flag = 0;
	}
	if(otpMbl=='000' || otpMbl=='')
	{
		$('.pymntPage4w').html('Required : Customer Mobile Number!');
		$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.pymntPage4w').fadeIn('slow');
		$('html,body').animate({scrollTop:0},500);
		flag = 0;
	}
	if(otpMbl==0)
	{
		$('.pymntPage4w').html('Required : Valid Mobile Number!');
		$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.pymntPage4w').fadeIn('slow');
		$('html,body').animate({scrollTop:0},500);
		flag = 0;
	}
	if(flag==1)
	{
		$.getJSON(serviceUrl+'verifyCode.php?otpPut=yes&app_user='+app_user+'&app_userId='+app_userId,function(data)
		{
			var jres = data.result;
			if(jres.status=='success')
			{
				var pwd = jres.otp;
				var otpDetails = app_userId+','+shopNameTxt;
				sendPassword(otpMbl,pwd,Pymnt_Amnt,otpDetails);
			}
			else
			{
				$('.pymntPage4w').html('Please try later!');
				$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.pymntPage4w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
			}
		});
	}
});

$(document).on('click','#otpResndBtn',function()
{
	var app_user = localStorage.getItem('app_user');
	var app_userId = localStorage.getItem('app_userId');
	var shopNameTxt = $('#shopNamePymnt').val();
	var otpMbl = $('#otpMblHidd').val();
	var Pymnt_Amnt = $('#pymntTotal').val();
	var flag = 1;
	
	if(Pymnt_Amnt=='')
	{
		$('.pymntPage4w').html('Required : Amount!');
		$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.pymntPage4w').fadeIn('slow');
		$('html,body').animate({scrollTop:0},500);
		flag = 0;
	}
	else if(otpMbl=='000')
	{
		$('.pymntPage4w').html('Required : Customer Mobile Number!');
		$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.pymntPage4w').fadeIn('slow');
		$('html,body').animate({scrollTop:0},500);
		flag = 0;
	}
	if(flag==1)
	{
		$.getJSON(serviceUrl+'verifyCode.php?otpPut=yes&app_user='+app_user+'&app_userId='+app_userId,function(data)
		{
			var jres = data.result;
			if(jres.status=='success')
			{
				var pwd = jres.otp;
				var otpDetails = app_userId+','+shopNameTxt;
				sendPassword(otpMbl,pwd,Pymnt_Amnt,otpDetails);
			}
			else
			{
				$('.pymntPage4w').html('Please try later!');
				$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.pymntPage4w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
			}
		});
	}
});

function sendPassword(otpMbl,pwd,Pymnt_Amnt,otpDetails)
{
	var otpDetails = otpDetails;
	$.ajax(
	{
	   url: serviceUrl + "smspassword.php",   	
	   type: "GET",  
	   data:  'number=' + otpMbl + '&pwd=' + pwd + '&Pymnt_Amnt=' + Pymnt_Amnt + '&otpDetails=' +otpDetails, 
	   contentType: false,       		
       cache: false,					
	   processData:false,      				
	   success: function(response)
	   {
		   console.log(response);
		   var smsResult =  $.parseJSON(response);
		   sms = smsResult.result;
		   console.log(smsResult);
		   var errCode = sms.ErrorCode;
		   var msgId = sms.msgId;
		   var message = sms.message;
		   if(errCode == '017')
		   {
			  	$('.pymntPage4w').html('This number is mapped to another shop.');
				$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.pymntPage4w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
				return 0;
		   }		   
		   if(errCode == '018')
		   {
			  	$('.pymntPage4w').html('Unable to Send OTP For this number');
				$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.pymntPage4w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
				return 0;
		   }
		   if( errCode == '013' || errCode == '014')
		   {
			  	$('.pymntPage4w').html('Please enter valid Mobile Number');
				$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.pymntPage4w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
		   }
		   if(errCode != '013' && errCode != '014' && errCode != '000')
		   {
			  	$('.pymntPage4w').html('We have trouble in Sending your Password');
				$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.pymntPage4w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
		   }
		   if( errCode == '000')
		   {
			 	if(pwd=='smsonly')
				{	
					$('.pymntPage4w').html('Payment confirmation sent to partner mobile');
					$('.pymntPage4w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
					$('.pymntPage4w').fadeIn('slow');
					$('html,body').animate({scrollTop:0},500);
				}
				else if(pwd=='orderCnfrm')
				{
					$('.pymntPage4w').html('Order received message sent to partner mobile');
					$('.pymntPage4w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
					$('.pymntPage4w').fadeIn('slow');
					$('html,body').animate({scrollTop:0},500);
				}
				else if(pwd=='deliveryCnfrm')
				{
					$('.pymntPage4w').html('Order delivered message sent to partner mobile');
					$('.pymntPage4w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
					$('.pymntPage4w').fadeIn('slow');
					$('html,body').animate({scrollTop:0},500);
				}
				else if(Pymnt_Amnt=='popupOtpMsg')
				{
					otpDetails = otpDetails.split(",");
					var userId = otpDetails[0];
					var shpId = otpDetails[1];
					var recvrMbl = otpDetails[2];
					$.post(serviceUrl+'schemes.php',{putOtpMsgRefId:'yes',req:'pymntPopupOTP',msgId:msgId,message:message,userId:userId,shpId:shpId,msgType:'OTP',recvrMbl:recvrMbl},function(data)
					{
						console.log(data);
					});
					$('#cnfrmOtpPopupDiv').fadeOut('slow');
					$('#cnfrmOtpPopupDiv2').fadeIn('slow');
				}
				else if(Pymnt_Amnt=='popupOtpMsg_order')
				{
					otpDetails = otpDetails.split(",");
					var userId = otpDetails[0];
					var shpId = otpDetails[1];
					var recvrMbl = otpDetails[2];
					$.post(serviceUrl+'schemes.php',{putOtpMsgRefId:'yes',req:'orderPopupOTP',msgId:msgId,message:message,userId:userId,shpId:shpId,msgType:'OTP',recvrMbl:recvrMbl},function(data)
					{
						console.log(data);
					});
					$('#cnfrmOtpPopupDiv_order').fadeOut('slow');
					$('#cnfrmOtpPopupDiv2_order').fadeIn('slow');					
				}
				else
				{
					otpDetails = otpDetails.split(",");
					var userId = otpDetails[0];
					var shpId = otpDetails[1];
					$.post(serviceUrl+'schemes.php',{putOtpMsgRefId:'yes',req:'pymntOTP',msgId:msgId,message:message,userId:userId,shpId:shpId,msgType:'OTP'},function(data)
					{
						console.log(data);
					});
					$('.pymntPage4w').html('One Time Password(OTP) has sent to given number');
					$('.pymntPage4w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
					$('.pymntPage4w').fadeIn('slow');
					$('html,body').animate({scrollTop:0},500);
					$('#otpDiv').fadeOut('slow');
					$('#otpCnfrmDiv').fadeIn('slow');
					$('#otpResndBtn').prop('disabled',true);
					$('#otpResndBtn').parent().css({"background-color":"red","color":"white","text-shadow":"none"});
					$('.pymntPage4w').html('Re-send button will be enabled after 1-minute');
					$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
					$('.pymntPage4w').fadeIn('slow');
					$('html,body').animate({scrollTop:0},500);
					setTimeout(function()
					{ 
						$('#otpResndBtn').prop('disabled',false);
						$('#otpResndBtn').parent().css({"background-color":"green","color":"white","text-shadow":"none"});
					}, 60000);
					$('#otpCnfrm').val('');
					$('#Pymnt_Amnt').prop('disabled',true);
					$('#otpCnfrm').prop('disabled',false);
					$('#ownrName').prop('disabled',true);
					$('#ownrEmail').prop('disabled',true);
					$('.c_rd').prop('disabled',true);
					
				}
		   }
	   }
   });
}

$(document).on('click','#otpCnfrmOkBtn',function()
{
	var app_user = localStorage.getItem('app_user');
	var app_userId = localStorage.getItem('app_userId');
	var otpCnfrmOkTxt = $('#otpCnfrm').val();
	var flag = 1;
	if(otpCnfrmOkTxt=='')
	{
		$('.pymntPage4w').html('Please enter OTP!');
		$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.pymntPage4w').fadeIn('slow');
		$('html,body').animate({scrollTop:0},500);
		flag = 0;
	}
	if(flag==1)
	{
		$.getJSON(serviceUrl+'verifyCode.php?app_user='+app_user+'&app_userId='+app_userId+'&cnfrmCode='+otpCnfrmOkTxt,function(data)
		{
			var jres = data.result;
			if(jres.status=='success')
			{
				$('.pymntPage4w').html('OTP is Successfully verified');
				$('.pymntPage4w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.pymntPage4w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
				$('#getPymnt_btn').prop('disabled',false);
				$('#otpCnfrm').prop('disabled',true);
				$('#otpCnfrmOkBtn').prop('disabled',true);
				$('#otpResndBtn').prop('disabled',true);
				$('#getPymnt_btn').parent().css({"background-color":"green","color":"white","text-shadow":"none"});
			}
			else
			{
				$('#getPymnt_btn').parent().css({"background-color":"#3188cb","color":"white","text-shadow":"none"});
				$('.pymntPage4w').html('Please give correct Otp!');
				$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.pymntPage4w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
			}
		});
	}
});

/* OTP End*/

/* Outstanding , Stock in Hand , Sales page script start */
$(document).on('click','#m_outstanding',function()
{
	location.replace('#page7');
});

$('#page7').on('pageshow',function()
{
	getLatLong();
	$('#page7ShpSelectLbl').fadeOut('slow');
	$('#page7ShpSelectDiv').fadeIn('slow');
	$('.oustndPage7w').fadeOut('slow');
	$('#outStndForm')[0].reset();
	$('#AddedOutstndsDiv').hide();
	$('#shpNameOutstnds').html('<option value=""><-- : select shop : --></option>');
	var isOff = 'onLine' in navigator && !navigator.onLine;

	if ( isOff ) {
    	onFailedPmnts();
	}
	else {
		getCurrentPosition6();
	}

	$('#outStndDiv').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Outlet Name</span><span style="float:right;">Due</span></p>');
	var app_userId = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl+'getOutstanding.php?app_userId='+app_userId,function(data)
	{
		var totlOutstnds = 0;
		var jres = data.Result;
		if(jres.length!=0)
		{
			$.each(jres,function(index,objj)
			{
				if(objj.Status=='success')
				{
					totlOutstnds = parseInt(totlOutstnds)+parseInt(objj.pending_amount);
					$('#outStndDiv').append('<div style="width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="outstnd'+index+'" party_name="'+objj.party_name+'" class="partyNameCls"><span style="width:80%;word-wrap:break-word">'+objj.party_name+'</span><span style="width:19%;float:right;text-align:right;">'+Math.round(objj.pending_amount)+'</span></div><div class="partyHidDataDiv" id="outstnd'+index+'" style="display:none;"></div><hr>');
				}
				else
					$('#outStndDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
			});
		}
		else
			$('#outStndDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
		$('.totlOutstnd').html(Math.round(parseInt(totlOutstnds)));
	});
});

$(document).on('click','.partyNameCls',function()
{
	$('.partyNameCls').removeClass('greenClr');
	$(this).addClass('greenClr');
	$('.partyHidDataDiv').html('');
	$('.partyHidDataDiv').slideUp('slow');
	var party_name = $(this).attr('party_name');
	var srchPName = party_name.includes("&");
	var srchPName1 = party_name.includes("#");
	if(srchPName)	
		var party_name = party_name.replace("&","!!");
	if(srchPName1)	
		var party_name = party_name.replace("#","@@");
		
	var app_userId = localStorage.getItem('app_userId');
	var uId = $(this).attr('uId');
	$('#'+uId).html('<table border="1" width="100%" style="border-collapse: collapse;text-align:center;"><tr style="font-weight:bold;"><th>Date</th><th>RefNo</th><th>p_amt</th><th>o_due</th></tr>');
	var outstnd_res = $('#outstnd_res').val();
	$.getJSON(serviceUrl+'getOutstanding.php?app_userId='+app_userId+'&party_name='+party_name+'&outstnd_res='+outstnd_res,function(data)
	{
		var jres = data.Result;
		$.each(jres,function(index,objj)
		{
			if(objj.Status=='success')
			{
				var refNo = objj.ref_no;
				var start   = refNo.lastIndexOf('/');
				var end = refNo.length;
				var refNoFinal = refNo.substring(start+1,end);
				
				$('#'+uId+' table').append('<tr style="text-align: center;"><td>'+objj.outstanding_date+'</td><td>'+refNoFinal+'</td><td>'+objj.pending_amount+'</td><td>'+objj.overdue+'</td></tr>');	
			}
			else
			{
				$('#'+uId).html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
			}
		});
		$('#'+uId).append('</table>');
		$('#'+uId).slideDown('slow');
	});
});


function getCurrentPosition6(){
	navigator.geolocation.getCurrentPosition(
		onSuccessOutstnds,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					onSuccessOutstnds, 
					function(error){
							console.log('gps error');
						},
					geoOptionsLow);
			 	return;
				}
				console.log('gps error');	
			}, 
		geoOptionsHigh
	); 
}



/* shop select and search function start */

function onSuccessOutstnds(position)
{
	var Shp_lat  = position.coords.latitude;
	var Shp_long = position.coords.longitude;
	var appUsrIdLcl = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl +'NearestShop.php?appUsrIdLcl='+appUsrIdLcl+'&lat='+Shp_lat+'&long='+Shp_long,function(data)
	{
		$('#shopNameOutstnds').html('<option value=""><-- : select shop : --></option>');
		var jres = data.Result;
		$.each(jres,function(index,obj)
		{
			if(obj.Status=='success')
			{
				$('#shpNameOutstnds').append('<option value="'+obj.shopName+'">'+obj.shopName+'</option>');
			}
			if(obj.shopName=='emptySet')
			{
				$('#shpNameOutstnds').html('<option value=""><---: Select current shop :---></option><option>No shops found !!</option>');
			}
		});
	});
}
function onFailedPmnts()
{
	window.plugins.toast.showLongCenter(':: No internet connectivity ::');
}
$(document).on('change','#shpNameOutstnds',function()
{
	var shpId = $(this).val();
	$('#shopNameOutstnds').val(shpId);
});

$(document).on('keyup','#shopNameOutstndsView',function()
{
	if($(this).val()!='')
	{
		var srchShopName = $('#shopNameOutstndsView').val();
		var srchStr = srchShopName.includes("&");
		var srchStr1 = srchShopName.includes("#");
		if(srchStr)
			var srchShopName = srchShopName.replace("&","!!");
		if(srchStr1)
			var srchShopName = srchShopName.replace("#","@@");
		var appUsrIdLcl = localStorage.getItem('app_userId');	
		$.getJSON(serviceUrl+'searchShops.php?appUsrIdLcl='+appUsrIdLcl+'&SrchShopTxt='+srchShopName,function(data)
		{
			$('#srchOutstndsList').html('');
			$('#srchOutstndsList').html('<p style="text-align:right;margin: 0;"><img src="images/ic_action.png" id="srchOutstndCls"></p>');
			var jres = data.Result;
			$.each(jres,function(index,objct)
			{
				if(objct.Status=='Success')
					$('#srchOutstndsList').append('<p style="text-align:center;" srchSId="'+objct.id+'" class="srchShpOutstndName">'+ objct.shopName +'</p>');
				if(objct.Status=='NoRows')
					$('#srchOutstndsList').html('<p style="color:red;text-align:center;font-weight:bold;">:: No Records ::</p>');
			});
			$('#srchOutstndsList').fadeIn('fast');
		});
	}
	else
		$('#srchOutstndsList').fadeOut('fast');
});

$(document).on('click','#srchOutstndCls',function()
{
	$('#srchOutstndsList').fadeOut('fast');
	$('#shpNameOutstnds').val('');
});

$(document).on('click','.srchShpOutstndName',function()
{
	if($(this).text()!='')
	{
		$('#srchOutstndsList').fadeOut('slow');
		$('#shopNameOutstndsView').val('');
		var days = $('#outstnd_res').val();
		if(days=='')
			var days = 0;
		var shpId = $(this).text();
		var srchShpsName = $(this).text();
		$('#shpNameOutstndsTxt').val(srchShpsName);
		$('#page7ShpSelectLbl').html(srchShpsName);
		$('#page7ShpSelectDiv').fadeOut('slow');
		$('#page7ShpSelectLbl').fadeIn('slow');
		
		var srchStr = srchShpsName.includes("&");
		var srchStr1 = srchShpsName.includes("#");
		if(srchStr)
			var srchShpsName = srchShpsName.replace("&","!!");
		if(srchStr1)
			var srchShpsName = srchShpsName.replace("#","@@");
		
		$('#shopNameOutstnds').val(shpId);
		var srchShopName = $('#shopNameOutstnds').val();
		$('#outStndDiv').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Outlet Name</span><span style="float:right;">Due</span></p>');
		var app_userId = localStorage.getItem('app_userId');
		$.getJSON(serviceUrl+'getOutstanding.php?app_userId='+app_userId+'&srchShpsName='+srchShpsName+'&days='+days,function(data)
		{
			var totlOutstnds = 0;
			var jres = data.Result;
			if(jres.length!=0)
			{
				$.each(jres,function(index,objj)
				{
					if(objj.Status=='success')
					{
						totlOutstnds = parseInt(totlOutstnds)+parseInt(objj.pending_amount);
						$('#outStndDiv').append('<div style="width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="outstnd'+index+'" party_name="'+objj.party_name+'" class="partyNameCls"><span style="width:80%;word-wrap:break-word">'+objj.party_name+'</span><span style="width:19%;float:right;text-align:right;">'+Math.round(objj.pending_amount)+'</span></div><div class="partyHidDataDiv" id="outstnd'+index+'" style="display:none;"></div><hr>');
					}
					else
						$('#outStndDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
				});
			}
			else
				$('#outStndDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
			$('.totlOutstnd').html(Math.round(parseInt(totlOutstnds)));
		});
	}
});

$(document).on('change','#shpNameOutstnds',function()
{
	if($(this).val()!='')
	{
		$('#shopNameOutstndsView').val('');
		var days = $('#outstnd_res').val();
		if(days=='')
			var days = 0;
		var shpId = $(this).find("option:selected").text();
		var srchShpsName = $(this).find("option:selected").text();
		$('#shpNameOutstndsTxt').val(srchShpsName);
		$('#page7ShpSelectLbl').html(srchShpsName);
		$('#page7ShpSelectDiv').fadeOut('slow');
		$('#page7ShpSelectLbl').fadeIn('slow');
		
		var srchStr = srchShpsName.includes("&");
		var srchStr1 = srchShpsName.includes("#");
		if(srchStr)
			var srchShpsName = srchShpsName.replace("&","!!");
		if(srchStr1)
			var srchShpsName = srchShpsName.replace("#","@@");
		
		$('#shopNameOutstnds').val(shpId);
		var srchShopName = $('#shopNameOutstnds').val();
		$('#outStndDiv').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Outlet Name</span><span style="float:right;">Due</span></p>');
		var app_userId = localStorage.getItem('app_userId');
		$.getJSON(serviceUrl+'getOutstanding.php?app_userId='+app_userId+'&srchShpsName='+srchShpsName+'&days='+days,function(data)
		{
			var totlOutstnds = 0;
			var jres = data.Result;
			if(jres.length!=0)
			{
				$.each(jres,function(index,objj)
				{
					if(objj.Status=='success')
					{
						totlOutstnds = parseInt(totlOutstnds)+parseInt(objj.pending_amount);
						$('#outStndDiv').append('<div style="width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="outstnd'+index+'" party_name="'+objj.party_name+'" class="partyNameCls"><span style="width:80%;word-wrap:break-word">'+objj.party_name+'</span><span style="width:19%;float:right;text-align:right;">'+Math.round(objj.pending_amount)+'</span></div><div class="partyHidDataDiv" id="outstnd'+index+'" style="display:none;"></div><hr>');
					}
					else
						$('#outStndDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
				});
			}
			else
				$('#outStndDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
			$('.totlOutstnd').html(Math.round(parseInt(totlOutstnds)));
		});
	}
	else 	
		location.reload();
});

/* shop select and search function start */

$(document).on('click','.outstndRadio',function()
{
	var days = $(this).val();
	$('#shopNameOutstndsView').val('');
	var shpName = $('#shpNameOutstndsTxt').val();
	$('#outstnd_res').val(days);
	$('#outStndDiv').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Outlet Name</span><span style="float:right;">Due</span></p>');
	var app_userId = localStorage.getItem('app_userId');
	var srchShpsName = $(this).val();
		
	if(shpName=='')
	{
		var srUrl = serviceUrl+'getOutstanding.php?app_userId='+app_userId+'&daysWise='+days;
	}
	else
	{	
		var srchStr = shpName.includes("&");
		var srchStr1 = shpName.includes("#");
		if(srchStr)
			var shpName = shpName.replace("&","!!");
		if(srchStr1)
			var shpName = shpName.replace("#","@@");
		var srUrl = serviceUrl+'getOutstanding.php?app_userId='+app_userId+'&daysWise_val='+days+'&shpName='+shpName;
	}
	$.getJSON(srUrl,function(data)
	{
		var totlOutstnds = 0;
		var jres = data.Result;
		if(jres.length!=0)
		{
			$.each(jres,function(index,objj)
			{
				if(objj.Status=='success')
				{
					totlOutstnds = parseInt(totlOutstnds)+parseInt(objj.pending_amount);
					$('#outStndDiv').append('<div style="width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="outstnd'+index+'" party_name="'+objj.party_name+'" class="partyNameCls"><span style="width:80%;word-wrap:break-word">'+objj.party_name+'</span><span style="width:19%;float:right;text-align:right;">'+Math.round(objj.pending_amount)+'</span></div><div class="partyHidDataDiv" id="outstnd'+index+'" style="display:none;"></div><hr>');
				}
				else
					$('#outStndDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
			});
		}
		else
			$('#outStndDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
		$('.totlOutstnd').html(Math.round(parseInt(totlOutstnds)));
	});
});
/* Outstanding page script end */

/* Sales Page script Start */

$(document).on('click','#m_sales',function()
{
	window.location.replace('#page8');
});
$('#page8').on('pageshow',function()
{
	getLatLong();
	$('#page8ShpSelectLbl').fadeOut('slow');
	$('#page8ShpSelectDiv').fadeIn('slow');
	$('.salesPage8w').fadeOut('slow');
	$('#salesForm')[0].reset();
	$('#AddedSalesDiv').hide();
	$('#shpNameSales').html('<option value=""><-- : select shop : --></option>');
	var isOff = 'onLine' in navigator && !navigator.onLine;

	if ( isOff ) {
    	onFailedPmnts();
	}
	else {
		getCurrentPosition7();
	}
	setMonths();
	$('#salesDiv').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Outlet Name</span><span style="float:right;">Value</span></p>');
	var app_userId = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl+'getSales.php?app_userId='+app_userId,function(data)
	{
		var jres = data.Result;
		var totlSales = 0;
		if(jres.length!=0)
		{
			$.each(jres,function(index,objj)
			{
				if(objj.Status=='success')
				{
					totlSales = parseInt(totlSales)+parseInt(objj.debit_amount);
					$('#salesDiv').append('<div style="width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="sales'+index+'" particulars="'+objj.particulars+'" class="partyNameCls1"><span style="width:80%;word-wrap:break-word">'+objj.particulars+'</span><span style="width:19%;float:right;text-align:right;">'+Math.round(objj.debit_amount)+'</span></div><div class="partyHidDataDiv" id="sales'+index+'" style="display:none;"></div><hr>');
				}
				else
					$('#salesDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
			});
		}
		else
		{	
			$('#salesDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
			$('.totlSales').html(0);
		}
		$('.totlSales').html(totlSales);
	});
});

$(document).on('click','.partyNameCls1',function()
{
	$('.partyNameCls1').removeClass('greenClr');
	$(this).addClass('greenClr');
	$('.partyHidDataDiv').html('');
	$('.partyHidDataDiv').slideUp('slow');
	var particulars = $(this).attr('particulars');
	var srchPatcls = particulars.includes("&");
	var srchPatcls1 = particulars.includes("#");
	if(srchPatcls)	
		var particulars = particulars.replace("&","!!");
	if(srchPatcls1)	
		var particulars = particulars.replace("#","@@");
	var app_userId = localStorage.getItem('app_userId');
	var uId = $(this).attr('uId');
	var days = $('#sales_res').val();
	var mnthN = $('#monthwiseSalesNew').val();
	if(mnthN=='' || mnthN=='000' || mnthN=='0')
		mnthN = 'empty';
	if(days=='')
		days = 0;
	$('#'+uId).html('<table border="1" width="100%" style="border-collapse: collapse;text-align:center;"><tr style="font-weight:bold;"><th>Date</th><th>VchNo</th><th>Value</th><th>vch_type</th></tr>');
	$.getJSON(serviceUrl+'getSales.php?app_userId='+app_userId+'&particulars='+particulars+'&days='+days+'&mnthN='+mnthN,function(data)
	{
		var jres = data.Result;
		$.each(jres,function(index,objj)
		{
			if(objj.Status=='success')
			{
				var vch_no = objj.vch_no;
				var start   = vch_no.lastIndexOf('/');
				var end = vch_no.length;
				var vchNoFinal = vch_no.substring(start+1,end);
				
				$('#'+uId+' table').append('<tr style="text-align: center;"><td>'+objj.sales_date+'</td><td>'+vchNoFinal+'</td><td>'+objj.debit_amount+'</td><td>'+objj.vch_type+'</td></tr>');	
			}
			else
			{
				$('#'+uId).html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
			}
		});
		$('#'+uId).append('</table>');
		$('#'+uId).slideDown('slow');
	});
});


function getCurrentPosition7(){
	navigator.geolocation.getCurrentPosition(
		onSuccessSales,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					onSuccessSales, 
					function(error){
							console.log('gps error');
						},
					geoOptionsLow);
			 	return;
				}
				console.log('gps error');	
			}, 
		geoOptionsHigh
	); 
}


/* shop select and search function start */

function onSuccessSales(position)
{
	var Shp_lat  = position.coords.latitude;
	var Shp_long = position.coords.longitude;
	var appUsrIdLcl = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl +'NearestShop.php?appUsrIdLcl='+appUsrIdLcl+'&lat='+Shp_lat+'&long='+Shp_long,function(data)
	{
		$('#shopNameSales').html('<option value=""><-- : select shop : --></option>');
		var jres = data.Result;
		$.each(jres,function(index,obj)
		{
			if(obj.Status=='success')
			{
				$('#shpNameSales').append('<option value="'+obj.shopName+'">'+obj.shopName+'</option>');
			}
			if(obj.shopName=='emptySet')
			{
				$('#shpNameSales').html('<option value=""><---: Select current shop :---></option><option>No shops found !!</option>');
			}
		});
	});
}
function onFailedPmnts()
{
	window.plugins.toast.showLongCenter(':: No internet connectivity ::');
}
$(document).on('change','#shpNameSales',function()
{
	var shpId = $(this).val();
	$('#shopNameSales').val(shpId);
});

$(document).on('keyup','#shopNameSalesView',function()
{
	if($(this).val()!='')
	{
		var srchShopName = $('#shopNameSalesView').val();
		var srchStr = srchShopName.includes("&");
		var srchStr1 = srchShopName.includes("#");
		if(srchStr)
			var srchShopName = srchShopName.replace("&","!!");
		if(srchStr1)
			var srchShopName = srchShopName.replace("#","@@");
		var appUsrIdLcl = localStorage.getItem('app_userId');	
		$.getJSON(serviceUrl+'searchShops.php?appUsrIdLcl='+appUsrIdLcl+'&SrchShopTxt='+srchShopName,function(data)
		{
			$('#srchSalesList').html('');
			$('#srchSalesList').html('<p style="text-align:right;margin: 0;"><img src="images/ic_action.png" id="srchSalesCls"></p>');
			var jres = data.Result;
			$.each(jres,function(index,objct)
			{
				if(objct.Status=='Success')
					$('#srchSalesList').append('<p style="text-align:center;" srchSId="'+objct.id+'" class="srchShpSalesName">'+ objct.shopName +'</p>');
				if(objct.Status=='NoRows')
					$('#srchSalesList').html('<p style="color:red;text-align:center;font-weight:bold;">:: No Records ::</p>');
			});
			$('#srchSalesList').fadeIn('fast');
		});
	}
	else
		$('#srchSalesList').fadeOut('fast');
});

$(document).on('click','#srchSalesCls',function()
{
	$('#srchSalesList').fadeOut('fast');
	$('#shpNameSales').val('');
});

$(document).on('click','.srchShpSalesName',function()
{
	if($(this).text()!='')
	{
		$('#srchSalesList').fadeOut('slow');
		$('#shopNameSalesView').val('');
		var days = $('#sales_res').val();
		if(days=='')
			var days = 0;
		var shpId = $(this).text();
		var srchShpsName = $(this).text();
		$('#shpNameSalesTxt').val(srchShpsName);
		$('#page8ShpSelectLbl').html(srchShpsName);
		$('#page8ShpSelectDiv').fadeOut('slow');
		$('#page8ShpSelectLbl').fadeIn('slow');
		
		var srchStr = srchShpsName.includes("&");
		var srchStr1 = srchShpsName.includes("#");
		if(srchStr)
			var srchShpsName = srchShpsName.replace("&","!!");
		if(srchStr1)
			var srchShpsName = srchShpsName.replace("#","@@");
		
		var mnthM = $('#monthwiseSalesNew').val();	
		if(mnthM=='' || mnthM=='000' || mnthM==0)
			mnthM = 'empty';
		$('#shopNameSales').val(shpId);
		var srchShopName = $('#shopNameSales').val();
		$('#salesDiv').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Outlet Name</span><span style="float:right;">Value</span></p>');
		var app_userId = localStorage.getItem('app_userId');
		$.getJSON(serviceUrl+'getSales.php?app_userId='+app_userId+'&srchShpsName='+srchShpsName+'&days='+days+'&mnthM='+mnthM,function(data)
		{
			console.log(data);
			var jres = data.Result;
			var totlSales = 0;
			if(jres.length!=0)
			{
				$.each(jres,function(index,objj)
				{
					if(objj.Status=='success')
					{
						totlSales = parseInt(totlSales)+parseInt(objj.debit_amount);
						$('#salesDiv').append('<div style="width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="outstnd'+index+'" particulars="'+objj.particulars+'" class="partyNameCls1"><span style="width:80%;word-wrap:break-word">'+objj.particulars+'</span><span style="width:19%;float:right;text-align:right;">'+Math.round(objj.debit_amount)+'</span></div><div class="partyHidDataDiv" id="outstnd'+index+'" style="display:none;"></div><hr>');
					}
					else
						$('#salesDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
				});
			}
			else
			{	
				$('#salesDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
				$('.totlSales').html(0);
			}
			$('.totlSales').html(totlSales);
		});
	}
});

$(document).on('change','#shpNameSales',function()
{
	if($(this).val()!='')
	{
		$('#shopNameSalesView').val('');
		var days = $('#sales_res').val();
		if(days=='')
			var days = 0;
		var shpId = $(this).find("option:selected").text();
		var srchShpsName = $(this).find("option:selected").text();
		$('#shpNameSalesTxt').val(srchShpsName);
		$('#page8ShpSelectLbl').html(srchShpsName);
		$('#page8ShpSelectDiv').fadeOut('slow');
		$('#page8ShpSelectLbl').fadeIn('slow');
		
		var srchStr = srchShpsName.includes("&");
		var srchStr1 = srchShpsName.includes("#");
		if(srchStr)
			var srchShpsName = srchShpsName.replace("&","!!");
		if(srchStr1)
			var srchShpsName = srchShpsName.replace("#","@@");
			
		var mnthM = $('#monthwiseSalesNew').val();	
		if(mnthM=='' || mnthM=='000' || mnthM=='0')
			mnthM = 'empty';
		$('#shopNameSales').val(shpId);
		var srchShopName = $('#shopNameSales').val();
		$('#salesDiv').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Outlet Name</span><span style="float:right;">Value</span></p>');
		var app_userId = localStorage.getItem('app_userId');
		$.getJSON(serviceUrl+'getSales.php?app_userId='+app_userId+'&srchShpsName='+srchShpsName+'&days='+days+'&mnthM='+mnthM,function(data)
		{
			var jres = data.Result;
			var totlSales = 0;
			if(jres.length!=0)
			{
				$.each(jres,function(index,objj)
				{
					if(objj.Status=='success')
					{
						totlSales = parseInt(totlSales)+parseInt(objj.debit_amount);
						$('#salesDiv').append('<div style="width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="outstnd'+index+'" particulars="'+objj.particulars+'" class="partyNameCls1"><span style="width:80%;word-wrap:break-word">'+objj.particulars+'</span><span style="width:19%;float:right;text-align:right;">'+Math.round(objj.debit_amount)+'</span></div><div class="partyHidDataDiv" id="outstnd'+index+'" style="display:none;"></div><hr>');
					}
					else
						$('#salesDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
				});
			}
			else
			{	
				$('#salesDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
				$('.totlSales').html(0);
			}
			$('.totlSales').html(totlSales);
		});
	}
});

/* shop select and search function start */

$(document).on('click','.salesRadio',function()
{
	$('#monthwiseSalesNew').val('0');
	$('#shopNameSalesView').val('');
	$('#shpNameSales').val('');
	var shpName = $('#shpNameSalesTxt').val();
	var days = $(this).val();
	$('#sales_res').val(days);
	$('#salesDiv').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Outlet Name</span><span style="float:right;">Value</span></p>');
	var app_userId = localStorage.getItem('app_userId');
	var srchShpsName = $(this).val();
	if(shpName=='')
		var srUrl = serviceUrl+'getSales.php?app_userId='+app_userId+'&daysWise='+days;
	else
	{
		var srchStr = shpName.includes("&");
		var srchStr1 = shpName.includes("#");
		if(srchStr)
			var shpName = shpName.replace("&","!!");
		if(srchStr1)
			var shpName = shpName.replace("#","@@");
		var srUrl = serviceUrl+'getSales.php?app_userId='+app_userId+'&daysWise_val='+days+'&shpName='+shpName;
	}
	$.getJSON(srUrl,function(data)
	{
		var jres = data.Result;
		var totlSales = 0;
		if(jres.length!=0)
		{
			$.each(jres,function(index,objj)
			{
				if(objj.Status=='success')
				{
					totlSales = parseInt(totlSales)+parseInt(objj.debit_amount);
					$('#salesDiv').append('<div style="width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="outstnd'+index+'" particulars="'+objj.particulars+'" class="partyNameCls1"><span style="width:80%;word-wrap:break-word">'+objj.particulars+'</span><span style="width:19%;float:right;text-align:right;">'+Math.round(objj.debit_amount)+'</span></div><div class="partyHidDataDiv" id="outstnd'+index+'" style="display:none;"></div><hr>');
				}
				else
					$('#salesDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
			});
		}
		else
		{	
			$('#salesDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
			$('.totlSales').html(0);
		}
		$('.totlSales').html(totlSales);
	});
});
/* Outstanding page script end */

/* Sales Page script end */
/* --------------------------------------------------------------------------------------------------- */
/* Delivery Page script start */

$(document).on('click','#delivery',function()
{
	window.location.replace('#page9');
});
$('#page9').on('pageshow',function()
{
	getLatLong();
	localStorage.removeItem('chBoxUncIds');
	$('.deliveryPage9w').fadeOut('slow');
	$('#DeliveryForm')[0].reset();
	$('#shopNameDeliveryView').val('');
	$('.hidncls').hide();
	$('.hidncls1').hide();
	$('#AddedDeliveryDiv').hide();
	$('#shpNameDelivery').html('<option value=""><-- : select shop : --></option>');
	var isOff = 'onLine' in navigator && !navigator.onLine;

	if ( isOff ) {
    	onFailedPmnts();
	}
	else {
		getCurrentPosition8();
	}
});


function getCurrentPosition8(){
	navigator.geolocation.getCurrentPosition(
		onSuccessDelivery,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					onSuccessDelivery, 
					function(error){
							console.log('gps error');
						},
					geoOptionsLow);
			 	return;
				}
				console.log('gps error');	
			}, 
		geoOptionsHigh
	); 
}


/* shop select and search function start */

function onSuccessDelivery(position)
{
	var Shp_lat  = position.coords.latitude;
	var Shp_long = position.coords.longitude;
	var appUsrIdLcl = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl +'NearestShop.php?appUsrIdLcl='+appUsrIdLcl+'&getAllShp=yes&lat='+Shp_lat+'&long='+Shp_long,function(data)
	{
		$('#shopNameDelivery').html('');
		$('#shopNameDelivery').html('<option value=""><-- : select shop : --></option>');
		var jres = data.Result;
		$.each(jres,function(index,obj)
		{
			if(obj.Status=='success')
			{
				$('#shpNameDelivery').append('<option value="'+obj.shopId+'">'+obj.shopName+'</option>');
			}
			if(obj.shopName=='emptySet')
			{
				$('#shpNameDelivery').html('<option value=""><---: Select current shop :---></option><option>No shops found !!</option>');
			}
		});
	});
}
function onFailedPmnts()
{
	window.plugins.toast.showLongCenter(':: No internet connectivity ::');
}

$(document).on('keyup','#shopNameDeliveryView',function()
{
	if($(this).val()!='')
	{
		$('#srchDeliveryList').html('<p style="text-align:right;margin: 0;"><img src="images/ic_action.png" id="srchCls2"></p>');
		var srchShopName = $('#shopNameDeliveryView').val();
		var srchStr = srchShopName.includes("&");
		var srchStr1 = srchShopName.includes("#");
		if(srchStr)
			var srchShopName = srchShopName.replace("&","!!");
		if(srchStr1)
			var srchShopName = srchShopName.replace("#","@@");
		var appUsrIdLcl = localStorage.getItem('app_userId');	
		$.getJSON(serviceUrl+'searchShops.php?appUsrIdLcl='+appUsrIdLcl+'&SrchShopTxt='+srchShopName,function(data)
		{
			var jres = data.Result;
			$.each(jres,function(index,objct)
			{
				if(objct.Status=='Success')
					$('#srchDeliveryList').append('<p style="text-align:center;" srchSId="'+objct.id+'" class="srchShpName3">'+ objct.shopName +'</p>');
				if(objct.Status=='NoRows')
					$('#srchDeliveryList').html('<p style="color:red;text-align:center;font-weight:bold;">:: No Records ::</p>');
			});
			$('#srchDeliveryList').fadeIn('fast');
		});
	}
	else
	{	
		location.reload();
	}
});

$(document).on('change','#shpNameDelivery',function()
{
	$('.deliveryPage9w').fadeOut('slow');
	if($(this).val()!='')
	{
		var shpId = $(this).val();
		$('#shopNameDelivery').val(shpId);
		var srchShopName = $('#shopNameDelivery').val();
		var app_userId = localStorage.getItem('app_userId');
		getOrderId(app_userId,srchShopName);
	}
	else 	
		location.reload();

});
$(document).on('click','#srchCls2',function()
{
	$('#srchDeliveryList').fadeOut('fast');
	$('#shopNameDelivery').val('');
});
$(document).on('click','.srchShpName3',function()
{
	var srchsid = $(this).attr('srchsid');
	$('#shopNameDelivery').val(srchsid);
	$('#shopNameDeliveryView').val($(this).text());
	$('#srchDeliveryList').fadeOut('fast');
	var srchShopName = $('#shopNameDelivery').val();
	var app_userId = localStorage.getItem('app_userId');
	getOrderId(app_userId,srchShopName);
});

function getOrderId(app_userId,srchShopName)
{
		$.getJSON(serviceUrl+'getDeliveryOrderId.php?app_userId='+app_userId+'&srchShopName='+srchShopName,function(data)
		{
			var jres = data.Result;
			$('#DeliveryDiv').html('');
			if(jres.length!=0)
			{
				$.each(jres,function(index,objj)
				{
					if(objj.status=='success')
					{
						var indx = index+1;
						$('#DeliveryDiv').append('<div style="width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="Delivery'+index+'" unique_id="'+objj.unique_id+'" Inv_no="'+objj.Inv_no+'" shop_id="'+objj.shop_id+'" class="deliveryIdCls3">'+indx+' . '+objj.Inv_no+'<span><input type="checkbox" class="chBoxOrdId" ordrId="'+objj.Inv_no+'" style="float:right;"></span></div><div class="deliveryHidDataDiv" id="Delivery'+index+'" style="display:none;"></div><hr>');
					}
					else
						$('#DeliveryDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
				});
				$('.hidncls').fadeIn('slow');
				$('.hidncls1').slideDown('slow');
			}
			else
				$('#DeliveryDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
		});
}
var chBoxUncIds = [];
$(document).on('click','.chBoxOrdId',function()
{
	var ordrId = $(this).attr('ordrId');
	var a = chBoxUncIds.indexOf(ordrId);
	var ischecked= $(this).is(':checked');
    if(ischecked)
	{
		chBoxUncIds.push(ordrId);
	}
	else
	{
		chBoxUncIds.splice(a,1);
	}
	localStorage.setItem('chBoxUncIds',JSON.stringify(chBoxUncIds));
});

$(document).on('click','#deliveryBtn',function()
{
	var app_userId = localStorage.getItem('app_userId');
	if(localStorage.getItem('chBoxUncIds'))
	{
		var chBoxUncIds = JSON.parse(localStorage.getItem('chBoxUncIds'));
		var len = chBoxUncIds.length;
		if(len!=0)
		{
			var InvIds = '';
			$.each(chBoxUncIds,function(index,objj)
			{
				if(len-1==index)
					InvIds +=  objj;
				else
					InvIds +=  objj+',';
			});
			var shpId = $('#shopNameDelivery').val();
			$.post(serviceUrl+'delivery.php',{'shpId':shpId,'chBoxUncIds':InvIds,'app_userId':app_userId},function(data)
			{
				var jres = $.parseJSON(data).Result;
				if(jres.status=='success')
				{
					$('.deliveryPage9w').html('Success : Order delivered');
					$('.deliveryPage9w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
					$('.deliveryPage9w').fadeIn('slow');
					$('html,body').animate({scrollTop:0},500);
					var otpMbl = jres.primary_mobile;
					var Pymnt_Amnt = InvIds;
					var pwd = 'deliveryCnfrm';
					var otpDetails = app_userId+','+shpId;
					sendPassword(otpMbl,pwd,Pymnt_Amnt,otpDetails);
					$('#shopNameDeliveryView').val('');
					$('.hidncls').hide();
					$('.hidncls1').hide();
				}
				else if(jres.status == 'someDataNotUpdated')
				{
					var errorInv = jres.errorInv;
					$('.deliveryPage9w').html('Order Id : '+errorInv+' Not Updated!');
					$('.deliveryPage9w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
					$('.deliveryPage9w').fadeIn('slow');
					$('html,body').animate({scrollTop:0},500);
				}
				else
				{	
					$('.deliveryPage9w').html('Please try later!');
					$('.deliveryPage9w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
					$('.deliveryPage9w').fadeIn('slow');
					$('html,body').animate({scrollTop:0},500);
				}
			});
		}
		else
		{	
			$('.deliveryPage9w').html('Please select the order id!');
			$('.deliveryPage9w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.deliveryPage9w').fadeIn('slow');
			$('html,body').animate({scrollTop:0},500);
		}
	}
	else
	{	
		$('.deliveryPage9w').html('Please select orders!');
		$('.deliveryPage9w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.deliveryPage9w').fadeIn('slow');
		$('html,body').animate({scrollTop:0},500);
	}
});

$(document).on('click','#DeliveryIcon',function(e)
{
	var app_user = localStorage['app_userId'];
	$('.menu_delivery').slideToggle('fast');
	$.getJSON(serviceUrl +'delivery.php?app_user='+app_user,function(data)
	{
		console.log(data);
		$('.menuUl_delivery').html('');
		var Jres = data.Result;
		console.log(Jres);
		$('.menuUl_delivery').html('<table style="width:100%;border-collapse:collapse;" border="1"><thead><tr><th>Date</th><th>Partner</th><th>Invoice</th></tr></thead><tbody id="delvInvTblBody"></tbody></table>');
		if(Jres.status!='failed')
		{
			$.each(Jres,function(index,objj)
			{
				if(objj.status=='success')
				{
					var htm='';
					htm += '<tr><td class="shopLi" style="padding: 10px;border-bottom: 1px solid gray;"> '+ objj.delivery_date+'</td><td style="padding: 10px;"> '+ objj.Name+'</td><td style="font-size:12px;padding: 10px;"> '+ objj.Inv_no+'</td></tr>';
					$('#delvInvTblBody').append(htm);
				}
			});
		}
		else
		{
			$('#delvInvTblBody').html('<tr><td colspan="3" class="cntr">Empty Result!</td></tr>');
		}
	});

});
/* shop select and search function start */
/* Delivery Page script end */
/* ---------------------------------------------------------------------------------------------------------- */

/* Report Page script Start */

$(document).on('click','#report',function()
{
	window.location.replace('#page10');
});
$('#page10').on('pageshow',function()
{
	getLatLong();
	$('.salesReviewPage10w').fadeOut('slow');
	$('#reportForm')[0].reset();
	$('#Orderwise').prop('checked',true);
	var d = new Date();
	var day = d.getDate()>=10? d.getDate():0+''+d.getDate();
	var mnth = d.getMonth()+1;
	var fmnth = mnth>=10? mnth:0+''+mnth;
	var currDate = d.getFullYear()+'-'+fmnth+'-'+day;	
	//alert(currDate);
	var threeMonthsFromNow = new Date(d.setMonth(d.getMonth() - 0));
	var mnthFull = String(threeMonthsFromNow).split(" ");
	var a = getMonthName(mnthFull[1]);
	var b = a.split(" ");
	$('#monthwiseSalesReport').append('<option value="'+b[1]+'">&nbsp;&nbsp;'+b[0]+'</option>');
	var d1 = new Date();
	var threeMonthsFromNow1 = new Date(d1.setMonth(d1.getMonth() - 1));
	var mnthFull1 = String(threeMonthsFromNow1).split(" ");
	var a = getMonthName(mnthFull1[1]);
	var b = a.split(" ");
	var d2 = new Date();
	$('#monthwiseSalesReport').append('<option value="'+b[1]+'">&nbsp;&nbsp;'+b[0]+'</option>');
	var threeMonthsFromNow2 = new Date(d2.setMonth(d2.getMonth() - 2));
	var mnthFull2 = String(threeMonthsFromNow2).split(" ");
	var a = getMonthName(mnthFull2[1]);
	var b = a.split(" ");
	$('#monthwiseSalesReport').append('<option value="'+b[1]+'">&nbsp;&nbsp;'+b[0]+'</option>');

	var monthName = $(this).val();
	var d= new Date();
	var year = d.getFullYear();
	$('#monthwiseSalesReport').attr('monthYear',year+'-'+fmnth);
	var report_res = 'monthWise';
	var currDate = $('#report_resHid').val();
	var monthNameyear = year+'-'+fmnth;
	getReportData(report_res,currDate,monthNameyear);
});

function getMonthName(mnthFull)
{
	switch(mnthFull)
	{
		case "Jan":
		  return 'January 01';break;
		case "Feb":
		  return 'February 02';break;
		case "Mar":
		  return 'March 03';break;
		case "Apr":
		  return 'April 04';break;
		case "May":
		  return 'May 05';break;
		case "Jun":
		  return 'June 06';break;
		case "Jul":
		  return 'July 07';break;
		case "Aug":
		  return 'August 08';break;
		case "Sep":
		  return 'September 09';break;
		case "Oct":
		  return 'October 10';break;
		case "Nov":
		  return 'November 11';break; 
		case "Dec":
		  return 'December 12';break;
	}
}

$(document).on('click','.reportRadio',function()
{
	var dt= new Date();
	var year = dt.getFullYear();
	var mnthYr = $('#monthwiseSalesReport').val();
	if(mnthYr=='000')
	{
		if($('#orders_res').val()=='')
		{
			$('#report_resHid').val($(this).val());
			var report_res = $(this).val();
			var currDate = $('#srchDateWiseReport').val();
			var date2 = $('#srchDateWiseReport1').val();
			getReportData(report_res,currDate,date2);
		}
		else
		{
			$('#report_resHid').val($(this).val());
			var report_resHid = $(this).val();
			var days = $('#orders_res').val();
			var report_res = 'ordersradio';
			getReportData(report_res,report_resHid,days);
		}
	}
	else
	{
		$('#report_resHid').val($(this).val());
		var report_res = 'monthWise';
		var currDate = $(this).val();
		var monthNameyear = year+'-'+mnthYr;
		var days = $('#orders_res').val();
		getReportData(report_res,currDate,monthNameyear);	
	}
});

$(document).on('change','#monthwiseSalesReport',function()
{
	if($(this).val()!='000')
	{
		$('.ordersRadio').prop('checked',false);
		$('#orders_res').val('');
		var monthName = $(this).val();
		var d= new Date();
		var year = d.getFullYear();
		$('#monthwiseSalesReport').attr('monthYear',year+'-'+monthName);
		var report_res = 'monthWise';
		var currDate = $('#report_resHid').val();
		var monthNameyear = year+'-'+monthName;
		getReportData(report_res,currDate,monthNameyear);
	}
});

$(document).on('click','.ordersRadio',function()
{
	var days = $(this).val();
	if(days=='')
		days = 0;
	var report_resHid = $('#report_resHid').val();
	$('#orders_res').val(days);
	var report_res = 'ordersradio';
	getReportData(report_res,report_resHid,days);
});

function getReportData(report_res,currDate,date2)
{
	var report_res = report_res;
	var currDate = currDate;
	var date2 = date2;
	var app_userId = localStorage.getItem('app_userId');
	if(report_res=='pageshow' || report_res=='Orderwise')
		var reportUrl = serviceUrl+'getDeliveryOrderId.php?app_userId='+app_userId+'&currDate='+currDate+'&date2='+date2+'&req=1';
	if(report_res=='Modelwise')
		var reportUrl = serviceUrl+'getDeliveryOrderId.php?app_userId='+app_userId+'&currDate='+currDate+'&date2='+date2+'&modelwise=1';
	if(report_res=='ordersradio')
	{
		if(currDate=='Orderwise')
			var reportUrl = serviceUrl+'getDeliveryOrderId.php?app_userId='+app_userId+'&days='+date2+'&orderwiseradio=1';
		if(currDate=='Modelwise')
			var reportUrl = serviceUrl+'getDeliveryOrderId.php?app_userId='+app_userId+'&days='+date2+'&modelwiseradio=1';
	}
	if(report_res=='monthWise')
	{
		if(currDate=='Orderwise')
			var reportUrl = serviceUrl+'getDeliveryOrderId.php?app_userId='+app_userId+'&currDate='+currDate+'&date2='+date2+'&OrderWiseMonth=1';
		if(currDate=='Modelwise')
			var reportUrl = serviceUrl+'getDeliveryOrderId.php?app_userId='+app_userId+'&currDate='+currDate+'&date2='+date2+'&ModelWiseMonth=1';
	}
	
	$.getJSON(reportUrl,function(data)
	{
		var jres = data.Result;
		$('#reportDiv').html('');
		if(jres.length!=0 && jres.length!='undefined')
		{
			if(report_res=='pageshow' || report_res=='Orderwise' || currDate=='Orderwise')
			  $('#reportDiv').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Party Name</span><span style="float:right;">Total Value</span></p>');
			if(report_res=='Modelwise' || currDate=='Modelwise')
			  $('#reportDiv').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Model No</span><span style="float:right;">Quantity</span></p>');
			if(report_res=='ordersradio')
			{
				if(currDate=='Orderwise')
					$('#reportDiv').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Party Name</span><span style="float:right;">Total Value</span></p>');	
				if(currDate=='Modelwise')	
					$('#reportDiv').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Model No</span><span style="float:right;">Quantity</span></p>');
			}
			var debit_amount = 0;
			$.each(jres,function(index,objj)
			{
				if(objj.status=='success')
				{
					var indx = index+1;
					if(report_res=='ordersradio')
					{
						if(currDate=='Orderwise')
						{
							debit_amount = parseInt(debit_amount)+parseInt(objj.debit_amount);
							$('#reportDiv').append('<div style="text-shadow:none;width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="report'+index+'" shop_id="'+objj.shop_id+'" shpName="'+objj.Name+'" class="reportIdCls3">'+indx+'. <span style="width:70%;word-wrap:break-word">'+ objj.Name+'</span><span style="width:30%;float:right;text-align:right;">'+objj.debit_amount+'</span></div><div class="reportHidDataDiv" id="report'+index+'" style="display:none;"></div><hr>');
						}
						if(currDate=='Modelwise')	
						{
							debit_amount = parseInt(debit_amount)+parseInt(objj.value);
							$('#reportDiv').append('<div style="text-shadow:none;width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="reportData'+index+'"  product_name="'+objj.product_model+'" Quantity="'+objj.qty+'" class="reportIdCls4">'+indx+'. <span style="width:70%;word-wrap:break-word">'+ objj.product_model+'</span><span style="width:30%;float:right;text-align:right;">'+objj.qty+'</span></div><div class="reportHidDataDiv" id="reportData'+index+'" style="display:none;"></div><hr>');
						}
					}
					if(report_res=='monthWise')
					{
						if(currDate=='Orderwise')
						{
							debit_amount = parseInt(debit_amount)+parseInt(objj.debit_amount);
							$('#reportDiv').append('<div style="text-shadow:none;width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="report'+index+'" shop_id="'+objj.shop_id+'" shpName="'+objj.Name+'" class="reportIdCls5">'+indx+'. <span style="width:70%;word-wrap:break-word">'+ objj.Name+'</span><span style="width:30%;float:right;text-align:right;">'+objj.debit_amount+'</span></div><div class="reportHidDataDiv" id="report'+index+'" style="display:none;"></div><hr>');
						}
						if(currDate=='Modelwise')
						{
							debit_amount = parseInt(debit_amount)+parseInt(objj.value);
							$('#reportDiv').append('<div style="text-shadow:none;width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="reportData'+index+'"  product_name="'+objj.product_model+'" Quantity="'+objj.qty+'" class="reportIdCls6">'+indx+'. <span style="width:70%;word-wrap:break-word">'+ objj.product_model +'</span><span style="width:30%;float:right;text-align:right;">'+objj.qty+'</span></div><div class="reportHidDataDiv" id="reportData'+index+'" style="display:none;"></div><hr>');
						}
					}
				}
				else
				{	
					$('#reportDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
					$('.salseReviewTotalCls').html(0);
				}
				
			});
			$('.hidncls').fadeIn('slow');
			$('.hidncls1').slideDown('slow');
		}
		else
		{	
			$('#reportDiv').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
			$('.salseReviewTotalCls').html(0);
		}
		$('.salseReviewTotalCls').html(debit_amount);			
	});
}

$(document).on('click','.reportIdCls3',function()
{
	$('.reportIdCls3').removeClass('greenClr');
	$(this).addClass('greenClr');
	var app_userId = localStorage.getItem('app_userId');
	var unique_id = $(this).attr('unique_id');
	var shop_id = $(this).attr('shop_id');
	var uId = $(this).attr('uId');
	var days = $('#orders_res').val();
	var report_resHid = $('#report_resHid').val();
	$.getJSON(serviceUrl+'getDeliveryOrderId.php?app_userId='+app_userId+'&uniqueOrd_id='+unique_id+'&shop_id='+shop_id+'&days='+days+'&request=1',function(data)
	{
		$('.reportHidDataDiv').slideUp('slow');
		$('#'+uId).html('<table width="100%" border="1" class="reportTbl" style="text-align:center;border-collapse: collapse;"><tr><th>Model No</th><th>Color</th><th>Qty</th><th>Total Qty</th><th>Total Val</th></tr>');
		var jres = data.Result;
			
			if(jres.length!=0)
			{
				var modelArr = [];
				$.each(jres,function(index,objj)
				{
					if(objj.status=='success')
					{
						modelArr.push(objj.product_model);
					}
				});
				var counts = {};
				$.each(modelArr, function(key,value) {
				  if (!counts.hasOwnProperty(value)) {
					counts[value] = 1;
				  } else {
					counts[value]++;
				  }
				});
				var salesArr = {};
				var obj = null;
				for(var i=0; i < jres.length; i++) 
				{
   					obj=jres[i];
					if(!salesArr[obj.product_model])
						salesArr[obj.product_model]=obj;
					else
					{
						salesArr[obj.product_model].totalQty=parseInt(salesArr[obj.product_model].totalQty)+parseInt(obj.totalQty);
						salesArr[obj.product_model].value=parseInt(salesArr[obj.product_model].value)+parseInt(obj.value);
					}
				}
				localStorage.removeItem('currentMdl');
				$.each(jres,function(index,objj)
				{
					if(objj.status=='success')
					{
						if(counts[objj.product_model]==1)
							$('.reportTbl').append('<tr><td>'+objj.product_model+'</td><td>'+objj.color+'</td><td>'+objj.Quantity+'</td><td>'+objj.Quantity+'</td><td>'+objj.value+'</td></tr>');
						else
						{
							if(localStorage.getItem('currentMdl'))
							{
								if(localStorage.getItem('currentMdl')==objj.product_model)
									$('.reportTbl').append('<tr><td>'+objj.product_model+'</td><td>'+objj.color+'</td><td>'+objj.Quantity+'</td></tr>');
								else
								{	
									$('.reportTbl').append('<tr><td>'+objj.product_model+'</td><td>'+objj.color+'</td><td>'+objj.Quantity+'</td><td rowspan="'+counts[objj.product_model]+'">'+objj.totalQty+'</td><td rowspan="'+counts[objj.product_model]+'">'+objj.value+'</td></tr>');
									localStorage['currentMdl']=objj.product_model;
								}
							}
							else
							{
								$('.reportTbl').append('<tr><td>'+objj.product_model+'</td><td>'+objj.color+'</td><td>'+objj.Quantity+'</td><td rowspan="'+counts[objj.product_model]+'">'+objj.totalQty+'</td><td rowspan="'+counts[objj.product_model]+'">'+objj.value+'</td></tr>');
								localStorage['currentMdl']=objj.product_model;
							}	
						}
					}
				});
			}
		$('#'+uId).append('</table>');	
		$('#'+uId).slideDown('slow');
	});
});
$(document).on('click','.reportIdCls4',function()
{
	$('.reportIdCls4').removeClass('greenClr');
	$(this).addClass('greenClr');
	var app_userId = localStorage.getItem('app_userId');
	var product_model = $(this).attr('product_name');
	var uId = $(this).attr('uId');
	if($('#orders_res').val()=='')
	{
		var curr_date = $('#srchDateWiseReport').val();
		var date2 = $('#srchDateWiseReport1').val();
	    var modelwiseclickUrl=serviceUrl+'getDeliveryOrderId.php?app_userId='+app_userId+'&product_model='+product_model+'&curr_date='+curr_date+'&date2='+date2+'&modelwiseclick=1';
	}
	else
	{
		var days = $('#orders_res').val();
		var modelwiseclickUrl=serviceUrl+'getDeliveryOrderId.php?app_userId='+app_userId+'&product_model='+product_model+'&days='+days+'&modelwiseclick2=1';
	}
	
	$.getJSON(modelwiseclickUrl,function(data)
	{
		$('.reportHidDataDiv').slideUp('slow');
		$('#'+uId).html('<table width="100%" border="1" class="reportTbl" style="text-align:center;border-collapse: collapse;"><tr><th>Shop Name</th><th>Qty</th><th>Value</th></tr>');
		var jres = data.Result;
			if(jres.length!=0)
			{
				$.each(jres,function(index,objj)
				{
					if(objj.status=='success')
					{
						$('.reportTbl').append('<tr><td>'+objj.Name+'</td><td>'+objj.qty+'</td><td>'+objj.value+'</td></tr>');
					}
				});
			}
		$('#'+uId).append('</table>');	
		$('#'+uId).slideDown('slow');
	});
});

$(document).on('click','.reportIdCls5',function()
{
	$('.reportIdCls5').removeClass('greenClr');
	$(this).addClass('greenClr');
	var app_userId = localStorage.getItem('app_userId');
	var unique_id = $(this).attr('unique_id');
	var shop_id = $(this).attr('shop_id');
	var uId = $(this).attr('uId');
	var monthYear = $('#monthwiseSalesReport').attr('monthyear');
	$.getJSON(serviceUrl+'getDeliveryOrderId.php?app_userId='+app_userId+'&uniqueOrd_id='+unique_id+'&shop_id='+shop_id+'&monthYear='+monthYear+'&monthWiseOrdersClick=1',function(data)
	{
		$('.reportHidDataDiv').slideUp('slow');
		$('#'+uId).html('<table width="100%" border="1" class="reportTbl" style="text-align:center;border-collapse: collapse;"><tr><th>Model No</th><th>Color</th><th>Qty</th><th>Total Qty</th><th>Total Val</th></tr>');
		var jres = data.Result;
			if(jres.length!=0)
			{
				var modelArr = [];
				$.each(jres,function(index,objj)
				{
					if(objj.status=='success')
					{
						modelArr.push(objj.product_model);
					}
				});
				var counts = {};
				$.each(modelArr, function(key,value) {
				  if (!counts.hasOwnProperty(value)) {
					counts[value] = 1;
				  } else {
					counts[value]++;
				  }
				});
				var salesArr = {};
				var obj = null;
				for(var i=0; i < jres.length; i++) 
				{
   					obj=jres[i];
					if(!salesArr[obj.product_model])
						salesArr[obj.product_model]=obj;
					else
					{
						salesArr[obj.product_model].totalQty=parseInt(salesArr[obj.product_model].totalQty)+parseInt(obj.totalQty);
						salesArr[obj.product_model].value=parseInt(salesArr[obj.product_model].value)+parseInt(obj.value);
					}
				}
				localStorage.removeItem('currentMdl');
				$.each(jres,function(index,objj)
				{
					if(objj.status=='success')
					{
						if(counts[objj.product_model]==1)
							$('.reportTbl').append('<tr><td>'+objj.product_model+'</td><td>'+objj.color+'</td><td>'+objj.Quantity+'</td><td>'+objj.Quantity+'</td><td>'+objj.value+'</td></tr>');
						else
						{
							if(localStorage.getItem('currentMdl'))
							{
								if(localStorage.getItem('currentMdl')==objj.product_model)
									$('.reportTbl').append('<tr><td>'+objj.product_model+'</td><td>'+objj.color+'</td><td>'+objj.Quantity+'</td></tr>');
								else
								{	
									$('.reportTbl').append('<tr><td>'+objj.product_model+'</td><td>'+objj.color+'</td><td>'+objj.Quantity+'</td><td rowspan="'+counts[objj.product_model]+'">'+objj.totalQty+'</td><td rowspan="'+counts[objj.product_model]+'">'+objj.value+'</td></tr>');
									localStorage['currentMdl']=objj.product_model;
								}
							}
							else
							{
								$('.reportTbl').append('<tr><td>'+objj.product_model+'</td><td>'+objj.color+'</td><td>'+objj.Quantity+'</td><td rowspan="'+counts[objj.product_model]+'">'+objj.totalQty+'</td><td rowspan="'+counts[objj.product_model]+'">'+objj.value+'</td></tr>');
								localStorage['currentMdl']=objj.product_model;
							}	
						}
					}
				});
			}
		$('#'+uId).append('</table>');	
		$('#'+uId).slideDown('slow');
	});
});
$(document).on('click','.reportIdCls6',function()
{
	$('.reportIdCls6').removeClass('greenClr');
	$(this).addClass('greenClr');
	var app_userId = localStorage.getItem('app_userId');
	var product_model = $(this).attr('product_name');
	var uId = $(this).attr('uId');
	var monthYear = $('#monthwiseSalesReport').attr('monthyear');
	var modelwiseclickUrl=serviceUrl+'getDeliveryOrderId.php?app_userId='+app_userId+'&product_model='+product_model+'&monthYear='+monthYear+'&monthWiseModelsClick=1';
	
	$.getJSON(modelwiseclickUrl,function(data)
	{
		$('.reportHidDataDiv').slideUp('slow');
		$('#'+uId).html('<table width="100%" border="1" class="reportTbl" style="text-align:center;border-collapse: collapse;"><tr><th>Shop Name</th><th>Qty</th><th>Value</th></tr>');
		var jres = data.Result;
			if(jres.length!=0)
			{
				$.each(jres,function(index,objj)
				{
					if(objj.status=='success')
					{
						$('.reportTbl').append('<tr><td>'+objj.Name+'</td><td>'+objj.qty+'</td><td>'+objj.value+'</td></tr>');
					}
				});
			}
		$('#'+uId).append('</table>');	
		$('#'+uId).slideDown('slow');
	});
});
/* Report Page script end */

$(document).on('click','#stockinhand',function()
{
	window.location.replace('#page11');
});
$(document).on('click','#stockinhand_unfos',function()
{
	window.location.replace('#page11');
});

$('#page11').on('pageshow',function()
{
	getLatLong();
	$('.stkInHandPage11w').fadeOut('slow');
	$('#stkinhandForm')[0].reset();
	$('#stkinhandDiv').html('');
	$.getJSON(serviceUrl+'getProducts.php?getAllProducts=yes',function(data)
	{
		var jres = data.Result;
		if(jres.status=='error')
		{
			$('#stkinhandDiv').html('Empty stock').css('color','red');
		}
		if(jres.status=='failed')
		{
			$('#stkinhandDiv').html('Error').css('color','red');
		}
		if(jres.status!='error' && jres.status!='failed')
		{
			var prdctName;
			$.each(jres,function(index,objj)
			{
				if(objj.product_name=='Nokia')
					var product_name = 'Nokia Mobiles';
				else
					var product_name = objj.product_name;
				$('#stkinhandDiv').append('<p class="prdctUnicName" uId="'+objj.product_name+'" uuId="prdctUuid'+index+'" style="text-align:center;background:rgb(0,140,186);padding:10px;width: 80%;margin: auto;margin-bottom: 8px;margin-top: 8px;color: white;border-radius:3px;text-transform: uppercase;box-shadow: 1px 1px 2px 0px black;border-left: 5px solid darkturquoise;letter-spacing: 2px;">'+product_name+'</p><div style="display:none;" class="prdctInr" id="sprdctUuid'+index+'"><table id="prdctUuid'+index+'" width="100%" border="1" style="border-collapse:collapse;"></table></div>');
			});
		}
	});
});

$(document).on('click','.prdctUnicName',function()
{
	$('.prdctInr').slideUp('slow');
	var uId = $(this).attr('uId');
	var uuId = $(this).attr('uuId');
	$.getJSON(serviceUrl+'getProducts.php?getAllData=yes&uId='+uId,function(data)
	{
		console.log(data);
		var jres = data.Result;
		if(jres.status=='error')
		{
			$('#'+uuId).html('Empty stock').css('color','red');
		}
		if(jres.status=='failed')
		{
			$('#'+uuId).html('Error').css('color','red');
		}
		if(jres.status!='error' && jres.status!='failed')
		{
			$('#'+uuId).html('<tr id="stckInHnd_thead"><th>&nbsp;Product Model</th><th>DP</th><th>MOP</th><th>MRP</th><th>Qty</th></tr>');
			var productsArr = [];
			$.each(jres,function(index,objj)
			{
				var quantity = objj.quantity;
				if (quantity.indexOf(" NOS") >= 0)
					quantity = quantity.replace(' NOS','');
				if (quantity.indexOf("NOS") >= 0)
					quantity = quantity.replace('NOS','');
				if ($.inArray(objj.product_model, productsArr) != -1)
				{
					$('#'+uuId).append('<tr style="font-style:italic;font-size: 13px;"><td style="padding-left: 15px;">'+objj.color+'</td><td></td><td></td><td></td><td class="rght">'+quantity+'&nbsp;</td></tr>');
				}
				else
				{
					$('#'+uuId).append('<tr class="prdctCls" style="font-weight:bold;text-shadow: none;background: ghostwhite;"><td>&nbsp;'+ objj.product_model+'</td><td class="rght">'+ objj.dp+'&nbsp;</td><td class="rght">'+ objj.mop+'&nbsp;</td><td class="rght">'+ objj.mrp+'&nbsp;</td><td class="rght">'+objj.ttlQty+'</td></tr>');
					$('#'+uuId).append('<tr style="font-style:italic;font-size: 13px;"><td style="padding-left: 15px;">'+objj.color+'</td><td></td><td></td><td></td><td class="rght">'+quantity+'&nbsp;</td></tr>');
					productsArr.push(objj.product_model);
				}
			});
		}
		var displaySts = $('#s'+uuId).css('display');
		if(displaySts=='none')
			$('#s'+uuId).slideDown('slow');
		else
			$('#s'+uuId).slideUp('slow');
	});
});

/* Pending cheques start here.. */
$(document).on('click','#cheques',function()
{
	window.location.replace('#page12');
});
var todayCheque_Arr = [];
var unprsntCheque_Arr = [];
var PndngCheque_Arr = [];
$('#page12').on('pageshow',function()
{
	getLatLong();
	$('.pndChequePage12w').fadeOut('slow');
	$('#pendingChequesForm')[0].reset();
	var app_user = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl+'getChequesDates.php?app_user='+app_user,function(data)
	{
		console.log(data);
		$('#unprentedChquesDiv').html('<p style="font-style:italic;color:crimson;">Unpresented Cheques : <span id="unprsntValue">0</span></p>');
		$('#todayChequesDiv').html('<p style="font-style:italic;color:crimson;">Today : <span class="todayAmtCls" style="color:black;">0</span></p>');
		$('#pendingChequesDiv').html('<p style="font-style:italic;color:crimson;">Pending - <span class="pndgAmtCls" style="color:black;">0</span></p>');
		var jres = data.Result;
		var totayTtl = 0;
		if(jres.status=='emptySet')
			$('#pendingChequesDiv').html('<p style="text-align:center;color:red;">No Pending Cheques!!</p>');
		else
		{
			var todayCheques;
			var pendingCheques;
			var unpresentedCheques;
			$.each(jres,function(index,objj)
			{
				todayCheques = objj.todayCheques;
				pendingCheques = objj.pendingCheques;
				unpresentedCheques = objj.unpresentedCheques;
			});
			$('#unprentedChquesDiv').append('<table border="1" width="100%" id="unprsntTbl" style="border-collapse:collapse;line-height:30px;border-color:crimson;"><thead><tr style="font-size:14px;"><th>S.No</th><th>Shop Name</th><th>Recv On</th><th>Cheque</th><th>Date</th><th>Amount</th></tr></thead><tbody id="unprsntTbl_bdy"></tbody></table>');
			$('#todayChquesDiv').append('<table border="1" width="100%" id="tdyChequeTbl" style="border-collapse:collapse;line-height:30px;margin-top: 5%;"><thead><tr style="font-size: 14px;"><th>S.No</th><th>Shop Name</th><th>Recv On</th><th>Cheque</th><th>Date</th><th>Amount</th></tr></thead><tbody id="tdyChequeTbl_bdy"></tbody></table>');
			$('#pendingChequesDiv').append('<table border="1" width="100%" id="chequeTbl" style="border-collapse:collapse;line-height:30px;margin-top: 5%;"><thead><tr style="font-size: 14px;"><th>S.No</th><th>Shop Name</th><th>Recv On</th><th>Cheque</th><th>Date</th><th>Amount</th></tr></thead><tbody id="chequeTbl_bdy"></tbody></table>');
			var indx = 1;
			var pndgTtl = 0;
			var len = todayCheques.length;
			var len_p = pendingCheques.length;
			var ttlShopsArr = [];
			if(len>0)
			{
				$.each(todayCheques,function(index,objj)
				{
					if(objj.status=='success')
					{
						indx = index+1;
						totayTtl = parseInt(totayTtl)+parseInt(objj.amount);
						var cheque_date = objj.cheque_date;
						var pymnt_date = objj.pymnt_date;
						var res = getMnthYear(cheque_date,pymnt_date);
						res = res.split("!");
						cheque_date = res[0];
						pymnt_date = res[1];
						
						$('.tdyCheque').html(cheque_date);
						$('#tdyChequeTbl_bdy').append('<tr style="color:blue;font-size: 11px;"><td style="text-align:center;">'+indx+'</td><td style="word-wrap:break-word;" shpTd="'+objj.shopName+'">&nbsp;'+objj.shopName+'</td><td style="text-align:center;">'+pymnt_date+'</td><td style="text-align:center;">'+objj.cheque_no+'</td><td style="text-align:center;">'+cheque_date+'</td><td style="text-align:right;">'+objj.amount+'&nbsp;</td></tr>');
						todayCheque_Arr.push({'shopName':objj.shopName,'pymnt_date':pymnt_date,'cheque_no':objj.cheque_no,'cheque_date':objj.cheque_date,'amount':objj.amount,});
						if($.inArray(objj.shopName,ttlShopsArr)==-1)
						{
							ttlShopsArr.push(objj.shopName);
							$('#pndndChequesShp').append('<option value="'+objj.shopName+'">'+objj.shopName+'</option>');
						}
					}
				});
			}
			else
				$('#tdyChequeTbl_bdy').html('<tr style="text-align:center;color:red;"><td colspan="6">No Records!!</td></tr>');
			
			if(len_p>0)
			{
				var len1 = pendingCheques.length;
				$.each(pendingCheques,function(index,objj)
				{
					if(objj.status=='success')
					{
						indx = index+1;	
						pndgTtl = parseInt(pndgTtl)+parseInt(objj.amount);
						var cheque_date = objj.cheque_date;
						var pymnt_date = objj.pymnt_date;
						var res = getMnthYear(cheque_date,pymnt_date);
						res = res.split("!");
						cheque_date = res[0];
						pymnt_date = res[1];
						
						$('#chequeTbl_bdy').append('<tr style="font-size: 11px;"><td style="text-align:center;">'+indx+'</td><td style="word-wrap:break-word;" shpTd="'+objj.shopName+'">&nbsp;'+objj.shopName+'</td><td style="text-align:center;">'+pymnt_date+'</td><td style="text-align:center;">'+objj.cheque_no+'</td><td style="text-align:center;">'+cheque_date+'</td><td style="text-align:right;">'+objj.amount+'&nbsp;</td></tr>');
						PndngCheque_Arr.push({'shopName':objj.shopName,'pymnt_date':pymnt_date,'cheque_no':objj.cheque_no,'cheque_date':objj.cheque_date,'amount':objj.amount,});
						len++;
						if($.inArray(objj.shopName,ttlShopsArr)==-1)
						{
							ttlShopsArr.push(objj.shopName);
							$('#pndndChequesShp').append('<option value="'+objj.shopName+'">'+objj.shopName+'</option>');
						}
					}
				});
			}
			else
				$('#chequeTbl_bdy').html('<tr style="text-align:center;color:red;"><td colspan="6">No Records!!</td></tr>');	
			
			var unprsntVal = 0;
			var len2 = unpresentedCheques.length;
			if(len2>0)
			{
				$.each(unpresentedCheques,function(index,objj)
				{
					if(objj.status=='success')
					{
						var sno = index+1;	
						unprsntVal = parseInt(unprsntVal)+parseInt(objj.amount);
						var cheque_date = objj.cheque_date;
						var pymnt_date = objj.pymnt_date;
						var res = getMnthYear(cheque_date,pymnt_date);
						res = res.split("!");
						cheque_date = res[0];
						pymnt_date = res[1];
						
						$('#unprsntTbl_bdy').append('<tr style="font-size: 11px;"><td style="text-align:center;">'+sno+'</td><td style="word-wrap:break-word;" shpTd="'+objj.shopName+'">&nbsp;'+objj.shopName+'</td><td style="text-align:center;">'+pymnt_date+'</td><td style="text-align:center;">'+objj.cheque_no+'</td><td style="text-align:center;">'+cheque_date+'</td><td style="text-align:right;">'+objj.amount+'&nbsp;</td></tr>');
						unprsntCheque_Arr.push({'shopName':objj.shopName,'pymnt_date':pymnt_date,'cheque_no':objj.cheque_no,'cheque_date':objj.cheque_date,'amount':objj.amount,});
						if($.inArray(objj.shopName,ttlShopsArr)==-1)
						{
							ttlShopsArr.push(objj.shopName);
							$('#pndndChequesShp').append('<option value="'+objj.shopName+'">'+objj.shopName+'</option>');
						}
					}
				});
				$('#unprentedChquesDiv').fadeIn('slow');
			}
			else
				$('#unprsntTbl_bdy').html('<tr style="text-align:center;color:red;"><td colspan="6">No Records!!</td></tr>');
			$('#pendingChequesDiv').fadeIn('slow');
			$('#todayChequesDiv').fadeIn('slow');
			$('#unprsntValue').html('&nbsp;<strong style="color:black">Rs.&nbsp;'+unprsntVal+'</strong>');
			$('.todayAmtCls').html('<strong>Rs.&nbsp;'+totayTtl+'</strong>');
			$('.pndgAmtCls').html('<strong>Rs.&nbsp;'+pndgTtl+'</strong>');
		}
	});
});

function getMnthYear(cheque_date,pymnt_date)
{
	cheque_date = cheque_date.toString();
	cheque_date = cheque_date.split("-");
	cheque_date = cheque_date[2]+'-'+cheque_date[1];
	pymnt_date = pymnt_date.toString();
	pymnt_date = pymnt_date.split("-");
	pymnt_date = pymnt_date[2]+'-'+pymnt_date[1];
	return cheque_date+'!'+pymnt_date;
}

$(document).on('click','.pHeadStyle',function()
{
	window.location.reload();
});


/* Pending cheques end */

$(document).on('click','#ords_report',function()
{
	window.location.replace('#page13');
});

$('#page13').on('pageshow',function()
{
	getLatLong();
	$('.ordersViewPage13w').fadeOut('slow');
	$('#ordsRptForm')[0].reset();
	var d = new Date();
	var day = d.getDate()>10? d.getDate():0+''+d.getDate();
	var mnth = d.getMonth()+1;
	var fmnth = mnth>10? mnth:0+''+mnth;
	var currDate = d.getFullYear()+'-'+fmnth+'-'+day;
	$('#Orderwise_ordsRpt').prop('checked',true);
	$('#srchDateWiseReport').val(currDate);
	var ordsRpt_resHid = $('#ordsRpt_resHid').val();
	var report_res = 'pageshow';
	ordsRptRes(report_res,currDate);
});
$(document).on('click','.reportRadio_ordsRpt',function()
{
	$('#report_resHid').val($(this).val());
	var report_res = $('#report_resHid').val();
	var currDate = $('#srchDateWiseReport').val();
	ordsRptRes(report_res,currDate);
});
$(document).on('change','#srchDateWiseReport',function()
{
	var currDate = $(this).val();
	var report_res = $('#report_resHid').val();
	ordsRptRes(report_res,currDate);
});

function ordsRptRes(report_res,currDate)
{
	var app_user = localStorage.getItem('app_userId');
	var report_res = report_res;
	var currDate = currDate;
	if(report_res=='pageshow' || report_res=='Orderwise')
		var reportUrl = serviceUrl+'getFilterOrders.php?app_user='+app_user+'&currDate='+currDate+'&orderwise=1';
	if(report_res=='Modelwise')
		var reportUrl = serviceUrl+'getFilterOrders.php?app_user='+app_user+'&currDate='+currDate+'&modelwise=1';
	$.getJSON(reportUrl,function(data)
	{
		var jres = data.Result;
		$('#reportDiv').html('');
		if(jres.length!=0 && jres.length!='undefined')
		{
			$('#reportDiv_ordsRpt').html('<p style="padding: 2px 0px 17px 0px;font-weight: bold;"><span style="float:left">Shop Name</span><span style="float:right;">Order-Id</span></p>');	
			$.each(jres,function(index,objj)
			{
				if(objj.status=='success')
				{
					var indx = index+1;
					if(report_res=='pageshow' || report_res=='Orderwise')
					{
						$('#reportDiv_ordsRpt').append('<div style="text-shadow:none;width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="report'+index+'" shop_id="'+objj.shop_id+'" shpName="'+objj.Name+'" unique_id="'+objj.unique_id+'" class="reportIdCls_ords">'+indx+'. <span style="width:70%;word-wrap:break-word">'+ objj.Name+'</span><span style="width:30%;float:right;text-align:right;">'+objj.unique_id+'</span></div><div class="reportHidDataDiv" id="report'+index+'" style="display:none;"></div><hr>');
					}
					if(report_res=='Modelwise')
					{
						$('#reportDiv_ordsRpt').append('<div style="text-shadow:none;width:100%;font-size:13px;padding: 10px 0 10px 0;cursor:pointer;" uId="reportData'+index+'"  product_name="'+objj.product_name+'" color="'+objj.color+'" Quantity="'+objj.Quantity+'" class="reportIdCls_mdl">'+indx+'. <span style="width:70%;word-wrap:break-word">'+ objj.product_name +'</span><span style="width:30%;float:right;text-align:right;">'+objj.Quantity+'</span></div><div class="reportHidDataDiv" id="reportData'+index+'" style="display:none;"></div><hr>');
					}	
				}
				else
					$('#reportDiv_ordsRpt').html('<p style="text-align:center;color:red;">No Records Found!</p>');
			});
		}
	});
}

$(document).on('click','.reportIdCls_ords',function()
{
	$('.reportIdCls_ords').removeClass('greenClr');
	$(this).addClass('greenClr');
	var app_userId = localStorage.getItem('app_userId');
	var unique_id = $(this).attr('unique_id');
	var shop_id = $(this).attr('shop_id');
	var uId = $(this).attr('uId');
	var currDate = $('#srchDateWiseReport').val();
	$.getJSON(serviceUrl+'getFilterOrders.php?app_userId='+app_userId+'&uniqueOrd_id='+unique_id+'&shop_id='+shop_id+'&currDate='+currDate+'&request_ords=1',function(data)
	{
		$('.reportHidDataDiv').slideUp('slow');
		$('#'+uId).html('<table width="100%" border="1" class="reportTbl" style="text-align:center;border-collapse: collapse;"><tr><th>Model No</th><th>Color</th><th>Quantity</th></tr>');
		var jres = data.Result;
			if(jres.length!=0)
			{
				$.each(jres,function(index,objj)
				{
					if(objj.status=='success')
					{
						$('.reportTbl').append('<tr><td>'+objj.product_name+'</td><td>'+objj.color+'</td><td>'+objj.Quantity+'</td></tr>');
					}
				});
			}
		$('#'+uId).append('</table>');	
		$('#'+uId).slideDown('slow');
	});
});

$(document).on('click','.reportIdCls_mdl',function()
{
	$('.reportIdCls_mdl').removeClass('greenClr');
	$(this).addClass('greenClr');
	var app_userId = localStorage.getItem('app_userId');
	var product_name = $(this).attr('product_name');
	var uId = $(this).attr('uId');
	var curr_date = $('#srchDateWiseReport').val();
	$.getJSON(serviceUrl+'getFilterOrders.php?app_userId='+app_userId+'&product_name='+product_name+'&curr_date='+curr_date+'&modelwiseclick=1',function(data)
	{
		$('.reportHidDataDiv').slideUp('slow');
		$('#'+uId).html('<table width="100%" border="1" class="reportTbl" style="text-align:center;border-collapse: collapse;"><tr><th>Shop Name</th><th>Color</th><th>Quantity</th></tr>');
		var jres = data.Result;
			if(jres.length!=0)
			{
				$.each(jres,function(index,objj)
				{
					if(objj.status=='success')
					{
						$('.reportTbl').append('<tr><td>'+objj.Name+'</td><td>'+objj.color+'</td><td>'+objj.Quantity+'</td></tr>');
					}
				});
			}
		$('#'+uId).append('</table>');	
		$('#'+uId).slideDown('slow');
	});
});

/* Today Task page start here..  */

$(document).on('click','#tdyTsk',function()
{
	window.location.replace('#page14');
});

$('#page14').on('pageshow',function()
{
	getLatLong();
	$('.todayTaskPage14w').fadeOut('slow');
	$('#dtyTskForm')[0].reset();
	$('#tdyDues').html('<p style="padding:10px;background:aliceblue;border-left: 5px solid skyblue;border-bottom: 1px solid skyblue;"><strong>Today Overdue Shops :- </strong></p><p style="color:cadetblue;font-weight:bold;text-shadow:none"><span>&nbsp;Partner</span><span style="float:right;">Pending_amt&nbsp;&nbsp;&nbsp;</span></p><hr>');
	$('#pndngDues').html('<p style="padding:10px;background:aliceblue;border-left: 5px solid skyblue;border-bottom: 1px solid skyblue;"><strong>Pending Shops :- </strong></p><p style="color:cadetblue;font-weight:bold;text-shadow:none"><span>&nbsp;Partner</span><span style="float:right;">Pending_amt&nbsp;&nbsp;&nbsp;</span></p><hr>');
	var app_userId = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl+'todayTask.php?app_userId='+app_userId+'&tdyTask=yes',function(data)
	{
		var jres = data.Result;
		$.each(jres,function(index,objj)
		{
			if(objj.status=='success')
			{
				var tdyOverdues = objj.today;
				var pndgOverdues = objj.pendings;
				if(tdyOverdues.length!=0)
				{
					$.each(tdyOverdues,function(index,objj1)
					{
						var indx = index+1;
						$('#tdyDues').append('<p style="text-transform:lowercase;"><span>&nbsp;'+indx+'.&nbsp'+objj1.shopName+'</span><span style="float:right;margin-right:10px;">'+objj1.pending_amount+'</span</p>');
					});
				}
				else
					$('#tdyDues').append('<p style="text-align:center;color:red;">No Records!</p>');
				
				if(pndgOverdues.length!=0)
				{
					$.each(pndgOverdues,function(index,objj2)
					{
						var indx = index+1;
						$('#pndngDues').append('<p style="text-transform:lowercase;"><span>&nbsp;'+indx+'.&nbsp'+objj2.shopName+'</span><span style="float:right;margin-right:10px;">'+objj2.pending_amount+'</span</p>');
					});
				}
				else
					$('#pndngDues').append('<p style="text-align:center;color:red;">No Records!</p>');
			}
			else
			{
				$('#tdyDues').append('<p style="text-align:center;color:red;">No Records!</p>');
				$('#pndngDues').append('<p style="text-align:center;color:red;">No Records!</p>');
			}
		});
	});
});

$(document).on('click','#unVstdShp',function()
{
	window.location.replace('#page15');
});
$('#page15').on('pageshow',function()
{
	getLatLong();
	var status = 'pageShow';
	unvstAndBilling(status);
});

function unvstAndBilling(status)
{
	var status = status;
	var app_userId = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl+'delivery.php?app_userId='+app_userId+'&status='+status+'&unvisitedShp=yes',function(data)
	{
		if(status!='notification')
		{
			$('#unvisitedShpList').html('');
			$('#unvisitedShpList').html('<table id="unVstdShpTbl" style="width:99.9%;height:auto;border-collapse:collapse;" border="1"><tr><tr style="background: silver;color: white;text-shadow: none;"><th style="padding:5px;" rowspan="2">Shop Name</th><th colspan="2">0-Billing</th><th colspan="2">Unvisited</th></tr><tr style="background: lightgray;font-weight: normal;color: rgb(49, 136, 203);text-shadow: none;font-size:13px;"><th style="padding:3px;">Date</th><th>Days</th><th style="padding:3px;">Date</th><th>Days</th></tr></table>');
		}
		else
		{
			$('#unvisitedShpListNtfy').html('');
			$('#unvisitedShpListNtfy').html('<table id="unVstdShpTblNtfy" style="width:99.9%;height:auto;border-collapse:collapse;border-color: rgb(49, 136, 203);" border="1"><tr><tr style="background: silver;color: white;text-shadow: none;"><th style="padding:5px;" rowspan="2">Shop Name</th><th colspan="2">0-Billing</th><th colspan="2">Unvisited</th></tr><tr style="background: lightgray;font-weight: normal;color: rgb(49, 136, 203);text-shadow: none;font-size:13px;"><th style="padding:3px;">Date</th><th>Days</th><th style="padding:3px;">Date</th><th>Days</th></tr></table>');
		}	
		var jres = data.Result;
		var ShpCnt = jres.length;
		if(status!='notification')
			$('.unVstShpCls').html(ShpCnt);
		$.each(jres,function(index,objct)
		{
			if(objct.status=='success')
			{
				var attnds_date = objct.attnds_date;
				if(attnds_date!=null)
				{
					attnds_date = attnds_date.toString();
					attnds_date = attnds_date.split("-");
					var dd  = attnds_date[0];
					var mm  = attnds_date[1];
					attnds_date = dd+'-'+mm;
				}
				else
					attnds_date = '';
					
				var sales_date = objct.sales_date;
			 	if(sales_date!=null)
				{
					sales_date = sales_date.toString();
					sales_date = sales_date.split("-");
					var dd1  = sales_date[0];
					var mm1  = sales_date[1];
					sales_date = dd1+'-'+mm1;
				}
				else
					sales_date = '';
					
				var sales_days = objct.sales_days;
				if(sales_days=='0')
					sales_days = '';
				var attnds_days = objct.attnds_days;
				if(attnds_days=='0')
					attnds_days = '';
				if(status!='notification')	
					$('#unVstdShpTbl').append('<tr style="font-size: 12px;text-align:center;"><td style="padding: 5px 0px;word-break: break-all;">&nbsp;'+objct.Name+'</td><td>'+sales_date+'</td><td style="text-align:center;">'+sales_days+'</td><td>'+attnds_date+'</td><td>'+attnds_days+'</td></tr>');
				else
				{
					if(index<3)
					$('#unVstdShpTblNtfy').append('<tr style="font-size: 12px;text-align:center;"><td style="padding: 5px 0px;word-break: break-all;">&nbsp;'+objct.Name+'</td><td>'+sales_date+'</td><td style="text-align:center;">'+sales_days+'</td><td>'+attnds_date+'</td><td>'+attnds_days+'</td></tr>');
				}
			}
			if(objct.status=='norows')
			{
				if(status!='notification')
				{
					$('.unVstShpCls').html('0');
					$('#unVstdShpTbl').append('<tr style="font-size: 12px;text-align:center;"><td style="padding: 5px 0px;" colspan="5">No Records!</td>');
				}
				else
					$('#unVstdShpTblNtfy').append('<tr style="font-size: 12px;text-align:center;"><td style="padding: 5px 0px;" colspan="5">No Records!</td>');
			}
			if(objct.status=='failed')
			{
				if(status!='notification')
				{
					$('.unVstShpCls').html('0');
					$('#unVstdShpTbl').append('<tr style="font-size: 12px;text-align:center;"><td style="padding: 5px 0px;" colspan="5">Failed : No Records!</td>');
				}
				else
					$('#unVstdShpTblNtfy').append('<tr style="font-size: 12px;text-align:center;"><td style="padding: 5px 0px;" colspan="5">Failed : No Records!</td>');
			}
		});
		if(status!='notification')
			$('#unvisitedShpList').fadeIn('slow');
		else
			$('#unvisitedShpListNtfy').fadeIn('slow');
	});
}

/* Today Task end. */

/* Target Achievement Start */

$(document).on('click','#trgtAchvmnt',function()
{
	window.location.replace('#page16');
});

$('#page16').on('pageshow',function()
{
	getLatLong();
	$('#shpWiseTrgtDiv').fadeOut('slow');
	var statusTrgt = 'pageshow';
	getTrgtAchvmt(statusTrgt);
});

function getTrgtAchvmt(statusTrgt)
{
	var status = statusTrgt;
	var app_user = localStorage.getItem('app_user');
	var app_userId = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl+'modelwiseTarget.php?app_user='+app_user+'&app_userId='+app_userId,function(data)
	{
		console.log(data);
		var jres = data.result;
		$('#trgtAchievementNtfy_inner').html('');
		if(status!='notification_trgtAch')
		{
			$('#trgtAchvmntList').html('');
			var product_category_namesArr = [];
			$.each(jres,function(index,ob)
			{
				if($.inArray(ob.product_category.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ''),product_category_namesArr)==-1)
				{
					product_category_namesArr.push(ob.product_category.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ''));
					var ht = '';
					ht += '<table id="'+ob.product_category.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '')+'_tbl" width="100%" border="1" style="border-collapse:collapse;margin-bottom:5%;border-color: cornflowerblue;"><tr"><th colspan="6" class="trgtAchTbl_th">'+ob.product_category+'</th></tr><tr style="background: cornsilk;"><th>Model</th><th>Trgt</th><th>Achv</th><th>Diff</th><th>%</th>';
					if(ob.product_category=="Smart Phones")
						ht += '<th>Act</th>';
					ht += '</tr></table>';
					$('#trgtAchvmntList').append(ht);
				}
			});
			console.log(product_category_namesArr);
		}
		$.each(jres,function(index,objj)
		{
			if(status!='notification_trgtAch')
			{
				if(parseInt(objj.target)>parseInt(objj.totalQty) || parseInt(objj.target)==parseInt(objj.totalQty))
					var diff = parseInt(objj.target)-parseInt(objj.totalQty);
				else
					var diff = '';
				var percnt = parseInt(objj.totalQty)/parseInt(objj.target)*100;
				if(diff==0)
					percnt = '100';
				var htm = '';
				htm += '<tr style="text-align: center;text-shadow: none;"><td style="padding:3px;">'+objj.modelFullName+'</td><td style="padding:3px;">'+objj.target+'</td><td style="padding:3px;">'+objj.totalQty+'</td><td style="padding:3px;">'+diff+'</td><td style="padding:3px;">'+percnt.toFixed(1)+'</td>';
				if(objj.product_category=="Smart Phones")
					htm += '<td style="font-size: 13px;background: darkkhaki;color: white;" mdl="'+objj.modelFullName+'" class="shpTrgt_Btn trgtAchvMdlLink"><i class="fa fa-chevron-right" aria-hidden="true"></i></td>';
				htm += '</tr>';	
				$('#'+objj.product_category.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '')+'_tbl').append(htm);
			}
			else
			{
				if(jres.status!='failed')
					$('#trgtAchievementNtfy_inner').append('<table id="trgtNtfyId'+index+'" class="trgtNtfyTblCls" model="'+objj.modelFullName+'" height="auto" style="float:left;border-collapse:collapse;background-color: rgb(49, 136, 203);border-right: 3px solid white;text-shadow:none;border-top: 2px solid darksalmon;width:87px;" border="0"><tr><td style="color:white;text-shadow:none;font-weight:bold;border: none;padding: 4px;font-size:13px;">&nbsp;'+objj.modelFullName+'</td></tr><tr><td style="color: chartreuse;padding: 3px;border: none;font-size: 13px;">&nbsp;&nbsp;'+objj.target+'</td></tr><tr><td style="color: chartreuse;padding: 3px;border: none;font-size: 13px;">&nbsp;&nbsp;'+objj.totalQty+'</td></tr></table>');	
				else
					$('#trgtAchievementNtfy_inner').html('<p style="color:rgb(49, 136, 203);"><strong>&nbsp;&nbsp;Target Achievement Not Found!</strong></p>');
			}
		});
	});
}
$(document).on('click','.trgtNtfyTblCls',function()
{
	window.location.replace('#page16');
	localStorage.removeItem('modelClick');
	var model = $(this).attr('model');
	localStorage.setItem('modelClick',model);
});
$(document).on('click','#unvisitedShpListNtfy',function()
{
	window.location.replace('#page15');
});

$(document).on('click','.trgtAchvMdlLink',function()
{
	$('.trgtAchvMdlLink').parent('tr').css({'border':'1px solid black','color':'black','text-shadow':'none','font-weight': 'normal'});
	$(this).parent('tr').css({'border': '1px solid blue','color': 'blue','font-weight': 'bold'});
	var mdl = $(this).attr('mdl');
	getShpwiseTrgt(mdl);
	$('html,body').animate({scrollTop:0},500);
});

function getShpwiseTrgt(mdl)
{
	$('.spinner').fadeIn('slow');
	var app_userId = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl+'modelwiseTarget.php?app_userId='+app_userId+'&modelName='+mdl+'&shpWiseTrgt=yes',function(data)
	{
		console.log(data);
		var jres = data.result;
		if(jres.status!='failed')
		{
			$('#shpWiseTrgtDiv').html('<p><img src="images/ic_action.png" id="trgtClsBtn" style="margin: 4px 5px 0 0;float: right;"></p><p class="shpwiseTrgtMdl" style="padding: 10px;color: orange;"></p><table id="shpwiseTargetTbl" width="100%" style="border-collapse:collapse;font-size: 13px;" border="1"><tr><th>Shop Name</th><th>Target</th><th>Sold</th><th>Diff</th><th>%</th></tr></table>');
			var totalTrgt = 0;
			var totalSold = 0;
			var totalPrcnt = 0;
			$.each(jres,function(index,objj)
			{
				totalTrgt = parseInt(totalTrgt)+parseInt(objj.target);
				totalSold  = parseInt(totalSold)+parseInt(objj.TotalQty);
				
				if(parseInt(objj.target)>parseInt(objj.TotalQty) || parseInt(objj.target)==parseInt(objj.TotalQty))
				{	
					var diff = parseInt(objj.target)-parseInt(objj.TotalQty);
					var percnt = parseInt(objj.TotalQty)/parseInt(objj.target)*100;
				}
				else
				{
					var diff = '';
					var percnt = '';
					totalPrcnt = '';
				}
				$('#shpwiseTargetTbl').append('<tr style="text-shadow: none;"><td style="padding: 4px;" class="lft">&nbsp;'+objj.Name+'</td><td class="rght">'+objj.target+'&nbsp;</td><td class="rght">'+objj.TotalQty+'&nbsp;</td><td class="rght">'+diff+'&nbsp;</td><td class="rght">'+Math.round(percnt)+'&nbsp;</td></tr>');
			});
			totalPrcnt = parseInt(totalSold)/parseInt(totalTrgt)*100;
			totalPrcnt = totalPrcnt.toString();
			totalPrcnt = totalPrcnt.substring(0,4);
			$('#shpwiseTargetTbl').append('<tr style="text-align: center;text-shadow: none;font-weight:bold;"><td>Total</td><td></td><td>'+totalSold+'</td><td></td><td>'+totalPrcnt+'</td></tr>');
			$('.shpwiseTrgtMdl').html('Model : '+mdl);
			$('.spinner').fadeOut('slow');
			$('#shpWiseTrgtDiv').fadeIn('slow');
		}
		else
		{
			$('#shpWiseTrgtDiv').html('<p><img src="images/ic_action.png" id="trgtClsBtn" style="margin: 4px 5px 0 0;float: right;"></p><p class="shpwiseTrgtMdl" style="padding: 10px;color: orange;">'+mdl+'</p><table id="shpwiseTargetTbl" width="100%" style="border-collapse:collapse;font-size: 13px;" border="1"><tr><th colspan="5" style="text-align:center;color:red;">No Records Found!</th></tr></table>');
			$('.spinner').fadeOut('slow');
			$('#shpWiseTrgtDiv').fadeIn('slow');
		}
	});
}

$(document).on('click','#trgtClsBtn',function()
{
	$('#shpWiseTrgtDiv').fadeOut('slow');
	$('.trgtAchvMdlLink').parent('tr').css({'border':'1px solid black','color':'black','text-shadow':'none','font-weight': 'normal'});
});

/* Target Achievement End */

$(document).on('click','#page3ShpSelectLbl',function()
{
	$('#page3ShpSelectLbl').fadeOut('slow');
	$('#page3ShpSelectDiv').fadeIn('slow');
});
$(document).on('click','#page4ShpSelectLbl',function()
{
	$('#page4ShpSelectLbl').fadeOut('slow');
	$('#page4ShpSelectDiv').fadeIn('slow');
});
$(document).on('click','#page7ShpSelectLbl',function()
{
	$('#page7ShpSelectLbl').fadeOut('slow');
	$('#page7ShpSelectDiv').fadeIn('slow');
});
$(document).on('click','#page8ShpSelectLbl',function()
{
	$('#page8ShpSelectLbl').fadeOut('slow');
	$('#page8ShpSelectDiv').fadeIn('slow');
});

$(document).on('click','#addStocks',function()
{
	window.location.replace('#page18');
});
var prdctMdlArr = [];
var prdctOriginalMdlArr = [];
var stckLoadedMdlValArr = [];
var stckUpdtMdlValArr = [];
var stckOrdersDataArr = [];
$('#page18').on('pageshow',function()
{
	$('#page18ShpSelectDiv').fadeIn('slow');
	$('#page18ShpSelectLbl').fadeOut('slow');
	if(localStorage.getItem('stckOrder'))
		localStorage.removeItem('stckOrder');
	if(localStorage.getItem('stckOrdersDataLcl'))
		localStorage.removeItem('stckOrdersDataLcl');
	getLatLong();
	prdctMdlArr = [];
	prdctOriginalMdlArr = [];
	$('#srchShopListStck').fadeOut('fast');
	$('#stcksForm')[0].reset();
	$('#shpNameStcks').html('<option value=""><-- : select shop : --></option>');
	var isOff = 'onLine' in navigator && !navigator.onLine;

	if ( isOff ) {
    	onFailedStcks();
	}
	else {
		getCurrentPosition9();
	}
	var req = 'pageshow';
	var shpId = 0;
	$('.stcksPage18w').fadeOut('slow');
	$('#stcksList').html('<table border="0" style="border-collapse:collapse;font-size: 15px;" width="99.9%" height="auto" id="stckTbl"><tr style="color: white;font-weight: bold;text-shadow: none;background: rgb(49, 136, 203);"><th style="padding:10px;text-align: left;">Model</th><th style="text-align: center;">Qty</th></tr><tr><td colspan="3" style="text-align:center;padding: 50px;">Select the shop to get available stocks!.</td></tr></table>');
	$('.stckBtn_p').hide('slow');
});

function getCurrentPosition9(){
	navigator.geolocation.getCurrentPosition(
		onSuccessStcks,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					onSuccessStcks, 
					function(error){
							console.log('gps error');
						},
					geoOptionsLow);
			 	return;
				}
				console.log('gps error');	
			}, 
		geoOptionsHigh
	); 
}
function onSuccessStcks(position)
{
	var Shp_lat  = position.coords.latitude;
	var Shp_long = position.coords.longitude;
	$('#orderLat').val(Shp_lat);
	$('#orderLong').val(Shp_long);
	var appUsrIdLcl = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl +'NearestShop.php?appUsrIdLcl='+appUsrIdLcl+'&lat='+Shp_lat+'&long='+Shp_long,function(data)
	{
		var jres = data.Result;
		$.each(jres,function(index,obj)
		{
			if(obj.Status=='success')
			{
				$('#shpNameStcks').append('<option value="'+obj.shopId+'">'+obj.shopName+'</option>');
			}
			if(obj.shopName=='emptySet')
			{
				$('#shpNameStcks').html('<option value=""><---: Select current shop :---></option><option value="">No shops found !!</option>');
			}
		});
	});
}
function onFailedStcks()
{
	window.plugins.toast.showLongCenter(':: No internet connectivity ::');
}
$(document).on('change','#shpNameStcks',function()
{
	if($(this).val()!='')
	{
		prdctMdlArr = [];
		prdctOriginalMdlArr = [];
		$('#shopNameViewStck').val('');
		var shpId = $(this).val();
		$('#shopNameTxtStck').val(shpId);
		var srchShpsName = $(this).find("option:selected").text();
		$('#page18ShpSelectLbl').html(srchShpsName);
		$('#page18ShpSelectDiv').fadeOut('slow');
		$('#page18ShpSelectLbl').fadeIn('slow');
		$('#stckShpId').val($(this).val());
		$('#stckShpName').val(srchShpsName);
		getCurrentStocks(shpId);
	}
	else
	{
		$('.stcksPage18w').fadeOut('slow');
		$('#stcksList').html('<table border="0" style="border-collapse:collapse;font-size: 15px;" width="99.9%" height="auto" id="stckTbl"><tr style="color: white;font-weight: bold;text-shadow: none;background: rgb(49, 136, 203);"><th style="padding:10px;text-align: left;">Model</th><th style="text-align: center;">Qty</th></tr><tr><td colspan="3" style="text-align:center;padding: 50px;">Select the shop to get available stocks!.</td></tr></table>');
		$('.stckBtn_p').hide('slow');
	}
});
$(document).on('click','#page18ShpSelectLbl',function()
{
	$('#page18ShpSelectLbl').fadeOut('slow');
	$('#page18ShpSelectDiv').fadeIn('slow');
});

$(document).on('keyup','#shopNameViewStck',function()
{
	if($(this).val()!='')
	{
		var srchShopName = $('#shopNameViewStck').val();
		var srchStr = srchShopName.includes("&");
		var srchStr1 = srchShopName.includes("#");
		if(srchStr)
			var srchShopName = srchShopName.replace("&","!!");
		if(srchStr1)
			var srchShopName = srchShopName.replace("#","@@");
		var appUsrIdLcl = localStorage.getItem('app_userId');	
		$.getJSON(serviceUrl+'searchShops.php?appUsrIdLcl='+appUsrIdLcl+'&SrchShopTxt='+srchShopName,function(data)
		{
			$('#srchShopListStck').html('');
			$('#srchShopListStck').html('<p style="text-align:right;margin: 0;"><img src="images/ic_action.png" id="srchClsStck"></p>');
			var jres = data.Result;
			$.each(jres,function(index,objct)
			{
				if(objct.Status=='Success')
					$('#srchShopListStck').append('<p style="text-align:center;" srchSId="'+objct.id+'" class="srchShpNameStck">'+ objct.shopName +'</p>');
				if(objct.Status=='NoRows')
					$('#srchShopListStck').html('<p style="color:red;text-align:center;font-weight:bold;">:: No Records ::</p>');
			});
			$('#srchShopListStck').fadeIn('fast');
		});
	}
	else
		$('#srchShopListStck').fadeOut('fast');
});
$(document).on('click','#srchClsStck',function()
{
	$('#srchShopListStck').fadeOut('fast');
	$('#shopNameTxtStck').val('');
});
$(document).on('click','.srchShpNameStck',function()
{
	$('#shopNameTxtStck').val($(this).attr('srchSId'));
	$('#srchShopListStck').fadeOut('slow');
	var srchShpsName = $(this).text();
	var shpId = $(this).attr('srchsid');
	$('#page18ShpSelectLbl').html(srchShpsName);
	$('#page18ShpSelectDiv').fadeOut('slow');
	$('#page18ShpSelectLbl').fadeIn('slow');
	getCurrentStocks(shpId);
});

$(document).on('click','.stckHitCls',function()
{
	$('.stckHitCls').removeClass('stkCls');
	$(this).addClass('stkCls');
	var stckId = $(this).attr('stckId');
	$('#'+stckId).focus();
});

$(document).on('click','#stckSaveBtn',function()
{
	$('#shpFullNameTxt').val($('#stckShpName').val());
	stckUpdtMdlValArr = [];
	$('.stcksPage18w').fadeOut('fast');
	var app_userId = localStorage.getItem('app_userId');
	var shpId = $('#shpNameStcks').val();
	var flag = 1;
	if(localStorage.getItem('stckValStatus'))
		localStorage.removeItem('stckValStatus');
	
	$.each(prdctMdlArr,function(index,obj)
	{
		var val = obj.toString();
		var val = $('.'+val).val();
		if(val!='')
			localStorage.setItem('stckValStatus','1');
	});
	$.each(prdctOriginalMdlArr,function(index,a)
	{
		var prdct_mdl = a.toString();
		prdct_mdl = a.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
		var qty = $('.'+prdct_mdl).val();
		//if(qty!=0)
			stckUpdtMdlValArr.push({'Model':a,'Qty':qty});
	});
	
	if(shpId=='')
	{
		$('.stcksPage18w').html('Please select shop!');
		$('.stcksPage18w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.stcksPage18w').fadeIn('slow');
		flag = 0;
	}
	else if(!localStorage.getItem('stckValStatus'))
	{
		$('.stcksPage18w').html('Required : Fill atleast one field!');
		$('.stcksPage18w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.stcksPage18w').fadeIn('slow');
		flag = 0;
	}
	if(flag==1)
	{
		var stckInput = '';
		var len = prdctOriginalMdlArr.length;
		$.each(prdctOriginalMdlArr,function(index,objct)
		{
			var product_model = objct.toString();
			product_model = product_model.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
			//product_model = product_model.replace('.', '');
			var valFinal = $('.'+product_model).val();
			if(valFinal=='')
				valFinal = 0;
			if(len-1!=index)
				stckInput += objct+'!'+valFinal+',';
			else
				stckInput += objct+'!'+valFinal;
		});
		console.log(stckInput);
		$.getJSON(serviceUrl+'saveStocks.php?userId='+app_userId+'&shpId='+shpId+'&stckInput='+stckInput,function(data)
		{
			console.log(data);
			var sts = data.result;
			if(sts.status=='success')
			{
				$('.stcksPage18w').html('Success : Your data saved!');
				$('.stcksPage18w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.stcksPage18w').fadeIn('slow');
				setTimeout(function(){
					$('.stcksPage18w').fadeOut('slow');
					$('#page18ShpSelectDiv').fadeIn('slow');
					$('#page18ShpSelectLbl').fadeOut('slow');
					$('#shopNameViewStck').val('');
					$('#shpNameStcks').val('');
					$('#stcksList').html('<table border="0" style="border-collapse:collapse;font-size: 15px;" width="99.9%" height="auto" id="stckTbl"><tr style="color: white;font-weight: bold;text-shadow: none;background: rgb(49, 136, 203);"><th style="padding:10px;text-align: left;">Model</th><th style="text-align: center;">Qty</th></tr></table>');
					$('#stckTbl').append('<tr><td colspan="3" style="text-align:center;padding: 50px;">Select the shop to get available stocks!.</td></tr>');
				},2000);
				
				setTimeout(function(){
					console.log(stckLoadedMdlValArr);
					console.log(stckUpdtMdlValArr);
					if(stckLoadedMdlValArr.length!=0)
					{
						$('#stckOrderBody').html('');
						var dataVal = 'no'
						$.each(stckUpdtMdlValArr,function(index,b)
						{
							var Qty = 0;
							var req = 'false';
							$.each(stckLoadedMdlValArr,function(index,bb)
							{
								if(b.Model==bb.Model)
								{
									if(parseInt(bb.Qty)>parseInt(b.Qty))
									{	
										Qty = parseInt(bb.Qty)-parseInt(b.Qty);	
										req = 'true';
									}
								}
							});
							if(req=='true')
							{	
								dataVal = 'yes';
								//get model unique colors start
								$.getJSON(serviceUrl+'modelwiseTarget.php?getMdlColors=yes&model='+b.Model+'&Qty='+Qty,function(data)
								{
									console.log(data);
									var jsRes = data.result;
									$.each(jsRes,function(index,z)
									{
										if(z.status=='success')
										{
											$('#stckOrderBody').append('<tr id="stckOrderTr'+index+'" dp="'+z.dp+'"><td style="padding: 5px;" Model="'+b.Model+'" uId="'+index+'">&nbsp;'+b.Model+'</td><td color="'+z.color+'"><p style="width: 80%;margin: auto;">'+z.color+'</td></p><td Qty="'+z.qty+'"><p style="width: 80%;margin: auto;"><input type="number" class="stckQtyCls no-spinner" value="'+z.qty+'" placeholder="qty" style="text-align:center;width: 60px; style="padding: 5px;""/></p></td><td style="text-align:center"><i class="fas fa-trash fa-lg stckOrderDel" trId="stckOrderTr'+index+'"></i></td></tr>');
										}
									});
								});
							}
						});
					}
					else
						$('#stckOrderBody').html('<tr><td colspan="4">No Data!</td>');
					
					if(dataVal=='no')
						$('.otpPopup_stck').fadeOut('fast');
					else
						$('.otpPopup_stck').fadeIn('fast');
				},2100);
			}	
			else
			{
				$('.stcksPage18w').html('Failed : Error occured. Try again!');
				$('.stcksPage18w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.stcksPage18w').fadeIn('slow');
			}
		});
	}
});

$(document).on('change','#chequeDate',function()
{
	$('#otpPopupInside_pymnt').css('height','157px');
	var dateVal = $(this).val();
	var app_userId = localStorage.getItem('app_userId');
	var shpId = $('#shopNamePymnt').val();
	var inv_no = $('#Pymnt_InvoiceNo').val();
	var flag = 1;
	if(shpId=='')
	{
		$('.pymntPage4w').html('Please select shop!');
		$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.pymntPage4w').fadeIn('slow');
		$(this).val('');
		flag = 0;
	}
	if(inv_no=='')
	{
		$('.pymntPage4w').html('Choose Invoice Number!');
		$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.pymntPage4w').fadeIn('slow');
		$(this).val('');
		flag = 0;	
	}
	if(flag==1)
	{
		$('.pymntPage4w').fadeOut('slow');
	}
});
$(document).on('click','#prceedBtn',function()
{
	$('.popup').hide('slow');
});
$(document).on('click','#sendOtpCnclBtn',function()
{
	$('.otpPopup').hide('slow');
	$('#chequeDate').val('');
});
$(document).on('click','#sendOtpCnclBtn2',function()
{
	$('#cnfrmOtpPopupDiv').fadeIn('slow');
	$('#cnfrmOtpPopupDiv2').fadeOut('slow');
	$('.otpPopup').hide('slow');
	$('#chequeDate').val('');
});
$(document).on('click','#sendOtpBtnAdmin',function()
{
	$('.otpPopup').hide('slow');
	$('#cnfrmOtpPopupDiv').fadeIn('slow');
	$('#cnfrmOtpPopupDiv2').fadeOut('slow');
	$('#getPymnt_btn').prop('disabled',true);
	pymntBtnClickAction();
});
$(document).on('click','#sendOtpBtn',function()
{
	$('#rejectTxtId_pymnt').fadeOut();
	$('#ldrIconId_pymnt').show('fast');
	$('#otpCnclBtnId_pymnt').show('fast');
	$('#apprvdTxtId_pymnt').hide('fast');
	$('#otpPopupInside_pymnt').css('height','157px');
	var app_userId = localStorage.getItem('app_userId');
	var currentShpName =  $('#currentShpName').val();
	var shpId = $('#shopNamePymnt').val();
	var fos_name = localStorage.getItem('fos_name')+'!!'+currentShpName;
	var chequeDate = '';
	var inv_no = $('#Pymnt_InvoiceNo').val();
	var test_amt = $('#Pymnt_Amt2').val();
	var msg = '';
	if(localStorage.getItem('OutstandingsArrBackup'))
	{
		var outstnds = JSON.parse(localStorage.getItem('OutstandingsArrBackup'));
		if(localStorage.getItem('overdueCheques'))
		{
			var overdueCheques = JSON.parse(localStorage.getItem('overdueCheques'));
			var len_a = overdueCheques.length;
			$.each(overdueCheques,function(index,k)
			{
				var overdue = 0;
				var outstanding_date = '';
				var pending_amount = 0;
				$.each(outstnds,function(index,n)
				{
					if(n.ref_no==k.inv)
					{
						overdue = n.overdue;
						outstanding_date = n.outstanding_date;
						pending_amount = n.pending_amount;
					}
				});
				if(len_a-1==index)
					msg += k.inv+'!'+pending_amount+'!'+outstanding_date+'!'+k.dateVal+'!'+overdue+'!'+k.amt;
				else
					msg += k.inv+'!'+pending_amount+'!'+outstanding_date+'!'+k.dateVal+'!'+overdue+'!'+k.amt+',';	
			});
		}
	}
	console.log(msg);	
	$.post(serviceUrl+'verifyCode.php',{pymntPopupOtp:'yes',app_userId:app_userId,fos_name:fos_name,currentShpName:currentShpName,shpId:shpId,msg:msg,pymntOtpApprvl:'yes'},function(data)
	{
		var jres = $.parseJSON(data).result;
		if(jres.status=='success')
		{
			$('#otpPopupInside_pymnt').css('height','180px');
			
			$('#cnfrmOtpPopupDiv').fadeOut('slow');
			$('#cnfrmOtpPopupDiv2').fadeIn('slow');
			var currentDateTime = jres.currentDateTime;
			var myVar = setInterval(function()
			{
				//{
				$.ajax(
				{
					url : serviceUrl +"verifyCode.php",
					type:"GET",
					data:'getOTPResponse=yes&currentDateTime='+currentDateTime+'&app_userId='+app_userId+'&shpId='+shpId,
					contentType:false,
					cache:false,
					processData:false,
					success:function(data)
					{
						console.log(data);
						var jres = $.parseJSON(data).result;
						if(jres.status=='success')
						{
							if(jres.response_code=='Approved')
							{
								$('#ldrIconId_pymnt').fadeOut('fast');
								$('#apprvdTxtId_pymnt').fadeIn('fast');
								$('#otpCnclBtnId_pymnt').hide();
								$('#otpPopupInside_pymnt').css('height','auto');
								clearInterval(myVar);
								setTimeout(function(){
									$('.otpPopup').hide('slow');
									$('#cnfrmOtpPopupDiv').fadeIn('slow');
									$('#cnfrmOtpPopupDiv2').fadeOut('slow');
									$('#getPymnt_btn').prop('disabled',true);
									pymntBtnClickAction();
								},1500);
							}
							if(jres.response_code=='Rejected')
							{
								$('#ldrIconId_pymnt').fadeOut('fast');
								$('#apprvdTxtId_pymnt').fadeOut('fast');
								$('#otpCnclBtnId_pymnt').hide();
								$('#rejectTxtId_pymnt').fadeIn();
								$('#otpPopupInside_pymnt').css('height','190px');
								clearInterval(myVar);
							}
						}
					}//success:function(data)
				});//ajax
			},3000);
		}
		else
		{
			alert('Please try again!');
		}
	});
});
$(document).on('click','#cnfrmPopupOkBtn',function()
{
	var popupOtpTxt = $('#popupOtpTxt').val();
	var flag = 1;
	if(popupOtpTxt=='')
	{
		alert('Please enter OTP!');
		flag = 0;
	}
	if(popupOtpTxt.length<4)
	{
		alert('OTP must be a 4 digit!');
		flag = 0;
	}
	if(flag==1)
	{
		var app_userId = localStorage.getItem('app_userId');
		$.getJSON(serviceUrl+'verifyCode.php?cnfrmCodePopupOtp=yes&cnfrmOtpPopupCode='+popupOtpTxt+'&app_userId='+app_userId,function(data)
		{
			var jres = data.result;
			if(jres.status=='success')
			{
				$('.otpPopup').hide('slow');
				$('#cnfrmOtpPopupDiv').fadeIn('slow');
				$('#cnfrmOtpPopupDiv2').fadeOut('slow');
			}
			else
			{
				alert('Please give correct OTP!');
			}
		});
	}
});

/* payment collection page start */
$(document).on('click','#pymntCollection',function()
{
	window.location.replace('#page19');
});
$('#page19').on('pageshow',function()
{
	getLatLong();
	$('#srchShopListPymntColtn').fadeOut('fast');
	$('#shpNamePymntColtn').html('<option value=""><-- : select shop : --></option>');
	var isOff = 'onLine' in navigator && !navigator.onLine;

	if ( isOff ) {
    	onFailedPymntColect();
	}
	else {
		getCurrentPosition10();
	}
		$('#pymntColtnMnths').html('');

        var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
        var varDate = new Date();
        var month = varDate.getMonth()+1;
        var currentDay = varDate.getDate();
		$('#pymntColtnMnths').attr('yearmonth',varDate.getFullYear()+'-'+month);
        for ( var i = 0; i <= 5; i++) {

            var now = new Date();
            now.setDate(1);
            var date = new Date(now.setMonth(now.getMonth() - i));
            var datex = (("0" + (date.getMonth() + 1)).slice(-2));
			var year = date.getFullYear();

             $("#pymntColtnMnths").append("<option value='"+year+"-"+datex+"'>&nbsp;&nbsp;" + monthNames[date.getMonth()] +"</option>");
		}
});
$(document).on('change','#pymntColtnMnths',function()
{
	var yearmnth = $(this).val();
	$('#pymntColtnMnths').attr('yearmonth',yearmnth);
	var app_user = localStorage.getItem('app_userId');
	var shpId = $('#shopNameTxtPymntColtn').val();
	if(shpId!='')
		getPymntDataAll(app_user,shpId);
	else
		alert('Please select shop');
});

function getCurrentPosition10(){
	navigator.geolocation.getCurrentPosition(
		onSuccessPymntColect,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					onSuccessPymntColect, 
					function(error){
							console.log('gps error');
						},
					geoOptionsLow);
			 	return;
				}
				console.log('gps error');	
			}, 
		geoOptionsHigh
	); 
}
function onSuccessPymntColect(position)
{
	var Shp_lat  = position.coords.latitude;
	var Shp_long = position.coords.longitude;
	var appUsrIdLcl = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl +'NearestShop.php?appUsrIdLcl='+appUsrIdLcl+'&lat='+Shp_lat+'&long='+Shp_long,function(data)
	{
		var jres = data.Result;
		$.each(jres,function(index,obj)
		{
			if(obj.Status=='success')
			{
				$('#shpNamePymntColtn').append('<option value="'+obj.shopId+'">'+obj.shopName+'</option>');
			}
			if(obj.shopName=='emptySet')
			{
				$('#shpNamePymntColtn').html('<option value=""><---: Select current shop :---></option><option>No shops found !!</option>');
			}
		});
	});
}
function onFailedPymntColect()
{
	window.plugins.toast.showLongCenter(':: No internet connectivity ::');
}
$(document).on('change','#shpNamePymntColtn',function()
{
	$('#shopNameViewPymntColtn').val('');
	var shpId = $(this).val();
	$('#shopNameTxtPymntColtn').val(shpId);
	var srchShpsName = $(this).find("option:selected").text();
	$('#page19ShpSelectLbl').html(srchShpsName);
	$('#page19ShpSelectDiv').fadeOut('slow');
	$('#page19ShpSelectLbl').fadeIn('slow');
	var app_user = localStorage.getItem('app_userId');
	var shpId = $('#shopNameTxtPymntColtn').val();
	getPymntDataAll(app_user,shpId);
});
$(document).on('click','#page19ShpSelectLbl',function()
{
	$('#page19ShpSelectLbl').fadeOut('slow');
	$('#page19ShpSelectDiv').fadeIn('slow');
});

$(document).on('keyup','#shopNameViewPymntColtn',function()
{
	if($(this).val()!='')
	{
		var srchShopName = $('#shopNameViewPymntColtn').val();
		var srchStr = srchShopName.includes("&");
		var srchStr1 = srchShopName.includes("#");
		if(srchStr)
			var srchShopName = srchShopName.replace("&","!!");
		if(srchStr1)
			var srchShopName = srchShopName.replace("#","@@");
		
		var appUsrIdLcl = localStorage.getItem('app_userId');	
		$.getJSON(serviceUrl+'searchShops.php?appUsrIdLcl='+appUsrIdLcl+'&SrchShopTxt='+srchShopName,function(data)
		{
			$('#srchShopListPymntColtn').html('');
			$('#srchShopListPymntColtn').html('<p style="text-align:right;margin: 0;"><img src="images/ic_action.png" id="srchClsPymntColtn"></p>');
			var jres = data.Result;
			$.each(jres,function(index,objct)
			{
				if(objct.Status=='Success')
					$('#srchShopListPymntColtn').append('<p style="text-align:center;" srchSId="'+objct.id+'" class="srchShpNamePymntColtn">'+ objct.shopName +'</p>');
				if(objct.Status=='NoRows')
					$('#srchShopListPymntColtn').html('<p style="color:red;text-align:center;font-weight:bold;">:: No Records ::</p>');
			});
			$('#srchShopListPymntColtn').fadeIn('fast');
		});
	}
	else
		$('#srchShopListPymntColtn').fadeOut('fast');
});
$(document).on('click','#srchClsPymntCollect',function()
{
	$('#srchShopListPymntColtn').fadeOut('fast');
	$('#shopNameTxtPymntColtn').val('');
});
$(document).on('click','.srchShpNamePymntColtn',function()
{
	$('#shopNameTxtPymntColtn').val($(this).attr('srchSId'));
	$('#srchShopListPymntColtn').fadeOut('slow');
	var srchShpsName = $(this).text();
	$('#page19ShpSelectLbl').html(srchShpsName);
	$('#page19ShpSelectDiv').fadeOut('slow');
	$('#page19ShpSelectLbl').fadeIn('slow');
	var app_user = localStorage.getItem('app_userId');
	var shpId = $('#shopNameTxtPymntColtn').val();
	getPymntDataAll(app_user,shpId);
});
function getPymntDataAll(app_user,shpId)
{
	var yearmnth = $('#pymntColtnMnths').val();
	$.getJSON(serviceUrl+'delivery.php?pymntColectPage=yes&app_userId='+app_user+'&shpId='+shpId+'&yearmnth='+yearmnth,function(data)
	{
		$('#AllCollectedPymnt').html('');
		var jres = data.Result;
		if(jres.status=='failed')
		{
			$('#AllCollectedPymnt').append('<p style="text-align:center;color:red;">No Payments Found!</p>');
		}
		else
		{
			$('#AllCollectedPymnt').append('<p style="color:green;text-align: center;"><strong>Amount Received - Rs.</strong><span id="AlltotalPymnt" style="color:black;background:gainsboro;padding: 5px;"></span></p>')
			$('#AllCollectedPymnt').append('<div id="AllpymntClctDivCash" style="display:none;margin-top:20px;"><p style="padding:10px;background:cornflowerblue;color:azure">&nbsp;&nbsp;Payment Type - Cash</p><table width="100%" border="1" style="border-collapse:collapse;" id="AllpymntCollectedTbl"><tr style="font-size:13px;font-weight:bold;"><th style="padding:4px;">Date</th><th>Fos</th><th>Invoice</th><th>Amt</th></tr></table></div><div id="AllpymntClctDivCheque" style="display:none;margin-top:20px;"><p style="padding:10px;background:cornflowerblue;color:azure">&nbsp;&nbsp;Payment Type - Cheque</p><table width="100%" border="1" style="border-collapse:collapse;" id="AllpymntCollectedTbl1"><tr style="font-size:13px;font-weight:bold;"><th style="padding:4px;">Date</th><th>Fos</th><th>Invoice</th><th>Cheque</th><th>Amt</th></tr></table></div></div><div id="AllpymntClctDivNeft" style="display:none;margin-top:20px;"><p style="padding:10px;background:cornflowerblue;color:azure">&nbsp;&nbsp;Payment Type - NEFT</p><table width="100%" border="1" style="border-collapse:collapse;" id="AllpymntCollectedTbl2"><tr style="font-size:13px;font-weight:bold;"><th style="padding:4px;">Date</th><th>Fos</th><th>Invoice</th><th>Ref No</th><th>Amt</th></tr></table></div><div id="AllpymntClctDivCn" style="display:none;margin-top:20px;"><p style="padding:10px;background:cornflowerblue;color:azure">&nbsp;&nbsp;Payment Type - Credit Note</p><table width="100%" border="1" style="border-collapse:collapse;" id="AllpymntCollectedTbl3"><tr style="font-size:13px;font-weight:bold;"><th style="padding:4px;">Date</th><th>Fos</th><th>Invoice</th><th>CN No</th><th>Amt</th></tr></table></div>');
			var totalPymnt = 0;
			$.each(jres,function(index,objj)
			{
				var indx = index+1;
				totalPymnt += parseInt(objj.amount);
				fos_name = objj.fos_name;
				if(objj.cash_type=='cash')
				{
					$('#AllpymntCollectedTbl').append('<tr style="font-size:13px;"><td style="text-align:center;padding:3px;">'+objj.payment_date+'</td><td style="word-break: break-all;">'+fos_name+'</td><td class="cntr">'+objj.invoice_no+'</td><td style="text-align:center;">'+objj.amount+'</td></tr>');
					$('#AllpymntClctDivCash').fadeIn('slow');	
				}
				if(objj.cash_type=='cheque')
				{
					$('#AllpymntCollectedTbl1').append('<tr style="font-size:13px;"><td style="text-align:center;padding:3px;">'+objj.payment_date+'</td><td style="word-break: break-all;">'+fos_name+'</td><td class="cntr">'+objj.invoice_no+'</td><td style="text-align:center;">'+objj.cheque_no+'</td><td style="text-align:center;">'+objj.amount+'</td></tr>');
					$('#AllpymntClctDivCheque').fadeIn('slow');
				}
				if(objj.cash_type=='neft')
				{
					$('#AllpymntCollectedTbl2').append('<tr style="font-size:13px;"><td style="text-align:center;padding:3px;">'+objj.payment_date+'</td><td style="word-break: break-all;">'+fos_name+'</td><td class="cntr">'+objj.invoice_no+'</td><td style="text-align:center;">'+objj.cheque_no+'</td><td style="text-align:center;">'+objj.amount+'</td></tr>');
					$('#AllpymntClctDivNeft').fadeIn('slow');
				}
				if(objj.cash_type=='cn')
				{
					$('#AllpymntCollectedTbl3').append('<tr style="font-size:13px;"><td style="text-align:center;padding:3px;">'+objj.payment_date+'</td><td style="word-break: break-all;">'+fos_name+'</td><td class="cntr">'+objj.invoice_no+'</td><td style="text-align:center;">'+objj.cheque_no+'</td><td style="text-align:center;">'+objj.amount+'</td></tr>');
					$('#AllpymntClctDivCn').fadeIn('slow');
				}
			});
			$('#AlltotalPymnt').html('<strong>&nbsp;'+totalPymnt+'</strong>');	
		}
	});
}
/* payment collection page end */
// $('#input').datepicker({ minDate : new Date() });


