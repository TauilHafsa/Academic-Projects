<?php

// Inclure le fichier de connexion à la base de données
include 'components/connect.php';

// Vérifier si l'ID utilisateur est défini dans les cookies
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>courses</title>

   <!-- Lien CDN pour Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien du fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- Section des cours -->

<section class="courses">

   <h1 class="heading">all courses</h1>

   <div class="box-container">

      <?php
         // Sélectionner tous les cours actifs depuis la base de données
         $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE status = ? ORDER BY date DESC");
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
         <!--
          <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt=""> 
          -->
          
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

</section>

<!-- Fin de la section des cours -->

<?php include 'components/footer.php'; ?>

<!-- Lien du fichier JavaScript personnalisé -->
<script src="js/script.js"></script>
   
</body>
</html>
