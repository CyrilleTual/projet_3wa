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


/**
 * pour demasquer l'image en grand
 */
function toggleClass(id){ 
  document.getElementById(id).classList.toggle("visible")
}

/**
 * Set une selection par defaut correcte
 * @param {*} def  : id de la catégorie Ò
 */

function selecCatDefault(def){

      console.log(def);
      // script de set de valeur par defaut correcte form de modif produit
var temp = def;
var mySelect = document.getElementById('category');

for(var i, j = 0; i = mySelect.options[j]; j++) {
    if(i.value == temp) {
        mySelect.selectedIndex = j;
        break;
    }
}
   
}

// script de set de valeur par defaut correcte form de modif produit
var temp = "Les Miels";
var mySelect = document.getElementById('category');

for(var i, j = 0; i = mySelect.options[j]; j++) {
    if(i.value == temp) {
        mySelect.selectedIndex = j;
        break;
    }
}
