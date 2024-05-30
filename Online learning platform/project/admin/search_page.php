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

// Traitement de la suppression de PDF
if(isset($_POST['delete_pdf'])){
   // Récupération et filtrage de l'identifiant du PDF à supprimer
   $delete_id = $_POST['pdf_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Vérification de l'existence du PDF dans la base de données
   $verify_pdf = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
   $verify_pdf->execute([$delete_id]);

   // Si le PDF existe, le supprimer
   if($verify_pdf->rowCount() > 0){
      // Sélection du PDF pour récupérer le nom des fichiers associés
      $delete_pdf_thumb = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_pdf_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_pdf_thumb->fetch(PDO::FETCH_ASSOC);
      // Suppression du fichier image miniature associé au PDF
      unlink('../uploaded_files/'.$fetch_thumb['thumb']);
      
      // Sélection du PDF pour récupérer le nom du fichier PDF associé
      $delete_pdf = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_pdf->execute([$delete_id]);
      $fetch_pdf = $delete_pdf->fetch(PDO::FETCH_ASSOC);
      // Suppression du fichier PDF associé
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

      // Message indiquant que le PDF a été supprimé avec succès
      $message[] = 'pdf deleted!';
   }else{
      // Message indiquant que le PDF est déjà supprimé
      $message[] = 'pdf already deleted!';
   }

}

// Traitement de la suppression de playlist
if(isset($_POST['delete_playlist'])){
   // Récupération et filtrage de l'identifiant de la playlist à supprimer
   $delete_id = $_POST['playlist_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Vérification de l'existence de la playlist dans la base de données
   $verify_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND tutor_id = ? LIMIT 1");
   $verify_playlist->execute([$delete_id, $tutor_id]);

   // Si la playlist existe, la supprimer
   if($verify_playlist->rowCount() > 0){
      // Sélection de la playlist pour récupérer le nom des fichiers associés
      $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
      $delete_playlist_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
      // Suppression du fichier image miniature associé à la playlist
      unlink('../uploaded_files/'.$fetch_thumb['thumb']);
      
      // Suppression des signets associés à la playlist
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
   <title>Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="contents">

   <h1 class="heading">contents</h1>

   <div class="box-container">

   <?php
      // Vérification de la recherche ou de la soumission du formulaire de recherche
      if(isset($_POST['search']) or isset($_POST['search_btn'])){
         $search = $_POST['search'];
         // Sélection des PDF correspondant à la recherche
         $select_pdfs = $conn->prepare("SELECT * FROM `content` WHERE title LIKE '%{$search}%' AND tutor_id = ? ORDER BY date DESC");
         $select_pdfs->execute([$tutor_id]);
         // Vérification s'il existe des PDF correspondant à la recherche
         if($select_pdfs->rowCount() > 0){
            // Parcours des PDF correspondant à la recherche
            while($fecth_pdfs = $select_pdfs->fetch(PDO::FETCH_ASSOC)){ 
               // Récupération de l'identifiant du PDF
               $pdf_id = $fecth_pdfs['id'];
   ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-dot-circle" style="<?php if($fecth_pdfs['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fecth_pdfs['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fecth_pdfs['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fecth_pdfs['date']; ?></span></div>
         </div>
         <img src="../uploaded_files/<?= $fecth_pdfs['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fecth_pdfs['title']; ?></h3>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="pdf_id" value="<?= $pdf_id; ?>">
            <a href="update_content.php?get_id=<?= $pdf_id; ?>" class="option-btn">update</a>
            <!-- Bouton de suppression du PDF avec une confirmation -->
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this pdf?');" name="delete_pdf">
         </form>
         <a href="view_content.php?get_id=<?= $pdf_id; ?>" class="btn">view content</a>
      </div>
   <?php
         }
      }else{
         // Message indiquant qu'aucun contenu n'a été trouvé
         echo '<p class="empty">no contents founds!</p>';
      }
   }else{
      // Message indiquant qu'une recherche doit être effectuée
      echo '<p class="empty">please search something!</p>';
   }
   ?>

   </div>

</section>

<section class="playlists">

   <h1 class="heading">playlists</h1>

   <div class="box-container">
   
      <?php
      // Vérification de la recherche ou de la soumission du formulaire de recherche
      if(isset($_POST['search']) or isset($_POST['search_btn'])){
         $search = $_POST['search'];
         // Sélection des playlists correspondant à la recherche
         $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE title LIKE '%{$search}%' AND tutor_id = ? ORDER BY date DESC");
         $select_playlist->execute([$tutor_id]);
         // Vérification s'il existe des playlists correspondant à la recherche
         if($select_playlist->rowCount() > 0){
            // Parcours des playlists correspondant à la recherche
            while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
               // Récupération de l'identifiant de la playlist
               $playlist_id = $fetch_playlist['id'];
               // Comptage du nombre de PDFs dans la playlist
               $count_pdfs = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
               $count_pdfs->execute([$playlist_id]);
               $total_pdfs = $count_pdfs->rowCount();
      ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-circle-dot" style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fetch_playlist['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         </div>
         <div class="thumb">
            <!-- Affichage du nombre de PDFs dans la playlist -->
            <span><?= $total_pdfs; ?></span>
            <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
         </div>
         <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
         <p class="description"><?= $fetch_playlist['description']; ?></p>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
            <a href="update_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">update</a>
            <!-- Bouton de suppression de la playlist avec une confirmation -->
            <input type="submit" value="delete_playlist" class="delete-btn" onclick="return confirm('delete this playlist?');" name="delete_playlist">
         </form>
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="btn">view playlist</a>
      </div>
      <?php
         } 
      }else{
         // Message indiquant qu'aucune playlist n'a été trouvée
         echo '<p class="empty">no playlists found!</p>';
      }}else{
         // Message indiquant qu'une recherche doit être effectuée
         echo '<p class="empty">please search something!</p>';
      }
      ?>

   </div>

</section>















<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

<script>
   // Limiter la longueur de la description des playlists
   document.querySelectorAll('.playlists .box-container .box .description').forEach(content => {
      if(content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
   });
</script>

</body>
</html>
