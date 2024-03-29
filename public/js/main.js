//console.log("test de liaison js ");

/*********************************************************************
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

/*********************************************************************
 * pour de-masquer une grande image (toggle de classe) / modal
 * ---- pour ne pas utiliser le onclick dans le html ---------
 */
if(document.querySelectorAll(".modal-trigger")!== null){

  const btns = document.querySelectorAll(".modal-trigger");

  btns.forEach(function(btn) {
    // ecoute d'évenement click sur les vignettes
    btn.addEventListener('click',()=>{
      // on récupère la valeur de la src de la vignette 
      const info  = btn.src;
      // on reconstitue la valeur de l'id du modal en recupérant le texte
      // du chemin src depuis le mot "public"
      var result = info.indexOf('public/uploads/')
      var place = result+15 // longeur de la chaine "public/uploads/"
      var idModal = info.slice(place); 
      document.getElementById(idModal).classList.toggle("visible")
    });
  });
}

/*********************************************************************
 * pour re-masquer une grande image (toggle de classe) / modal
 */
if(document.querySelectorAll(".modal-window")!== null){
  const modals = document.querySelectorAll('.modal-window')
  // ecoute du click sur les modals
  modals.forEach(function(modal){    
     modal.addEventListener('click',()=>{
      // recupération de l'id 
      const idModal = modal.id;
      //toggle de la classe visible 
      document.getElementById(idModal).classList.toggle("visible")
    });
  }
  );
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


/***********************************************************************
 * ecoute evenement sur packaging / prix de la page produit publique
 */

if (document.querySelectorAll(".selectItems") !== null){
  const myForms =document.querySelectorAll(".selectItems")

  myForms.forEach(function(form) {
    // recupère l'id et la valeur selectionnée (par défaut) pour chaque form 
    const id    = form.id
    const value = form.value
    // écoute d'évenement change sur les formulaires 
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
        .then(res => { document.getElementById(idAffichage).innerHTML = res;})
    })
  })
}


/********************************************************************
 * homePage -> sur chargement construction du slider 
 * et display des 4 dernières news : 
 */

window.addEventListener("DOMContentLoaded", () =>{
  // recupération de la page courante pour savoir si l'on est sur homePage
  let curentUrl=document.location.href;
  // on recupère la position du caractère ? / 
  pos=curentUrl.lastIndexOf ("?"); // retourne -1 si pas de ? (page index de départ)
  let endOfUrl = curentUrl.substring(pos+1);

  // Si on est sur la page acceuil 
  if (endOfUrl=="view=homePage" | pos == -1){

    // on va chercher les photos à passer au slider 
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


    // on va chercher les 6 dernières news actives 
    let requestNews = new Request ('index.php?route=news&action=ajaxNews',{
      method : 'POST'
    })
    // traitement de la promesse ->  affichage dans div cible
    fetch(requestNews)
    .then(res => res.text())
    .then(res => {
      document.getElementById("displayNews").innerHTML = res; 
    })
    // 

  }
})













