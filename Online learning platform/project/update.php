<?php

// Inclure le fichier de connexion à la base de données
include 'components/connect.php';

// Initialisation de la variable user_id
$user_id = '';

// Vérifier si l'ID utilisateur est défini dans les cookies
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   // Rediriger vers la page de connexion si l'ID utilisateur n'est pas défini dans les cookies
   $user_id = '';
   header('location:login.php');
}

// Vérifier si le formulaire de soumission est soumis
if(isset($_POST['submit'])){

   // Requête pour sélectionner l'utilisateur en fonction de l'ID utilisateur
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
   $select_user->execute([$user_id]);
   $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

   // Récupérer le mot de passe précédent et l'image précédente de l'utilisateur
   $prev_pass = $fetch_user['password'];
   $prev_image = $fetch_user['image'];

   // Récupérer le nom de l'utilisateur du formulaire et le filtrer
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   // Vérifier si le nom n'est pas vide
   if(!empty($name)){
      // Mettre à jour le nom de l'utilisateur dans la base de données
      $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
      $update_name->execute([$name, $user_id]);
      $message[] = 'username updated successfully!';
   }

   // Récupérer l'e-mail de l'utilisateur du formulaire et le filtrer
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   // Vérifier si l'e-mail n'est pas vide
   if(!empty($email)){
      // Vérifier si l'e-mail est déjà pris par un autre utilisateur
      $select_email = $conn->prepare("SELECT email FROM `users` WHERE email = ?");
      $select_email->execute([$email]);
      if($select_email->rowCount() > 0){
         $message[] = 'email already taken!';
      }else{
         // Mettre à jour l'e-mail de l'utilisateur dans la base de données
         $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
         $update_email->execute([$email, $user_id]);
         $message[] = 'email updated successfully!';
      }
   }

   // Récupérer le nom de l'image du formulaire et la filtrer
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_files/'.$rename;

   // Vérifier si une nouvelle image est téléchargée
   if(!empty($image)){
      // Vérifier la taille de l'image
      if($image_size > 2000000){
         $message[] = 'image size too large!';
      }else{
         // Mettre à jour l'image de l'utilisateur dans la base de données et déplacer le fichier téléchargé vers le dossier
         $update_image = $conn->prepare("UPDATE `users` SET `image` = ? WHERE id = ?");
         $update_image->execute([$rename, $user_id]);
         move_uploaded_file($image_tmp_name, $image_folder);
         // Supprimer l'image précédente si elle existe et n'est pas la même que la nouvelle image
         if($prev_image != '' AND $prev_image != $rename){
            unlink('uploaded_files/'.$prev_image);
         }
         $message[] = 'image updated successfully!';
      }
   }

   // Définir le mot de passe vide
   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   // Vérifier si l'ancien mot de passe n'est pas vide
   if($old_pass != $empty_pass){
      // Vérifier si l'ancien mot de passe correspond au mot de passe précédent
      if($old_pass != $prev_pass){
         $message[] = 'old password not matched!';
      }elseif($new_pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         // Mettre à jour le mot de passe de l'utilisateur dans la base de données
         if($new_pass != $empty_pass){
            $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_pass->execute([$cpass, $user_id]);
            $message[] = 'password updated successfully!';
         }else{
            $message[] = 'please enter a new password!';
         }
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update profile</title>

   <!-- Lien CDN pour Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien du fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container" style="min-height: calc(100vh - 19rem);">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>update profile</h3>
      <div class="flex">
         <div class="col">
            <!-- Champ pour le nom de l'utilisateur -->
            <p>your name</p>
            <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" maxlength="100" class="box">
            <!-- Champ pour l'e-mail de l'utilisateur -->
            <p>your email</p>
            <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" maxlength="100" class="box">
            <!-- Champ pour télécharger une nouvelle image de profil -->
            <p>update pic</p>
            <input type="file" name="image" accept="image/*" class="box">
         </div>
         <div class="col">
               <!-- Champ pour l'ancien mot de passe -->
               <p>old password</p>
               <input type="password" name="old_pass" placeholder="enter your old password" maxlength="50" class="box">
               <!-- Champ pour le nouveau mot de passe -->
               <p>new password</p>
               <input type="password" name="new_pass" placeholder="enter your new password" maxlength="50" class="box">
               <!-- Champ pour confirmer le nouveau mot de passe -->
               <p>confirm password</p>
               <input type="password" name="cpass" placeholder="confirm your new password" maxlength="50" class="box">
         </div>
      </div>
      <!-- Bouton pour soumettre le formulaire -->
      <input type="submit" name="submit" value="update profile" class="btn">
   </form>

</section>

<!-- Fin de la section de mise à jour du profil -->

<?php include 'components/footer.php'; ?>

<!-- Lien vers le fichier JavaScript personnalisé -->
<script src="js/script.js"></script>
   
</body>
</html>
