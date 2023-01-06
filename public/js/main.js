console.log("test de liaison js ");
/*****************************************************************************
 * fonction preview pour images lors du chargement d'une image (upload) 
 * déclanché par "onChange" dans l'input de type file (creation / modif de produit)
 */
  function PreviewImage(event){
  if(event.target.files.length > 0){
    var src = URL.createObjectURL(event.target.files[0]);
    var preview = document.getElementById("uploadPreview"); // vise zone d'affichage 
    preview.src = src;
    preview.style.display = "block"; // demasque l'affichage 
  }
}

/****************************************************************************
 * pour de ou re-masquer l'image en grand (toggle de classe) (au click)
 */
function toggleClass(id){ 
  document.getElementById(id).classList.toggle("visible")
}


/*****************************************************************************
 * Set une selection par defaut correcte form de modif produit
 * @param  def  : id de la catégorie 
 */

// function selecCatDefault(def){

//   console.log(def);
//         // script de set de valeur par defaut correcte 
//   var temp = def;
//   var mySelect = document.getElementById('category');

//   for(var i, j = 0; i = mySelect.options[j]; j++) {
//       if(i.value == temp) {
//           mySelect.selectedIndex = j;
//           break;
//       }
//   }
   
// }

// script de set de valeur par defaut correcte form de modif produit

// if(document.getElementById('category')!== null){
//     var temp = "Les Miels";
//   var mySelect = document.getElementById('category');
//   for(var i, j = 0; i = mySelect.options[j]; j++) {
//       if(i.value == temp) {
//           mySelect.selectedIndex = j;
//           break;
//       }
//   }

// }



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
    console.log (id, value);

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



}







