<?php

// Inclure le fichier de connexion à la base de données
include 'components/connect.php';

// Vérifier si l'ID utilisateur est défini dans les cookies
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   // Rediriger vers la page d'accueil si l'ID utilisateur n'est pas défini
   $user_id = '';
   header('location:home.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>bookmarks</title>

   <!-- lien du CDN pour Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- lien du fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="courses">

   <h1 class="heading">bookmarked playlists</h1>

   <div class="box-container">

      <?php
         // Sélectionner les playlists mises en signet par l'utilisateur
         $select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
         $select_bookmark->execute([$user_id]);
         // Vérifier s'il y a des playlists mises en signet
         if($select_bookmark->rowCount() > 0){
            while($fetch_bookmark = $select_bookmark->fetch(PDO::FETCH_ASSOC)){
               // Sélectionner les détails de la playlist mise en signet
               $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND status = ? ORDER BY date DESC");
               $select_courses->execute([$fetch_bookmark['playlist_id'], 'active']);
               // Vérifier s'il y a des détails de playlist valides
               if($select_courses->rowCount() > 0){
                  while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
                     // Récupérer l'ID de la playlist
                     $course_id = $fetch_course['id'];

                     // Sélectionner les détails du tuteur associé à la playlist
                     $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
                     $select_tutor->execute([$fetch_course['tutor_id']]);
                     $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_course['date']; ?></span>
            </div>
         </div>
         <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">view playlist</a>
      </div>
      <?php
                  }
               }else{
                  // Afficher un message si aucune playlist n'est trouvée
                  echo '<p class="empty">no courses found!</p>';
               }
            }
         }else{
            // Afficher un message si aucune playlist n'est mise en signet
            echo '<p class="empty">nothing bookmarked yet!</p>';
         }
      ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<!-- lien du fichier JavaScript personnalisé -->
<script src="js/script.js"></script>
   
</body>
</html>
