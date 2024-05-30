<?php

// Inclusion du fichier de connexion à la base de données
include '../components/connect.php';

// Vérification de l'identifiant du tuteur dans les cookies
if(isset($_COOKIE['tutor_id'])){
   // Si l'identifiant du tuteur est défini dans les cookies, le récupérer
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   // Si l'identifiant du tuteur n'est pas défini, initialiser à une chaîne vide et rediriger vers la page de connexion
   $tutor_id = '';
   header('location:login.php');
}

// Traitement du formulaire de soumission
if(isset($_POST['submit'])){
   // Génération d'un identifiant unique pour la playlist
   $id = unique_id();
   // Récupération et filtrage du titre de la playlist
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   // Récupération et filtrage de la description de la playlist
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   // Récupération et filtrage du statut de la playlist
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);

   // Récupération, validation et traitement de l'image de la playlist
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/'.$rename;

   // Insertion des données de la playlist dans la base de données et déplacement de l'image vers le dossier de téléchargement
   $add_playlist = $conn->prepare("INSERT INTO `playlist`(id, tutor_id, title, description, thumb, status) VALUES(?,?,?,?,?,?)");
   $add_playlist->execute([$id, $tutor_id, $title, $description, $rename, $status]);
   move_uploaded_file($image_tmp_name, $image_folder);

   // Message de succès une fois la playlist créée
   $message[] = 'new playlist created!';  

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Add Playlist</title>

   <!-- Inclusion du lien vers la bibliothèque Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Inclusion du fichier CSS personnalisé -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="playlist-form">

   <h1 class="heading">create playlist</h1>

   <!-- Formulaire de création de playlist -->
   <form action="" method="post" enctype="multipart/form-data">
   <p>playlist status <span>*</span></p>
      <!-- Sélection du statut de la playlist -->
      <select name="status" class="box" required>
         <option value="" selected disabled>-- select status</option>
         <option value="active">active</option>
         <option value="deactive">deactive</option>
      </select>
      <p>playlist title <span>*</span></p>
      <!-- Champ de saisie du titre de la playlist -->
      <input type="text" name="title" maxlength="100" required placeholder="enter playlist title" class="box">
      <p>playlist description <span>*</span></p>
      <!-- Champ de saisie de la description de la playlist -->
      <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"></textarea>
      <p>playlist thumbnail <span>*</span></p>
      <!-- Champ de téléchargement de l'image de la playlist -->
      <input type="file" name="image" accept="image/*" required class="box">
      <!-- Bouton de soumission du formulaire -->
      <input type="submit" value="create playlist" name="submit" class="btn">
   </form>

</section>
