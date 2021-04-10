
$.support.cors = true;
var pictureSource;   // picture source
var destinationType; // sets the format of returned value
var bckKeyCount=0;
var attendanceArray = [];
var version = '2.8.3';

var geoOptionsHigh = {timeout:5000, enableHighAccuracy: true};
var geoOptionsLow = {timeout:5000, enableHighAccuracy: false};

// Wait for Cordova to connect with the device

$(document).ready(function(){
	document.addEventListener("backbutton", onBackKeyDown);
		getCurrentPosition();
		timer_entry();
})

function onSuccessFirst(position)
{
	var lat  = position.coords.latitude;
	var long = position.coords.longitude;
	//alert("lat : " + position.coords.latitude + " lng : " + position.coords.longitude);
	timer_entry();
	
}
 function getCurrentPosition(){
	var parseVal = '1';
	atndsFunction(parseVal);
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

/* GET GPS LAT,LONG FROM EVERY PAGE */

function getLatLong()
{
	var logo_url = localStorage.getItem('logo_url');
	$('.logoImg').attr('src',logo_url);
	
	/* Get Current time */
	var date = new Date();	
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var seconds = date.getSeconds();
	hours = hours < 10 ? '0'+hours : hours;
	minutes = minutes < 10 ? '0'+minutes : minutes;
	seconds = seconds< 10 ? '0'+seconds : seconds;
	var strTime = hours+':'+minutes+':'+seconds;
	/* End Time */
	if(localStorage.getItem('app_userId') && localStorage.getItem('app_userId_admin'))
	{
		var app_userId = localStorage.getItem('app_userId');
		var app_userId_admin = localStorage.getItem('app_userId_admin');
		if(app_userId==app_userId_admin && app_userId!='' && app_userId!='0')
		{
			//if(hours>=10 && hours<=20)
			if(hours)
			{	
				var today = new Date();
				var mnth = today.getMonth()+1;
				var todayDate = today.getDate()+'-'+mnth+'-'+today.getFullYear();
				if(localStorage.getItem('track_Date'))
				{
					var track_Date = localStorage.getItem('track_Date');
					if(todayDate==track_Date)
					{
						var track_Time = localStorage.getItem('track_Time');
						var a = track_Time.split(':'); // split it at the colons
						var seconds_lcl = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]); 
						var minutes_lcl = Math.round(seconds_lcl/60);
						var b = strTime.split(':'); // split it at the colons
						var seconds_now = (+b[0]) * 60 * 60 + (+b[1]) * 60 + (+b[2]); 
						var minutes_now = Math.round(seconds_now/60);
						
						var betweenTime = minutes_now-minutes_lcl;
						if(betweenTime>=15)
						{
							var isOff = 'onLine' in navigator && !navigator.onLine;
						
							if ( isOff )
								getLatLongFail();
							else
								getLatLongSuccess();
						}
					}
					else
					{
						localStorage.removeItem('attendanceTime');
						localStorage.removeItem('track_Time');
						localStorage.removeItem('track_Date');
						var isOff = 'onLine' in navigator && !navigator.onLine;
						if ( isOff )
							getLatLongFail();
						else
							getLatLongSuccess();
					}
				}
				else
				{
					var isOff = 'onLine' in navigator && !navigator.onLine;
					if ( isOff )
						getLatLongFail();
					else
						getLatLongSuccess();
				}
			}
		}
	}
}
function getLatLongSuccess(){
	
	navigator.geolocation.getCurrentPosition(
		GpsSuccess,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					GpsSuccess, 
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
	
function GpsSuccess(position)
{
	var Lat  = position.coords.latitude;
	var Long = position.coords.longitude;
	var app_userId = localStorage.getItem('app_userId');
	var app_userId_admin = localStorage.getItem('app_userId_admin');
	/* Get Current time */
	var date = new Date();	
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var seconds = date.getSeconds();
	hours = hours < 10 ? '0'+hours : hours;
	minutes = minutes < 10 ? '0'+minutes : minutes;
	seconds = seconds< 10 ? '0'+seconds : seconds;
	var strTime = hours+':'+minutes+':'+seconds;
	/* End Time */
	if(localStorage.getItem('track_Date'))
		var track_Date = localStorage.getItem('track_Date');
	var today = new Date();
	var mnth = today.getMonth()+1;
	var todayDate = today.getDate()+'-'+mnth+'-'+today.getFullYear();
					
	$.getJSON(serviceUrl +'schemes.php?tracking=yes&app_userId='+app_userId+'&lat='+Lat+'&long='+Long,function(data)
	{
		var jres = data.Result;
		if(jres.status=='success')
		{
			localStorage.setItem('track_Time',strTime);
			localStorage.setItem('track_Date',todayDate);
		}
	});	
}
function getLatLongFail()
{
	window.plugins.toast.showLongCenter(':: No internet connectivity ::');
	alert(':: No internet connectivity ::')
}

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
	//alert("function called")
		$('#workerName').val(localStorage['app_user']);
		var Shp_lat  = position.coords.latitude;
		var Shp_long = position.coords.longitude;
	//alert (Shp_lat +" NearestShop.php "+Shp_long);

	$('#attndsLat').val(Shp_lat);
	$('#attndsLong').val(Shp_long);
	var appUsrIdLcl = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl +'NearestShop.php?appUsrIdLcl='+appUsrIdLcl+'&lat='+Shp_lat+'&long='+Shp_long,function(data)
	{
		//alert("get fun api call")
		$('#shpName').html('');
		$('#shpName').html('<option value=""><-- : select shop : --></option>');
		var jres = data.Result;
		$.each(jres,function(index,obj)
		{
			if(obj.Status=='success')
			{
				$('#shpName').val(obj.shopId);
				$('#shopAttendsTxt').val(obj.shopId);
				$('#shop_name').val(obj.shopName);
				$('#shpName').append('<option value="'+obj.shopId+'" shpName="'+obj.shopName+'">'+obj.shopName+'</option>');
				//alert("success"+Shp_lat+"------"+Shp_long)
			}
			if(obj.shopName=='emptySet')
			{
				$('#shpName').html('<option value=""><---: Select current shop :---></option><option>No shops found !!</option>');
				//alert("null data"+Shp_lat+"------"+Shp_long)
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
			// $('.attndsPage2w').html('Please Select Current Shop.');
			// $('.attndsPage2w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			// $('.attndsPage2w').fadeIn('slow');
			// $('html,body').animate({scrollTop:0},500);
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