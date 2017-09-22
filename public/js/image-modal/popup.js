// Get the modal
var modal = document.getElementById('myModal');

// Get the image and insert it inside the modal - use its "alt" text as a caption
var modalImg = document.getElementById("image-popup");

function popupImage(param, errors){

    // alert(errors);
	
	if(errors == 0){

		// alert(errors);

    	modalImg.src = param.trim();

    	if(modalImg.width <= 550){
    		modalImg.setAttribute("style","max-width: 550px; padding-top: 20px;");
    	}else{
    		modalImg.removeAttribute("style");
    	}

    	modal.style.display = "block";
	}	

}

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];