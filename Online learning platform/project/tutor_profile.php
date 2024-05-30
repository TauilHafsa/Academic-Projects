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
}

// Vérifier si le formulaire pour récupérer les détails du tuteur a été soumis
if(isset($_POST['tutor_fetch'])){

   // Récupérer l'e-mail du tuteur depuis le formulaire
   $tutor_email = $_POST['tutor_email'];
   $tutor_email = filter_var($tutor_email, FILTER_SANITIZE_STRING);

   // Requête pour récupérer les détails du tuteur à partir de son e-mail
   $select_tutor = $conn->prepare('SELECT * FROM `tutors` WHERE email = ?');
   $select_tutor->execute([$tutor_email]);

   // Récupérer les données du tuteur
   $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
   $tutor_id = $fetch_tutor['id'];

   // Compter le nombre de playlists associées à ce tuteur
   $count_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
   $count_playlists->execute([$tutor_id]);
   $total_playlists = $count_playlists->rowCount();

   // Compter le nombre de cours associés à ce tuteur
   $count_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
   $count_contents->execute([$tutor_id]);
   $total_contents = $count_contents->rowCount();

   // Compter le nombre de likes associés à ce tuteur
   $count_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
   $count_likes->execute([$tutor_id]);
   $total_likes = $count_likes->rowCount();

   // Compter le nombre de commentaires associés à ce tuteur
   $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
   $count_comments->execute([$tutor_id]);
   $total_comments = $count_comments->rowCount();

}else{
   // Rediriger vers la page des enseignants si le formulaire n'a pas été soumis
   header('location:teachers.php');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>tutor's profile</title>

   <!-- Lien CDN pour Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien du fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- Section des profils des enseignants commence ici -->

<section class="tutor-profile">

   <h1 class="heading">profile details</h1>

   <div class="details">
      <div class="tutor">
         <!-- Affichage de l'image, du nom et de la profession du tuteur -->
         <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
         <h3><?= $fetch_tutor['name']; ?></h3>
         <span><?= $fetch_tutor['profession']; ?></span>
      </div>
      <div class="flex">
         <!-- Affichage des statistiques du tuteur -->
         <p>total playlists : <span><?= $total_playlists; ?></span></p>
         <p>total pdfs : <span><?= $total_contents; ?></span></p>
         <p>total likes : <span><?= $total_likes; ?></span></p>
         <p>total comments : <span><?= $total_comments; ?></span></p>
      </div>
   </div>

</section>

<!-- Section des cours commence ici -->

<section class="courses">

   <h1 class="heading">latest courese</h1>

   <div class="box-container">

      <?php
         // Récupérer les cours associés à ce tuteur
         $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ? AND status = ?");
         $select_courses->execute([$tutor_id, 'active']);
         if($select_courses->rowCount() > 0){
            // Afficher les cours s'ils existent
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id'];

               // Récupérer les détails du tuteur pour chaque cours
               $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
               $select_tutor->execute([$fetch_course['tutor_id']]);
               $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="tutor">
            <!-- Affichage de l'image et du nom du tuteur pour chaque cours -->
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_course['date']; ?></span>
            </div>
         </div>
         <!-- Affichage des détails du cours et du lien pour voir la playlist -->
         <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
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

</section>

<!-- Fin de la section des cours -->

<?php include 'components/footer.php'; ?>    

<!-- Lien vers le fichier JavaScript personnalisé -->
<script src="js/script.js"></script>
   
</body>
</html>
