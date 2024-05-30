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

// Vérification de l'existence de l'identifiant du cours à mettre à jour
if(isset($_GET['get_id'])){
   // Si l'identifiant du cours est défini, le récupérer
   $get_id = $_GET['get_id'];
}else{
   // Si l'identifiant du cours n'est pas défini, initialiser à une chaîne vide et rediriger vers le tableau de bord
   $get_id = '';
   header('location:dashboard.php');
}

// Traitement de la mise à jour du cours
if(isset($_POST['update'])){

   // Récupération et filtrage des données du formulaire
   $pdf_id = $_POST['pdf_id'] ?? '';
   $pdf_id = filter_var($pdf_id, FILTER_SANITIZE_STRING);
   $status = $_POST['status'] ?? '';
   $status = filter_var($status, FILTER_SANITIZE_STRING);
   $title = $_POST['title'] ?? '';
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $description = $_POST['description'] ?? '';
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $playlist = $_POST['playlist'] ?? '';
   $playlist = filter_var($playlist, FILTER_SANITIZE_STRING);

   // Mise à jour des données du cours dans la base de données
   $update_content = $conn->prepare("UPDATE `content` SET title = ?, description = ?, status = ? WHERE id = ?");
   $update_content->execute([$title, $description, $status, $pdf_id]);

   // Si une playlist est sélectionnée, mettre à jour l'identifiant de la playlist associée au cours
   if(!empty($playlist)){
      $update_playlist = $conn->prepare("UPDATE `content` SET playlist_id = ? WHERE id = ?");
      $update_playlist->execute([$playlist, $pdf_id]);
   }

   // Traitement de la mise à jour de l'image miniature
   $old_thumb = $_POST['old_thumb'] ?? '';
   $old_thumb = filter_var($old_thumb, FILTER_SANITIZE_STRING);
   $thumb = $_FILES['thumb']['name'] ?? '';
   $thumb = filter_var($thumb, FILTER_SANITIZE_STRING);
   $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
   $rename_thumb = unique_id().'.'.$thumb_ext;
   $thumb_size = $_FILES['thumb']['size'] ?? '';
   $thumb_tmp_name = $_FILES['thumb']['tmp_name'] ?? '';
   $thumb_folder = '../uploaded_files/'.$rename_thumb;

   // Si une nouvelle image miniature est téléchargée, la mettre à jour
   if(!empty($thumb)){
      if($thumb_size > 2000000){
         $message[] = 'image size is too large!';
      }else{
         $update_thumb = $conn->prepare("UPDATE `content` SET thumb = ? WHERE id = ?");
         $update_thumb->execute([$rename_thumb, $pdf_id]);
         move_uploaded_file($thumb_tmp_name, $thumb_folder);
         // Supprimer l'ancienne image miniature si elle existe et si elle est différente de la nouvelle
         if($old_thumb != '' AND $old_thumb != $rename_thumb){
            unlink('../uploaded_files/'.$old_thumb);
         }
      }
   }

   // Traitement de la mise à jour du fichier PDF du cours
   $old_pdf = $_POST['old_pdf'] ?? '';
   $old_pdf = filter_var($old_pdf, FILTER_SANITIZE_STRING);
   $pdf = $_FILES['pdf']['name'] ?? '';
   $pdf = filter_var($pdf, FILTER_SANITIZE_STRING);
   $pdf_ext = pathinfo($pdf, PATHINFO_EXTENSION);
   $rename_pdf = unique_id().'.'.$pdf_ext;
   $pdf_tmp_name = $_FILES['pdf']['tmp_name'] ?? '';
   $pdf_folder = '../uploaded_files/'.$rename_pdf;

   // Si un nouveau fichier PDF est téléchargé, le mettre à jour
   if(!empty($pdf)){
      $update_pdf = $conn->prepare("UPDATE `content` SET pdf = ? WHERE id = ?");
      $update_pdf->execute([$rename_pdf, $pdf_id]);
      move_uploaded_file($pdf_tmp_name, $pdf_folder);
      // Supprimer l'ancien fichier PDF s'il existe et s'il est différent du nouveau
      if($old_pdf != '' AND $old_pdf != $rename_pdf){
         unlink('../uploaded_files/'.$old_pdf);
      }
   }

   // Message indiquant que le cours a été mis à jour avec succès
   $message[] = 'content updated!';

}

// Traitement de la suppression du cours
if(isset($_POST['delete_pdf'])){

   // Récupération et filtrage de l'identifiant du cours à supprimer
   $delete_id = $_POST['pdf_id'] ?? '';
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Sélection de l'image miniature associée au cours à supprimer
   $delete_pdf_thumb = $conn->prepare("SELECT thumb FROM `content` WHERE id = ? LIMIT 1");
   $delete_pdf_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_pdf_thumb->fetch(PDO::FETCH_ASSOC);
   // Suppression de l'image miniature associée au cours
   unlink('../uploaded_files/'.$fetch_thumb['thumb']);

   // Sélection du fichier PDF associé au cours à supprimer
   $delete_pdf = $conn->prepare("SELECT pdf FROM `content` WHERE id = ? LIMIT 1");
   $delete_pdf->execute([$delete_id]);
   $fetch_pdf = $delete_pdf->fetch(PDO::FETCH_ASSOC);
   // Suppression du fichier PDF associé au cours
   unlink('../uploaded_files/'.$fetch_pdf['pdf']);

   // Suppression des likes associés au cours
   $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE content_id = ?");
   $delete_likes->execute([$delete_id]);
   // Suppression des commentaires associés au cours
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
   $delete_comments->execute([$delete_id]);

   // Suppression du cours de la base de données
   $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
   $delete_content->execute([$delete_id]);
   // Redirection vers la page de gestion des cours
   header('location:contents.php');
    
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update course</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
<?php include '../components/admin_header.php'; ?>
   
<section class="pdf-form">

   <h1 class="heading">Update Course</h1>

   <?php
      // Sélection des données du cours à mettre à jour
      $select_contents = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND tutor_id = ?");
      $select_contents->execute([$get_id, $tutor_id]);
      // Vérification de l'existence des données du cours
      if($select_contents->rowCount() > 0){
         // Affichage du formulaire de mise à jour du cours
         while($fetch_contents = $select_contents->fetch(PDO::FETCH_ASSOC)){ 
            $content_id = $fetch_contents['id'];
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="pdf_id" value="<?= $fetch_contents['id']; ?>">
      <input type="hidden" name="old_thumb" value="<?= $fetch_contents['thumb']; ?>">
      <input type="hidden" name="old_pdf" value="<?= $fetch_contents['pdf']; ?>">
      <p>Update Status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="<?= $fetch_contents['status']; ?>" selected><?= ucfirst($fetch_contents['status']); ?></option>
         <option value="active">Active</option>
         <option value="deactive">Deactive</option>
      </select>
      <p>Update Title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="Enter content title" class="box" value="<?= $fetch_contents['title']; ?>">
      <p>Update Description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="Write description" maxlength="1000" cols="30" rows="10"><?= $fetch_contents['description']; ?></textarea>
      <p>Update Playlist</p>
      <select name="playlist" class="box">
         <option value="<?= $fetch_contents['playlist_id']; ?>" selected>--Select playlist--</option>
         <?php
            // Sélection des playlists disponibles pour le tuteur
            $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
            $select_playlists->execute([$tutor_id]);
            // Vérification de l'existence des playlists
            if($select_playlists->rowCount() > 0){
               // Affichage des options de playlist
               while($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)){
         ?>
                  <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?></option>
         <?php
               }
            }else{
               // Message indiquant qu'aucune playlist n'a été créée
               echo '<option value="" disabled>No playlist created yet!</option>';
            }
         ?>
      </select>
      <img src="../uploaded_files/<?= $fetch_contents['thumb']; ?>" alt="">
      <p>Update Thumbnail</p>
      <input type="file" name="thumb" accept="image/*" class="box">
      <embed src="../uploaded_files/<?= $fetch_contents['pdf']; ?>" type="application/pdf" width="100%" height="600px" />
      <p>Update Course (PDF)</p>
      <input type="file" name="pdf" accept=".pdf" class="box">
      <input type="submit" value="Update Content" name="update" class="btn">
      <div class="flex-btn">
         <a href="view_content.php?get_id=<?= $content_id; ?>" class="option-btn">View Content</a>
         <input type="submit" value="Delete Content" name="delete_content" class="delete-btn">
      </div>
   </form>
   <?php
         }
      }else{
         // Message indiquant que le cours n'a pas été trouvé
         echo '<p class="empty">Content not found! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">Add content</a></p>';
      }
   ?>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
