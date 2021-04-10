
// JavaScript Document
$.support.cors = true;
var pictureSource;   // picture source
var destinationType; // sets the format of returned value
var serviceUrl = "https://att.vtt.im/app/php/";
var bckKeyCount=0;
var attendanceArray = [];
var version = '2.8.3';

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
				window.location.replace('#page3');
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
				if(rs.AdminId!='NoAmin'){
					localStorage.setItem('AdminId',rs.AdminId);
				}
				else{
					localStorage.removeItem('AdminId');
				}
				timer_entry();
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
/*checkin*/
$(document).on('click','#checked_in',function(e)
{
	var data = $('#homePageForm').serialize();
	$.ajax({
		url: serviceUrl+'api.php?type=checked_in&data='+data,
		processData: false,
		contentType: false,
		type: 'GET',
		success: function(data){
			console.log(data)
			if(data.status == 'success'){
				localStorage.setItem("shop_id_loc", $("#shpName").val().toString());
				document.getElementById("checked_in").disabled = true;
				$('#checked_in').parent().css({"color":"green",});
				$('#clock_started').html('Clock is Running!!!');
				$('#fafaclock').html('<i style="height:45px; width:45px; color:#1a8cff;"  class="fas fa-stopwatch"></i>');
				var checked_in_time = data.start_time;
					//timer(checked_in_time);
			}else if (data.status == 'alredy Checkin') {
			  alert("alredy check in")
			}else{
				alert("try again")
			}
		}
	});
	localStorage.setItem("condition_check", "check_out_fun");
});
function timer(checked_in_time){
	var countDownDate = new Date(checked_in_time).getTime();
	var x = setInterval(function() {
	var now = new Date().getTime();
	  var distance = now - countDownDate;
	  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
	  document.getElementById("show_timer").innerHTML =  hours + "h :"
	  + minutes + "m :" + seconds + "s ";
	  if (distance < 0) {
	    clearInterval(x);
	    document.getElementById("demo").innerHTML = "EXPIRED";
	  }
	}, 1000);
}
$(document).on('click','#checked_out',function(e){
var check_in_out = localStorage.getItem("condition_check");
	if(check_in_out == null){
		$('#checked_out').parent().css({"color":"red",});
		$('#clock_started').html('Please Check in');
	}else{
		localStorage.removeItem("condition_check");
		var data = $('#homePageForm').serialize();
		var totalTimeVal = "";
		var htm="";
		$.ajax({
			url: serviceUrl+'api.php?type=checked_out&data='+data,
			processData: false,
			contentType: false,
			type: 'GET',
			success: function(data){
				localStorage.setItem("shop_id_loc", "");
				localStorage.setItem("shop_name_loc", "");
				console.log(data)
				if(data.status == 'success'){
					htm += '<h3 style="text-align: center;">Working Hours</h3>';
					htm += '<center><h4> Duration : '+data.data+'</h4></center>';
					$('#total_time_value').html(htm);
					document.getElementById("checked_out").disabled = true;
					$('#checked_out').parent().css({"color":"red",});
					$('#clock_started').html('clock Stoped');
					$('#show_timer').hide();
				}else{
					alert("try again")
				}
			}
		});
	}
});
    $('#check_date').on('click', function(){
			var fromdate = new Date($('#fromDate').val());
			var todate = new Date($('#toDate').val());
			var from_date = [];
			var to_date = [];
			fromday = fromdate.getDate();
			frommonth = fromdate.getMonth() + 1;
			fromyear = fromdate.getFullYear();
			from_date = [fromyear,frommonth,fromday].join('-');

			today = todate.getDate()+1;
			tomonth = todate.getMonth() + 1;
			toyear = todate.getFullYear();
			to_date = [toyear,tomonth,today].join('-');
			var userId = localStorage.getItem('app_userId');
		$.ajax({ 
			url: serviceUrl+'api.php?type=logTimeData&userId='+userId+'&from_date='+from_date+'&to_date='+to_date,
			success: function(data){
				console.log(data);
				if(data.status == 'success'){
					var htm = "";
					htm += '<table style="width:100%; border-collapse: collapse;  border: 2px solid green;">';
					htm += '<tr style=" border: 1px solid black;border-collapse: collapse;">';
					htm += '<th style=" padding: 15px;    border: 2px solid green;">';
					htm += 'start Time';
					htm += '</th>';
					htm += '<th style=" padding: 15px;    border: 2px solid green;">';
					htm += 'end Time';
					htm += '</th>';	
					htm += '<th style=" padding: 15px;    border: 2px solid green;">';
					htm += 'duration';
					htm += '</th>';	
					htm += '<tr>';
					var jres = data.data;
					final_time = "";

						if(jres.length!=0)
						{
							$.each(jres,function(index,val)
							{
								console.log(jres);
								var start = new Date(val.start_time);
								var end = new Date(val.end_time);
								if(val.start_time == null || val.end_time == null){
									final_time = "<b style='color:red'>Please Check out</b>";
								}else{
									var seconds = Math.floor((end - start)/1000);
									var minutes = Math.floor(seconds/60);
									var hours = Math.floor(minutes/60);
									var days = Math.floor(hours/24);
									hours = hours-(days*24);
									minutes = minutes-(days*24*60)-(hours*60);
									seconds = seconds-(days*24*60*60)-(hours*60*60)-(minutes*60);	
									final_time = hours+":"+minutes+":"+seconds;
								}
									htm += '<tr style=" padding: 15px;    border: 2px solid green;"><td style=" padding: 15px;    border: 2px solid green;">'+val.start_time+'</td><td style="padding: 15px;    border: 2px solid green;">'+val.end_time+'</td><td style=" padding: 15px;    border: 2px solid green;">'+final_time+'</td>	</tr>';
							});
						}
					htm += '</table>';
					$('#tableData').html(htm);
					//document.getElementById("checked_out").disabled = true;
				}else{
					alert("data not found")
				}
			},error: function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  alert(err.Message);
			}
		});//ajax
 });
// $('#page2').on('pageshow',function(e){
function timer_entry(){
	//$('#PopupDiv_attendance').fadeOut();
		var shop_id_loc = localStorage.getItem("shop_id_loc");
		if(shop_id_loc)
		{
			setTimeout(function(){ 
				$('#checked_in').parent().css({"color":"green"});
				$('#fafaclock').html('<i style="height:45px; width:45px; color:#1a8cff;"  class="fas fa-stopwatch"></i>');
				$('#clock_started').html('Clock is Running!!!'); 
				$('#clock_started').html('Clock is Running!!!'); 
				$('#showShopName').attr('');
			}, 2000);
			$( '#tryToHide' ).hide();
		}

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
		//alert(isOff+"timer_entry");
	}
	//window.plugins.uniqueDeviceID.get(success, fail);
}
//});

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
				$('#productsName').append('<p style="width:50%;f	t:left;cursor:pointer;margin:10px 0;text-align:left;"><input type="radio" name="prdctType" data-role="none" uId="'+index+'" id="prdctNameAll'+index+'" class="radioBtn" value="'+objj.product_name+'"><label for="prdctNameAll'+index+'" style="display:inline-block">'+objj.product_name+'</label></p>');
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