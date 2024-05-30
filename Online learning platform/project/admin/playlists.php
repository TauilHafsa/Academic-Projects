<?php
// Inclusion du fichier de connexion à la base de données
include '../components/connect.php';

// Vérification de l'existence de l'identifiant du tuteur dans les cookies
if(isset($_COOKIE['tutor_id'])){
   // Si l'identifiant du tuteur est défini dans les cookies, le récupérer
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   // Si l'identifiant du tuteur n'est pas défini dans les cookies, initialiser à une chaîne vide et rediriger vers la page de connexion
   $tutor_id = '';
   header('location:login.php');
}

// Traitement de la suppression de playlist
if(isset($_POST['delete'])){
   // Récupération et filtrage de l'identifiant de la playlist à supprimer
   $delete_id = $_POST['playlist_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Vérification de l'existence de la playlist dans la base de données pour ce tuteur
   $verify_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND tutor_id = ? LIMIT 1");
   $verify_playlist->execute([$delete_id, $tutor_id]);

   // Si la playlist existe pour ce tuteur, la supprimer
   if($verify_playlist->rowCount() > 0){

      // Sélection de la playlist pour récupérer le nom du fichier image associé
      $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
      $delete_playlist_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
      // Suppression du fichier image associé à la playlist
      unlink('../uploaded_files/'.$fetch_thumb['thumb']);
      
      // Suppression des marque-pages associés à la playlist
      $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
      $delete_bookmark->execute([$delete_id]);

      // Suppression de la playlist de la base de données
      $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
      $delete_playlist->execute([$delete_id]);

      // Message indiquant que la playlist a été supprimée avec succès
      $message[] = 'playlist deleted!';
   }else{
      // Message indiquant que la playlist est déjà supprimée
      $message[] = 'playlist already deleted!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Playlists</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="playlists">

   <h1 class="heading">added playlists</h1>

   <div class="box-container">
   
      <div class="box" style="text-align: center;">
         <h3 class="title" style="margin-bottom: .5rem;">create new playlist</h3>
         <a href="add_playlist.php" class="btn">add playlist</a>
      </div>

      <?php
         // Sélection de toutes les playlists associées à ce tuteur, triées par date de manière décroissante
         $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ? ORDER BY date DESC");
         $select_playlist->execute([$tutor_id]);
         // Vérification s'il existe des playlists
         if($select_playlist->rowCount() > 0){
            // Parcours des playlists
            while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
               // Récupération de l'identifiant de la playlist
               $playlist_id = $fetch_playlist['id'];
               // Comptage du nombre de PDF associés à cette playlist
               $count_pdf = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
               $count_pdf->execute([$playlist_id]);
               $total_pdf = $count_pdf->rowCount();
      ?>
      <div class="box">
         <div class="flex">
            <!-- Affichage de l'état de la playlist (actif/inactif) -->
            <div><i class="fas fa-circle-dot" style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fetch_playlist['status']; ?></span></div>
            <!-- Affichage de la date de création de la playlist -->
            <div><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         </div>
         <!-- Affichage de l'image miniature de la playlist et du nombre total de PDF -->
         <div class="thumb">
            <span><?= $total_pdf; ?></span>
            <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
         </div>
         <!-- Affichage du titre et de la description de la playlist -->
         <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
         <p class="description"><?= $fetch_playlist['description']; ?></p>
         <!-- Formulaire pour supprimer la playlist -->
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
            <!-- Bouton pour mettre à jour la playlist -->
            <a href="update_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">update</a>
            <!-- Bouton pour supprimer la playlist -->
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this playlist?');" name="delete">
         </form>
         <!-- Bouton pour voir la playlist -->
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="btn">view playlist</a>
      </div>
      <?php
         } 
      }else{
         // Message affiché s'il n'y a aucune playlist ajoutée
         echo '<p class="empty">no playlist added yet!</p>';
      }
      ?>

   </div>

</section>

<?php include '../components/footer.php'; ?>

<!-- Script JavaScript pour raccourcir les descriptions des playlists -->
<script>
   document.querySelectorAll('.playlists .box-container .box .description').forEach(content => {
      if(content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
   });
</script>

</body>
</html>
