<?php

// Inclure le fichier de connexion à la base de données
include 'components/connect.php';

// Initialisation de la variable user_id
$user_id = '';

// Vérifier si l'ID utilisateur est défini dans les cookies
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   // Rediriger vers la page d'accueil si l'utilisateur n'est pas connecté
   $user_id = '';
   header('location:home.php');
}

// Vérifier si le formulaire de suppression a été soumis
if(isset($_POST['remove'])){

   // Vérifier si l'utilisateur est connecté
   if($user_id != ''){
      // Récupérer l'ID du contenu à supprimer depuis les données postées
      $content_id = $_POST['content_id'];
      $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

      // Vérifier si l'utilisateur a aimé ce contenu
      $verify_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
      $verify_likes->execute([$user_id, $content_id]);

      // Si l'utilisateur a aimé ce contenu, le supprimer de ses likes
      if($verify_likes->rowCount() > 0){
         $remove_likes = $conn->prepare("DELETE FROM `likes` WHERE user_id = ? AND content_id = ?");
         $remove_likes->execute([$user_id, $content_id]);
         $message[] = 'removed from likes!';
      }
   }else{
      $message[] = 'please login first!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>liked pdf</title>

   <!-- Lien CDN pour Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien du fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- Section des cours aimés -->

<section class="liked-pdf">

   <h1 class="heading">liked pdf</h1>

   <div class="box-container">

   <?php
      // Sélectionner tous les contenus aimés par l'utilisateur
      $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
      $select_likes->execute([$user_id]);
      if($select_likes->rowCount() > 0){
         while($fetch_likes = $select_likes->fetch(PDO::FETCH_ASSOC)){

            // Sélectionner les détails du contenu aimé
            $select_contents = $conn->prepare("SELECT * FROM `content` WHERE id = ? ORDER BY date DESC");
            $select_contents->execute([$fetch_likes['content_id']]);

            if($select_contents->rowCount() > 0){
               while($fetch_contents = $select_contents->fetch(PDO::FETCH_ASSOC)){

               // Sélectionner les détails du tuteur associé à ce contenu
               $select_tutors = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
               $select_tutors->execute([$fetch_contents['tutor_id']]);
               $fetch_tutor = $select_tutors->fetch(PDO::FETCH_ASSOC);
   ?>
   <div class="box">
      <div class="tutor">
         <!-- Afficher l'image du tuteur -->
         <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
         <div>
            <!-- Afficher le nom du tuteur et la date du contenu -->
            <h3><?= $fetch_tutor['name']; ?></h3>
            <span><?= $fetch_contents['date']; ?></span>
         </div>
      </div>
      <!-- Afficher l'image miniature du contenu -->
      <img src="uploaded_files/<?= $fetch_contents['thumb']; ?>" alt="" class="thumb">
      <!-- Afficher le titre du contenu -->
      <h3 class="title"><?= $fetch_contents['title']; ?></h3>
      <!-- Formulaire pour supprimer le contenu des likes -->
      <form action="" method="post" class="flex-btn">
         <input type="hidden" name="content_id" value="<?= $fetch_contents['id']; ?>">
         <a href="voirpdf.php?get_id=<?= $fetch_contents['id']; ?>" class="inline-btn">voir le cours</a>
         <input type="submit" value="remove" class="inline-delete-btn" name="remove">
      </form>
   </div>
   <?php
            }
         }else{
            echo '<p class="emtpy">content was not found!</p>';         
         }
      }
   }else{
      // Afficher un message si aucun contenu n'a été ajouté aux likes
      echo '<p class="empty">nothing added to likes yet!</p>';
   }
   ?>

   </div>

</section>

<!-- Fin de la section des cours aimés -->

<!-- Pied de page -->
<?php include 'components/footer.php'; ?>
<!-- Fin du pied de page -->

<!-- Lien du fichier JavaScript personnalisé -->
<script src="js/script.js"></script>
   
</body>
</html>
