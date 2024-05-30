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

<!-- Début de la section des cours -->
<section class="courses">

   <h1 class="heading">search results</h1>

   <div class="box-container">

      <?php
         // Vérifier si une recherche a été effectuée
         if(isset($_POST['search_course']) or isset($_POST['search_course_btn'])){
            // Récupérer la valeur de recherche
            $search_course = $_POST['search_course'];
            // Requête pour récupérer les cours correspondants à la recherche
            $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE title LIKE '%{$search_course}%' AND status = ?");
            $select_courses->execute(['active']);
            if($select_courses->rowCount() > 0){
               // Afficher les résultats de la recherche
               while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
                  $course_id = $fetch_course['id'];

                  // Récupérer les détails du tuteur
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
            // Afficher un message si aucun cours n'a été trouvé
            echo '<p class="empty">no courses found!</p>';
         }
      }else{
         // Afficher un message si aucune recherche n'a été effectuée
         echo '<p class="empty">please search something!</p>';
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
