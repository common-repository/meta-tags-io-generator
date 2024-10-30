  
  jQuery(document).ready(function( $ ){
	
	$('#site_name').on('input', function() {
		$('.card-smedia-title, .title-card').html($(this).val());
		if ($(".MTIOG_price_title")[0]){ 
			$('.card-smedia-title-woo, .title-card-woo').html($(this).val() + ' - Blue t-shirt (10,50 €)');
		}
		else {
			$('.card-smedia-title-woo, .title-card-woo').html($(this).val() + ' - Blue t-shirt');
		}
		
	})
	
	$('#description').on('input', function() {
		$('.card-smedia-content, .google-description').html($(this).val());
		var str = $(this).val();
		if(str.length > 150) $('.google-description').html(str.substring(0,150) + '...');
		
		if ($(".global_desc_woo")[0]){
			if ($(".MTIOG_price_descr ")[0]){ 
				$('.woo-description').html('10,50 € - ' + $(this).val());
				var str = $(this).val();
				if(str.length > 150) $('.woo-description').html('10,50€ - ' + str.substring(0,150) + '...');
			}
			else {
				$('.woo-description').html($(this).val());
				var str = $(this).val();
				if(str.length > 150) $('.woo-description').html(str.substring(0,150) + '...');
			}
		}
		
	})
	  
	$('body').on('change', '.MTIOG_input_file', function(event) {

		readURL(this);
	})
	
	//preview img
	function readURL(input) {
		
            if (input.files && input.files[0]) {
				
                var reader = new FileReader();
				
                reader.onload = function (e) {
                    $('#bgimagefile, .card-image-smedia.bgimage-g, .card-image-smedia.bgimage-woo.ch-dyn').css('background-image', 'url(' + e.target.result + ')');
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
 
 
 
	
  })
  
  
  // hover for drag & drop
	
	var drop = document.getElementById("filediv");
	var bgover = document.getElementById("uploaddiv");
	var ficon = document.getElementById("flowicon");
	drop.addEventListener("dragenter", change, false);
	drop.addEventListener("dragleave",change_back,false);

	function change() {
		bgover.style.backgroundColor = 'rgba(53,130,196,.6)';
		ficon.style.transform = 'translateY(-10px)';
	};

	function change_back() {
		bgover.style.backgroundColor = ' rgba(0,0,0,.6)';
		ficon.style.transform = 'translateY(0px)';
	};