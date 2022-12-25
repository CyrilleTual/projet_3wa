console.log("test de liaison js ");
/**
 * fonction preview pour images
 */

  function PreviewImage(event){

  if(event.target.files.length > 0){
   
    var src = URL.createObjectURL(event.target.files[0]);
    var preview = document.getElementById("uploadPreview");
    preview.src = src;
    preview.style.display = "block";
  }
}

function toggleClass(id){
   console.log(id);
  document.getElementById(id).classList.toggle("visible")
}

