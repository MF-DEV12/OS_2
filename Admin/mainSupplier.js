$(document).ready(function(){
	
	var getContHeight = parseInt($(".tblContainer").css("height").replace("px",""));
	var getHeight = parseInt($(".unscrollable").css("height").replace("px",""));
	
	$('.scrollable').css('height', (getContHeight - getHeight - 2) +"px");
	$('.selector').css('right',0+'px');		
	
	$('#btnAdd').click(function(){
		$('#addModal').addClass('show');
		$('.modalShadow').addClass('show');
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
	
	$('#viewRestore').click(function(){
		$('#restoreModal').addClass('show');
		$('.modalShadow').addClass('show');
		
		$('#restoreText input').val($('#vName').val() + "?");
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
	
	$('#addFam').click(function(){
		$('#famAdd').addClass('show');
		$('.modalShadow').addClass('show');
	});
	
	$('#btnEdit').click(function(){
		if($('#sctdFam').val() != ''){
			$('#famEdt').addClass('show');
			$('.modalShadow').addClass('show');
		}
		else if($('#sctdCat').val() != ''){
			$('#catEdt').addClass('show');
			$('.modalShadow').addClass('show');
		}
		else{
			alert('Please choose an item to edit.');
		}	
	});
	
	$('#btnDelete').click(function(){
		if($('#sctdFam').val() != ''){
			$('#removeText input').val($("#edtFamName").val());
			$('#famDel').addClass('show');
			$('.modalShadow').addClass('show');
		}
		else if($('#sctdCat').val() != ''){
			$('#removeText input').val($("#edtCatName").val());
			$('#catDel').addClass('show');
			$('.modalShadow').addClass('show');
		}
		else{
			alert('Please choose an item to delete.');
		}	
	});
	
	$('#addCat').click(function(){
		$('#catAdd').addClass('show');
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
	
	$('.btnClose').click(function(){
		$('.modal').removeClass('show');
		setTimeout(function(){
			$('.modalShadow').removeClass('show');
			$('.modal input').val("");
			$('.active').removeClass('active');
		}, 300 );		
	});
	
	$('.btnCancel').click(function(){
		$('.modal').removeClass('show');
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
	
	$('#itm tbody').on( 'click', 'tr', function(){
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
	
	$('#Fam tbody').on( 'click', 'tr', function () {
		$('tr').removeClass('selected');
		$(this).addClass('selected');
		var rowSlct = this.cells;
		var idSlct = rowSlct.item(0).innerHTML;
		
		$('.change0').val(idSlct);
		$('#itmNo').val('');
		$('#viewtemp').val('');
		$('#viewtemprev').val('');
		$('#sctdFam').val(idSlct);
		$('#sctdCat').val('');
		
		$('#edtFamName').val((rowSlct.item(1).innerHTML).toString());
		$('#edtFamDesc').val((rowSlct.item(2).innerHTML).toString());
	});
	
    $('#Cat tbody').on( 'click', 'tr', function () {
		$('tr').removeClass('selected');
		$(this).addClass('selected');
		var rowSlct = this.cells;
		var idSlct = rowSlct.item(0).innerHTML;
		
		$('.change0').val(idSlct);
		$('#itmNo').val('');
		$('#viewtemp').val('');
		$('#viewtemprev').val('');
		$('#sctdFam').val('');
		$('#sctdCat').val(idSlct);
			
		$('#edtCatName').val((rowSlct.item(1).innerHTML).toString());
		$('#edtCatDesc').val((rowSlct.item(2).innerHTML).toString());
	});
	
    $('#tblItem').on( 'click', 'tr', function () {
		$('tr').removeClass('selected');
		$(this).addClass('selected');
        
        var rowSlct = this.cells;
        $('.change1').val((rowSlct.item(1).innerHTML).toString());
        $('#itmNo').val((rowSlct.item(0).innerHTML).toString());
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
	
	$('#naviRequests').click(function(){
		$('.visible').removeClass('visible');
		$('#divAllRequests').addClass('visible');
		$('#requestsMenu').addClass('visible');
		$('li').removeClass('sdeselected');
		$('#sdeRequests1').addClass('sdeselected');
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.selector').css('right',0+'px');
		$('.topbarcmd1').css('top','0');
		$('.cmd').css('top','0vh');
		$('.cmd').css('z-index','-1');
		$('.topbarcmd1').css('top','0');
		$('.topbarcmd1').css('z-index','2');
	});
	
	$('#sdeRequests1').click(function(){
		$('.visible').removeClass('visible');
		$('#divAllRequests').addClass('visible');
		$('#requestsMenu').addClass('visible');
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd1').css('top','0');
	});
	
	$('#sdeRequests2').click(function(){
		$('.visible').removeClass('visible');
		$('#divNewRequests').addClass('visible');
		$('#requestsMenu').addClass('visible');
		sdeSelect = 46+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd1').css('top','-6vh');
	});
	
	$('#sdeRequests3').click(function(){
		$('.visible').removeClass('visible');
		$('#divProRequests').addClass('visible');
		$('#requestsMenu').addClass('visible');
		sdeSelect = (46*2)+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd1').css('top','-12vh');
	});
	
	$('#sdeRequests4').click(function(){
		$('.visible').removeClass('visible');
		$('#divIncRequests').addClass('visible');
		$('#requestsMenu').addClass('visible');
		$('.topbarcmd1').css('top','0');
		sdeSelect = (46*3)+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd1').css('top','0vh');
	});
	
	$('#sdeRequests5').click(function(){
		$('.visible').removeClass('visible');
		$('#divShipRequests').addClass('visible');
		$('#requestsMenu').addClass('visible');
		sdeSelect = (46*4)+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd1').css('top','0vh');
		sdeSelect = 44;
	});
	
	$('#sdeRequests6').click(function(){
		$('.visible').removeClass('visible');
		$('#divCancelRequests').addClass('visible');
		$('#requestsMenu').addClass('visible');
		sdeSelect = (46*5)+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd1').css('top','0vh');
		sdeSelect = 44;
	});
	
	$('#naviItems').click(function(){
		$('.visible').removeClass('visible');
		$('#divItems1').addClass('visible');
		$('#itemsMenu').addClass('visible');
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.selector').css('right',0+'px');
		$('.topbarcmd2').css('top','-6vh');
		$('li').removeClass('sdeselected');
		$('#sdeItems1').addClass('sdeselected');
		$('.cmd').css('top','-6vh');
		$('.cmd').css('z-index','-1');
		$('.topbarcmd2').css('z-index','2');
	});
	
	$('#sdeItems1').click(function(){
		$('.visible').removeClass('visible');
		$('#divItems1').addClass('visible');
		$('#itemsMenu').addClass('visible');
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd2').css('top','-6vh');
	});
    
	$('#sdeItems2').click(function(){
		$('.visible').removeClass('visible');
		$('#divItems2').addClass('visible');
		$('#itemsMenu').addClass('visible');
		$("input:text").val('');
		$(".addfield").removeClass('addfield');
		$("#chkSize").prop("checked", false);
		$("#chkColor").prop("checked", false);
		$("#chkDesc").prop("checked", false);
		$('#addSlctSize').prop('disabled', true);
		showslct();
		bFields = 0;
		sdeSelect = 46+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.removed').removeClass('viewFunc');
		$('.orderDisplayed').addClass('viewFunc');
		$('.topbarcmd2').css('top','0');
	});
    
    $('#viewAdd').click(function(){
		$('.visible').removeClass('visible');
		$('#divAddVariant').addClass('visible');
		$('#itemsMenu').addClass('visible');
		$("input:text").val('');
		$(".addfield").removeClass('addfield');
		$("#chkSize").prop("checked", false);
		$("#chkColor").prop("checked", false);
		$("#chkDesc").prop("checked", false);
		$('#addSlctSize').prop('disabled', true);
		showslct();
		bFields = 0;
		sdeSelect = 12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.removed').removeClass('viewFunc');
		$('.orderDisplayed').addClass('viewFunc');
		$('.topbarcmd2').css('top','0');
	});
	
	$('#sdeItems3').click(function(){
		$('.visible').removeClass('visible');
		$('#divItems3').addClass('visible');
		$('#itemsMenu').addClass('visible');
		sdeSelect = 46*2+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.topbarcmd2').css('top','-6vh');
	});

	$('.admindrop').click(function(){
		$('#adminmenu').toggleClass('show');
	});
	
	var bFields = 0;
	$('#chkSize').change(function(){
		var c = this.checked ? 1 : 0;
		if(c==1){
			$('#addSlctSize').prop('disabled', false);
			bFields = bFields + 1;
			$('#addedFieldSize').addClass('addfield');
			
			var slcted = $('#addSlctSize').val();
			if(slcted == 'Length' ){
				$('#addLength').addClass('addfield');
				$('#addVolume').removeClass('addfield');
				$('#addDimension').removeClass('addfield');
				$('.addedFieldSize2').removeClass('addfield');
			}
			else if(slcted == 'Volume' ){
				$('#addVolume').addClass('addfield');
				$('#addLength').removeClass('addfield');
				$('#addDimension').removeClass('addfield');
				$('.addedFieldSize2').removeClass('addfield');
			}
			else if(slcted == 'Dimension' ){
				$('#addDimension').addClass('addfield');
				$('.addedFieldSize2').addClass('addfield');
				$('#addLength').removeClass('addfield');
				$('#addVolume').removeClass('addfield');
			}
			
			$('.addedFieldPrice').addClass('addfield');
			$('#addPrice').addClass('addfield');
		}
		else{
			$('#addSlctSize').prop('disabled', true);
			if(bFields == 1 || bFields == 3 || bFields == 5 || bFields == 7){
				bFields = bFields - 1;
				$('#addedFieldSize').removeClass('addfield');
				
				var slcted = $('#addSlctSize').val();
				if(slcted == 'Length' ){
					$('#addLength').removeClass('addfield');
					$('.addedFieldSize2').removeClass('addfield');
				}
				else if(slcted == 'Volume' ){
					$('#addVolume').removeClass('addfield');
					$('.addedFieldSize2').removeClass('addfield');
				}
				else if(slcted == 'Dimension' ){
					$('#addDimension').removeClass('addfield');
					$('.addedFieldSize2').removeClass('addfield');
				}
				
				if(bFields == 0){
					$('.addedFieldPrice').removeClass('addfield');
					$('#addPrice').removeClass('addfield');
                    $("#addPrice input:text").val('');
				}
			}
		}
		$("#addSize input:text").val('');
		$('#addFillFieldTemp').val(bFields);
	});

	$('#chkColor').change(function(){
		var c = this.checked ? 1 : 0;
		if(c==1){
			bFields = bFields + 2;
			$('#addedFieldColor').addClass('addfield');
			$('#addColor').addClass('addfield');
			
			$('.addedFieldPrice').addClass('addfield');
			$('#addPrice').addClass('addfield');
		}
		else{
			if(bFields == 2 || bFields == 3 || bFields == 6 || bFields == 7){
				bFields = bFields - 2;
				$('#addedFieldColor').removeClass('addfield');
				$('#addColor').removeClass('addfield');
				
				if(bFields == 0){
					$('.addedFieldPrice').removeClass('addfield');
					$('#addPrice').removeClass('addfield');
                    $("#addPrice input:text").val('');
				}
			}
		}
		$("#addColor input:text").val('');
		$('#addFillFieldTemp').val(bFields);
	});

	$('#chkDesc').change(function(){
		var c = this.checked ? 1 : 0;
		if(c==1){
			bFields = bFields + 4;
			$('#addedFieldDesc').addClass('addfield');
			$('#addDesc').addClass('addfield');
			
			$('.addedFieldPrice').addClass('addfield');
			$('#addPrice').addClass('addfield');
		}
		else{
			if(bFields == 4 || bFields == 5 || bFields == 6 || bFields == 7){
				bFields = bFields - 4;
				$('#addedFieldDesc').removeClass('addfield');
				$('#addDesc').removeClass('addfield');
				
				if(bFields == 0){
					$('.addedFieldPrice').removeClass('addfield');
					$('#addPrice').removeClass('addfield');
                    $("#addPrice input:text").val('');
				}
			}
		}
		$("#addDesc input:text").val('');
		$('#addFillFieldTemp').val(bFields);
	});
	
	$('#addSlctSize').change(function(){
		var slcted = $(this).val();
		$("#addedInputs input:text").val('');
		if(slcted == 'Length' ){
			$('#addLength').addClass('addfield');
			$('#addVolume').removeClass('addfield');
			$('#addDimension').removeClass('addfield');
			$('.addedFieldSize2').removeClass('addfield');
		}
		else if(slcted == 'Volume' ){
			$('#addVolume').addClass('addfield');
			$('#addLength').removeClass('addfield');
			$('#addDimension').removeClass('addfield');
			$('.addedFieldSize2').removeClass('addfield');
		}
		else if(slcted == 'Dimension' ){
			$('#addDimension').addClass('addfield');
			$('.addedFieldSize2').addClass('addfield');
			$('#addLength').removeClass('addfield');
			$('#addVolume').removeClass('addfield');
		}
	});
	
	$('#additmReset').click(function(){
		$('.visible').removeClass('visible');
		$('#divItems2').addClass('visible');
		$('#itemsMenu').addClass('visible');
		$("input:text").val('');
		$(".addfield").removeClass('addfield');
		$("#chkSize").prop("checked", false);
		$("#chkColor").prop("checked", false);
		$("#chkDesc").prop("checked", false);
		$('#addSlctSize').prop('disabled', true);
		showslct();
		bFields = 0;
		sdeSelect = 46+12.5;
		$('.selector').css('top',sdeSelect+'px');
		$('.removed').removeClass('viewFunc');
		$('.orderDisplayed').addClass('viewFunc');
		$('.topbarcmd2').css('top','0');
	});
});