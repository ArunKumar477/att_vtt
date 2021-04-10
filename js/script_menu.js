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
			menus_script += '<a href="#page3"><li onclick="attendance_log()"><i class="fa fa-clock" style="color:#F7BE81;"></i> &nbsp;<span class="page3">Attendance Log</span></li></a>';
			menus_script += '<a href="#page2" ><li onclick="timer_entry()" ><i class="fa fa-check" style="color:#3ADF00;"></i> &nbsp;<span class="page2">Store Visit</span></li></a>';

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


/*logout*/
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