//console.log("test de liaison js ");

/*******************************************************
 *fonction preview pour images lors du chargement d'une image (upload) 
 * ---- écoute d'évenement pour remplacer le onchange ---- 
 * pages->  productAdd, ProductModify, carouselAddOrModify
 */

if(document.querySelector("#uploadImage")!== null){
  const input = document.querySelector("#uploadImage");
  input.addEventListener("change", PreviewImage);
  function PreviewImage(event){
      if(event.target.files.length > 0){
        var src = URL.createObjectURL(event.target.files[0]);
        // on cible et remplie la zone de préview.
        var contener = document.querySelector("form .preview");
        var preview = document.getElementById("uploadPreview"); 
        contener.style.display = "block"; // demasque l'affichage 
        preview.src = src;
       
      }
  }
}



/****************************************************************************
 * pour de ou re-masquer l'image en grand (toggle de classe) (au click)
 */
function toggleClass(id){ 
  document.getElementById(id).classList.toggle("visible")
}




/***********************************************************************
 * script de toogle class pour le bouton burger
 */

if(document.querySelector(".burger")!== null){
  const btn = document.querySelector(".burger");
btn.addEventListener("click", deploy);
function deploy() {
    btn.classList.toggle('active')
}
}



/****************************************************************************
 * ecoute evenement sur packaging / prix de la page produit publique
 */

if (document.querySelectorAll(".selectItems") !== null){
  const myForms =document.querySelectorAll(".selectItems")

  myForms.forEach(function(form) {

    // recupère l'id et la valeur selectionnée (par défaut) pour chaque form 
    const id    = form.id
    const value = form.value
   
    // ecoute d'évenement change sur les formulaires 
    form.addEventListener('change',()=>{
      const id    = form.id
      const value = form.value
      // on reconstitue l'id de l'affichage :
      const idAffichage = "idPrix"+id.substring(10)
      //console.log (id, value, idAffichage); 

      // on cherche le prix qui correspond à value (qui est l'id de l'item )

      let requestPrice = new Request ('index.php?route=items&action=ajaxPrice',{
        method : 'POST',
        body : JSON.stringify({ idToFind : value})
      })
      // traitement de la promesse > recup du texte et affichage dans div cible
      fetch(requestPrice)
      .then(res => res.text())
      .then(res => {
        document.getElementById(idAffichage).innerHTML = res; 
      })
    })
  })

  /********************************************************************
   * Ecoute d'évenement si la page est chargée et que l'on est 
   * sur la homePage
   */

  window.addEventListener("DOMContentLoaded", () =>{
    // recupération de la page courant pour savoir si l'on est sur homePage
    let curentUrl=document.location.href;
    // on recupère la position du caractère / 
    let endOfUrl = curentUrl.substring(curentUrl.lastIndexOf ("?")+1);
    if (endOfUrl=="view=homePage"){

     
      // sur la page acceuil on va chercher les photos à passer au slider 
      let requestPics = new Request ('index.php?route=carouselsPics&action=ajaxPics',{
        method : 'POST'
      })

       // traitement de la promesse ->  affichage dans div cible
      fetch(requestPics)
      .then(res => res.text())
      .then(res => {
        document.getElementById("displaySlider").innerHTML = res; 
      })
      // 
      .then(
        window.onload= function (){
            $(".my-flipster").flipster({
            style: 'carousel',
            spacing: -0.5,
            nav: false,
            buttons: true,
            })
        }
      )
    }
  })




}








