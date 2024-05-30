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

// Traitement du formulaire de mise à jour de profil
if(isset($_POST['submit'])){
    // Sélection des données du tuteur à partir de son identifiant
    $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
    $select_tutor->execute([$tutor_id]);
    $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

    // Récupération du mot de passe précédent et de l'image précédente du tuteur
    $prev_pass = $fetch_tutor['password'];
    $prev_image = $fetch_tutor['image'];

    // Récupération et filtrage des données du formulaire
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);

    // Traitement de l'image
    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = unique_id().'.'.$ext;
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_files/'.$rename;

    // Si une nouvelle image est téléchargée, la mettre à jour
    if(!empty($image)){
        if($image_size > 2000000){
            $message[] = 'La taille de l\'image est trop grande !';
        }else{
            $update_image = $conn->prepare("UPDATE `tutors` SET `image` = ? WHERE id = ?");
            $update_image->execute([$rename, $tutor_id]);
            move_uploaded_file($image_tmp_name, $image_folder);
            // Supprimer l'ancienne image si elle existe et si elle est différente de la nouvelle
            if($prev_image != '' AND $prev_image != $rename){
                unlink('../uploaded_files/'.$prev_image);
            }
            $message[] = 'Image mise à jour avec succès !';
        }
    }

    // Traitement du mot de passe
    $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
    $old_pass = sha1($_POST['old_pass']);
    $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
    $new_pass = sha1($_POST['new_pass']);
    $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
    $cpass = sha1($_POST['cpass']);
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    // Vérification de l'ancien mot de passe et mise à jour du mot de passe si nécessaire
    if($old_pass != $empty_pass){
        if($old_pass != $prev_pass){
            $message[] = 'Ancien mot de passe incorrect !';
        }elseif($new_pass != $cpass){
            $message[] = 'Le mot de passe de confirmation ne correspond pas !';
        }else{
            if($new_pass != $empty_pass){
                $update_pass = $conn->prepare("UPDATE `tutors` SET password = ? WHERE id = ?");
                $update_pass->execute([$cpass, $tutor_id]);
                $message[] = 'Mot de passe mis à jour avec succès !';
            }else{
                $message[] = 'Veuillez saisir un nouveau mot de passe !';
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
   <title>Update Profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<!-- Section de mise à jour du profil -->
<section class="form-container" style="min-height: calc(100vh - 19rem);">

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>update profile</h3>
      <div class="flex">
         <div class="col">
            <p>your name </p>
            <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" maxlength="50"  class="box">
            <p>your email </p>
            <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" maxlength="20"  class="box">
         </div>
         <div class="col">
            <p>old password :</p>
            <input type="password" name="old_pass" placeholder="enter your old password" maxlength="20"  class="box">
            <p>new password :</p>
            <input type="password" name="new_pass" placeholder="enter your new password" maxlength="20"  class="box">
            <p>confirm password :</p>
            <input type="password" name="cpass" placeholder="confirm your new password" maxlength="20"  class="box">
         </div>
      </div>
      <p>update pic :</p>
      <input type="file" name="image" accept="image/*"  class="box">
      <input type="submit" name="submit" value="update now" class="btn">
   </form>

</section>

<!-- Fin de la section de mise à jour du profil -->

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
   
</body>
</html>
