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

// Vérification de l'existence de l'identifiant de la playlist dans la requête GET
if(isset($_GET['get_id'])){
   // Si l'identifiant de la playlist est défini dans la requête GET, le récupérer
   $get_id = $_GET['get_id'];
}else{
   // Si l'identifiant de la playlist n'est pas défini dans la requête GET, initialiser à une chaîne vide et rediriger vers la page de visualisation des playlists
   $get_id = '';
   header('location:playlist.php');
}

// Traitement de la suppression d'une playlist
if(isset($_POST['delete_playlist'])){
   // Récupération de l'identifiant de la playlist à supprimer
   $delete_id = $_POST['playlist_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   
   // Sélection du chemin du thumbnail associé à la playlist à supprimer
   $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
   $delete_playlist_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
   // Suppression du thumbnail du serveur
   unlink('../uploaded_files/'.$fetch_thumb['thumb']);
   
   // Suppression des signets associés à la playlist
   $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
   $delete_bookmark->execute([$delete_id]);
   
   // Suppression de la playlist de la base de données
   $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
   $delete_playlist->execute([$delete_id]);
   
   // Redirection vers la page de visualisation des playlists
   header('location:playlists.php');
}

// Traitement de la suppression d'un PDF
if(isset($_POST['delete_pdf'])){
   // Récupération de l'identifiant du PDF à supprimer
   $delete_id = $_POST['pdf_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   
   // Vérification de l'existence du PDF dans la base de données
   $verify_pdf = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
   $verify_pdf->execute([$delete_id]);
   if($verify_pdf->rowCount() > 0){
      // Sélection du chemin du thumbnail associé au PDF à supprimer
      $delete_pdf_thumb = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_pdf_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_pdf_thumb->fetch(PDO::FETCH_ASSOC);
      // Suppression du thumbnail du serveur
      unlink('../uploaded_files/'.$fetch_thumb['thumb']);
      
      // Sélection du chemin du PDF à supprimer
      $delete_pdf = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_pdf->execute([$delete_id]);
      $fetch_pdf = $delete_pdf->fetch(PDO::FETCH_ASSOC);
      // Suppression du PDF du serveur
      unlink('../uploaded_files/'.$fetch_pdf['pdf']);
      
      // Suppression des likes associés au PDF
      $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE content_id = ?");
      $delete_likes->execute([$delete_id]);
      
      // Suppression des commentaires associés au PDF
      $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
      $delete_comments->execute([$delete_id]);
      
      // Suppression du PDF de la base de données
      $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
      $delete_content->execute([$delete_id]);
      
      // Message de confirmation de suppression
      $message[] = 'pdf deleted!';
   }else{
      // Si le PDF n'existe pas, afficher un message d'erreur
      $message[] = 'pdf already deleted!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Playlist Details</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="playlist-details">

   <h1 class="heading">playlist details</h1>

   <?php
      // Sélection de la playlist associée à l'identifiant spécifié et au tuteur actuel
      $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND tutor_id = ?");
      $select_playlist->execute([$get_id, $tutor_id]);
      if($select_playlist->rowCount() > 0){
         while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
            $playlist_id = $fetch_playlist['id'];
            // Comptage du nombre de PDFs associés à la playlist
            $count_pdfs = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
            $count_pdfs->execute([$playlist_id]);
            $total_pdfs = $count_pdfs->rowCount();
   ?>
   <div class="row">
      <!-- Affichage de la miniature de la playlist et des détails -->
      <div class="thumb">
         <span><?= $total_pdfs; ?></span>
         <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
      </div>
      <div class="details">
         <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
         <div class="date"><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         <div class="description"><?= $fetch_playlist['description']; ?></div>
         <!-- Formulaire pour la suppression de la playlist -->
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
            <a href="update_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">update playlist</a>
            <input type="submit" value="delete playlist" class="delete-btn" onclick="return confirm('delete this playlist?');" name="delete">
         </form>
      </div>
   </div>
   <?php
         }
      }else{
         // Si aucune playlist n'est trouvée, afficher un message
         echo '<p class="empty">no playlist found!</p>';
      }
   ?>

</section>

<section class="contents">

   <h1 class="heading">playlist courses</h1>

   <div class="box-container">

   <?php
      // Sélection des PDFs associés au tuteur actuel et à la playlist spécifiée
      $select_pdfs = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ? AND playlist_id = ?");
      $select_pdfs->execute([$tutor_id, $playlist_id]);
      if($select_pdfs->rowCount() > 0){
         while($fecth_pdfs = $select_pdfs->fetch(PDO::FETCH_ASSOC)){ 
            $pdf_id = $fecth_pdfs['id'];
   ?>
      <div class="box">
         <div class="flex">
            <!-- Affichage de l'état du PDF (actif ou non) -->
            <div><i class="fas fa-dot-circle" style="<?php if($fecth_pdfs['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fecth_pdfs['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fecth_pdfs['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fecth_pdfs['date']; ?></span></div>
         </div>
         <img src="../uploaded_files/<?= $fecth_pdfs['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fecth_pdfs['title']; ?></h3>
         <!-- Formulaire pour la suppression du PDF -->
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="pdf_id" value="<?= $pdf_id; ?>">
            <a href="update_content.php?get_id=<?= $pdf_id; ?>" class="option-btn">update</a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this pdf?');" name="delete_pdf">
         </form>
         <a href="../uploaded_files/<?= $fecth_pdfs['pdf']; ?>" class="btn" target="_blank">View PDF</a>
      </div>
   <?php
         }
      }else{
         // Si aucun PDF n'est trouvé, afficher un message avec un lien pour ajouter des cours
         echo '<p class="empty">no courses added yet! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">add courses</a></p>';
      }
   ?>

   </div>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
