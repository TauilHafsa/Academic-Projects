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

// Vérification de l'existence de l'identifiant de la playlist à mettre à jour
if(isset($_GET['get_id'])){
   // Si l'identifiant de la playlist est défini, le récupérer
   $get_id = $_GET['get_id'];
}else{
   // Si l'identifiant de la playlist n'est pas défini, initialiser à une chaîne vide et rediriger vers la page de gestion des playlists
   $get_id = '';
   header('location:playlist.php');
}

// Traitement de la mise à jour de la playlist
if(isset($_POST['submit'])){

   // Récupération et filtrage des données du formulaire
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);

   // Mise à jour des données de la playlist dans la base de données
   $update_playlist = $conn->prepare("UPDATE `playlist` SET title = ?, description = ?, status = ? WHERE id = ?");
   $update_playlist->execute([$title, $description, $status, $get_id]);

   // Traitement de la mise à jour de l'image miniature de la playlist
   $old_image = $_POST['old_image'];
   $old_image = filter_var($old_image, FILTER_SANITIZE_STRING);
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/'.$rename;

   // Si une nouvelle image miniature est téléchargée, la mettre à jour
   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'image size is too large!';
      }else{
         $update_image = $conn->prepare("UPDATE `playlist` SET thumb = ? WHERE id = ?");
         $update_image->execute([$rename, $get_id]);
         move_uploaded_file($image_tmp_name, $image_folder);
         // Supprimer l'ancienne image miniature si elle existe et si elle est différente de la nouvelle
         if($old_image != '' AND $old_image != $rename){
            unlink('../uploaded_files/'.$old_image);
         }
      }
   } 

   // Message indiquant que la playlist a été mise à jour avec succès
   $message[] = 'playlist updated!';  

}

// Traitement de la suppression de la playlist
if(isset($_POST['delete'])){
   // Récupération et filtrage de l'identifiant de la playlist à supprimer
   $delete_id = $_POST['playlist_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   // Sélection des données de la playlist à supprimer
   $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
   $delete_playlist_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
   // Suppression de l'image miniature associée à la playlist
   unlink('../uploaded_files/'.$fetch_thumb['thumb']);
   // Suppression des favoris associés à la playlist
   $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
   $delete_bookmark->execute([$delete_id]);
   // Suppression de la playlist de la base de données
   $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
   $delete_playlist->execute([$delete_id]);
   // Redirection vers la page de gestion des playlists
   header('location:playlists.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Playlist</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="playlist-form">

   <h1 class="heading">update playlist</h1>

   <?php
      // Sélection des données de la playlist à mettre à jour
      $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ?");
      $select_playlist->execute([$get_id]);
      // Vérification de l'existence des données de la playlist
      if($select_playlist->rowCount() > 0){
         // Affichage du formulaire de mise à jour de la playlist
         while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
            $playlist_id = $fetch_playlist['id'];
            // Comptage du nombre de PDF associés à la playlist
            $count_pdf = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
            $count_pdf->execute([$playlist_id]);
            $total_pdf = $count_pdf->rowCount();
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $fetch_playlist['thumb']; ?>">
      <p>playlist status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="<?= $fetch_playlist['status']; ?>" selected><?= $fetch_playlist['status']; ?></option>
         <option value="active">active</option>
         <option value="deactive">deactive</option>
      </select>
      
      <p>playlist title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="enter playlist title" value="<?= $fetch_playlist['title']; ?>" class="box">
      <p>playlist description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"><?= $fetch_playlist['description']; ?></textarea>
      <p>playlist thumbnail <span>*</span></p>
      <div class="thumb">
         <span><?= $total_pdf; ?></span>
         <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
      </div>
      <input type="file" name="image" accept="image/*" class="box">
      <input type="submit" value="update playlist" name="submit" class="btn">
      <div class="flex-btn">
         <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this playlist?');" name="delete">
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">view playlist</a>
      </div>
   </form>
   <?php
      } 
   }else{
      // Message indiquant qu'aucune playlist n'a été ajoutée
      echo '<p class="empty">no playlist added yet!</p>';
   }
   ?>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
