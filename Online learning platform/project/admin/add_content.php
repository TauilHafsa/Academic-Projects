<?php
// Inclusion du fichier de connexion à la base de données
include '../components/connect.php';

// Vérification de l'identifiant du tuteur dans les cookies
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   // Si l'identifiant du tuteur n'est pas défini, redirection vers la page de connexion
   $tutor_id = '';
   header('location:login.php');
}

// Traitement du formulaire de soumission
if(isset($_POST['submit'])){
   // Génération d'un identifiant unique pour le contenu
   $id = unique_id();
   // Récupération et filtrage du statut du cours
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);
   // Récupération et filtrage du titre du cours
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   // Récupération et filtrage de la description du cours
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   // Récupération et filtrage de l'identifiant de la playlist du cours
   $playlist = $_POST['playlist'];
   $playlist = filter_var($playlist, FILTER_SANITIZE_STRING);

   // Récupération, validation et traitement de la miniature du cours
   $thumb = $_FILES['thumb']['name'];
   $thumb = filter_var($thumb, FILTER_SANITIZE_STRING);
   $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
   $rename_thumb = unique_id().'.'.$thumb_ext;
   $thumb_size = $_FILES['thumb']['size'];
   $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
   $thumb_folder = '../uploaded_files/'.$rename_thumb;

   // Récupération, validation et traitement du PDF du cours
   $pdf = $_FILES['pdf']['name'];
   $pdf = filter_var($pdf, FILTER_SANITIZE_STRING);
   $pdf_ext = pathinfo($pdf, PATHINFO_EXTENSION);
   $rename_pdf = unique_id().'.'.$pdf_ext;
   $pdf_tmp_name = $_FILES['pdf']['tmp_name'];
   $pdf_folder = '../uploaded_files/'.$rename_pdf;

   // Vérification de la taille de la miniature
   if($thumb_size > 2000000){
      // Message d'erreur si la taille de la miniature est trop grande
      $message[] = 'image size is too large!';
   }else{
      // Insertion des données du cours dans la base de données et déplacement des fichiers vers le dossier de téléchargement
      $add_playlist = $conn->prepare("INSERT INTO `content`(id, tutor_id, playlist_id, title, description, pdf, thumb, status) VALUES(?,?,?,?,?,?,?,?)");
      $add_playlist->execute([$id, $tutor_id, $playlist, $title, $description, $rename_pdf, $rename_thumb, $status]);
      move_uploaded_file($thumb_tmp_name, $thumb_folder);
      move_uploaded_file($pdf_tmp_name, $pdf_folder);
      // Message de succès si le cours est téléchargé avec succès
      $message[] = 'new course uploaded!';
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

   <!-- Inclusion du lien vers la bibliothèque Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Inclusion du fichier CSS personnalisé -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="pdf-form">

   <h1 class="heading">upload content</h1>

   <!-- Formulaire d'upload de contenu -->
   <form action="" method="post" enctype="multipart/form-data">
      <p>course status <span>*</span></p>
      <!-- Sélection du statut du cours -->
      <select name="status" class="box" required>
         <option value="" selected disabled>-- select status</option>
         <option value="active">active</option>
         <option value="deactive">deactive</option>
      </select>
      <p>course title <span>*</span></p>
      <!-- Champ de saisie du titre du cours -->
      <input type="text" name="title" maxlength="100" required placeholder="enter course title" class="box">
      <p>course description <span>*</span></p>
      <!-- Champ de saisie de la description du cours -->
      <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"></textarea>
      <p>course playlist <span>*</span></p>
      <!-- Sélection de la playlist associée au cours -->
      <select name="playlist" class="box" required>
         <option value="" disabled selected>--select playlist</option>
         <?php
         // Récupération des playlists associées à ce tuteur depuis la base de données
         $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
         $select_playlists->execute([$tutor_id]);
         if($select_playlists->rowCount() > 0){
            while($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)){
         ?>
         <!-- Option de playlist dans le formulaire -->
         <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?></option>
         <?php
            }
         }else{
            // Message si aucune playlist n'est disponible pour ce tuteur
            echo '<option value="" disabled>no playlist created yet!</option>';
         }
         ?>
      
      <p>select course thumb <span>*</span></p>
      <!-- Champ de téléchargement de la miniature du cours -->
      <input type="file" name="thumb" accept="image/*" required class="box">
      <p>select PDF <span>*</span></p>
      <!-- Champ de téléchargement du PDF du cours -->
      <input type="file" name="pdf" accept=".pdf" required class="box">
      <!-- Bouton de soumission du formulaire -->
      <input type="submit" value="upload course" name="submit" class="btn">
   </form>

</section>

<?php include '../components/footer.php'; ?>

<!-- Inclusion du fichier JavaScript personnalisé -->
<script src="../js/admin_script.js"></script>

</body>
</html>
