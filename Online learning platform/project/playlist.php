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

// Vérifier si l'ID de la playlist est défini dans l'URL
if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   // Rediriger vers la page d'accueil si l'ID de la playlist n'est pas défini
   header('location:home.php');
}

// Vérifier si le formulaire de sauvegarde de la playlist a été soumis
if(isset($_POST['save_list'])){

   // Vérifier si l'utilisateur est connecté
   if($user_id != ''){
      
      // Récupérer et filtrer l'ID de la playlist
      $list_id = $_POST['list_id'];
      $list_id = filter_var($list_id, FILTER_SANITIZE_STRING);

      // Vérifier si la playlist est déjà enregistrée dans les favoris de l'utilisateur
      $select_list = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
      $select_list->execute([$user_id, $list_id]);

      // Si la playlist est déjà enregistrée, la supprimer des favoris de l'utilisateur
      if($select_list->rowCount() > 0){
         $remove_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
         $remove_bookmark->execute([$user_id, $list_id]);
         $message[] = 'playlist removed!';
      }else{
         // Sinon, enregistrer la playlist dans les favoris de l'utilisateur
         $insert_bookmark = $conn->prepare("INSERT INTO `bookmark`(user_id, playlist_id) VALUES(?,?)");
         $insert_bookmark->execute([$user_id, $list_id]);
         $message[] = 'playlist saved!';
      }

   }else{
      // Si l'utilisateur n'est pas connecté, afficher un message d'erreur
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
   <title>playlist</title>

   <!-- Lien CDN pour Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien du fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- Section de la playlist -->

<section class="playlist">

   <h1 class="heading">playlist details</h1>

   <div class="row">

      <?php
         // Sélectionner les détails de la playlist à partir de l'ID
         $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? and status = ? LIMIT 1");
         $select_playlist->execute([$get_id, 'active']);
         if($select_playlist->rowCount() > 0){
            $fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC);

            $playlist_id = $fetch_playlist['id'];

            // Compter le nombre de PDF dans la playlist
            $count_pdfs = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
            $count_pdfs->execute([$playlist_id]);
            $total_pdfs = $count_pdfs->rowCount();

            // Sélectionner les détails du tuteur de la playlist
            $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
            $select_tutor->execute([$fetch_playlist['tutor_id']]);
            $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

            // Vérifier si la playlist est déjà enregistrée dans les favoris de l'utilisateur
            $select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
            $select_bookmark->execute([$user_id, $playlist_id]);

      ?>

      <div class="col">
         <form action="" method="post" class="save-list">
            <input type="hidden" name="list_id" value="<?= $playlist_id; ?>">
            <?php
               // Afficher le bouton de sauvegarde ou de suppression de la playlist en fonction de son statut dans les favoris de l'utilisateur
               if($select_bookmark->rowCount() > 0){
            ?>
            <button type="submit" name="save_list"><i class="fas fa-bookmark"></i><span>saved</span></button>
            <?php
               }else{
            ?>
               <button type="submit" name="save_list"><i class="far fa-bookmark"></i><span>save playlist</span></button>
            <?php
               }
            ?>
         </form>
         <div class="thumb">
            <span><?= $total_pdfs; ?> courses</span>
            <img src="uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
         </div>
      </div>

      <div class="col">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
            </div>
         </div>
         <div class="details">
            <h3><?= $fetch_playlist['title']; ?></h3>
            <p><?= $fetch_playlist['description']; ?></p>
            <div class="date"><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         </div>
      </div>

      <?php
         }else{
            // Afficher un message si la playlist n'est pas trouvée
            echo '<p class="empty">this playlist was not found!</p>';
         }  
      ?>

   </div>

</section>

<!-- Fin de la section de la playlist -->

<!-- Section du conteneur des PDF -->

<section class="pdfs-container">

   <h1 class="heading">playlist </h1>

   <div class="box-container">

      <?php
         // Sélectionner les PDF associés à la playlist
         $select_content = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ? AND status = ? ORDER BY date DESC");
         $select_content->execute([$get_id, 'active']);
         if($select_content->rowCount() > 0){
            while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){  
      ?>
      <a href="voirpdf.php?get_id=<?= $fetch_content['id']; ?>" class="box">
         <i class="fas fa-file-pdf"></i>
         <img src="uploaded_files/<?= $fetch_content['thumb']; ?>" alt="">
         <h3><?= $fetch_content['title']; ?></h3>
      </a>
      <?php
            }
         }else{
            // Afficher un message si aucun PDF n'est trouvé dans la playlist
            echo '<p class="empty">no documents added yet!</p>';
         }
      ?>

   </div>

</section>
<!-- Fin de la section du conteneur des PDF -->

</body>
</html>
