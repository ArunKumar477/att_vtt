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
				document.getElementById("checked_in").disabled = true;

				$('#checked_in').parent().css({"color":"red",});
				$('#clock_started').html('clock started');
			}else{
				alert("try again")
			}
		}
	});
	localStorage.setItem("condition_check", "check_out_fun");
});
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
				console.log(data)
				if(data.status == 'success'){
					htm += '<h3 style="text-align: center;">Working Hours</h3>';
					htm += '<center><h4> Duration : '+data.data+'</h4></center>';
					$('#total_time_value').html(htm);
					document.getElementById("checked_out").disabled = true;
					$('#checked_out').parent().css({"color":"red",});
					$('#clock_started').html('clock Stoped');
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
					alert("try again")
				}
			},error: function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  alert(err.Message);
			}
		});//ajax
 });
