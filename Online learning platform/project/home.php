<?php

// Inclure le fichier de connexion à la base de données
include 'components/connect.php';

// Initialisation de la variable user_id
$user_id = '';

// Vérifier si l'ID utilisateur est défini dans les cookies
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}

// Sélectionner le nombre total de likes de l'utilisateur
$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
$select_likes->execute([$user_id]);
$total_likes = $select_likes->rowCount();

// Sélectionner le nombre total de commentaires de l'utilisateur
$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$select_comments->execute([$user_id]);
$total_comments = $select_comments->rowCount();

// Sélectionner le nombre total de playlists enregistrées par l'utilisateur
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
   <title>home</title>

   <!-- Lien CDN pour Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien du fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- Section des options rapides -->

<section class="quick-select">

   <h1 class="heading">quick options</h1>

   <div class="box-container">

      <?php
         // Vérifier si l'utilisateur est connecté
         if($user_id != ''){
      ?>
      <div class="box">
         <h3 class="title">playlists and comments</h3>

         <!-- Afficher le nombre total de commentaires -->
         <p>total comments : <span><?= $total_comments; ?></span></p>
         <a href="comments.php" class="inline-btn">view comments</a>
         <!-- Afficher le nombre total de playlists enregistrées -->
         <p>saved playlist : <span><?= $total_bookmarked; ?></span></p>
         <a href="bookmark.php" class="inline-btn">view bookmark</a>
      </div>
      <?php
         }else{ 
      ?>
      <div class="box" style="text-align: center;">
         <!-- Message d'invitation à se connecter ou à s'inscrire -->
         <h3 class="title">please login or register</h3>
          <div class="flex-btn" style="padding-top: .5rem;">
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div>
      </div>
      <?php
      }
      ?>

</section>

<!-- Fin de la section des options rapides -->

<!-- Section des cours -->

<section class="courses">

   <h1 class="heading">latest courses</h1>

   <div class="box-container">

      <?php
         // Sélectionner les 6 derniers cours actifs
         $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE status = ? ORDER BY date DESC LIMIT 6");
         $select_courses->execute(['active']);
         if($select_courses->rowCount() > 0){
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id'];

               // Récupérer les informations du tuteur pour ce cours
               $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
               $select_tutor->execute([$fetch_course['tutor_id']]);
               $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="tutor">
            <!-- Afficher l'image du tuteur -->
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <!-- Afficher le nom du tuteur et la date du cours -->
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_course['date']; ?></span>
            </div>
         </div>
         <!-- Afficher l'image miniature du cours -->
         <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <!-- Afficher le titre du cours -->
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <!-- Lien pour voir la playlist -->
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">view playlist</a>
      </div>
      <?php
         }
      }else{
         // Afficher un message si aucun cours n'est trouvé
         echo '<p class="empty">no courses added yet!</p>';
      }
      ?>

   </div>

   <div class="more-btn">
      <a href="courses.php" class="inline-option-btn">view more</a>
   </div>

</section>

<!-- Fin de la section des cours -->

<!-- Pied de page -->
<?php include 'components/footer.php'; ?>
<!-- Fin du pied de page -->

<!-- Lien du fichier JavaScript personnalisé -->
<script src="js/script.js"></script>
   
</body>
</html>
