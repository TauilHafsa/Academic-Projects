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

<section class="teachers">

   <h1 class="heading">expert tutors</h1>

   <!-- Formulaire de recherche de tuteurs -->
   <form action="" method="post" class="search-tutor">
      <input type="text" name="search_tutor" maxlength="100" placeholder="search tutor..." required>
      <button type="submit" name="search_tutor_btn" class="fas fa-search"></button>
   </form>

   <div class="box-container">

      <?php
         // Vérifier si une recherche de tuteur a été effectuée
         if(isset($_POST['search_tutor']) or isset($_POST['search_tutor_btn'])){
            $search_tutor = $_POST['search_tutor'];
            // Requête pour récupérer les tuteurs correspondant à la recherche
            $select_tutors = $conn->prepare("SELECT * FROM `tutors` WHERE name LIKE '%{$search_tutor}%'");
            $select_tutors->execute();
            if($select_tutors->rowCount() > 0){
               // Afficher les résultats de la recherche
               while($fetch_tutor = $select_tutors->fetch(PDO::FETCH_ASSOC)){

                  $tutor_id = $fetch_tutor['id'];

                  // Compter le nombre de playlists, de cours, de likes et de commentaires associés à ce tuteur
                  $count_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
                  $count_playlists->execute([$tutor_id]);
                  $total_playlists = $count_playlists->rowCount();

                  $count_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
                  $count_contents->execute([$tutor_id]);
                  $total_contents = $count_contents->rowCount();

                  $count_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
                  $count_likes->execute([$tutor_id]);
                  $total_likes = $count_likes->rowCount();

                  $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
                  $count_comments->execute([$tutor_id]);
                  $total_comments = $count_comments->rowCount();
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_tutor['profession']; ?></span>
            </div>
         </div>
         <!-- Affichage des statistiques du tuteur -->
         <p>playlists : <span><?= $total_playlists; ?></span></p>
         <p>total courses : <span><?= $total_contents ?></span></p>
         <p>total likes : <span><?= $total_likes ?></span></p>
         <p>total comments : <span><?= $total_comments ?></span></p>
         <!-- Formulaire pour afficher le profil du tuteur -->
         <form action="tutor_profile.php" method="post">
            <input type="hidden" name="tutor_email" value="<?= $fetch_tutor['email']; ?>">
            <input type="submit" value="view profile" name="tutor_fetch" class="inline-btn">
         </form>
      </div>
      <?php
               }
            }else{
               // Afficher un message si aucun résultat n'a été trouvé
               echo '<p class="empty">no results found!</p>';
            }
         }else{
            // Afficher un message si aucune recherche n'a été effectuée
            echo '<p class="empty">please search something!</p>';
         }
      ?>

   </div>

</section>

<!-- Fin de la section des enseignants -->

<?php include 'components/footer.php'; ?>

<!-- Lien vers le fichier JavaScript personnalisé -->
<script src="js/script.js"></script>
   
</body>
</html>
