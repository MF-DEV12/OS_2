$(document).ready(function () {
	
	$('#btnLogin').click(function () {
		
		var uname = $("#Username").val();
		var pword = $("#Password").val();
		var sel = document.getElementById('LoginType');
		var ltype = sel.options[sel.selectedIndex].value;
		
		if( uname ==''){
			$('#alrt1').addClass('active');
		}
		else{
			$('#alrt1').removeClass('active');
		}
		if( pword ==''){
			$('#alrt2').addClass('active');
		}
		else{
			$('#alrt2').removeClass('active');
		}
		if( ltype == "none"){
			$('#alrt3').addClass('active');
		}
		else{
			$('#alrt3').removeClass('active');
		}
		if( uname !='' && pword !='' && ltype!="none"){
						
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
				$('.Lin_inpt').css("border-bottom","2px solid red");
				$('#Lin_alrtNoInput').removeClass('visible');
				$('#Lin_alrtInvalid').addClass('visible');
			}
						
			if(bool==true){
				$('.Lin_inpt').css("border-bottom","2px solid #ccc");
			}
						
			if(logintype==1){
				$('#AlrtScss_container').addClass('visible');
				$('#Lin_Container').removeClass('visible');
				$('#SdeBar_container').toggleClass('visible');
				$('#SdeBar_frmSupplier').addClass('active');
				$('#SdeBar_frmLout').addClass('active');
				$('#SdeBar_frmLin').removeClass('active');
				$('#Lin_alrtNoInput').removeClass('visible');
				$('#Lin_alrtInvalid').removeClass('visible');
					setTimeout(function(){
							$('#AlrtScss_container').removeClass('visible');
					}, 2000);
			}
			else if(logintype==0){
				$('#AlrtScss_container').addClass('visible');
				$('#Lin_Container').removeClass('visible');
				$('#SdeBar_container').toggleClass('visible');
				$('#Sdebar_frmAdmin').addClass('active');
				$('#SdeBar_frmLout').addClass('active');
				$('#SdeBar_frmLin').removeClass('active');
				$('#Lin_alrtNoInput').removeClass('visible');
				$('#Lin_alrtInvalid').removeClass('visible');
					setTimeout(function(){
							$('#AlrtScss_container').removeClass('visible');
					}, 2000);
			}
						
			$("#Lin_inptUname").val("");
			$("#Lin_inptPword").val("");
		}
	});
});