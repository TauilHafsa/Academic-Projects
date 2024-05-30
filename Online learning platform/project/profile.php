<?php

// Inclure le fichier de connexion à la base de données
include 'components/connect.php';

// Initialisation de la variable user_id
$user_id = '';

// Vérifier si l'ID utilisateur est défini dans les cookies
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   // Rediriger vers la page de connexion si l'ID utilisateur n'est pas défini dans les cookies
   header('location:login.php');
}

// Préparation et exécution des requêtes pour récupérer le nombre total de likes, de commentaires et de playlists enregistrées par l'utilisateur
$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
$select_likes->execute([$user_id]);
$total_likes = $select_likes->rowCount();

$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$select_comments->execute([$user_id]);
$total_comments = $select_comments->rowCount();

$select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
$select_bookmark->execute([$user_id]);
$total_bookmarked = $select_bookmark->rowCount();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>profile</title>

   <!-- Lien CDN pour Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien du fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="profile">

   <h1 class="heading">profile details</h1>

   <div class="details">

      <div class="user">
         <!-- Affichage de l'image de profil, du nom et du rôle de l'utilisateur -->
         <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= $fetch_profile['name']; ?></h3>
         <p>student</p>
         <!-- Lien pour mettre à jour le profil -->
         <a href="update.php" class="inline-btn">update profile</a>
      </div>

      <div class="box-container">

         <!-- Boîte affichant le nombre de playlists enregistrées -->
         <div class="box">
            <div class="flex">
               <i class="fas fa-bookmark"></i>
               <div>
                  <h3><?= $total_bookmarked; ?></h3>
                  <span>saved playlists</span>
               </div>
            </div>
            <a href="#" class="inline-btn">view playlists</a>
         </div>

         <!-- Boîte affichant le nombre de tutoriels aimés -->
         <div class="box">
            <div class="flex">
               <i class="fas fa-heart"></i>
               <div>
                  <h3><?= $total_likes; ?></h3>
                  <span>liked tutorials</span>
               </div>
            </div>
            <a href="#" class="inline-btn">view liked</a>
         </div>

         <!-- Boîte affichant le nombre de commentaires sur les cours -->
         <div class="box">
            <div class="flex">
               <i class="fas fa-comment"></i>
               <div>
                  <h3><?= $total_comments; ?></h3>
                  <span>course comments</span>
               </div>
            </div>
            <a href="#" class="inline-btn">view comments</a>
         </div>

      </div>

   </div>

</section>

<!-- Fin de la section du profil -->

<!-- Section du pied de page -->

<footer class="footer">

   &copy; copyright @ 2022 by <span>mr. web designer</span> | all rights reserved!

</footer>

<!-- Fin de la section du pied de page -->

<!-- Lien vers le fichier JavaScript personnalisé -->
<script src="js/script.js"></script>
   
</body>
</html>
