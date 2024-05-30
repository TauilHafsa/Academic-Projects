<?php
// Vérifie s'il y a des messages à afficher
if(isset($message)){
   // Parcours chaque message et l'affiche dans une div avec une icône de fermeture
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">

      <!-- Logo Educa -->
      <a href="home.php" class="logo">Educa.</a>

      <!-- Formulaire de recherche de cours -->
      <form action="search_course.php" method="post" class="search-form">
         <input type="text" name="search_course" placeholder="search courses..." required maxlength="100">
         <button type="submit" class="fas fa-search" name="search_course_btn"></button>
      </form>

      <!-- Icônes de navigation -->
      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <!-- Profil utilisateur -->
      <div class="profile">
         <?php
            // Vérifie si l'utilisateur est connecté
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
               // Affiche le profil de l'utilisateur connecté
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= $fetch_profile['name']; ?></h3>
         <span>student</span>
         <a href="profile.php" class="btn">view profile</a>
         <div class="flex-btn">
            <!-- Boutons de connexion et d'inscription -->
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div>
         <!-- Bouton de déconnexion -->
         <a href="components/user_logout.php" onclick="return confirm('logout from this website?');" class="delete-btn">logout</a>
         <?php
            }else{
               // Affiche un message invitant l'utilisateur à se connecter ou à s'inscrire
         ?>
         <h3>please login or register</h3>
         <div class="flex-btn">
            <!-- Boutons de connexion et d'inscription -->
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div>
         <?php
            }
         ?>
      </div>

   </section>

</header>

<!-- Section de la barre latérale -->

<div class="side-bar">

   <div class="close-side-bar">
      <!-- Icône de fermeture -->
      <i class="fas fa-times"></i>
   </div>

   <!-- Profil utilisateur dans la barre latérale -->
   <div class="profile">
      <?php
         // Vérifie si l'utilisateur est connecté
         $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_profile->execute([$user_id]);
         if($select_profile->rowCount() > 0){
            // Affiche le profil de l'utilisateur connecté
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
      ?>
      <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
      <h3><?= $fetch_profile['name']; ?></h3>
      <span>student</span>
      <a href="profile.php" class="btn">view profile</a>
      <?php
         }else{
            // Affiche un message invitant l'utilisateur à se connecter ou à s'inscrire
      ?>
      <h3>please login or register</h3>
      <div class="flex-btn" style="padding-top: .5rem;">
         <!-- Boutons de connexion et d'inscription -->
         <a href="login.php" class="option-btn">login</a>
         <a href="register.php" class="option-btn">register</a>
      </div>
      <?php
         }
      ?>
   </div>

   <!-- Menu de navigation -->
   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>home</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
      <!-- Lien vers la page de contact -->
      <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
   </nav>

</div>
