$(document).ready(function(){
	
	var gridHeight=parseInt($(".itemGrids").css("width").replace("px",""));
	$('.itemGrids').css('height',(gridHeight)+'px');
	
	$(window).resize(function(){
		getHeight=parseInt($(".headBox").css("height").replace("px",""));
		$('.headBack').css('height',(getHeight+400)+'px');
		$('.headBack').css('background-size',"auto "+(getHeight+400)+'px');

		$('#SdeBar_container').css('height',(getHeight-40)+'px');
		$('#Cancel_Container').css('height',(getHeight-40)+'px');
		$('#AlrtScss_container').css('height',(getHeight-40)+'px');

		gridHeight=parseInt($(".itemGrids").css("width").replace("px",""));
		$('.itemGrids').css('height',(gridHeight)+'px');
	});
	
	$('#Navi_btnSidebar').click(function(){
		$('#SdeBar_container').toggleClass('visible');
		$('#Cancel_Container').removeClass('visible');
	});
		
	$('#Cancel_btnCancel').click(function(){
		$('#Cancel_Container').removeClass('visible');
	});
	
	$('#catContainer').hover(function(){
		$('#catBtnLeft').toggleClass('show');
		$('#catBtnRight').toggleClass('show');
	});
    
    $('.itemDisplay figure').each(function(i){
        setTimeout(function(){
            $('.itemDisplay figure').eq(i).addClass('isShowing');
        }, 100 * (i+1));
    });
	
	var pContainerHeight = $('.headBox').height();
	$(window).scroll(function(){
 		var wScroll = $(this).scrollTop();
		if (wScroll <= pContainerHeight){
			$('.headBack').css({
				'transform' : 'translate(0px, -'+ wScroll/20 +'%)'
			});
			$('.headLogo').css({
				'transform' : 'translate(0px, '+ wScroll/3.5 +'%)'
			});
		}
		
		if(wScroll > $('#Navi_container').offset().top - ($(window).height() / 1.7)){
					$('.itemsContent').addClass('showContent');
		}
		if(wScroll > $('.itemDisplay').offset().top+300){
					$('.valueContent').addClass('showContent');
		}
		if(wScroll > $('#catContainer').offset().top+400){
					$('.hardwareContent').addClass('showContent');
		}
		
		
		if(wScroll > $('#Navi_container').offset().top){
			$('#OLV_container').css('z-index','2');
			setTimeout(function(){
				$('#Navi_content').addClass('active');
				$('#OLV_container').css('opacity','1');
			},200 );
		}
		
		if(wScroll < $('#Navi_container').offset().top){
			$('#Navi_content').removeClass('active');
			$('#SdeBar_container').removeClass('visible');
			$('#Cancel_Container').removeClass('visible');
			$('#OLV_container').css('opacity','0');
			setTimeout(function(){
				$('#OLV_container').css('z-index','-1');
			}, 500);
		}
		
		if(wScroll > $('.hardwareContent').offset().top - $(window).height()){
			
			var offset = Math.min(0, wScroll -  $('.hardwareContent').offset().top - $(window).height()+900);
			
			$('#mission').css({'transform': 'translate('+ offset+'px,' +Math.abs(offset* 0.4)+'px)'
			});
			
			$('#vision').css({'transform': 'translate('+ Math.abs(offset)+'px,' +Math.abs(offset* 0.4)+'px)'
			});
		}
		
	});
    
    $('#IInfo_shadow').click(function(){
		$('#iteminfo').toggleClass('show');
		$('#IInfo_shadow').toggleClass('active');
	});
    
	$('#SdeBar_btnCancel').click(function(){
		$('#Cancel_Container').addClass('visible');
	});		
	
	$('#OLV_btnShow').click(function(event){
		event.preventDefault();
		$('.pageWrap').toggleClass('active');	
		$('#Page_container').toggleClass('active');
		$('#OLV_container').toggleClass('active');
		$('#OLV_btnShow').toggleClass('active');
		$('#OLV_form').toggleClass('visible');
		$('#OLV_btnBuy').toggleClass('visible');
		$('#OLV_btnClose').toggleClass('visible');
		$('#OLV_btnEdit').toggleClass('visible');
		$('#OLV_total').toggleClass('visible');
		$('#OLV_frmNoItemContent').toggleClass('visible');
		$('#OLV_btnShow').css('opacity','0');
		$('.aboutUs').toggleClass('active');	
		$('.bg').toggleClass('show');	
		
		setTimeout( function(){
			$('#OLV_form').css('opacity','1');
			$('#OLV_btnBuy').css('opacity','1');
			$('#OLV_btnClose').css('opacity','1');
			$('#OLV_btnEdit').css('opacity','1');
			$('#OLV_total').css('opacity','1');
			$('#OLV_frmNoItemContent').css('opacity','1');	
			gridHeight=parseInt($(".itemGrids").css("width").replace("px",""));
			$('.itemGrids').css('height',(gridHeight)+'px');
		}, 500);
	});
	
	$('#OLV_btnClose').click(function(event){
		event.preventDefault();
		$('.pageWrap').toggleClass('active');			
		$('#Page_container').toggleClass('active');
		$('#OLV_container').toggleClass('active');
		$('#OLV_btnShow').toggleClass('active');
		$('#OLV_form').toggleClass('visible');
		$('#OLV_btnBuy').toggleClass('visible');
		$('#OLV_btnClose').toggleClass('visible');
		$('#OLV_btnEdit').toggleClass('visible');
		$('#OLV_total').toggleClass('visible');
		$('#OLV_frmNoItemContent').toggleClass('visible');
		$('#OLV_form').css('opacity','0');
		$('#OLV_btnBuy').css('opacity','0');
		$('#OLV_btnClose').css('opacity','0');
		$('#OLV_btnEdit').css('opacity','0');
		$('#OLV_total').css('opacity','0');
		$('#OLV_frmNoItemContent').css('opacity','0');
		$('.bg').toggleClass('show');	
		$('.aboutUs').toggleClass('active');
			
		setTimeout( function(){
			$('#OLV_btnShow').css('opacity','1');
			gridHeight=parseInt($(".itemGrids").css("width").replace("px",""));
			$('.itemGrids').css('height',(gridHeight)+'px');
		}, 500);
	});
	
	$('#OLV_btnBuy').click(function(event){
		$('#CInfo_container').toggleClass('active');
		$('#CInfo_shadow').toggleClass('active');
	});
	
	$('#CInfo_btnClose').click(function(event){
		$('#CInfo_container').toggleClass('active');
		setTimeout( function(){
			$('#CInfo_shadow').toggleClass('active');
		}, 300);
	});
				
	$('#Cancel_btnSubmit').click(function(){
		var uname = $("#Cancel_inptORNo").val();
		var pword = $("#Cancel_inptCName").val();
		
		if( uname ==''){
			$('#Cancel_inptUname').css("border-bottom","2px solid red");
			$('#Cancel_alrtNoInput').addClass('visible');
		}
		else{
			$('#Cancel_inptUname').css("border-bottom","2px solid #ccc");
		}
		if( pword ==''){
			$('#Cancel_inptPword').css("border-bottom","2px solid red");
			$('#Cancel_alrtNoInput').addClass('visible');
			$('#Cancel_alrtInvalid').removeClass('visible');
		}else{
			$('#Cancel_inptPword').css("border-bottom","2px solid #ccc");
		}
		if( uname !='' && pword !=''){
						
			if(uname=="rolen" && pword =="lampano"){
				var logintype=0;
				var bool=true;
			}
			else if(uname=="patrick" && pword =="dejesus"){
				var logintype=0;
				var bool=true;
			}
			else if(uname=="melyza" && pword =="sobrino"){
				var logintype=1;
				var bool=true;
			}
			else if(uname=="raemond" && pword =="urbino"){
				var logintype=1;
				var bool=true;
			}
			else{
				$('.Cancel_inpt').css("border-bottom","2px solid red");
				$('#Cancel_alrtNoInput').removeClass('visible');
				$('#Cancel_alrtInvalid').addClass('visible');
			}
						
			if(bool==true){
				$('.Cancel_inpt').css("border-bottom","2px solid #ccc");
			}
						
			if(logintype==1){
				$('#AlrtScss_container').addClass('visible');
				$('#Cancel_Container').removeClass('visible');
				$('#SdeBar_container').toggleClass('visible');
				$('#SdeBar_frmSupplier').addClass('active');
				$('#SdeBar_frmLout').addClass('active');
				$('#SdeBar_frmLin').removeClass('active');
				$('#Cancel_alrtNoInput').removeClass('visible');
				$('#Cancel_alrtInvalid').removeClass('visible');
					setTimeout(function(){
							$('#AlrtScss_container').removeClass('visible');
					}, 2000);
			}
			else if(logintype==0){
				$('#AlrtScss_container').addClass('visible');
				$('#Cancel_Container').removeClass('visible');
				$('#SdeBar_container').toggleClass('visible');
				$('#Sdebar_frmAdmin').addClass('active');
				$('#SdeBar_frmLout').addClass('active');
				$('#SdeBar_frmLin').removeClass('active');
				$('#Cancel_alrtNoInput').removeClass('visible');
				$('#Cancel_alrtInvalid').removeClass('visible');
					setTimeout(function(){
							$('#AlrtScss_container').removeClass('visible');
					}, 2000);
			}
						
			$("#Cancel_inptUname").val("");
			$("#Cancel_inptPword").val("");
		}
	});
				
	$('#SdeBar_btnLout').click(function(){
		$('#SdeBar_container').toggleClass('visible');
		$('#AlrtLout_container').addClass('visible');
		$('#SdeBar_frmLin').addClass('active');
		$('#SdeBar_frmLout').removeClass('active');
		$('#Sdebar_frmAdmin').removeClass('active');
		$('#SdeBar_frmSupplier').removeClass('active');
			setTimeout(function(){
					$('#AlrtLout_container').removeClass('visible');
			}, 2000);
	});
	
	$('#CInfo_btnSubmit').click(function(){
		var Lname = $("#CInfo_inptLName").val();
		var Fname = $("#CInfo_inptFName").val();
		var Contact = Number($("#CInfo_inptContact").val());
		var Address = $("#CInfo_inptAddress").val();
		var Email = $("#CInfo_inptEmail").val();
		
		var dgtContact = Number(String(Contact).replace(/[^0-9]/g,'').length);
		if( Lname ==''){
			$('#CInfo_inptLName').css("border-bottom","2px solid red");
			$('#CInfo_alrtNoInput').addClass('visible');
		}
		else{
			$('#CInfo_inptLName').css("border-bottom","2px solid #ccc");
			var LnameBool=true;
		}
		if( Fname =='' ){
			$('#CInfo_inptFName').css("border-bottom","2px solid red");
			$('#CInfo_alrtNoInput').addClass('visible');
		}
		else{
			$('#CInfo_inptFName').css("border-bottom","2px solid #ccc");
			var FnameBool=true;
		}
		if( Contact =='' || dgtContact != 10){
			$('#CInfo_inptContact').css("border-bottom","2px solid red");
			$('#CInfo_alrtNoInput').addClass('visible');
		}
		else{
			$('#CInfo_inptContact').css("border-bottom","2px solid #ccc");
			var ContactBool=true;
		}
        if( Address ==''){
			$('#CInfo_inptAddress').css("border-bottom","2px solid red");
			$('#CInfo_alrtNoInput').addClass('visible');
		}
		else{
			$('#CInfo_inptAddress').css("border-bottom","2px solid #ccc");
			var AddressBool=true;
		}
		if( LnameBool == true && FnameBool == true && ContactBool == true && AddressBool == true){
			$('#CInfo_alrtNoInput').removeClass('visible');
			$('#CInfo_container').removeClass('active');
			setTimeout( function(){
				$('#CInfo_shadow').removeClass('active');
			}, 300);
		}
	});
	
});	