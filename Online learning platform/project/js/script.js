// Sélection de l'élément body
let body = document.body;

// Sélection de l'élément du profil utilisateur dans le header
let profile = document.querySelector('.header .flex .profile');

// Gestion de l'affichage du profil utilisateur lors du clic sur le bouton utilisateur
document.querySelector('#user-btn').onclick = () =>{
   profile.classList.toggle('active');
   searchForm.classList.remove('active');
}

// Sélection de l'élément du formulaire de recherche dans le header
let searchForm = document.querySelector('.header .flex .search-form');

// Gestion de l'affichage du formulaire de recherche lors du clic sur le bouton de recherche
document.querySelector('#search-btn').onclick = () =>{
   searchForm.classList.toggle('active');
   profile.classList.remove('active');
}

// Sélection de la barre latérale
let sideBar = document.querySelector('.side-bar');

// Gestion de l'affichage de la barre latérale lors du clic sur le bouton de menu
document.querySelector('#menu-btn').onclick = () =>{
   sideBar.classList.toggle('active');
   body.classList.toggle('active');
}

// Gestion de la fermeture de la barre latérale lors du clic sur le bouton de fermeture
document.querySelector('.side-bar .close-side-bar').onclick = () =>{
   sideBar.classList.remove('active');
   body.classList.remove('active');
}

// Limiter la longueur des champs de type nombre à la longueur maximale spécifiée
document.querySelectorAll('input[type="number"]').forEach(InputNumber => {
   InputNumber.oninput = () =>{
      if(InputNumber.value.length > InputNumber.maxLength) InputNumber.value = InputNumber.value.slice(0, InputNumber.maxLength);
   }
});

// Gestion du comportement lors du défilement de la page
window.onscroll = () =>{
   profile.classList.remove('active');
   searchForm.classList.remove('active');

   // Si la largeur de la fenêtre est inférieure à 1200 pixels, désactiver également la barre latérale
   if(window.innerWidth < 1200){
      sideBar.classList.remove('active');
      body.classList.remove('active');
   }
}

// Sélection du bouton de bascule pour le mode sombre
let toggleBtn = document.querySelector('#toggle-btn');
// Récupération de l'état actuel du mode sombre depuis le stockage local
let darkMode = localStorage.getItem('dark-mode');

// Fonction pour activer le mode sombre
const enableDarkMode = () =>{
   toggleBtn.classList.replace('fa-sun', 'fa-moon');
   body.classList.add('dark');
   localStorage.setItem('dark-mode', 'enabled');
}

// Fonction pour désactiver le mode sombre
const disableDarkMode = () =>{
   toggleBtn.classList.replace('fa-moon', 'fa-sun');
   body.classList.remove('dark');
   localStorage.setItem('dark-mode', 'disabled');
}

// Si le mode sombre est activé, appliquer le mode sombre au chargement de la page
if(darkMode === 'enabled'){
   enableDarkMode();
}

// Gestion du clic sur le bouton de bascule pour activer ou désactiver le mode sombre
toggleBtn.onclick = (e) =>{
   let darkMode = localStorage.getItem('dark-mode');
   if(darkMode === 'disabled'){
      enableDarkMode();
   }else{
      disableDarkMode();
   }
}
