// JavaScript Document
$(document).ready(function(e) {
	if(localStorage.getItem('cmpnyName')){
		var cmpnyName = localStorage.getItem('cmpnyName');
		var logo_url = localStorage.getItem('logo_url');
		$('.cmpnyName').html('&nbsp;&nbsp;'+cmpnyName);
		$('.logoImg').attr('src',logo_url);
	}
});
$(document).on('pageinit',function() {
    $('.overlayCls').hide();
});
$(document).on('click','#retailerSchemes',function()
{
	window.location.replace('#page20');
});
$(document).on('click','#retailerSchemes_unfos',function()
{
	window.location.replace('#page20');
});
$('#page20').on('pageshow',function()
{
	getLatLong();
	$.getJSON(serviceUrl+'schemes.php?getSchemes=yes',function(data)
	{
		console.log(data);
		$('#retailerSchemesDiv').html('');
		var jres = data.Result;
		$.each(jres,function(index,obj)
		{
			if(obj.status=='success')
			{
				var indx = index+1;
				console.log(obj.file_name);
				$('#retailerSchemesDiv').append('<p style="padding: 8px 0 8px 16px;background: floralwhite;border-left: 2px solid red;"><img src="images/pdf.png" style="height:30px;width:30px;"/><span class="schemeLink" id="'+obj.id+'" style="padding: 7px;color: red;font-size: 18px;">&nbsp;&nbsp;<a href="'+obj.file_url+'" style="text-decoration:none;">'+ obj.file_name+'</a></span></p>');
			}
			else
			{
				$('#retailerSchemesDiv').html('<p style="text-align:center;color:red;padding: 15px;"><strong>No Files!</strong></p>');
			}
		});
	});
});

function getCurrentStocks(shpId)
{
	prdctMdlArr = [];
	prdctOriginalMdlArr = [];
	stckLoadedMdlValArr = [];
	$('.stcksPage18w').fadeOut('slow');
	$('#stcksList').html('<table border="0" style="border-collapse:collapse;font-size: 15px;" width="99.9%" height="auto" id="stckTbl"><tr style="color: white;font-weight: bold;text-shadow: none;background: rgb(49, 136, 203);"><th style="padding:10px;text-align: left;">Model</th><th style="text-align: center;">Qty</th></tr></table>');
	var app_userId = localStorage.getItem('app_userId');
	var shpId = shpId;
	//alert(shpId);
	$.getJSON(serviceUrl+'modelwiseTarget.php?app_stkUser='+app_userId+'&shpId='+shpId,function(data)
	{
		console.log(data);
		var jres = data.result;
		if(jres.status!='norows')
		{
			$('#stcksList').html('<table border="0" style="border-collapse:collapse;font-size: 15px;" width="99.9%" height="auto" id="stckTbl"><tr style="color: white;font-weight: bold;text-shadow: none;background: rgb(49, 136, 203);"><th style="padding:10px;text-align: left;">Model</th><th style="text-align: center;">Qty</th></tr></table>');
			$.each(jres,function(index,objj)
			{
				if(objj.status=='success')
				{
					var product_model = objj.product_model.toString();
					product_model = product_model.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
					//product_model = product_model.replace('.', '');
					$('#stckTbl').append('<tr class="stckHitCls" stckId="stck'+index+'" style="line-height: 30px;"><td style="padding:4px;font-size: 17px;color:#716c6c;">'+objj.product_model+'</td><td style="width:30%;"><input type="number" class="'+product_model+' no-spinner" id="stck'+index+'" value="'+objj.quantity+'" style="text-align:center;background-color: #fff;border-color: #ddd;color: #333;text-shadow: 0 1px 0 #f3f3f3;padding: 6px;border-width: 1px;border-style: solid;width:90%;float: right;border-radius: 5px;"></td></tr>');
					prdctMdlArr.push(product_model);
					prdctOriginalMdlArr.push(objj.product_model);
					stckLoadedMdlValArr.push({'Model':objj.product_model,'Qty':objj.quantity,'rmvdPointMdl':product_model});
				}	
			});
			$('#stcksList').append('<p style="text-align:center;" class="stckBtn_p"><button id="stckSaveBtn" type="button" style="margin-top:10px;margin-bottom: 10px;background: rgb(49, 136, 203);color: white;font-weight: bold;padding: 7px 23px;">SAVE</button></p>');
			$("#stckTbl tr:odd").css({"background-color":"lightgray","box-shadow":"-3px 1px 1px 0px grey","font-weight":" bold","text-shadow":" none"});
			$("#stckTbl tr:nth-child(2)").css("border-top","3px solid white");
			$('.stckBtn_p').show('slow');
		}
		else
		{	
			$('#stckTbl').append('<tr><td colspan="3" style="text-align:center;padding: 50px;">No Available Stocks!.</td></tr>');
			$('.stckBtn_p').hide('slow');
		}
	});
}

$(document).on('click','.stckOrderDel',function()
{
	var trLen = $('#stckOrderBody tr').length;
	if(trLen!=1)
	{
		var trId = $(this).attr('trId');
		$('#'+trId).remove();
	}
	else
		$('.otpPopup_stck').fadeOut('fast');
});

$(document).on('click','#sendOtpBtn_stck',function()
{
	var trLen = $('#stckOrderBody tr').length;
	if(trLen!=0)
	{
		var stckShpId = $('#stckShpId').val();
		var stckShpName = $('#stckShpName').val();
		localStorage.setItem('stckShpName',stckShpName);
		if(stckShpId!='' || stckShpId!='0')
		{
			$('#stckOrderBody tr').each(function() {
				var Model = $(this).find("td:nth-child(1)").attr('model');
				var Color = $(this).find("td:nth-child(2)").attr('color');
				var Qty = $(this).find("td:nth-child(3)").attr('Qty');
				var dp = $(this).attr('dp');
				if(Qty!='0')
					stckOrdersDataArr.push({'Model':Model,'Color':Color,'Qty':Qty,'dp':dp});
			});
			localStorage.setItem('stckOrdersDataLcl',JSON.stringify(stckOrdersDataArr));
			$('.otpPopup_stck').fadeOut('fast');
			window.location.replace('#page3');
			$('#shopNameView').val(stckShpName);
			$('#shopNameTxt').val(stckShpId);
			localStorage.setItem('stckOrder','yes');
			//checkOrderDisabled();
			/* get Order Approval Start */
				/*var shpName = localStorage.getItem('stckShpName');
				var srchStr = stckShpName.includes("&");
				var srchStr1 = stckShpName.includes("#");
				if(srchStr)
					var shpName = stckShpName.replace("&","!!");
				if(srchStr1)
					var shpName = stckShpName.replace("#","@@");
					
				var app_userId = localStorage.getItem('app_userId');
				$.getJSON(serviceUrl+'todayTask.php?app_userId='+app_userId+'&shpName='+shpName+'&orderDisable=yes',function(data)
				{
					console.log(data);
					var jres = data.Result;
					$('.orderDisabledInfo').html('<p style="margin: 3px;">Shop Credit Period : <span class="crdPrd" style="font-weight:bold"></span></p>');
					$('.orderDisabledInfo').append('<table id="orderInvTbl" border="1" width="99.9%" height="auto" style="border:1px solid darkseagreen;border-collapse:collapse;"><tr><th style="padding: 4px;">Inv Date</th><th>Invoice Number</th><th>Value</th><th>Days</th></tr></table>');
					$.each(jres,function(index,objj)
					{
						if(objj.disable=='yes')
						{
							var indx = index+1;
							localStorage.setItem('orderDisabled','yes');
							$('.getOrderPage3w').html('Due Date Exceeded for Below Invoices!');
							$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
							$('.crdPrd').html(objj.credit_period +' days');
							$('#orderInvTbl').append('<tr style="text-align:center;"><td style="padding: 3px;">'+objj.outstanding_date+'</td><td>'+objj.invoices+'</td><td>'+objj.pending_amount+'</td><td>'+objj.overdue+'</td></tr>');
							$('.getOrderPage3w').fadeIn('slow');
							$('.orderDisabledInfo').fadeIn('slow');
						}
					});
					$('.orderDisabledInfo').append('<p class="orderGetApproval" style="visibility:hidden;font-style:italic;color:blue;font-weight:bold;text-shadow:none;line-height: 0;font-size:1px;">Get Approval to Continue -></p>');

					if(jres.disable=='no')
					{
						$('.orderDisabledInfo').fadeOut('slow');
						$('.getOrderPage3w').fadeOut('slow');
					}	
					getStckOrderInOrderPage();
				});*/
			/* end Order Approval end */
		}
	}
});
function getStckOrderInOrderPage()
{
	$('#orderVal_p').show();
	if(localStorage.getItem('stckOrder'))
	{
		$('#page3ShpSelectLbl').html(localStorage.getItem('stckShpName')).fadeIn('slow');
		$('#page3ShpSelectDiv').fadeOut('slow');
		var stckOrderDataArr = [];
		var stckOrder = JSON.parse(localStorage.getItem('stckOrdersDataLcl'));
		var orderVal_ttl = 0;
		$.each(stckOrder,function(index,c)
		{
			stckOrderDataArr.push({"prdctType":"Nokia","prdctName":c.Model,"prdctColor":c.Color,"prdctQuantity":c.Qty,"orderVal":c.dp});
			orderVal_ttl = parseInt(orderVal_ttl)+(parseInt(c.dp)*parseInt(c.Qty));
		});
		$('#orderVal_span').html(orderVal_ttl);
		localStorage.setItem('productData',JSON.stringify(stckOrderDataArr));
		var prdctFinal = JSON.parse(localStorage.getItem('productData'));
		$('#AddedPrdctDiv').html('<p style="text-align:center;"><button type="button" id="clearLocal" style="padding:5px 10px;">Clear</button></p>');
		$.each(prdctFinal,function(index,objj)
		{
			var indexData = index+1;
			$('#AddedPrdctDiv').append('<p style="color:green;text-align:left;margin-left:10px;"><span><img src="images/ic_action.png" style="height:25px;" class="prdctDelCls" id="prdtDel'+index+'" pDelUnic="'+index+'"></span><span style="margin-left:10px;">'+ indexData +' .'+objj.prdctType+', '+objj.prdctName+', '+objj.prdctColor+', '+objj.prdctQuantity+'</span></p>');			
		});
		$('#AddedPrdctDiv').fadeIn('slow');
	}
}
/* Market Statistics page start */
$(document).on('click','#sendOtpCnclBtn_stck',function()
{
	$('.otpPopup_stck').fadeOut('slow');
});

$(document).on('click','#marketStats',function()
{
	window.location.replace('#page21');
});
$('#page21').on('pageshow',function()
{	
	getLatLong();
	$('.MarketStatsPage21w').hide();
	$('#page21ShpSelectLbl').fadeOut('slow');
	$('#page21ShpSelectDiv').fadeIn('slow');
	$('#shopNameTxtMarketStats').val('');
	$('#shpNameTxtVal').val('');
	$('#shopNameViewMarketStats').val('');
	$('#srchShopListMarketStats').fadeOut('fast');
	$('#mdlListHorizontal').hide();
	$('#statsTBodyId').html('<tr style="height: 120px;text-align: center;"><td colspan="3">Select Shop get Market Stats!</td></tr>');
	$('#saveBtnDivMarketStats').hide();
	$('#shpNameMarketStats').html('<option value=""><-- : select shop : --></option>');
	var isOff = 'onLine' in navigator && !navigator.onLine;

	if ( isOff ) {
    	onFailedMarketStats();
	}
	else {
		getCurrentPosition11();
	}
});

function getCurrentPosition11(){
	navigator.geolocation.getCurrentPosition(
		onSuccessMarketStats,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					onSuccessMarketStats, 
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
function onSuccessMarketStats(position)
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
				$('#shpNameMarketStats').append('<option value="'+obj.shopId+'">'+obj.shopName+'</option>');
			}
			if(obj.shopName=='emptySet')
			{
				$('#shpNameMarketStats').html('<option value=""><---: Select current shop :---></option><option>No shops found !!</option>');
			}
		});
	});
}
function onFailedMarketStats()
{
	window.plugins.toast.showLongCenter(':: No internet connectivity ::');
}
$(document).on('change','#shpNameMarketStats',function()
{
	$('#shopNameViewMarketStats').val('');
	var shpId = $(this).val();
	$('#shopNameTxtMarketStats').val(shpId);
	var srchShpsName = $(this).find("option:selected").text();
	$('#shpNameTxtVal').val(srchShpsName);
	$('#page21ShpSelectLbl').html(srchShpsName);
	$('#page21ShpSelectDiv').fadeOut('slow');
	$('#page21ShpSelectLbl').fadeIn('slow');
	var app_user = localStorage.getItem('app_userId');
	var shpId = $('#shopNameTxtMarketStats').val();
	getMarketStats(app_user,srchShpsName);
});
$(document).on('click','#page21ShpSelectLbl',function()
{
	$('#page21ShpSelectLbl').fadeOut('slow');
	$('#page21ShpSelectDiv').fadeIn('slow');
});

$(document).on('keyup','#shopNameViewMarketStats',function()
{
	if($(this).val()!='')
	{
		var srchShopName = $('#shopNameViewMarketStats').val();
		var srchStr = srchShopName.includes("&");
		var srchStr1 = srchShopName.includes("#");
		if(srchStr)
			var srchShopName = srchShopName.replace("&","!!");
		if(srchStr1)
			var srchShopName = srchShopName.replace("#","@@");
		var appUsrIdLcl = localStorage.getItem('app_userId');	
		$.getJSON(serviceUrl+'searchShops.php?appUsrIdLcl='+appUsrIdLcl+'&SrchShopTxt='+srchShopName,function(data)
		{
			$('#srchShopListMarketStats').html('');
			$('#srchShopListMarketStats').html('<p style="text-align:right;margin: 0;"><img src="images/ic_action.png" id="srchClsMarketStats"></p>');
			var jres = data.Result;
			$.each(jres,function(index,objct)
			{
				if(objct.Status=='Success')
					$('#srchShopListMarketStats').append('<p style="text-align:center;" srchSId="'+objct.id+'" class="srchShpNameMarketStats">'+ objct.shopName +'</p>');
				if(objct.Status=='NoRows')
					$('#srchShopListMarketStats').html('<p style="color:red;text-align:center;font-weight:bold;">:: No Records ::</p>');
			});
			$('#srchShopListMarketStats').fadeIn('fast');
		});
	}
	else
		$('#srchShopListMarketStats').fadeOut('fast');
});
$(document).on('click','#srchClsMarketStats',function()
{
	$('#srchShopListMarketStats').fadeOut('fast');
	$('#shopNameTxtMarketStats').val('');
});
$(document).on('click','.srchShpNameMarketStats',function()
{
	$('#shopNameTxtMarketStats').val($(this).attr('srchSId'));
	$('#srchShopListMarketStats').fadeOut('slow');
	var srchShpsName = $(this).text();
	$('#shpNameTxtVal').val(srchShpsName);
	$('#page21ShpSelectLbl').html(srchShpsName);
	$('#page21ShpSelectDiv').fadeOut('slow');
	$('#page21ShpSelectLbl').fadeIn('slow');
	var app_user = localStorage.getItem('app_userId');
	var shpId = $('#shopNameTxtMarketStats').val();
	getMarketStats(app_user,srchShpsName);
});

function getMarketStats(app_user,srchShpsName)
{
	var shpId = $('#shopNameTxtMarketStats').val();
	$('#mdlListHorizontal').fadeOut('slow');
	$('#statsTBodyId').html('<tr style="height: 120px;text-align: center;"><td colspan="3">Select Shop get Market Stats!</td></tr>');
	var unicProductMdlArr = [];
	var srchShopName = srchShpsName;
	var srchStr = srchShopName.includes("&");
	var srchStr1 = srchShopName.includes("#");
	if(srchStr)
		var srchShopName = srchShopName.replace("&","!!");
	if(srchStr1)
		var srchShopName = srchShopName.replace("#","@@");
	var MdlsAvailStatus = 'no';	
	$.post(serviceUrl+'market_stats.php',{getShpWiseMarketStats:'yes',shpId:shpId,app_user:app_user,SrchShopTxt:srchShopName,req:'shopSelect'},function(data)
	{
		console.log(data);
		var jres = $.parseJSON(data).Result;
		$('#statsTBodyId').html('');
		$.each(jres,function(index,obj)
		{
			if(obj.status=='success')
			{
				if(obj.dataExistsStatus=='notavailable')
				{
					if($.inArray(obj.product_name,unicProductMdlArr)==-1)
						unicProductMdlArr.push(obj.product_name);
				}
			}
			if(obj.status=='norows')
			{
				console.log(obj.MdlsAvailStatus);
				if(obj.MdlsAvailStatus=='yes')
				{
					MdlsAvailStatus = 'yes';
					$('#statsTBodyId').html('<tr style="height: 120px;text-align: center;"><td colspan="3">Great! You have finished all.</td></tr>');
					$('#saveBtnDivMarketStats').hide('slow');
				}
				else
				{
					$('#statsTBodyId').html('<tr style="height: 120px;text-align: center;"><td colspan="3">Sorry, Empty Records Found!</td></tr>');
					$('#saveBtnDivMarketStats').hide('slow');
				}
			}
		});
		if(unicProductMdlArr.length>0)
		{
			$('#mdlListData').html('');
			$.each(unicProductMdlArr,function(index,objct)
			{
				if(index==0)
				{
					$('#mdlListData').append('<div class="mdlTop" id="firstMdl" mdlName="'+objct+'" style="float:left;padding: 19px;border-right: 1px solid darkturquoise;cursor:pointer;background:darkturquoise;color:white;">'+objct+'</div>');
				}
				else
				{
					$('#mdlListData').append('<div class="mdlTop" mdlName="'+objct+'" style="float:left;padding: 19px;border-right: 1px solid darkturquoise;cursor:pointer;">'+objct+'</div>');
				}
			});
			if(unicProductMdlArr.length!=1)
				$('#mdlListData').append('<div class="mdlTop" mdlName="All" id="allStatsId" style="float:left;padding: 19px;border-right: 1px solid darkturquoise;cursor:pointer;">All</div>');
			$('#mdlListHorizontal').fadeIn('slow');
			$('#firstMdl').click();
		}
		else
		{
			if(MdlsAvailStatus=='yes')
				$('#statsTBodyId').html('<tr style="height: 120px;text-align: center;"><td colspan="3">Great! You have finished all.</td></tr>');
			else
				$('#statsTBodyId').html('<tr style="height: 120px;text-align: center;"><td colspan="3">Sorry, Empty Records Found!</td></tr>');
			$('#mdlListHorizontal').fadeOut('slow');
		}
	});
}

function getMarketStatsMdlData(app_user,srchShpsName,req)
{
	var shpId = $('#shopNameTxtMarketStats').val();
	$('#statsTBodyId').html('');
	$('#statsTBodyId').fadeOut('fast');
	var unicProductMdlArr = [];
	var srchShopName = srchShpsName;
	var srchStr = srchShopName.includes("&");
	var srchStr1 = srchShopName.includes("#");
	if(srchStr)
		var srchShopName = srchShopName.replace("&","!!");
	if(srchStr1)
		var srchShopName = srchShopName.replace("#","@@");
		
	$.post(serviceUrl+'market_stats.php',{getShpWiseMarketStats:'yes',shpId:shpId,app_user:app_user,SrchShopTxt:srchShopName,req:req},function(data)
	{
		console.log(data);
		var jres = $.parseJSON(data).Result;
		$('#statsTBodyId').html('');
		$.each(jres,function(index,obj)
		{
			if(obj.status=='success')
			{
				if(obj.dataExistsStatus=='notavailable')
				{
					var indx = index+1;	
					var product_name = obj.product_name.toString();
					product_name = product_name.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
					$('#statsTBodyId').append('<tr style="text-align:center;" class="statsTrCls mdl'+product_name+'" id="msTr'+indx+'" uId="'+indx+'" dbId="'+obj.id+'" product_name="'+obj.product_name+'" competitive_model="'+obj.competitive_model+'" mop="'+obj.mop+'"><td style="width:32%;padding: 15px;">'+obj.competitive_model+'</td><td style="width:32%">'+obj.mop+'</td><td style="width:36%;"><input type="number" id="nlm'+indx+'" class="statsCls" value="" placeholder="Qty" style="text-align:center;width: 70%;padding: 5px;border-radius: 5px;"/></td></tr>');
					$('#saveBtnDivMarketStats').show('slow');
					$('#statsTBodyId').fadeIn('slow');
				}
			}
			if(obj.status=='norows')
			{
				if(obj.MdlsAvailStatus=='yes')
				{
					$('#statsTBodyId').html('<tr style="height: 120px;text-align: center;"><td colspan="3">Great! You have finished all.</td></tr>');
					$('#saveBtnDivMarketStats').hide('slow');
					$('#statsTBodyId').fadeIn('slow');
				}
				else
				{
					$('#statsTBodyId').html('<tr style="height: 120px;text-align: center;"><td colspan="3">Sorry, Empty Records Found!</td></tr>');
					$('#saveBtnDivMarketStats').hide('slow');
					$('#statsTBodyId').fadeIn('slow');
				}
			}
		});
		$("#statsTbl tr:odd").css({"background-color":"aliceblue","font-weight":" bold","text-shadow":" none","color":"darkturquoise"});
	});
}

$(document).on('click','#marketStatsSaveBtn',function()
{
	$('.MarketStatsPage21w').fadeOut('slow');
	var totalDatas = $('#statsTBodyId tr').length;
	var app_user = localStorage.getItem('app_userId');
	var shpId = $('#shopNameTxtMarketStats').val();
	var srchShopName = $('#shpNameTxtVal').val();
	var srchStr = srchShopName.includes("&");
	var srchStr1 = srchShopName.includes("#");
	if(srchStr)
		srchShopName = srchShopName.replace("&","!!");
	if(srchStr1)
		srchShopName = srchShopName.replace("#","@@");
	var allData = '';
	var validStatus = 'yes';
	for(var i=1;i<=totalDatas;i++)
	{
		var nlm = $('#nlm'+i).val();
		if(nlm=='')
			validStatus = 'no';	
	}
	if(validStatus == 'yes')
	{
		$('.MarketStatsPage21w').fadeOut();
		for(var i=1;i<=totalDatas;i++)
		{
			var dbId = $('#msTr'+i).attr('dbId');
			var product_name = $('#msTr'+i).attr('product_name');
			var competitive_model = $('#msTr'+i).attr('competitive_model');
			var mop = $('#msTr'+i).attr('mop');
			var nlm = $('#nlm'+i).val();
			if(nlm=='')
				nlm = 0;
			if(totalDatas!=i)
				allData += dbId+'@'+product_name+'@'+competitive_model+'@'+mop+'@'+nlm+',';
			else
				allData += dbId+'@'+product_name+'@'+competitive_model+'@'+mop+'@'+nlm;
		}
		$.post(serviceUrl+'market_stats.php',{putShpWiseMarketData:'yes',app_user:app_user,shpId:shpId,SrchShopTxt:srchShopName,allData:allData},function(data)
		{
			console.log(data);
			var jres = $.parseJSON(data).Result;
			if(jres.status=='success')
			{
				$('.MarketStatsPage21w').html('Success : Your data saved!');
				$('.MarketStatsPage21w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.MarketStatsPage21w').fadeIn('slow');
				var mdlVal = 'All';
				getMarketStatsMdlData(app_user,srchShopName,mdlVal);
				getMarketStats(app_user,srchShopName);
				setTimeout(function(){$('.MarketStatsPage21w').fadeOut('slow');},2000);
			}
			if(jres.status=='failed')
			{
				$('.MarketStatsPage21w').html('Some error occur. So please try later!');
				$('.MarketStatsPage21w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.MarketStatsPage21w').fadeIn('slow');
			}
		});
	}
	else
	{
		$('.MarketStatsPage21w').html('Some NLM Qty value missing!');
		$('.MarketStatsPage21w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.MarketStatsPage21w').fadeIn('slow');
	}
});

$(document).on('click','.mdlTop',function()
{
	var srchShpsName = $('#shpNameTxtVal').val();
	var app_user = localStorage.getItem('app_userId');
	var shpId = $('#shopNameTxtMarketStats').val();
	var mdlTxt = $(this).attr('mdlName');
	mdlTxt = mdlTxt.toString();
	mdlTxt = mdlTxt.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
	$('.mdlTop').css({'background':'white','color':'black'});
	$(this).css({'background':'darkturquoise','color':'white'});
	var mdlVal = $(this).attr('mdlName');
	if(mdlTxt=='All')
	{
		mdlVal = 'All';
		getMarketStatsMdlData(app_user,srchShpsName,mdlVal);
	}
	else
	{
		getMarketStatsMdlData(app_user,srchShpsName,mdlVal);
	}
});


/* market Statistics */

$('#page22').on('pageshow',function()
{
	getLatLong();
	$('#adminLgtIcon').show();
	var app_userId = localStorage.getItem('app_userId_admin');
	var app_user = localStorage.getItem('app_user_admin');
	var fos_name = localStorage.getItem('fos_name_admin');
	var un_fos_admin = localStorage.getItem('un_fos_admin');
	var app_admin_user = localStorage.getItem('app_admin_user');
	var app_admin = localStorage.getItem('app_admin');
	var un_fos_admin = localStorage.getItem('un_fos_admin');
	$.post(serviceUrl+'allUsersLink.php',{app_userId:app_userId},function(data)
	{
			console.log(data);
			var jres = $.parseJSON(data).Result;
			var htm1 = '';
			htm1 += '<div class="userLink" style="height: 76px;margin-top: 10px;margin-bottom: 1px;background: floralwhite;" uId = "'+app_userId+'" fos_name = "'+fos_name+'" user_name = "'+app_user+'" un_fos = "'+un_fos_admin+'" app_admin= "1">';
            htm1 += '	<div style="width:20%;float:left;"><img src="images/user_icon.png" style="width:66px;opacity: 0.1;"/></div>';
            htm1 += '    <div style="width:70%;float:left;" class="ui-alt-icon">';
            htm1 += '    	<p style="line-height: 5px;color: coral;font-weight: bold;text-shadow: none;font-size: 18px;">&nbsp;&nbsp;&nbsp;'+fos_name+'</p>';
			htm1 += '    	<p style="color:darkorange;font-size:15px;">&nbsp;&nbsp;&nbsp;'+app_user+'</p>';            
			htm1 += '    </div>';
			htm1 += '    <div style="width:10%;float:left;font-size: 27px;line-height: 72px;color:lightgray;background: aliceblue;text-align:center;"><i class="fas fa-angle-double-right"></i></div>';
            htm1 += '</div>';
			$('#AllUserDiv').html(htm1);
			if(app_admin=='1' && un_fos_admin=='2')
			{
				var AdminId = localStorage.getItem('AdminId');
				var htm2 = '';
				htm2 += '<div class="userLink" style="height: 76px;margin-top: 5px;margin-bottom: 20px;background: aliceblue;" uId = "'+AdminId+'" fos_name = "ADMIN" user_name = "'+app_user+'" un_fos = "0" app_admin= "1">';
				htm2 += '	<div style="width:20%;float:left;"><img src="images/user_icon.png" style="width:66px;opacity: 0.1;"/></div>';
				htm2 += '    <div style="width:70%;float:left;" class="ui-alt-icon">';
				htm2 += '    	<p style="color: cornflowerblue;font-weight: bold;text-shadow: none;line-height: 40px;">&nbsp;&nbsp;&nbsp;ADMIN PANEL</p>';
				htm2 += '    </div>';
				htm2 += '    <div style="width:10%;float:left;font-size: 27px;line-height: 72px;color:lightgray;background: aliceblue;text-align:center;"><i class="fas fa-angle-double-right"></i></div>';
				htm2 += '</div>';
				$('#AllUserDiv').append(htm2);
			}
			$.each(jres,function(index,obj)
			{
				if(obj.status=='success')
				{
					if(app_userId!=obj.id)
					{
						var htm = '';
						htm += '<div class="userLink" style="height: 76px;border-bottom:1px solid lightgray;margin-top: 10px;" uId = "'+obj.id+'" fos_name = "'+obj.fos_name+'" user_name = "'+obj.user_name+'" un_fos = "'+obj.un_fos+'" app_admin= "'+obj.app_admin+'">';
						htm += '	<div style="width:20%;float:left;"><img src="images/user_icon.png" style="width:66px;opacity: 0.1;"/></div>';
						htm += '    <div style="width:70%;float:left;" class="ui-alt-icon">';
						htm += '    	<p style="line-height: 5px;color: rgb(0,187,238);font-weight: bold;text-shadow: none;font-size: 18px;">&nbsp;&nbsp;&nbsp;'+obj.fos_name+'</p>';
						htm += '    	<p style="color:darkcyan;font-size:15px;">&nbsp;&nbsp;&nbsp;'+obj.user_name+'</p>';
						htm += '    </div>';
						htm += '    <div style="width:10%;float:left;font-size: 27px;line-height: 72px;color:lightgray;background: aliceblue;text-align:center;"><i class="fas fa-angle-double-right"></i></div>';
						htm += '</div>';
						$('#AllUserDiv').append(htm);
					}
				}
				if(obj.status=='norows')
				{
					$('#AllUserDiv').html('<p style="font-weight:bold;text-align:center;">No Users Found!</p>');
				}
			});
	});
});

$(document).on('click','.userLink',function()
{
	var uId = $(this).attr('uId');
	var user_name = $(this).attr('user_name'); 
	var fos_name = $(this).attr('fos_name'); 
	var un_fos = $(this).attr('un_fos');
	var app_admin = $(this).attr('app_admin');
	var app_userId_admin = localStorage.getItem('app_userId_admin');
	var AdminId = localStorage.getItem('AdminId');
	localStorage.setItem('app_userId',uId);
	localStorage.setItem('app_user',user_name);
	localStorage.setItem('fos_name',fos_name);
	localStorage.setItem('un_fos',un_fos);
	localStorage.setItem('app_admin_user',app_admin);
	
	window.location.replace('#page_home');
});

$('#page_home2').on('pageshow',function()
{
	getLatLong();
});

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

/* GET GPS END */

$(document).on('click','#switch_userLink',function()
{
	window.location.replace('#page22');
});

function getCompanyPrfl(c)
{
	var today = new Date();
	var mnth = today.getMonth()+1;
	var todayDate = today.getDate()+'-'+mnth+'-'+today.getFullYear();
	localStorage.setItem('todayDate',todayDate);
	var rUrl = '';
	if(c=='home_page')
	{
		var app_userId = localStorage.getItem('app_userId');
		rUrl = serviceUrl +'schemes.php?getCmpyName=yes&version='+version+'&app_userId='+app_userId;
	}
	if(c=='login_page')
		rUrl = serviceUrl +'schemes.php?getCmpyName=yes&version='+version;
	$.getJSON(rUrl,function(data)
	{
		console.log(data);
		var jres = data.Result;
		if(jres.status=='success')
		{
			localStorage.setItem('cmpnyName',jres.name);
			localStorage.setItem('logo_url',jres.logo_url);
			if(localStorage.getItem('cmpnyName'))
			{
				var cmpnyName = localStorage.getItem('cmpnyName');
				$('.cmpnyName').html('&nbsp;&nbsp;'+cmpnyName);
				$('.logoImg').attr('src',jres.logo_url);
			}
		}
		else if(jres.status=='expired')
		{
			logoutApp();
			$('.loginPageLw').html('Your App is Expired!. Use Latest One (V-'+jres.current_version+')');
			$('.loginPageLw').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.loginPageLw').fadeOut('slow');
			$('.loginPageLw').fadeIn('slow');
		}
		else
		{
			localStorage.setItem('cmpnyName','Company Name!');
		}
	});
}

/////////////////////////////////// Value Target////////////////////////cpk//
$(document).on('click','#valtrgtbt',function()
{
	window.location.replace('#page23');
});

$('#page23').on('pageshow',function()
{
	var app_userId = localStorage.getItem('app_userId');
	var app_admin = localStorage.getItem('app_admin');
	var un_fos = localStorage.getItem('un_fos');
	getLatLong();
	if(app_admin=='1')
	{
		var app_userId_admin = localStorage.getItem('app_userId_admin');
		if(app_admin=='1' && un_fos=='0')
			valuetarget('all');
		else
			valuetarget('each');
	}
	else
		valuetarget('each');
});

function valuetarget(a)
{
	var reqUrl = '';
	if(a=='all')
	{
		reqUrl = serviceUrl+'valuetarget.php?getvalTrget=yes&req=all';
	}
	else
	{
		var app_userId = localStorage.getItem('app_userId');
		reqUrl = serviceUrl+"valuetarget.php?getvalTrget=yes&req=each&id="+app_userId;
	}
	$.getJSON(reqUrl,function(data)
	{
		VT(data);
		console.log(data);
	});
}

function VT(data)
{
	$('#valtrget').html('<table width="100%" style="border-collapse:collapse;font-size: 13px;margin-top: 2%;" border="1" id="tblvaltrget"><thead><tr><th class="valtrgetCls" db_attr="Name">Name</th><th class="valtrgetCls" db_attr="Target">Target</th><th class="valtrgetCls" db_attr="Achieved">Achieved</th><th class="valtrgetCls" db_attr="Pending"> Pending</th><th class="valtrgetCls" db_attr="AchievedPerc"> Ach (%)</th></tr></thead><tbody id="valtrget_body"></tbody></table>');
	var jres = data.Result;
	var cnt = jres.length;
	$('#valtrget_body').html('');
	$.each(jres,function(index,obj)
	{
		if(obj.status=='success')
		{
			$('.ttlvaltrget').html(jres.length);
			var indx = index+1;
			$('#valtrget_body').append('<tr style="text-align:right" class="valtrgetTr"   Name="'+obj.Name+'" Target="'+obj.Target+'" Achieved="'+obj.Achieved+'"  Pending="'+obj.Pending+'" AchievedPerc="'+obj.AchievedPerc+'" ><td style="text-align:left">'+obj.Name+'</td><td>'+obj.Target+'</td><td>'+obj.Achieved+'</td><td>'+obj.Pending+'</td><td>'+obj.AchievedPerc+'</td></tr>');
		}
		else
			$('#valtrget_body').html('<tr><td colspan="10" class="text-center">No Result Found!</td></tr>');
	});
 }

function setMonths()
{
	 $('#monthwiseSalesNew').html('');
	var mnthsArr = [{"mnth":"January","no":"1"},{"mnth":"February","no":"2"},{"mnth":"March","no":"3"},{"mnth":"April","no":"4"},{"mnth":"May","no":"5"},{"mnth":"June","no":"6"},
				{"mnth":"July","no":"7"},{"mnth":"August","no":"8"},{"mnth":"September","no":"9"},{"mnth":"October","no":"10"},{"mnth":"November","no":"11"},{"mnth":"December","no":"12"}];
	var today = new Date();
	var d;
	var month;
	for(var i = 0; i <= 5; i++) 
	{
	  d = new Date(today.getFullYear(), today.getMonth() - i, 1);
	  month = mnthsArr[d.getMonth()];
	  console.log(month);
	  $('#monthwiseSalesNew').append('<option value="'+month['no']+'">&nbsp;'+month['mnth']+'</option>');
	}
}
$(document).on('change','#monthwiseSalesNew',function()
{
	$('.salesRadio').prop('checked',false);
	$('#sales_res').val('');
	var mnthVal = $(this).val();
	var shpNameSalesTxt = $('#shpNameSalesTxt').val();
	if(shpNameSalesTxt!='' && shpNameSalesTxt!='000')
	{
		var srchPatcls = shpNameSalesTxt.includes("&");
		var srchPatcls1 = shpNameSalesTxt.includes("#");
		if(srchPatcls)	
			var shpNameSalesTxt = shpNameSalesTxt.replace("&","!!");
		if(srchPatcls1)	
			var shpNameSalesTxt = shpNameSalesTxt.replace("#","@@");
	}
	else
		var shpNameSalesTxt = 'empty';
	var app_userId = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl+'getSales.php?app_userId='+app_userId+'&shpNameSalesTxt='+shpNameSalesTxt+'&mnthVal='+mnthVal,function(data)
	{
		console.log(data);
		var jres = data.Result;
		var totlSales = 0;
		$('#salesDiv').html('');
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

$(document).on('click','#otpDetailsLink',function()
{
	window.location.replace('#page24');
});

$('#page24').on('pageshow',function(e)
{
	var app_admin = localStorage.getItem('app_admin');
	var app_userId = localStorage.getItem('app_userId');
	var getOtp = setInterval(function()
	{
		var page_name = document.location.href;
		if (page_name.indexOf("#page24") >= 0)
		{
			if(app_admin==1 && app_userId!='')
			{
				$.ajax(
				{
					url : serviceUrl +"Otp_Details.php",
					type:"GET",
					data:'getOtpInfo=yes&app_userId='+app_userId+'&tt='+(Date.now()/1000|0),
					contentType:false,
					cache:false,
					processData:false,
					success:function(data)
					{
						console.log(data);
						var jres = $.parseJSON(data).Result;
						$('#OtpDetails').html('');
						if(jres.length!=0)
						{
							$.each(jres,function(index,objj)
							{
								if(objj.status=='success')
								{
									var sent_time = format24to12(objj.sent_time);
									var message_id = objj.message_id;
									message_id = message_id.split("!!");
									var fos_name = message_id[0];
									var shop_name = message_id[1];
									var message = objj.message;
									var invoices = '';
									var cp = '';
									var orderVal = '';
									var dueDays = 0;
									var msg1 = '';
									var msg2 = '';
									var msg3 = '';
									var msg4 = '';
									if(objj.purpose=='Order Approval')
									{
										message = message.split("@");
										var msg1 = message[0];
										var msg2 = message[1];
										var msg3 = message[2];
										var msg4 = message[3];
									}
										
									if(objj.purpose=='Payment Approval')
									{
										message = message.split(",");
										for(var j=0;j<message.length;j++)
										{
											var msgSplit = message[j].split("!");
											var rDate = msgSplit[2].split("-");
											invoices += '<div width="100%" class="pymntApprDiv">';
											invoices += '<p style="width:100%;"><strong><span>'+msgSplit[0]+'</span><span class="rght">Rs. '+msgSplit[5]+'</span></strong></p>';
											invoices += '<p><span>Due Days</span><span class="rght">'+msgSplit[4]+'</span></p>';
											invoices += '<p><span>Inv Date</span><span class="rght">'+ddmmyyyy(msgSplit[2])+'</span></p>';
											invoices += '<p><span>Cheque Date</span><span class="rght">'+ddmmyyyy(msgSplit[3])+'</span></p>';
											var invDate = mmddyyy(msgSplit[2]);
											var chequeDate = mmddyyy(msgSplit[3]);
											console.log(invDate+','+chequeDate);
											var diff = datediff(parseDate(invDate), parseDate(chequeDate));
											if(diff)
												invoices += '<p><span>Post dated cheque diff</span><span class="rght">'+diff+'</span></p>';
											invoices += '</div><hr>';
										}
									}
									var htm = '';
									htm += '<div class="apprvdDivCls'+index+' otpDivOverall"><div style="width:100%;float:left;margin-bottom:3%;word-break: break-word;color: black;text-shadow: none;padding: 5px;border: 1px solid silver;border-radius: 10px;background: white;">';
									htm += '            	<table width="100%" style="cursor:pointer;">';
									htm += '                	<thead><tr><th></th><th></th></tr></thead>';
									htm += '                	<tbody style="font-size: 14px;">';
									htm += '                    	<tr><td>Fos</td><td>-</td><td><strong>'+fos_name+'</strong></td></tr>';
									htm += '                    <tr><td>R-ID</td><td>-</td><td style="font-style:italic;">'+objj.reference_number+'</td></tr>';
									htm += '                   		<tr><td>Shop</td><td>-</td><td>'+shop_name+'</td></tr>';
									htm += '                    	<tr><td>Approval Type</td><td>-</td><td>'+objj.purpose+'</td></tr>';
									if(objj.purpose=='Order Approval')
									{	
										htm += '                        <tr><td>Order Value</td><td>-</td><td id="ordrNo'+index+'">0</td></tr>';	
										htm += '                        <tr><td colspan="4" style="color: chocolate;font-weight: bold;">Reason :</td></tr>';
										console.log(msg1+'<<>>'+msg2+'<<>>'+msg3+'<<>>'+msg4)
										if(msg1!='no_data')
										{
											msg1 = msg1.split(",");
											htm += '<tr class="err1"><td colspan="4"> <strong>>></strong> Credit Period Exceeded</td></tr>';
											htm += '<tr><td colspan="3"><table border="1" style="border-collapse:collapse;width:98%;margin: auto;">';
											htm += '<tr style="text-align: center;"><th>Invoice</th><th>Overdue</th><th>CP</th><th>Exceeded</th></tr>';
											for(var k=0;k<msg1.length;k++)
											{
												msg1_a = msg1[k].split("!");
												htm += '<tr style="text-align: center;"><td>'+msg1_a[0]+'</td><td>'+msg1_a[3]+'</td><td>'+msg1_a[1]+'</td><td>'+msg1_a[2]+'</td></tr>';
												if(msg1_a[4]!='')
													orderVal = msg1_a[4];
											}
											htm += '</table></td></tr>';
											htm += '<tr><td colspan="4"></td></tr>';
										}
										if(msg2!='no_data')
										{
											htm += '<tr class="err1"><td colspan="3"> <strong>>></strong> Credit Value Exceeded</td></tr>';
											htm += '<tr><td colspan="3"><table border="1" style="border-collapse:collapse;width:98%;margin: auto;">';
											htm += '<tr style="text-align: center;"><th>Credit Value</th><th>Overdue</th><th>Exceeded</th></tr>';
											msg2 = msg2.split("!");
											htm += '<tr style="text-align: center;"><td>'+msg2[0]+'</td><td>'+msg2[1]+'</td><td>'+msg2[2]+'</td></tr></table></td></tr>';
											htm += '<tr><td colspan="4"></td></tr>';
											if(msg2[5]!='')
												orderVal = msg2[3];
										}
										if(msg3!='no_data')
										{
											msg3 = msg3.split(",");
											
											htm += '<tr class="err1"><td colspan="4"> <strong> >></strong> Pending Cheques Days Exceeded!</td></tr>';
											htm += '<tr><td colspan="3"><table border="1" style="border-collapse:collapse;width:98%;margin: auto;">';
											htm += '<tr style="text-align: center;"><th>Inv_Date</th><th>Inv_No</th><th>Chq_No</th><th>Chq_Date</th><th>Days</th></tr>';
											for(var m=0;m<msg3.length;m++)
											{
												msg3_a = msg3[m].split("!");
												htm += '<tr style="text-align: center;"><td>'+msg3_a[0]+'</td><td>'+msg3_a[1]+'</td><td>'+msg3_a[2]+'</td><td>'+msg3_a[3]+'</td><td>'+msg3_a[4]+'</td></tr>';
												if(msg3_a[5]!='')
													orderVal = msg3_a[5];
											}
											htm += '</table></td></tr>';
											htm += '<tr><td colspan="4"></td></tr>';
										}
										if(msg4!='no_data')
										{
											msg4 = msg4.split(",");
											
											htm += '<tr class="err1"><td colspan="4"> <strong> >></strong> Pending Cheques Value Exceeded!</td></tr>';
											htm += '<tr><td colspan="3"><table border="1" style="border-collapse:collapse;width:98%;margin: auto;">';
											htm += '<tr style="text-align: center;"><th>Inv_Date</th><th>Inv_No</th><th>Chq_No</th><th>Chq_Date</th><th>Days</th></tr>';
											for(var n=0;n<msg4.length;n++)
											{
												msg4_a = msg4[n].split("!");
												htm += '<tr style="text-align: center;"><td>'+msg4_a[0]+'</td><td>'+msg4_a[1]+'</td><td>'+msg4_a[2]+'</td><td>'+msg4_a[3]+'</td><td>'+msg4_a[4]+'</td></tr>';
												if(msg4_a[5]!='')
													orderVal = msg4_a[5];
											}
											htm += '</table></td></tr>';
											htm += '<tr><td colspan="4"></td></tr>';
										}
									}
									if(objj.purpose=='Payment Approval')
									{
										htm += '                        <tr><td>Reason</td><td>-</td><td>Due Date Exceeded.</td></tr>';
										htm += '                        <tr style="background: cornsilk;"><td colspan="3" style="font-style:italic;line-height:0px;color:gray;padding: 5px;">'+invoices+'</td></tr>';
									}
									htm += '                        <tr class="responseCls'+index+'"><td><button type="button" class="rejectBtn" style="background:#FF4000;color: white;box-shadow: none;text-shadow: none;padding: 8px;width: 100%;" ref_no="'+objj.reference_number+'" uId_R="'+index+'">Reject</button></td><td></td><td><button type="button" class="apprBtn" style="color:white;background: #8BC34A;box-shadow: none;text-shadow: none;padding: 8px;width: 100%;" ref_no="'+objj.reference_number+'" uId="'+index+'">Approve</button></td></tr>';
									htm += '                    </tbody>';
									htm += '                </table>';
									htm += '            </div>';
									htm += '            <div style="width:100%;float:left;margin-bottom:3%;font-size: 10px;font-weight: bold;color: gray;text-align: right;padding-top: 8px;">'+sent_time+'</div></div>';
									$('#OtpDetails').append(htm);
									$('#ordrNo'+index).html(orderVal);
								}
								else
									$('#OtpDetails').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
							});
						}
						else
						{	
							$('#OtpDetails').html('<p style="text-align:center;color:red;"> :: No Records Found :: </p>');
						}
					}
				});// ajax
				e.preventDefault();
			}
			else
			{
				window.location.replace('#page_home');
			}
		}
		else
			clearInterval(getOtp);
	},3000);	
});

function parseDate(str) 
{
	var mdy = str.split('-');
	return new Date(mdy[2], mdy[0]-1, mdy[1]);
}

function datediff(first, second) {
	// Take the difference between the dates and divide by milliseconds per day.
	// Round to nearest whole number to deal with DST.
	return Math.round((second-first)/(1000*60*60*24));
}

function mmddyyy(a)
{
	var splitDate = a.split("-");
	return splitDate[1]+'-'+splitDate[2]+'-'+splitDate[0];	
}
function ddmmyyyy(b)
{
	var splitDate = b.split("-");
	return splitDate[2]+'-'+splitDate[1]+'-'+splitDate[0];
}
function ddmmyy(c)
{
	var splitDate = c.split("-");
	return splitDate[2]+'-'+splitDate[1]+'-'+splitDate[0].substring(2, 4);
}
function yy(d)
{
	var splitDate = d.split("-");
	return splitDate[0]+'-'+splitDate[1]+'-'+splitDate[2].substring(2, 4);
}


$(document).on('click','.apprBtn',function()
{
	var ref_no = $(this).attr('ref_no');
	var uId = $(this).attr('uId');
	var req = 'Approved';
	setOTPApprvReject(ref_no,uId,req);
});

$(document).on('click','.rejectBtn',function()
{
	var ref_no = $(this).attr('ref_no');
	var uId = $(this).attr('uId_R');
	var req = 'Rejected';
	setOTPApprvReject(ref_no,uId,req);
});

function setOTPApprvReject(ref_no,uId,req)
{
	var app_userId = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl+'Otp_Details.php?setOtpApproval=yes&ref_no='+ref_no+'&req='+req+'&app_userId='+app_userId,function(data)
	{
		console.log(data);
		var jres = data.Result;
		if(jres.status=='success')
		{
			if(req=='Approved')
				$('.responseCls'+uId).html('<p style="color:green;text-align:center;font-weight:bold;font-size:15px;">'+req+'!</p>');
			if(req=='Rejected')
				$('.responseCls'+uId).html('<p style="color:red;text-align:center;font-weight:bold;font-size:15px;">'+req+'!</p>');
			var otpApprvdTblLen = $('#OtpDetails .otpDivOverall').length;
			setTimeout(function(){
				$('.apprvdDivCls'+uId).fadeOut('slow');
				$('.apprvdDivCls'+uId).remove();
				if(otpApprvdTblLen=='1')
				{
					$('#OtpDetails').html('<p style="color:red;text-align:center;">No Requests Found!!</p>');
				}	
			},1000);
		}
	});
}

function format24to12(time_val)
{
	var splt_time = time_val.split(":");
	var hours_orig = splt_time[0];
	var hours = splt_time[0];
	var minutes = splt_time[1];
	var ampm = splt_time[2];
	hours = hours > 12 ? hours-12 : hours;
	hours = hours < 10 ? '0'+hours : hours;
	ampm = hours_orig>12 ? 'pm' : 'am';
	var sent_time = hours+':'+minutes+' '+ampm;
	return sent_time;
}

$(document).on('click','.lineBetwTxt',function()
{
	location.reload();
});


function checkOrderDisabled()
{
	if(localStorage.getItem('disable_overdue'))
		localStorage.removeItem('disable_overdue');
	if(localStorage.getItem('disable_cv'))
		localStorage.removeItem('disable_cv');
	if(localStorage.getItem('disable_unprsnt'))
		localStorage.removeItem('disable_unprsnt');
	var app_userId = localStorage.getItem('app_userId');
	var shpName = $('#shpFullNameTxt').val();
	var srchStr = shpName.includes("&");
	var srchStr1 = shpName.includes("#");
	if(srchStr)
		var shpName = shpName.replace("&","!!");
	if(srchStr1)
		var shpName = shpName.replace("#","@@");
	
	
	$.getJSON(serviceUrl+'todayTask.php?app_userId='+app_userId+'&shpName='+shpName+'&orderDisable=yes',function(data)
	{
		console.log(data);
		var jres = data.Result;
		var overdue_sts = jres.overdue_sts;
		var creditVal_sts = jres.creditVal_sts;
		var chqDays_exceeds_sts = jres.chqDays_exceeds_sts;
		var chqAmt_exceeds_sts = jres.chqAmt_exceeds_sts;
		$('.orderDisabledInfo').html('');
		/* Overdue Disabled Script Start */
		if(overdue_sts.disable=='no' && creditVal_sts.length==0 && chqDays_exceeds_sts.length==0 && chqAmt_exceeds_sts.length==0)
			disabledNo();
		else
		{
			$.each(overdue_sts,function(index,objj)
			{	
				if(objj.disable=='yes')
				{
					var indx = index+1;
					$('.getOrderPage3w').html('Due Date Exceeded for Below Invoices!');
					$('.getOrderPage3w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
					if(indx==1)
					{
						$('.orderDisabledInfo').html('<p class="disableHeadCls">Shop Credit Period : <span class="crdPrd" style="font-weight:bold">'+objj.credit_period +'</span></p>');
						$('.orderDisabledInfo').append('<table id="orderInvTbl" border="1" width="99.9%" height="auto" style="border:1px solid darkturquoise;border-collapse:collapse;"><tr style="font-size: 15px;"><th style="padding: 4px;">Inv Date</th><th>Invoice Number</th><th>Value</th><th>Days</th></tr></table>');
					}
					$('#orderInvTbl').append('<tr style="text-align:center;font-size: 14px;"><td style="padding: 3px;">'+objj.outstanding_date+'</td><td>'+objj.invoices+'</td><td>'+objj.pending_amount+'</td><td>'+objj.overdue+'</td></tr>');
					$('.getOrderPage3w').fadeOut('slow');
					$('.orderDisabledInfo').fadeIn('slow');
					localStorage.setItem('orderDisabled','yes');
					localStorage.setItem('disable_overdue','yes');
				}
			});
			
			if(creditVal_sts.disable=='yes')
			{
				var ttlAmt = creditVal_sts.ttlAmt;
				var credit_value = creditVal_sts.credit_value;
				var exceeded = parseInt(ttlAmt)-parseInt(credit_value);
				$('.orderDisabledInfo').append('<p class="disableHeadCls">Credit Value Exceeded!!</p>');
				$('.orderDisabledInfo').append('<table id="orderInvTbl_cv" border="1" width="99.9%" height="auto" style="border:1px solid darkturquoise;border-collapse:collapse;"><tr style="font-size: 15px;"><th>Credit Value</th><th>Overdue</th><th>Exceeded</th></tr></table>');
				$('#orderInvTbl_cv').append('<tr style="text-align:center;font-size: 14px;"><td>'+credit_value+'</td><td>'+ttlAmt+'</td><td>'+exceeded+'</td></tr>');
				$('.getOrderPage3w').fadeOut('slow');
				$('.orderDisabledInfo').fadeIn('slow');
				localStorage.setItem('orderDisabled','yes');
				localStorage.setItem('disable_cv','yes');
			}
				
			if(chqDays_exceeds_sts!=0 && chqAmt_exceeds_sts!=0)
			{
				$('.orderDisabledInfo').append('<div><p style="color: red;padding-top: 5px;">Pending Cheques CP/CV Exceeded</p><div id="pndngChqExcdDiv"></div></div>');
			}
	
			if(chqDays_exceeds_sts.length!=0)
			{
				$('#pndngChqExcdDiv').append('<table width="100%" border="1" style="border-collapse:collapse;border-color: lightskyblue;"><thead><tr class="tblHd"><th colspan="5">CREDIT PERIOD</th></tr><tr style="font-size: 15px;"><th style="padding:4px;">Inv_Date</th><th>Inv_No</th><th>Chq_No</th><th>Chq_Date</th><th>Days</th></tr></head><tbody id="pndngChqDaysExcdDiv_tbl"></body></table>');
				$.each(chqDays_exceeds_sts,function(index,ob)
				{
					if(ob.disable=='yes')
					{
						var inv_date = ob.inv_date!=''?yy(ob.inv_date):'--';
						var chq_date = ob.chq_date!=''?ddmmyy(ob.chq_date):'--';
						$('#pndngChqDaysExcdDiv_tbl').append('<tr style="font-size: 14px;"><td style="padding:3px;">'+inv_date+'</td><td>'+ob.inv_no+'</td><td>'+ob.chq_no+'</td><td>'+chq_date+'</td><td>'+ob.days+'</td></tr>');
						$('.orderDisabledInfo').fadeIn('slow');
						localStorage.setItem('orderDisabled','yes');
						localStorage.setItem('disable_unprsnt','yes');
					}
				});
			}
			if(chqAmt_exceeds_sts.length!=0)
			{
				$('#pndngChqExcdDiv').append('<table width="100%" border="1" style="border-collapse:collapse;margin-top:10px;border-color: lightskyblue;"><head><tr class="tblHd"><th colspan="5">CREDIT VALUE</th></tr><tr style="font-size: 15px;"><th style="padding:4px;">Inv_Date</th><th>Inv_No</th><th>Chq_No</th><th>Chq_Date</th><th>Val</th></tr></head><tbody id="pndngChqAmtExcdDiv_tbl"></body></table>');
				$.each(chqAmt_exceeds_sts,function(index,ob1)
				{
					if(ob1.disable=='yes')
					{
						var inv_date = ob1.inv_date!=''?yy(ob1.inv_date):'--';
						var chq_date = ob1.chq_date!=''?ddmmyy(ob1.chq_date):'--';
						$('#pndngChqAmtExcdDiv_tbl').append('<tr style="font-size: 14px;"><td style="padding:3px;">'+inv_date+'</td><td>'+ob1.inv_no+'</td><td>'+ob1.chq_no+'</td><td>'+chq_date+'</td><td>'+ob1.amount+'</td></tr>');
						$('.orderDisabledInfo').fadeIn('slow');
						localStorage.setItem('orderDisabled','yes');
						localStorage.setItem('disable_unprsnt','yes');
					}
				});
			}
		
			$('.orderDisabledInfo').append('<p class="orderGetApproval" style="visibility:hidden;font-style:italic;color:blue;font-weight:bold;text-shadow:none;line-height: 0;font-size:1px;">Get Approval to Continue -></p>');
		}
				
		function disabledNo()
		{
			$('.orderDisabledInfo').fadeOut('slow');
			$('.getOrderPage3w').fadeOut('slow');
			if(localStorage.getItem('orderDisabled'))
				localStorage.removeItem('orderDisabled');
		}
		/* Overdue Disabled Script end */	
	});
} 

function currentTimeData()
{
	var date = new Date();	
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var seconds = date.getSeconds();
	hours = hours < 10 ? '0'+hours : hours;
	minutes = minutes < 10 ? '0'+minutes : minutes;
	seconds = seconds< 10 ? '0'+seconds : seconds;
	var strTime = hours+':'+minutes+':'+seconds;
	return strTime;
}

function check15MinAtten(track_Time)
{	
	var a = track_Time.split(':'); // split it at the colons
	var seconds_lcl = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]); 
	var minutes_lcl = Math.round(seconds_lcl/60);
	
	var strTime = currentTimeData();
	var b = strTime.split(':'); // split it at the colons
	var seconds_now = (+b[0]) * 60 * 60 + (+b[1]) * 60 + (+b[2]);
	var minutes_now = Math.round(seconds_now/60);

	console.log(minutes_lcl+'-'+minutes_now);
	var betweenTime = parseInt(minutes_now)-parseInt(minutes_lcl);
	console.log(betweenTime);
	if(betweenTime>=0)
	{
		if(parseInt(betweenTime)>=10)
		{
			$('.attndsPage2w').fadeOut('slow');
			return 1;
		}
		else
		{
			var needTime = 10-parseInt(betweenTime);
			$('.attndsPage2w').fadeOut('slow');
			$('.attndsPage2w').html('Please try after '+needTime+' minutes...');
			$('.attndsPage2w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.attndsPage2w').fadeIn('slow');
			$('html,body').animate({scrollTop:0},500);
			
			return 0;
		}
	}
}

$(document).on('click','#getPymnt_btn',function()
{
	$('#rejectTxtId_pymnt').hide();
	if(localStorage.getItem('overdueCheques'))
		localStorage.removeItem('overdueCheques');
	var shpId = $('#shopNamePymnt').val();
	var app_userId = localStorage.getItem('app_userId');
	var invoiceCheques = '';
	if(localStorage.getItem('pymntGroup'))
	{
		var pymntGroup = JSON.parse(localStorage.getItem('pymntGroup'));
		if(pymntGroup.length!=0)
		{
			var chequeIs = 0;
			$.each(pymntGroup,function(index,objj)
			{
				if(objj.cashType=='cheque')
					chequeIs = 1;
			});
			
			if(chequeIs==1)
			{
				var chequeDataLen = 0;
				var uniquePymntGroupArr = [];
				$.each(pymntGroup,function(index,obj)
				{
					if(obj.cashType=='cheque')
					{
						if(uniquePymntGroupArr.length!=0)
						{
							var duplicate = 0;
							$.each(uniquePymntGroupArr,function(ind,ob)
							{
								if(obj.inv_no==ob.inv_no && obj.chequeDate==ob.chequeDate)
								{
									console.log('duplicate entry');
									duplicate = 1;
								}
							});
							if(duplicate!=1)
							{
								uniquePymntGroupArr.push({'inv_no':obj.inv_no,'chequeDate':obj.chequeDate,'amt':obj.amt,'chequeNo':obj.chequeNo});
								chequeDataLen++;
							}
						}
						else
						{
							uniquePymntGroupArr.push({'inv_no':obj.inv_no,'chequeDate':obj.chequeDate,'amt':obj.amt,'chequeNo':obj.chequeNo});
							chequeDataLen++;
						}
					}
				});
				$.each(uniquePymntGroupArr,function(indx,obj)
				{
					if(chequeDataLen-1==indx)
						invoiceCheques += obj.inv_no+'!'+obj.chequeDate+'!'+obj.amt+'!'+obj.chequeNo;
					else
						invoiceCheques += obj.inv_no+'!'+obj.chequeDate+'!'+obj.amt+'!'+obj.chequeNo+',';
				});
				if(invoiceCheques!='')
				{
					$.getJSON(serviceUrl+'validateCheque.php?app_userId='+app_userId+'&shpId='+shpId+'&invoiceCheques='+invoiceCheques,function(data)
					{
						console.log(data);
						var jres = data.Result;
						var flag = '1';
						var overdueCheques = [];
						$.each(jres,function(index,ob)
						{
							if(ob.status=='otpVerificationBox')
							{
								overdueCheques.push({'inv':ob.inv_no,'dateVal':ob.dateVal,'amt':ob.amt,'chequeNo':ob.chequeNo});
								var app_admin = localStorage.getItem('app_admin');
								if(app_admin==1)
								{
									$('#getApprvlBtnDivPymnt').html('<button type="button" id="sendOtpBtnAdmin" style="background-color:dodgerblue;color: white;font-size: 15px;text-shadow: none;padding: 7px;width: 80%;margin: auto;" class=" ui-btn ui-shadow ui-corner-all">Approve</button>');
								}	
								else
								{
									$('#getApprvlBtnDivPymnt').html('<button type="button" id="sendOtpBtn" style="background-color:dodgerblue;color: white;font-size: 15px;text-shadow: none;padding: 7px;width: 80%;margin: auto;" class=" ui-btn ui-shadow ui-corner-all">Get Approval</button>');
								}
								flag = '0';
							}
						});
						if(overdueCheques.length!=0)
						{
							localStorage.setItem('overdueCheques',JSON.stringify(overdueCheques));
						}
						if(flag=='0')
						{
							$('.otpPopup').show('slow');
						}
						else
						{
							pymntBtnClickAction();
						}
					});
				}//if(invoiceCheques!='')
			}
			else
				pymntBtnClickAction();
		}//if(pymntGroup.length!=0)
		else
		{
			$('.pymntPage4w').html('Please add payments!');
			$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
			$('.pymntPage4w').fadeIn('slow');
			$('html,body').animate({scrollTop:0},500);
		}
	}//if(localStorage.getItem('pymntGroup'))
	else
	{
		$('.pymntPage4w').html('Please add payments!');
		$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.pymntPage4w').fadeIn('slow');
		$('html,body').animate({scrollTop:0},500);
	}
});

function pymntBtnClickAction()
{
	var shopNameTxt = $('#shopNamePymnt').val();
	var invoiceNo='';
	var app_user = localStorage.getItem('app_user');
	var app_userId = localStorage.getItem('app_userId');
	var shpLat = $('#pmyntLat').val();
	var shpLong = $('#pmyntLong').val();
	var cashTypeHidTxt = $('#cashTypeHidTxt').val();
	
	var otpMbl = $('#otpMblHidd').val();
	var Pymnt_Amnt = $('#pymntTotal').val();
	var pwd = 'smsonly';
	
	if(cashTypeHidTxt=='cheque')
	{
		var chequeNmbr = $('#chequeNmbr').val();
		var chequeDate = $('#chequeDate').val();
	}
	if(cashTypeHidTxt=='neft')
	{
		var refNmbr = $('#refNmbr').val();
		var neftDate = $('#neftDate').val();
	}
	if(cashTypeHidTxt=='cn')
	{
		var cnNo = $('#cnNmbr').val();
	}
	var cashFullPartHidTxt = $('#cash_fullPartHidTxt').val();
	var shpFullName = $('#shpFullName').val();
	var srchStr = shpFullName.includes("&");
	var srchStr1 = shpFullName.includes("#");
	if(srchStr)
		var shpFullName = shpFullName.replace("&","!!");
	if(srchStr1)
		var shpFullName = shpFullName.replace("#","@@");
	
	var pymntTotal = $('#pymntTotal').val();
	var infoRdBtn = $('#infoRdBtn').val();
	if(localStorage.getItem('pymntGroup'))
	{
		var pymntGroup = JSON.parse(localStorage.getItem('pymntGroup'));
		var pymntLclSz = pymntGroup.length;
		var cnDateVal = 0;
		if(pymntGroup.length!=0)
		{
			$.each(pymntGroup,function(index,obj)
			{
				if(localStorage.getItem('OutstandingsArrBackup'))
				{
					var ActualAmt = 0;
					var arrBcs = JSON.parse(localStorage.getItem('OutstandingsArrBackup'));
					$.each(arrBcs,function(index,objct)
					{
						if(obj.inv_no==objct.ref_no)
							ActualAmt = objct.pending_amount;
					});
				}
				if(pymntLclSz!=index+1)
				{	
					if(obj.cashType=='cash')
						invoiceNo += obj.inv_no+'^'+obj.amt+'^'+obj.cashType+'^'+obj.pymntType+'^'+obj.originalAmt+'^'+ActualAmt+',';
					if(obj.cashType=='cheque')
						invoiceNo += obj.inv_no+'^'+obj.amt+'^'+obj.cashType+'^'+obj.chequeNo+'^'+obj.chequeDate+'^'+obj.pymntType+'^'+obj.originalAmt+'^'+ActualAmt+',';
					if(obj.cashType=='neft')
						invoiceNo += obj.inv_no+'^'+obj.amt+'^'+obj.cashType+'^'+obj.refNo+'^'+obj.neftDate+'^'+obj.pymntType+'^'+obj.originalAmt+'^'+ActualAmt+',';
					if(obj.cashType=='cn')
						invoiceNo += obj.inv_no+'^'+obj.amt+'^'+obj.cashType+'^'+obj.cnNo+'^'+cnDateVal+'^'+obj.pymntType+'^'+obj.originalAmt+'^'+ActualAmt+',';
				}
				else
				{
					if(obj.cashType=='cash')
						invoiceNo += obj.inv_no+'^'+obj.amt+'^'+obj.cashType+'^'+obj.pymntType+'^'+obj.originalAmt+'^'+ActualAmt;
					if(obj.cashType=='cheque')
						invoiceNo += obj.inv_no+'^'+obj.amt+'^'+obj.cashType+'^'+obj.chequeNo+'^'+obj.chequeDate+'^'+obj.pymntType+'^'+obj.originalAmt+'^'+ActualAmt;
					if(obj.cashType=='neft')
						invoiceNo += obj.inv_no+'^'+obj.amt+'^'+obj.cashType+'^'+obj.refNo+'^'+obj.neftDate+'^'+obj.pymntType+'^'+obj.originalAmt+'^'+ActualAmt;
					if(obj.cashType=='cn')
						invoiceNo += obj.inv_no+'^'+obj.amt+'^'+obj.cashType+'^'+obj.cnNo+'^'+cnDateVal+'^'+obj.pymntType+'^'+obj.originalAmt+'^'+ActualAmt;
				}
			});
		}
	}
	if(shopNameTxt!='')
	{		
		$.post(serviceUrl+"addPayments.php",{shopNameTxt:shopNameTxt,app_user:app_user,invoiceNo:invoiceNo,shpLat:shpLat,shpLong:shpLong,otpMbl:otpMbl,shpFullName:shpFullName,pymntTotal:pymntTotal,infoRdBtn:infoRdBtn},function(data)
		{
			var res = $.parseJSON(data);
			var jres = res.Result;
			console.log(res);
			var lastInsertedIds = '';
			var lastInsertedIds_all = '';
			if(jres.status=='success')
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
				if(jres.ownermail=='sent')
				{	
					$('.pymntPage4w').html('E-receipt sent to partner');
					$('.pymntPage4w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
					$('.pymntPage4w').fadeIn('slow');
					$('html,body').animate({scrollTop:0},500);
				}
				if(jres.ownermail=='error')
				{
					$('.pymntPage4w').html('E-receipt mail sending error!');
					$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
					$('.pymntPage4w').fadeIn('slow');
					$('html,body').animate({scrollTop:0},500);
				}
				$('.pymntPage4w').html('Your Invoices data Saved');
				$('.pymntPage4w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.pymntPage4w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
				var fos_name = localStorage.getItem('fos_name');

				if(localStorage.getItem('pymntGroup'))
				{
					var type = '';
					var allRefNo = '';
					var pymntGrpLcl = JSON.parse(localStorage.getItem('pymntGroup'));
					var pymntLen = pymntGrpLcl.length;
					var dupes = {};
					var singles = [];
					var dupes1 = {};
					var singles1 = [];
					$.each(pymntGrpLcl, function(i, el) 
					{
						if(!dupes[el.cashType]) 
						{
							dupes[el.cashType] = true;
							singles.push(el.cashType);
						}
						if(!dupes1[el.inv_no]) 
						{
							dupes1[el.inv_no] = true;
							singles1.push(el.inv_no);
						}
					});
					var singles1Len = singles1.length;
					$.each(singles1,function(index,el)
					{
						if(singles1Len!=index+1)
							allRefNo += el+',';
						else
							allRefNo += el;
					});
					var singlesLen = singles.length; 
					$.each(singles,function(index,el)
					{
						var amtFinal = 0;
						if(singlesLen!=index+1)
						{
							$.each(pymntGrpLcl, function(i, obj) 
							{
								if(el==obj.cashType)
									amtFinal = parseInt(amtFinal)+parseInt(obj.amt);
							});
							type += el+'-'+amtFinal+',';
						}
						else
						{
							$.each(pymntGrpLcl, function(i, obj) 
							{
								if(el==obj.cashType)
									amtFinal = parseInt(amtFinal)+parseInt(obj.amt);
							});
							type += el+'-'+amtFinal;
						}
					});
					otpMbl = otpMbl+'@'+allRefNo+'@'+type;
				}
				
				Pymnt_Amnt = Pymnt_Amnt+'@'+fos_name;
				var otpDetails = app_userId+','+shopNameTxt+'@'+lastInsertedIds_all;
				sendPassword(otpMbl,pwd,Pymnt_Amnt,otpDetails);
				$.getJSON(serviceUrl+'ordersMail.php?mailSts=pymnt&shopNameTxt='+shopNameTxt+'&app_user='+app_user,function(data)			
   				{
					var dres = data.result;
					if(dres.mail=='sent')
					{
						$('.pymntPage4w').html('Mail sent successfully');
						$('.pymntPage4w').css({"color":"#3c763d","background-color":"#dff0d8","border":"2px solid #d6e9c6","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
						$('.pymntPage4w').fadeIn('slow');
						$('html,body').animate({scrollTop:0},500);
						localStorage.removeItem('pymntGroup');
						location.reload();
					}
					if(dres.mail=='failed')
					{	
						$('.pymntPage4w').html('Mail sending error');
						$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
						$('.pymntPage4w').fadeIn('slow');
						$('html,body').animate({scrollTop:0},500);
					}
					if(dres.status=='norows')
					{
						$('.pymntPage4w').html('Error occure.Please try later.');
						$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
						$('.pymntPage4w').fadeIn('slow');
						$('html,body').animate({scrollTop:0},500);
					}
				});
							
			}
			if(jres.status=='failed')
			{	
				$('.pymntPage4w').html('Error occure.Please try later.');
				$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.pymntPage4w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
			}
			if(jres.status=='OrdersExist')
			{
				$('.pymntPage4w').html('Exists Payments');
				$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
				$('.pymntPage4w').fadeIn('slow');
				$('html,body').animate({scrollTop:0},500);
			}
		});
	}
	else
	{	
		$('.pymntPage4w').html('Please select or Give shop');
		$('.pymntPage4w').css({"color":"#8a6d3b","background-color":"#fcf8e3","border":"2px solid #faebcc","padding":"10px","font-weight":"bold","text-shadow":"none","box-shadow":"0px 1px 1px 1px","border-radius":"3px"});
		$('.pymntPage4w').fadeIn('slow');
		$('html,body').animate({scrollTop:0},500);
	}
}

$(document).on('click','#OtpBtn_attendance',function()
{
	window.location.replace('#page18');
});
$(document).on('click','.prfl_user',function()
{
	window.location.replace('#page25');
});
$('#page25').on('pageshow',function()
{
	getPrflPageDetails();
});

function getPrflPageDetails()
{
	var app_userId = localStorage.getItem('app_userId');
	$.getJSON(serviceUrl+'pswdReset.php?getUserPrflInfo=yes&app_userId='+app_userId,function(data)			
	{
		console.log(data);
		var dres = data.Result;
		if(dres.status=='success')
		{
			if(dres.Edit_status==0)
				UserPrflEditSts('readonly_mode',dres);
			if(dres.Edit_status==1)
				UserPrflEditSts('edit_mode',dres);
		}
		else
		{
			alert('User not available!');
		}
	});
}

function UserPrflEditSts(a,dres)
{
	if(dres.Address=='')
		Address = '';
	else
		Address = dres.Address;
	var htm = '';
	htm += '<div style="width:100%;height:auto;border-top: 5px solid darkseagreen;">';
	htm += '<p class="ldrIcon" style="display:none;position:absolute;margin-top:55%;margin-left:40%;margin-right:40%;"><img src="images/ajax-loader.gif"></p>';
	htm += '	<p style="text-align:center;padding-top: 5%;"><img src="images/user_icon.png" style="height: 65px;border: 3px solid white;border-radius:50%;margin:0;"></p>';
	htm += '	<p style="text-align:center;font-size: 24px;color: white;line-height: 0px;font-family: cursive;margin: 0;">'+dres.fos_name+'</p>';
	htm += '	<div style="width:100%;height:auto;background:white;margin-top: 8%;">';
	htm += '		<div style="height: 65px;width:100%">';
	htm += '			<p style="width: 100%;background: aquamarine;">';
	htm += '				<span style="width:50%;float:left;background:rgb(101,220,229);padding: 7px 0;color: white;text-align: center;">';
	htm += '					<label class="lat_info">'+dres.Latitude+'</label>';
	htm += '					<label style="font-size: 12px;font-family: cursive;">Latitude</label>';
	htm += '				</span>';
	htm += '				<span style="width:50%;float:left;background:rgb(73,189,255);padding: 7px 0;color: white;text-align: center;">';
	htm += '					<label class="lng_info">'+dres.Longitude+'</label>';
	htm += '					<label style="font-size: 12px;font-family: cursive;">Longitude</label>';
	htm += '				</span>';
	htm += '			</p>';
	htm += '		</div>';
	htm += '		<div style="padding:15px;">';
	htm += '			<div  style="height: auto;width:100%;">';
	htm += '					<label style="color:skyblue">Email</label><p style="margin-top: 5px;" class="prflEmail">'+dres.email+'</p><hr>';
	htm += '					<label style="color:skyblue">Phone</label><p style="margin-top: 5px;" class="prflMbl">+91 '+dres.user_name+'</p><hr>';
	if(a=='edit_mode')
	{
		htm += '				<label style="color:skyblue">Address</label><p style="margin-top: 5px;" class="prflAddrs"><textarea class="prflAddrsTxt" value="" style="width:100%;border:none;outline:none;background: aliceblue;" placeholder=" Your home address.." rows="3" autofocus>'+Address+'</textarea></p>';
		htm += '				<p><button type="button" id="homeLocationBtn_prfl">Get Your Location</button><button type="button" id="saveBtn_prfl" style="margin-left:15px;">Save</button></p>';
		htm += '               <p style="font-style: italic;color:gray;opacity: 0.6;"><strong>Note</strong> - If you want to get your location, please hit GET YOUR LOCATION button.</p>';
	}
	else
	{
		htm += '				<label style="color:skyblue">Address</label><p style="margin-top: 5px;" class="prflAddrs">'+Address+'</p>';
		htm += '               <p style="font-style: italic;color:gray;opacity: 0.6;"><strong>Note</strong> - Now you have no rights to edit this page. If you want to edit please contact to the Admin.</p>';
	}
	htm += '			</div>';    
	htm += '		</div>';
	htm += '	</div>';
	htm += '</div>';
	$('#userPrfl').html(htm);
}

$(document).on('click','#homeLocationBtn_prfl',function()
{
	$('.ldrIcon').fadeIn();
	getLatLngCommon();
});

function getLatLngCommon()
{
	var isOff = 'onLine' in navigator && !navigator.onLine;
	if ( isOff )
		getLatLongFailCommon();
	else
		getLatLongSuccessCommon();
}
function getLatLongFailCommon()
{
	window.plugins.toast.showLongCenter(':: No internet connectivity ::');
}

function getLatLongSuccessCommon()
{
	navigator.geolocation.getCurrentPosition(
		GpsSuccessCommon,
		function(error){
			if (error.code == error.TIMEOUT){
				navigator.geolocation.getCurrentPosition(
					GpsSuccessCommon, 
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
	
function GpsSuccessCommon(position)
{
	var Lat  = position.coords.latitude;
	var Long = position.coords.longitude;
	$('.lat_info').html(Lat);
	$('.lng_info').html(Long);
	$('#saveBtn_prfl').show();
	
	var geolocation = Lat+','+Long;
	$.getJSON('http://maps.googleapis.com/maps/api/geocode/json?latlng='+geolocation+'&sensor=true',function(data)
	{
		console.log(data);
		var fullAddress = data.results[0].formatted_address;
		if(fullAddress!='')
			$('.prflAddrs').html('<textarea class="prflAddrsTxt" value="'+fullAddress+'" style="width:100%;outline:none;border:none;" placeholder="Your Home Address..">'+ fullAddress+'</textarea>');
		else
			$('.prflAddrs').html('<textarea class="prflAddrsTxt" value="" style="width:100%;outline:none;" placeholder="Your Home Address.."></textarea>');
	});
	$('.ldrIcon').fadeOut('slow');
}

$(document).on('click','#saveBtn_prfl',function()
{
	$('.ldrIcon').fadeIn('slow');
	var lat_info = $('.lat_info').html();
	var lng_info = $('.lng_info').html();
	var prflAddrsTxt = $('.prflAddrsTxt').val();
	var flag = 1; 
	if(lat_info == '' || lng_info == '' || prflAddrsTxt == '')
	{
		alert('All Fields are mandatory!!');
		flag = 0;
		$('.ldrIcon').fadeOut('slow');
	}
	if(flag==1)
	{
		var app_userId = localStorage.getItem('app_userId');
		$.post(serviceUrl+'pswdReset.php',{'setUserPrflInfo':'yes','app_userId':app_userId,'lat_info':lat_info,'lng_info':lng_info,'prflAddrsTxt':prflAddrsTxt},function(data)			
		{
			console.log(data);
			var jres = $.parseJSON(data).Result;
			console.log(jres);
			if(jres.status=='success')
			{
				alert('Your location details has been saved..');
				getPrflPageDetails();
				$('.ldrIcon').fadeOut('slow');
			}
			else
			{
				alert('Some error occurred. Please try after some times!');
				$('.ldrIcon').fadeOut('slow');
			}
		});
	}
});

$(document).on('change','.pndngCheq_Cls',function()
{
	$('#pndndChequesShp').val('empty');
	$('#pndgCheqRdBtnValId').val($(this).attr('rd_btn'));
	$('#pndndChequesShp').html('<option value="empty">-- Shop Name --</option>');
	var uniqueShpArr = [];
	var ttlAmt_a = 0;
	var rd_btn = $(this).attr('rd_btn');
	if(rd_btn=='0')
	{
		$('#unprentedChquesDiv').hide();
		$('#pendingChequesDiv').hide();
		$('#todayChquesDiv').fadeIn();
		if($('#tdyChequeTbl_bdy tr').length>0)
		{
			$.each(todayCheque_Arr,function(index,a)
			{
				if($.inArray(a.shopName,uniqueShpArr)==-1)
				{
					$('#pndndChequesShp').append('<option value="'+a.shopName+'">'+a.shopName+'</option>');
					uniqueShpArr.push(a.shopName);
				}
			});
			if(todayCheque_Arr.length>0)
			{
				$('#tdyChequeTbl_bdy').html('');
				$.each(todayCheque_Arr,function(index,b)
				{
					var indx = index+1;
					var cheque_date = b.cheque_date;
					var pymnt_date = b.pymnt_date;
					var res = getMnthYear(cheque_date,pymnt_date);
					res = res.split("!");
					cheque_date = res[0];
					
					$('#tdyChequeTbl_bdy').append('<tr style="color:blue;font-size: 11px;"><td style="text-align:center;">'+indx+'</td><td style="word-wrap:break-word;" shpTd="'+b.shopName+'">&nbsp;'+b.shopName+'</td><td style="text-align:center;">'+pymnt_date+'</td><td style="text-align:center;">'+b.cheque_no+'</td><td style="text-align:center;">'+cheque_date+'</td><td style="text-align:right;">'+b.amount+'&nbsp;</td></tr>');
					ttlAmt_a = parseInt(ttlAmt_a)+parseInt(b.amount);
				});
				$('.todayAmtCls').html('<strong>Rs.&nbsp;'+ttlAmt_a+'</strong>');
			}
			else
				$('#tdyChequeTbl_bdy').html('<tr style="text-align:center;color:red;"><td colspan="6">No Records!!</td></tr>');
		}
		else
			$('#pndndChequesShp').append('<option value="000">No Shop Found!</option>');
	}
	else if(rd_btn=='1')
	{
		$('#unprentedChquesDiv').fadeIn();
		$('#pendingChequesDiv').hide();
		$('#todayChquesDiv').hide();
		if($('#unprsntTbl_bdy tr').length>0)
		{
			$.each(unprsntCheque_Arr,function(index,a)
			{
				if($.inArray(a.shopName,uniqueShpArr)==-1)
				{
					$('#pndndChequesShp').append('<option value="'+a.shopName+'">'+a.shopName+'</option>');
					uniqueShpArr.push(a.shopName);
				}
			});
			if(unprsntCheque_Arr.length>0)
			{
				$('#unprsntTbl_bdy').html('');
				$.each(unprsntCheque_Arr,function(index,b)
				{
					var indx = index+1;
					var cheque_date = b.cheque_date;
					var pymnt_date = b.pymnt_date;
					var res = getMnthYear(cheque_date,pymnt_date);
					res = res.split("!");
					cheque_date = res[0];
					
					$('#unprsntTbl_bdy').append('<tr style="font-size: 11px;"><td style="text-align:center;">'+indx+'</td><td style="word-wrap:break-word;" shpTd="'+b.shopName+'">&nbsp;'+b.shopName+'</td><td style="text-align:center;">'+pymnt_date+'</td><td style="text-align:center;">'+b.cheque_no+'</td><td style="text-align:center;">'+cheque_date+'</td><td style="text-align:right;">'+b.amount+'&nbsp;</td></tr>');
					ttlAmt_a = parseInt(ttlAmt_a)+parseInt(b.amount);
				});
				$('#unprsntValue').html('<strong>Rs.&nbsp;'+ttlAmt_a+'</strong>');
			}
			else
				$('#unprsntTbl_bdy').html('<tr style="text-align:center;color:red;"><td colspan="6">No Records!!</td></tr>');
		}
		else
			$('#pndndChequesShp').append('<option value="000">No Shop Found!</option>');
	}
	else
	{
		$('#unprentedChquesDiv').hide();
		$('#pendingChequesDiv').fadeIn();
		$('#todayChquesDiv').hide();
		if($('#chequeTbl_bdy tr').length>0)
		{	
			$.each(PndngCheque_Arr,function(index,a)
			{
				if($.inArray(a.shopName,uniqueShpArr)==-1)
				{
					$('#pndndChequesShp').append('<option value="'+a.shopName+'">'+a.shopName+'</option>');
					uniqueShpArr.push(a.shopName);
				}
			});
			if(PndngCheque_Arr.length>0)
			{
				$('#chequeTbl_bdy').html('');
				$.each(PndngCheque_Arr,function(index,b)
				{
					var indx = index+1;
					var cheque_date = b.cheque_date;
					var pymnt_date = b.pymnt_date;
					var res = getMnthYear(cheque_date,pymnt_date);
					res = res.split("!");
					cheque_date = res[0];
					
					$('#chequeTbl_bdy').append('<tr style="font-size: 11px;"><td style="text-align:center;">'+indx+'</td><td style="word-wrap:break-word;" shpTd="'+b.shopName+'">&nbsp;'+b.shopName+'</td><td style="text-align:center;">'+pymnt_date+'</td><td style="text-align:center;">'+b.cheque_no+'</td><td style="text-align:center;">'+cheque_date+'</td><td style="text-align:right;">'+b.amount+'&nbsp;</td></tr>');
					ttlAmt_a = parseInt(ttlAmt_a)+parseInt(b.amount);
				});
				$('.pndgAmtCls').html('<strong>Rs.&nbsp;'+ttlAmt_a+'</strong>');
			}
			else
				$('#chequeTbl_bdy').html('<tr style="text-align:center;color:red;"><td colspan="6">No Records!!</td></tr>');
		}
		else
				$('#pndndChequesShp').append('<option value="">No Shop Found!</option>');
	}
});

$(document).on('change','#pndndChequesShp',function()
{
	var rdBtn_val = $('#pndgCheqRdBtnValId').val();
	var shpName = $(this).val();
	var filterShpDataArr_tdy = [];
	var filterShpDataArr_unprsnt = [];
	var filterShpDataArr_pndng = [];
	var ttlAmt = 0;
	if(shpName!='empty')
	{
		if(rdBtn_val==0)
		{
			$.each(todayCheque_Arr,function(index,a)
			{
				if(a.shopName==shpName)
				{
					filterShpDataArr_tdy.push({'shopName':a.shopName,'pymnt_date':a.pymnt_date,'cheque_no':a.cheque_no,'cheque_date':a.cheque_date,'amount':a.amount,});
					ttlAmt = parseInt(ttlAmt)+parseInt(a.amount);
				}
			});
			$('.todayAmtCls').html('<strong>Rs.&nbsp;'+ttlAmt+'</strong>');
			if(filterShpDataArr_tdy.length>0)
			{
				$('#tdyChequeTbl_bdy').html('');
				$.each(filterShpDataArr_tdy,function(index,b)
				{
					var indx = index+1;
					var cheque_date = b.cheque_date;
					var pymnt_date = b.pymnt_date;
					var res = getMnthYear(cheque_date,pymnt_date);
					res = res.split("!");
					cheque_date = res[0];
					
					$('#tdyChequeTbl_bdy').append('<tr style="color:blue;font-size: 11px;"><td style="text-align:center;">'+indx+'</td><td style="word-wrap:break-word;" shpTd="'+b.shopName+'">&nbsp;'+b.shopName+'</td><td style="text-align:center;">'+pymnt_date+'</td><td style="text-align:center;">'+b.cheque_no+'</td><td style="text-align:center;">'+cheque_date+'</td><td style="text-align:right;">'+b.amount+'&nbsp;</td></tr>');
				});
			}
			else
				$('#tdyChequeTbl_bdy').html('<tr style="text-align:center;color:red;"><td colspan="6">No Records!!</td></tr>');	
		}
		else if(rdBtn_val==1)
		{
			$.each(unprsntCheque_Arr,function(index,a)
			{
				if(a.shopName==shpName)
				{
					filterShpDataArr_unprsnt.push({'shopName':a.shopName,'pymnt_date':a.pymnt_date,'cheque_no':a.cheque_no,'cheque_date':a.cheque_date,'amount':a.amount,});
					ttlAmt = parseInt(ttlAmt)+parseInt(a.amount);
				}
			});
			$('#unprsntValue').html('<strong>Rs.&nbsp;'+ttlAmt+'</strong>');
			if(filterShpDataArr_unprsnt.length>0)
			{
				$('#unprsntTbl_bdy').html('');
				$.each(filterShpDataArr_unprsnt,function(index,b)
				{
					var indx = index+1;
					var cheque_date = b.cheque_date;
					var pymnt_date = b.pymnt_date;
					var res = getMnthYear(cheque_date,pymnt_date);
					res = res.split("!");
					cheque_date = res[0];
					
					$('#unprsntTbl_bdy').append('<tr style="font-size: 11px;"><td style="text-align:center;">'+indx+'</td><td style="word-wrap:break-word;" shpTd="'+b.shopName+'">&nbsp;'+b.shopName+'</td><td style="text-align:center;">'+pymnt_date+'</td><td style="text-align:center;">'+b.cheque_no+'</td><td style="text-align:center;">'+cheque_date+'</td><td style="text-align:right;">'+b.amount+'&nbsp;</td></tr>');
				});
			}
			else
				$('#unprsntTbl_bdy').html('<tr style="text-align:center;color:red;"><td colspan="6">No Records!!</td></tr>');
		}
		else if(rdBtn_val==2)
		{
			$.each(PndngCheque_Arr,function(index,a)
			{
				if(a.shopName==shpName)
				{
					filterShpDataArr_pndng.push({'shopName':a.shopName,'pymnt_date':a.pymnt_date,'cheque_no':a.cheque_no,'cheque_date':a.cheque_date,'amount':a.amount,});
					ttlAmt = parseInt(ttlAmt)+parseInt(a.amount);
				}
			});
			$('.pndgAmtCls').html('<strong>Rs.&nbsp;'+ttlAmt+'</strong>');
			if(filterShpDataArr_pndng.length>0)
			{
				$('#chequeTbl_bdy').html('');
				$.each(filterShpDataArr_pndng,function(index,b)
				{
					var indx = index+1;
					var cheque_date = b.cheque_date;
					var pymnt_date = b.pymnt_date;
					var res = getMnthYear(cheque_date,pymnt_date);
					res = res.split("!");
					cheque_date = res[0];
					
					$('#chequeTbl_bdy').append('<tr style="font-size: 11px;"><td style="text-align:center;">'+indx+'</td><td style="word-wrap:break-word;" shpTd="'+b.shopName+'">&nbsp;'+b.shopName+'</td><td style="text-align:center;">'+pymnt_date+'</td><td style="text-align:center;">'+b.cheque_no+'</td><td style="text-align:center;">'+cheque_date+'</td><td style="text-align:right;">'+b.amount+'&nbsp;</td></tr>');
				});
			}
			else
				$('#chequeTbl_bdy').html('<tr style="text-align:center;color:red;"><td colspan="6">No Records!!</td></tr>');
		}
		else
		{
			allShpFilterData(shpName);
		}
	}
});

function allShpFilterData(shpName)
{
	var ttlAmt_t = 0;
	var ttlAmt_u = 0;
	var ttlAmt_p = 0;
	filterShpDataArr_tdy = [];
	filterShpDataArr_unprsnt = [];
	filterShpDataArr_pndng = [];
	$.each(todayCheque_Arr,function(index,a)
	{
		if(a.shopName==shpName)
		{
			filterShpDataArr_tdy.push({'shopName':a.shopName,'pymnt_date':a.pymnt_date,'cheque_no':a.cheque_no,'cheque_date':a.cheque_date,'amount':a.amount,});
			ttlAmt_t = parseInt(ttlAmt_t)+parseInt(a.amount);
		}
	});
	$('.todayAmtCls').html('<strong>Rs.&nbsp;'+ttlAmt_t+'</strong>');
	if(filterShpDataArr_tdy.length>0)
	{
		$('#tdyChequeTbl_bdy').html('');
		$.each(filterShpDataArr_tdy,function(index,b)
		{
			var indx = index+1;
			var cheque_date = b.cheque_date;
			var pymnt_date = b.pymnt_date;
			var res = getMnthYear(cheque_date,pymnt_date);
			res = res.split("!");
			cheque_date = res[0];
			
			$('#tdyChequeTbl_bdy').append('<tr style="color:blue;font-size: 11px;"><td style="text-align:center;">'+indx+'</td><td style="word-wrap:break-word;" shpTd="'+b.shopName+'">&nbsp;'+b.shopName+'</td><td style="text-align:center;">'+pymnt_date+'</td><td style="text-align:center;">'+b.cheque_no+'</td><td style="text-align:center;">'+cheque_date+'</td><td style="text-align:right;">'+b.amount+'&nbsp;</td></tr>');
		});
	}
	else
		$('#tdyChequeTbl_bdy').html('<tr style="text-align:center;color:red;"><td colspan="6">No Records!!</td></tr>');
		
	$.each(unprsntCheque_Arr,function(index,a)
	{
		if(a.shopName==shpName)
		{
			filterShpDataArr_unprsnt.push({'shopName':a.shopName,'pymnt_date':a.pymnt_date,'cheque_no':a.cheque_no,'cheque_date':a.cheque_date,'amount':a.amount,});
			ttlAmt_u = parseInt(ttlAmt_u)+parseInt(a.amount);
		}
	});
	$('#unprsntValue').html('<strong>Rs.&nbsp;'+ttlAmt_u+'</strong>');
	if(filterShpDataArr_unprsnt.length>0)
	{
		$('#unprsntTbl_bdy').html('');
		$.each(filterShpDataArr_unprsnt,function(index,b)
		{
			var indx = index+1;
			var cheque_date = b.cheque_date;
			var pymnt_date = b.pymnt_date;
			var res = getMnthYear(cheque_date,pymnt_date);
			res = res.split("!");
			cheque_date = res[0];
			
			$('#unprsntTbl_bdy').append('<tr style="font-size: 11px;"><td style="text-align:center;">'+indx+'</td><td style="word-wrap:break-word;" shpTd="'+b.shopName+'">&nbsp;'+b.shopName+'</td><td style="text-align:center;">'+pymnt_date+'</td><td style="text-align:center;">'+b.cheque_no+'</td><td style="text-align:center;">'+cheque_date+'</td><td style="text-align:right;">'+b.amount+'&nbsp;</td></tr>');
		});
	}
	else
		$('#unprsntTbl_bdy').html('<tr style="text-align:center;color:red;"><td colspan="6">No Records!!</td></tr>');
		
	$.each(PndngCheque_Arr,function(index,a)
	{
		if(a.shopName==shpName)
		{
			filterShpDataArr_pndng.push({'shopName':a.shopName,'pymnt_date':a.pymnt_date,'cheque_no':a.cheque_no,'cheque_date':a.cheque_date,'amount':a.amount,});
			ttlAmt_p = parseInt(ttlAmt_p)+parseInt(a.amount);
		}
	});
	$('.pndgAmtCls').html('<strong>Rs.&nbsp;'+ttlAmt_p+'</strong>');
	if(filterShpDataArr_pndng.length>0)
	{
		$('#chequeTbl_bdy').html('');
		$.each(filterShpDataArr_pndng,function(index,b)
		{
			var indx = index+1;
			var cheque_date = b.cheque_date;
			var pymnt_date = b.pymnt_date;
			var res = getMnthYear(cheque_date,pymnt_date);
			res = res.split("!");
			cheque_date = res[0];
			
			$('#chequeTbl_bdy').append('<tr style="font-size: 11px;"><td style="text-align:center;">'+indx+'</td><td style="word-wrap:break-word;" shpTd="'+b.shopName+'">&nbsp;'+b.shopName+'</td><td style="text-align:center;">'+pymnt_date+'</td><td style="text-align:center;">'+b.cheque_no+'</td><td style="text-align:center;">'+cheque_date+'</td><td style="text-align:right;">'+b.amount+'&nbsp;</td></tr>');
		});
	}
	else
		$('#chequeTbl_bdy').html('<tr style="text-align:center;color:red;"><td colspan="6">No Records!!</td></tr>');
}

/* Sidebar Navigation Script Start */	
$(document).on('click','.reprtLink',function()
{
	if($(this).hasClass('opened'))
	{
		$(this).removeClass('opened');
		$('.upArrow').hide('fast');
		$('.downArrow').show('fast');
	}
	else
	{
		$('.menu').animate({scrollTop:1000},800);
		$(this).addClass('opened');
		$('.downArrow').hide('fast');
		$('.upArrow').show('fast');
	}
	$('.reportsUlId').slideToggle('slow');
});
$(document).on('click','.menuLinkBtn',function()
{
	var page_name = $(this).attr('page_name');
	$('.'+page_name).css({'font-weight':'bold','text-shadow': 'none'});
	$('.'+page_name).parent().css('background','aliceblue');
	overlayProcess();
});
$(document).click('.container',function(e) {
	overlayProcess();
});

function overlayProcess()
{
	var pageHeight = $(window).height();
	$('.menu').css('height',pageHeight-52+'px');
	if($('.menuContent').hasClass('ui-panel-open'))
		$('.overlayCls').fadeIn();
	else
		$('.overlayCls').fadeOut();
}
/* Sidebar End */
var deliveredShp = [];
var undeliveredShp = [];
function home_delivery_Info(app_userId)
{
	$.ajax({
		type : "POST",
		data: {app_userId:app_userId},
		url  : serviceUrl+"common_file.php",
		cache:false,
		async: false,
    	success: function(data) 
		{
			console.log(data);
			if(data!='No rows')
			{
				var res = $.parseJSON(data).Result;
				$('#delvry_ShpSelect').html('<option value="000">&nbsp;<-- All Shops --></option>');
				$('#delivered_Tbl_body').html('');
				$('#undelivered_Tbl_body').html('');
				$.each(res,function(index,obj)
				{
					$('#delvry_ShpSelect').append('<option value="'+obj.shop_id+'">&nbsp;'+obj.ShpName+'</option>');
					if(obj.delivery_status==1)
					{
						$('#delivered_Tbl_body').append('<tr shpId="'+obj.shop_id+'"><td>&nbsp;'+obj.ShpName+'</td><td>&nbsp;'+obj.Area+'</td><td>&nbsp;'+obj.Inv_no+'</td></tr>');
						deliveredShp.push({'shop_id':obj.shop_id,'ShpName':obj.ShpName,'Area':obj.Area,'Inv_no':obj.Inv_no});
					}
					if(obj.delivery_status==0)
					{
						$('#undelivered_Tbl_body').append('<tr shpId="'+obj.shop_id+'"><td>&nbsp;'+obj.ShpName+'</td><td>&nbsp;'+obj.Area+'</td><td>&nbsp;'+obj.Inv_no+'</td></tr>');
						undeliveredShp.push({'shop_id':obj.shop_id,'ShpName':obj.ShpName,'Area':obj.Area,'Inv_no':obj.Inv_no});
					}
				});
			}// if(data==..
			else
			{
				$('#undelivered_Tbl_body').html('<tr><td colspan="3" style="color:red;text-align:center;">No Records Found!</td></tr>');
				$('#delivered_Tbl_body').html('<tr><td colspan="3" style="color:red;text-align:center;">No Records Found!</td></tr>');
			}
		}
	});
}
$(document).on('change','#delvry_ShpSelect',function()
{
	var shpId = $(this).val();
	var undeliveredTblTr = $('#undelivered_Tbl_body tr').length;
	var deliveredTblTr = $('#delivered_Tbl_body tr').length;
	$('#undelivered_Tbl_body').html('');
	$('#delivered_Tbl_body').html('');
	if(undeliveredShp.length>0)
	{
		var len_u = 0;
		$.each(undeliveredShp,function(index,obj)
		{
			if(shpId!='000')
			{
				if(shpId==obj.shop_id)
				{
					$('#undelivered_Tbl_body').append('<tr shpId="'+obj.shop_id+'"><td>&nbsp;'+obj.ShpName+'</td><td>&nbsp;'+obj.Area+'</td><td>&nbsp;'+obj.Inv_no+'</td></tr>');	
					len_u = 1;
				}
			}
			else
			{	
				$('#undelivered_Tbl_body').append('<tr shpId="'+obj.shop_id+'"><td>&nbsp;'+obj.ShpName+'</td><td>&nbsp;'+obj.Area+'</td><td>&nbsp;'+obj.Inv_no+'</td></tr>');
				len_u = 1;
			}
		});
		if(len_u==0)
			$('#undelivered_Tbl_body').html('<tr><td colspan="3" class="cntr err">No Records!</td></tr>');
	}
	else
		$('#undelivered_Tbl_body').html('<tr><td colspan="3" class="cntr err">No Records!</td></tr>');
	if(deliveredShp.length>0)
	{
		var len_d = 0;
		$.each(deliveredShp,function(index,obj)
		{
			if(shpId!='000')
			{
				if(shpId==obj.shop_id)
				{
					$('#delivered_Tbl_body').append('<tr shpId="'+obj.shop_id+'"><td>&nbsp;'+obj.ShpName+'</td><td>&nbsp;'+obj.Area+'</td><td>&nbsp;'+obj.Inv_no+'</td></tr>');
					len_d = 1;
				}
			}
			else
			{
				$('#delivered_Tbl_body').append('<tr shpId="'+obj.shop_id+'"><td>&nbsp;'+obj.ShpName+'</td><td>&nbsp;'+obj.Area+'</td><td>&nbsp;'+obj.Inv_no+'</td></tr>');
				len_d = 1;
			}
		});
		if(len_d==0)
			$('#delivered_Tbl_body').html('<tr><td colspan="3" class="cntr err">No Records!</td></tr>');
	}
	else
		$('#delivered_Tbl_body').html('<tr><td colspan="3" class="cntr err">No Records!</td></tr>');
});

var mdlColorArr = [];
var modelArr = [];
var modelProdCatArr = [];
$('#page26').on('pageshow',function()
{
	$('.overlayCls').hide();
	$('.CloseStckPage26w').hide();
	$('#mdl_slct').html('<option value="000"><-- Model --></option>');
	$('#color_slct').html('<option value="000"><-- Color --></option>');
	$('#stockCnt').val('');
	modelArr = [];
	mdlColorArr = [];
	modelProdCatArr = [];
	$('#closeStockActions').html('<div style="width:96%;overflow:auto;height:56px;margin: 1px;padding: 5px;margin-bottom: 2%;"><div style="width:3000px;" class="productTypeCls"></div></div><table width="99%" border="0" style="border-collapse:collapse;margin:auto;height: 60px;"><thead><tr><th></th><th></th><th></th><th></th></tr></thead><tbody><tr><td style="width:40%;"><select style="padding: 16px 6px;width: 95%;color: cornflowerblue;" id="mdl_slct" class="noneOutline"><option value="000"><-- Model --></option></select></td><td style="width:40%;"><select style="padding: 16px 6px;color: cornflowerblue; width: 95%;" id="color_slct" class="noneOutline"><option value="000"><-- Color --></option></select></td><td style="width:10%;"><input type="number" class="noneOutline" id="stockCnt" style="text-align:center;padding: 14px;width: 100px;border-radius: 5px;color: cornflowerblue;" placeholder="Qty"/></td><td style="width:10%;"><button type="button" class="noneOutline" id="clsStckAddBtn" style="padding: 13px 33px;color: cornflowerblue;"><i class="fa fa-plus" aria-hidden="true"></i></button></td></tr></tbody></table>');
	$.ajax({
		url  : serviceUrl+"common_file.php",
		type : 'POST',
		data : {getAllModels:'yes'},
		cache: false,
		async: false,
		success : function(data)
		{
			//console.log(data);
			var jres = $.parseJSON(data).Result;
			var allMdls = jres.allMdls;
			var todayStocksAll = jres.todayStocksAll;
			var product_categoryArr = jres.product_categoryArr;
			//console.log(allMdls);
			//console.log(todayStocksAll);
			var colorArr = [];
			$('#mdl_slct').html('<option value="000"><-- Model --></option>');
			$('.productTypeCls').html();
			$.each(product_categoryArr,function(index,ob)
			{
				if(ob.status=='success')
					$('.productTypeCls').append('<p style="float:left;" class="prdctCatryCls">'+ob.product_category+'</p>');
				else
					$('.productTypeCls').html('No Category Available!');
			});
			$.each(allMdls,function(index,obb)
			{
				var product_model = obb.product_model;
				if($.inArray(product_model,modelArr)==-1)
				{
					//$('#mdl_slct').append('<option value="'+obb.product_model+'">'+obb.product_model+'</option>');
					modelArr.push(product_model);
					modelProdCatArr.push({product_model:obb.product_model,color:obb.color,product_category:obb.product_category})
				}
				mdlColorArr.push({product_model:obb.product_model,color:obb.color})
			});
			if(todayStocksAll.length>0)
			{
				var verify = 0;
				$('#closeStockActions').show();
				$('#closeStckSaveBtn').show();
				var data_avail = 'no';
				$('#closeStocksData_tbody').html('');
				$.each(todayStocksAll,function(index,o)
				{
					if(index==0)
					{
						if(o.verified_by==1)
						{
							verify = 1;
							$('#closeStockActions').hide();
							$('#closeStckSaveBtn').hide();
						}	
					}
					if(o.status=='success')
					{	
						$('#closeStocksData_tbody').append('<tr><td style="padding: 15px;">'+o.product_model+'</td><td>'+o.color+'</td><td>'+o.quantity+'</td><td><i class="fa fa-trash closeStckDel"></i></td></tr>');
						if(o.verified_by==0)
						{
							data_avail = 'yes';
							$('#closeStckSaveBtn').show();
						}
					}
					else
					{
						$('#closeStocksData_tbody').html('<tr><td colspan="5"><p style="color:gray;opacity:0.3;text-align:center;">Please Add Stock!</p></td></tr>');
						$('#closeStckSaveBtn').hide();
					}
				});
				if(data_avail=='yes')
				{
					$('#closeStckSaveBtn').fadeIn();
				}
				if(verify==1)
				{
					$('.closeStckDel').css('display','none');
				}
			}
		} 
	});
});
$(document).on('click','.prdctCatryCls',function()
{
	var prdctCat = $(this).text();
	$('#prdctCat_tmp').val(prdctCat);
	$('#mdl_slct').html('<option value="000"><-- Model --></option>');
	$('#color_slct').html('<option value="000"><-- Color --></option>');
	$('.prdctCatryCls').css({'color':'darkcyan','background':'white'});
	$(this).css({'color':'white','background':'darkcyan'});
	//console.log(modelProdCatArr);
	$.each(modelProdCatArr,function(index,obb)
	{
		if(obb.product_category==prdctCat)
			$('#mdl_slct').append('<option value="'+obb.product_model+'">'+obb.product_model+'</option>');
	});
});

$(document).on('click','#mdl_slct',function()
{
	var prdctCat_tmp = $('#prdctCat_tmp').val();
	if(prdctCat_tmp=='')
		alert('Please select above category!');
});
$(document).on('click','#color_slct',function()
{
	var prdctCat_tmp = $('#prdctCat_tmp').val();
	if(prdctCat_tmp=='')
		alert('Please select above category!');
});
$(document).on('change','#mdl_slct',function()
{
	$('#color_slct').html('<option value="000"><-- Color --></option>');
	var product_model = $(this).val();	
	//console.log(mdlColorArr);
	$.each(mdlColorArr,function(index,obj)
	{
		if(obj.product_model==product_model)
		{
			$('#color_slct').append('<option value="'+obj.color+'">'+obj.color+'</option>');
		}
	});
	$('#color_slct').click();
});

$(document).on('change','#color_slct',function()
{
	if($(this).val()!==000)
	{
		$('#stockCnt').focus();
		$('#stockCnt').click();
	}
	else
		$('#color_slct').click();
});

var closeStockDataArr = [];
$(document).on('click','#clsStckAddBtn',function()
{
	closeStockDataArr = [];
	var product_model = $('#mdl_slct').val();
	var color = $('#color_slct').val();
	var qty = $('#stockCnt').val();
	if(product_model!='000' && color!='000' && qty!=0)
	{
		$('#closeStocksData_tbody').fadeOut('fast');
		$('#closeStocksData_tbody tr').each(function()
		{
			product_model_new  = $(this).children('td:nth-child(1)').text();
			color_new  = $(this).children('td:nth-child(2)').text();
			qty_new  = $(this).children('td:nth-child(3)').text();
			closeStockDataArr.push({product_model:product_model_new,color:color_new,qty:qty_new});
		});
		if($('#closeStocksData_tbody tr').length==1)
		{
			if($('#closeStocksData_tbody p').html()=='Please Add Stock!')
				$('#closeStocksData_tbody').html('<tr><td style="padding: 15px;">'+product_model+'</td><td>'+color+'</td><td>'+qty+'</td><td><i class="fa fa-trash closeStckDel"></i></td></tr>');
			else
			{		
				$('#closeStocksData_tbody tr').each(function()
				{
					//qty = '';
					product_model_new  = $(this).children('td:nth-child(1)').text();
					color_new  = $(this).children('td:nth-child(2)').text();
					qty_new  = $(this).children('td:nth-child(3)').text();
					if(product_model_new==product_model && color_new==color)
					{	
						qty = qty_new+'+'+qty;
						$('#closeStocksData_tbody').html('<tr><td style="padding: 15px;">'+product_model+'</td><td>'+color+'</td><td>'+qty+'</td><td><i class="fa fa-trash closeStckDel"></i></td></tr>');
					}
					else
						$('#closeStocksData_tbody').append('<tr><td style="padding: 15px;">'+product_model+'</td><td>'+color+'</td><td>'+qty+'</td><td><i class="fa fa-trash closeStckDel"></i></td></tr>');
				});
			}
		}
		else
		{
			$('#closeStocksData_tbody').html('');
			$.each(closeStockDataArr,function(index,o)
			{
				$('#closeStocksData_tbody').append('<tr><td style="padding: 15px;">'+o.product_model+'</td><td>'+o.color+'</td><td id="qty'+index+'">'+o.qty+'</td><td><i class="fa fa-trash closeStckDel"></i></td></tr>');
			});
			var flag = 0;
			var uId = '';
			$('#closeStocksData_tbody tr').each(function()
			{
				//qty = '';
				product_model_new  = $(this).children('td:nth-child(1)').text();
				color_new  = $(this).children('td:nth-child(2)').text();
				qty_new  = $(this).children('td:nth-child(3)').text();
				uId = $(this).children('td:nth-child(3)').attr('id');
				if(product_model_new==product_model && color_new==color)
				{	
					qty = qty_new+'+'+qty;
					$('#'+uId).text(qty);
					flag = 1;
				}
			});
			if(flag == 0)
			{
				$('#closeStocksData_tbody').append('<tr><td style="padding: 15px;">'+product_model+'</td><td>'+color+'</td><td id="'+uId+'">'+qty+'</td><td><i class="fa fa-trash closeStckDel"></i></td></tr>');
			}
		}
		$('#stockCnt').val('');
		$('#closeStocksData_tbody').fadeIn('slow');
		$('#closeStckSaveBtn').show();
		$('.CloseStckPage26w').fadeOut();
	}
	else
	{	
		$('.CloseStckPage26w').fadeOut();
		$('.CloseStckPage26w').html('Please check the values!!').removeClass('successPtxt').addClass('errPtxt').fadeIn();
	}
	$('#closeStocksData').animate({scrollTop:6000},800);
});


/*$(document).on('click','.closeStckDel',function()
{
	if($('#closeStocksData_tbody tr').length==1)
	{
		$('#closeStocksData_tbody').html('<tr><td colspan="5"><p style="color:gray;opacity:0.3;text-align:center;">Please Add Stock!</p></td></tr>');
		$('#closeStckSaveBtn').hide();
	}
	$(this).parent().remove();
		
});*/
$(document).on('click','#closeStckSaveBtn',function()
{
	$('.CloseStckPage26w').fadeOut();
	var closeStckDataFinalArr = [];
	if($('#closeStocksData_tbody tr').length>0)
	{
		$('#closeStocksData_tbody tr').each(function() {
            var product_model = $(this).children('td:nth-child(1)').text();
			var color = $(this).children('td:nth-child(2)').text()
			var qty = $(this).children('td:nth-child(3)').text()
			var qtyLen = qty.split("+");
			var qty_val = 0;
			if(qtyLen.length>1)
			{
				for(var i=0;i<qtyLen.length;i++)
				{
					qty_val+=parseInt(qtyLen[i]);	
				}
			}
			else
				qty_val = qty;
			//console.log(product_model+','+color+','+qty+','+qty_val);
			closeStckDataFinalArr.push({product_model:product_model,color:color,qty:qty,totalQty:qty_val});
        });
		var myJsonString = JSON.stringify(closeStckDataFinalArr);
		//console.log(myJsonString);
		var user_id = localStorage.getItem('app_userId');
		$.ajax(
		{
			type : 'POST',
			url  : serviceUrl+"common_file.php",
			data : {'closeStockData':myJsonString,'user_id':user_id},
			dataType:"json",	
			cache: false,
			async: false, 
			success : function(data)
			{
				console.log(data);
				var jres = data.Result;
				if(jres.status=='success')
				{
					$('.CloseStckPage26w').html('Current stocks added successfully!!').removeClass('errPtxt').addClass('successPtxt').fadeIn();
					$('#mdl_slct').html('<option value="000"><-- Model --></option>');
					$('#color_slct').html('<option value="000"><-- Color --></option>');
					$('.prdctCatryCls').css({'color':'darkcyan','background':'white'});
				}
				else
				{
					$('.CloseStckPage26w').html('Stocks not added correctly!!').removeClass('successPtxt').addClass('errPtxt').fadeIn();
				}
			}
		});
	}
});
$(document).on('click','.closeStckDel',function()
{
	$(this).parent().html('<i class="fa fa-times cnclAction" style="padding: 5px 10px;background: red;border-radius: 5px;color:white;"></i><i class="fa fa-check delAction" style="padding: 5px 10px;background: green;margin-left: 10px;border-radius: 5px;color:white;"></i>');
});
$(document).on('click','.delAction',function()
{
	if($('#closeStocksData_tbody tr').length==1)
	{
		$('#closeStocksData_tbody').html('<tr><td colspan="5"><p style="color:gray;opacity:0.3;text-align:center;">Please Add Stock!</p></td></tr>');
		$('#closeStckSaveBtn').hide();
	}
	else
	{
		$(this).parent().parent().remove();
		$('#closeStckSaveBtn').show();
	}
});
$(document).on('click','.cnclAction',function()
{
	$(this).parent().html('<i class="fa fa-trash closeStckDel"></i>');
});