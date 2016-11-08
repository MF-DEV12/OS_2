$(document).ready(function(){
	
	var getContHeight = parseInt($(".tblContainer").css("height").replace("px",""));
	var getHeight = parseInt($(".unscrollable").css("height").replace("px",""));

	$('.scrollable').css('height', (getContHeight - getHeight ) +"px");
	
	var getContHeight6 = parseInt($(".tblContainerRecForm").css("height").replace("px",""));
	var getHeight6 = parseInt($(".unscrollableRecForm").css("height").replace("px",""));

	$('.scrollableRecForm').css('height', (getContHeight6 - getHeight6 ) +"px");
	
	var getContHeight5 = parseInt($(".tblContainertwolines").css("height").replace("px",""));
	var getHeight5 = parseInt($(".unscrollabletwolines").css("height").replace("px",""));

	$('.scrollabletwolines').css('height', (getContHeight5 - getHeight5 ) +"px");
	
	var getContHeight4 = parseInt($(".tblContaineritemdisplay").css("height").replace("px",""));
	var getHeight4 = parseInt($(".unscrollableitemdisplay").css("height").replace("px",""));

	$('.scrollableitemdisplay').css('height', (getContHeight4 - getHeight4 ) +"px");
	
	var getContHeight3 = parseInt($(".tblContainerPOForm").css("height").replace("px",""));
	var getHeight3 = parseInt($(".unscrollablePOForm").css("height").replace("px",""));
	var getBotHeight3 = parseInt($(".unscrollablePOFormBot").css("height").replace("px",""));

	$('.scrollablePOForm').css('height', (getContHeight3 - getHeight3 - getBotHeight3) +"px");
	
	var getContHeight1 = parseInt($(".tblContainerPOFormSmall").css("height").replace("px",""));
	var getHeight1 = parseInt($(".unscrollablePOFormSmall").css("height").replace("px",""));

	$('.scrollablePOFormSmall').css('height', (getContHeight1 - getHeight1 ) +"px");
	
	$('#btnAdd').click(function(){
		$('#addForm').toggleClass('visible');
	});

	$('#viewEdit').click(function(){
		$('#editModal').addClass('show');
		$('.modalShadow').addClass('show');
		
		$('#edtName').attr('placeholder', $('#vName').val());
		$('#edtSize').attr('placeholder', $('#vSize').val());
		$('#edtUnit').attr('placeholder', $('#vUnit').val());
		$('#edtDescription').attr('placeholder', $('#vDesc').val());
		$('#edtDpo').attr('placeholder', $('#vDPO').val());
		$('#edtUnit_cost').attr('placeholder', $('#vAve').val());
		$('#edtlow_stock').attr('placeholder', $('#vLSs').val());
		$('#edtUnit_price').attr('placeholder', $('#vPrice').val());
		$('#edtRetail').attr('placeholder', $('#vRet').val());
		$('#edtWholesale').attr('placeholder', $('#vWsl').val());
		$('#edtDistribution').attr('placeholder', $('#vDis').val());
		
		$('#edtName').val($('#vName').val());
		$('#edtSize').val($('#vSize').val());
		$('#edtUnit').val($('#vUnit').val());
		$('#edtDescription').val($('#vDesc').val());
		$('#edtDpo').val($('#vDPO').val());
		$('#edtUnit_cost').val($('#vAve').val());
		$('#edtlow_stock').val($('#vLSs').val());
		$('#edtUnit_price').val($('#vPrice').val());
		$('#edtRetail').val($('#vRet').val());
		$('#edtWholesale').val($('#vWsl').val());
		$('#edtDistribution').val($('#vDis').val());
	});
	
	$('#viewRemove').click(function(){
		$('#removeModal').addClass('show');
		$('.modalShadow').addClass('show');
		
		$('#removeText input').val($('#vName').val() + "?");
	});

	$('#btnDirRec').click(function(){
		$('#ReceiveTemp').val("");
	});

	$('#viewRestore').click(function(){
		$('#restoreModal').addClass('show');
		$('.modalShadow').addClass('show');
		
		$('#restoreText input').val($('#vName').val() + "?");
	});
	
	$('#btnAddSup').click(function(){
		$("input").val("");
		$("input").css("border", "1px solid #ccc");
		$('#addSupForm').toggleClass('visible');
	});
	
	$('#addSupReset').click(function(){
		$("input").val("");
	});
	
	$('#btnPC').click(function(){
		$('#physCount').addClass('show');
		$('.modalShadow').addClass('show');
	});
	
	$('#btnPro').click(function(){
		$('#modalProcess').addClass('show');
		$('.modalShadow').addClass('show');
	});
	
	$('#btnCan').click(function(){
		$('#modalCancel').addClass('show');
		$('.modalShadow').addClass('show');
	});
	
	$('#btnShp').click(function(){
		$('#modalShip').addClass('show');
		$('.modalShadow').addClass('show');
	});
	
	$('#Delete').click(function(){
		$('.modalShadow').addClass('show');
	});
	
	$('#Logout').click(function(){
		$('.adminLogout').addClass('active');
		$('.modal').addClass('show');
		$('.modalShadow').addClass('show');
	});
	
	$('.btnClose').click(function(){
		$('.addmodal').removeClass('show');
		setTimeout(function(){
			$('.modalShadow').removeClass('show');
			$('.modal input').val("");
			$('.active').removeClass('active');
		}, 300 );		
	});
	
	$('.additmCancel').click(function(){
		$('.addmodal').removeClass('show');
		setTimeout(function(){
			$('.modalShadow').removeClass('show');
			$('.modal input').val("");
			$('.active').removeClass('active');
		}, 300 );		
	});
	
	$('#RecListForm tbody').on( 'click', 'tr', function () {
		$('tr').removeClass('selected');
		$(this).toggleClass('selected');
		var rowSlct = this.cells;
		var idSlct = rowSlct.item(0).innerHTML;
		
		$('#ReceiveTemp').val(idSlct);
	});
	
	$('#addListForm tbody').on( 'click', 'tr', function () {
		$('tr').removeClass('selected');
		$(this).toggleClass('selected');
		var rowSlct = this.cells;
		var idSlct = rowSlct.item(0).innerHTML;
		
		$('#addTemp').val(idSlct);
		$('#addTemp').val(idSlct);
	});
	
	$('#acc tbody').on( 'click', 'tr', function () {
		$('tr').removeClass('selected');
		$(this).toggleClass('selected');
		var rowSlct = this.cells;
		var idSlct = rowSlct.item(0).innerHTML;
		
		$('.change0').val(idSlct);
		$(rowSlct).each(function(i){
			var n = (i+1).toString();
			$('.change'+n).val((rowSlct.item(i+1).innerHTML).toString());
		});
	});
	
	$('#itm tbody').on( 'click', 'tr', function () {
		$('tr').removeClass('selected');
		$(this).toggleClass('selected');
		var rowSlct = this.cells;
		var idSlct = rowSlct.item(0).innerHTML;
		
		$('#itmNo').val(idSlct);
		$('#viewtemp').val(idSlct);
		$('#viewtemprev').val('');
		$('.change1').val((rowSlct.item(1).innerHTML).toString());
	});
	
	$('#inv tbody').on( 'click', 'tr', function () {
		$('tr').removeClass('selected');
		$(this).addClass('selected');
		var rowSlct = this.cells;
		var idSlct = rowSlct.item(0).innerHTML;
		
		$('.change0').val(idSlct);
		$('#itmNo').val('');
		$('#viewtemp').val('');
		$('#viewtemprev').val('');
		$(rowSlct).each(function(i){
			var n = (i+1).toString();
			$('.change'+n).val((rowSlct.item(i+1).innerHTML).toString());
		});
	});
	
	$('#tblCategories').on( 'click', 'td', function () {
		$('td').removeClass('selected');
		$(this).toggleClass('selected');
		var rowSlct = this.cells;
		var idSlct = rowSlct.item(0).innerHTML;
	});

	$('#suppliertable tbody').on( 'click', 'tr', function () {
		$('tr').removeClass('selected');
		$(this).addClass('selected');
		var rowSlct = this.cells;
		var idSlct = rowSlct.item(0).innerHTML;
		
		$('#supptemp').val(idSlct);
	});
	
    $('#tblItem').on( 'click', 'tr', function () {
		$('tr').removeClass('selected');
		$(this).addClass('selected');
        
        var rowSlct = this.cells;
		var idSlct = rowSlct.item(0).innerHTML;
        $('.change1').val((rowSlct.item(1).innerHTML).toString());
        $('.item0').val((rowSlct.item(0).innerHTML).toString());
        $('#viewitemtemp').val((rowSlct.item(0).innerHTML).toString());
	});
    
	$('#inv5 tbody').on( 'click', 'tr', function () {
		$('tr').removeClass('selected');
		$(this).addClass('selected');
		var rowSlct = this.cells;
		var idSlct = rowSlct.item(0).innerHTML;
		
		$('#itmNo').val(idSlct);
		$('#viewtemprev').val(idSlct);
		$('#viewtemp').val('');
		$('.change1').val((rowSlct.item(1).innerHTML).toString());
	});
	
	$('.topMenu').on( 'click', 'li', function () {
		$('li').removeClass('sdeselected');
		$(this).addClass('sdeselected');
	});
	
	$('.navi').on( 'click', 'li', function () {
		$('li').removeClass('naviselected');
		$(this).addClass('naviselected');
	});
	
$('#naviPurchaseOrder').click(function(){
	$('.visible').removeClass('visible');
		$('#divPurchaseOrder').addClass('visible');
		$('#POMenu').addClass('visible');
		$('li').removeClass('sdeselected');
		$('#sdePO1').addClass('sdeselected');
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.selector').css('right',0+'px');
		$('.topbar').addClass('enable');
		$('.table').removeClass('enable');
		$('.sidebar').removeClass('enable');
		$('.cmd').css('top','0');
		$('.cmd').css('z-index','-1');
		$('.topbarcmd1').css('top','-6vh');
		$('.topbarcmd1').css('z-index','2');
	});
	
	$('#sdePO1').click(function(){
		$('.visible').removeClass('visible');
		$('#divPurchaseOrder').addClass('visible');
		$('#POMenu').addClass('visible');
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd1').css('top','-6vh');
	});
	
	$('#sdePO2').click(function(){
		$('.visible').removeClass('visible');
		$('#divReceiving').addClass('visible');
		$('#POMenu').addClass('visible');
		sdeSelect = 46+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.removed').removeClass('viewFunc');
		$('.orderDisplayed').addClass('viewFunc');
		$('.topbarcmd1').css('top','-12vh');
	});
	
	$('#sdePO3').click(function(){
		$('.visible').removeClass('visible');
		$('#divBackOrder').addClass('visible');
		$('#POMenu').addClass('visible');
		sdeSelect = 46*2+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd1').css('top','-18vh');
	});
	
	$('#sdePO4').click(function(){
		$('.visible').removeClass('visible');
		$('#divSuppliers').addClass('visible');
		$('#POMenu').addClass('visible');
		sdeSelect = 46*3+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd1').css('top','-24vh');
	});
	
	$('#naviInventory').click(function(){
		$('.visible').removeClass('visible');
		$('#divInventory').addClass('visible');
		$('#inventoryMenu').addClass('visible');
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.selector').css('right',0+'px');
		$('.topbarcmd1').css('top','0');
		$('li').removeClass('sdeselected');
		$('#sdeInventory1').addClass('sdeselected');
		$('.topbar').addClass('enable');
		$('.table').removeClass('enable');
		$('.sidebar').removeClass('enable');
		$('.cmd').css('top','0vh');
		$('.cmd').css('z-index','-1');
		$('.topbarcmd2').css('z-index','2');
		$('.topbarcmd2').css('top','-6vh');
	});
	
	$('#sdeInventory1').click(function(){
		$('.visible').removeClass('visible');
		$('#divInventory').addClass('visible');
		$('#inventoryMenu').addClass('visible');
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd2').css('top','-6vh');
	});
	
	$('#sdeInventory2').click(function(){
		$('.visible').removeClass('visible');
		$('#divItems').addClass('visible');
		$('#inventoryMenu').addClass('visible');
		sdeSelect = 46+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.removed').removeClass('viewFunc');
		$('.orderDisplayed').addClass('viewFunc');
		$('.topbarcmd2').css('top','-12vh');
	});
	
	$('#sdeInventory3').click(function(){
		$('.visible').removeClass('visible');
		$('#divLowStock').addClass('visible');
		$('#inventoryMenu').addClass('visible');
		sdeSelect = 46*2+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd2').css('top','0');
	});
	
	$('#sdeInventory4').click(function(){
		$('.visible').removeClass('visible');
		$('#divFaC').addClass('visible');
		$('#inventoryMenu').addClass('visible');
		sdeSelect = 46*3+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd2').css('top','-18vh');
	});
	
	$('#sdeInventory5').click(function(){
		$('.visible').removeClass('visible');
		$('#divRemoved').addClass('visible');
		$('#inventoryMenu').addClass('visible');
		sdeSelect = 46*4+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.removed').addClass('viewFunc');
		$('.orderDisplayed').removeClass('viewFunc');
		$('.topbarcmd2').css('top','-24vh');
	});
	
$('#naviOrders').click(function(){
	$('.visible').removeClass('visible');
		$('#divAllOrders').addClass('visible');
		$('#ordersMenu').addClass('visible');
		$('li').removeClass('sdeselected');
		$('#sdeOrders1').addClass('sdeselected');
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.selector').css('right',0+'px');
		$('.topbarcmd2').css('top','0');
		$('.topbar').addClass('enable');
		$('.table').removeClass('enable');
		$('.sidebar').removeClass('enable');
		$('.cmd').css('top','0vh');
		$('.cmd').css('z-index','-1');
		$('.topbarcmd3').css('top','0');
		$('.topbarcmd3').css('z-index','2');
	});
	
$('#sdeOrders1').click(function(){
	$('.visible').removeClass('visible');
		$('#divAllOrders').addClass('visible');
		$('#ordersMenu').addClass('visible');
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd3').css('top','0');
	});
	
$('#sdeOrders2').click(function(){
	$('.visible').removeClass('visible');
		$('#divNewOrders').addClass('visible');
		$('#ordersMenu').addClass('visible');
		sdeSelect = 46+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd3').css('top','-6vh');
	});
	
$('#sdeOrders3').click(function(){
	$('.visible').removeClass('visible');
		$('#divProOrders').addClass('visible');
		$('#ordersMenu').addClass('visible');
		sdeSelect = (46*2)+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd3').css('top','-12vh');
	});
	
$('#sdeOrders4').click(function(){
	$('.visible').removeClass('visible');
		$('#divShipOrders').addClass('visible');
		$('#ordersMenu').addClass('visible');
		$('.topbarcmd3').css('top','0');
		sdeSelect = (46*3)+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd3').css('top','0vh');
	});
	
$('#sdeOrders5').click(function(){
	$('.visible').removeClass('visible');
		$('#divCancelOrders').addClass('visible');
		$('#ordersMenu').addClass('visible');
		sdeSelect = (46*4)+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd3').css('top','0vh');
		sdeSelect = 44;
	});

$('#naviReports').click(function(){
	$('.visible').removeClass('visible');
		$('#divReports1').addClass('visible');
		$('#reportsMenu').addClass('visible');
		$('li').removeClass('sdeselected');
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.selector').css('right',0+'px');
		$('#sdeReports1').addClass('sdeselected');
		$('.enable').removeClass('enable');
		$('.sidebar').addClass('enable');
		$('.table').addClass('enable');
		$('.cmd').css('top','0vh');
	});
	
$('#sdeReports1').click(function(){
	$('.visible').removeClass('visible');
		$('#divReports1').addClass('visible');
		$('#reportsMenu').addClass('visible');
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
	});
	
$('#sdeReports2').click(function(){
	$('.visible').removeClass('visible');
		$('#divReports2').addClass('visible');
		$('#reportsMenu').addClass('visible');
		sdeSelect = 46+12.5;
		$('.selector').css('top',sdeSelect+'px');
	});
	
$('#sdeReports3').click(function(){
	$('.visible').removeClass('visible');
		$('#divReports3').addClass('visible');
		$('#reportsMenu').addClass('visible');
		sdeSelect = 46*2+12.5;
		$('.selector').css('top',sdeSelect+'px');
	});
	
	$('.admindrop').click(function(){
		$('#adminmenu').toggleClass('show');
	});
});