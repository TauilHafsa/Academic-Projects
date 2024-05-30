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

   <h1 class="heading">your courses</h1>

   <div class="box-container">

   <div class="box" style="text-align: center;">
      <h3 class="title" style="margin-bottom: .5rem;">create new course</h3>
      <a href="add_content.php" class="btn">add course</a>
   </div>

   <?php
      // Sélection de tous les PDF associés à ce tuteur, triés par date de manière décroissante
      $select_pdfs = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ? ORDER BY date DESC");
      $select_pdfs->execute([$tutor_id]);
      // Vérification s'il existe des PDF
      if($select_pdfs->rowCount() > 0){
         // Parcours des PDF
         while($fecth_pdfs = $select_pdfs->fetch(PDO::FETCH_ASSOC)){ 
            // Récupération de l'identifiant du PDF
            $pdf_id = $fecth_pdfs['id'];
   ?>
      <div class="box">
         <div class="flex">
            <!-- Affichage de l'état du PDF (actif/inactif) -->
            <div><i class="fas fa-dot-circle" style="<?php if($fecth_pdfs['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fecth_pdfs['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fecth_pdfs['status']; ?></span></div>
            <!-- Affichage de la date de création du PDF -->
            <div><i class="fas fa-calendar"></i><span><?= $fecth_pdfs['date']; ?></span></div>
         </div>
         <!-- Affichage de l'image miniature du PDF -->
         <img src="../uploaded_files/<?= $fecth_pdfs['thumb']; ?>" class="thumb" alt="">
         <!-- Affichage du titre du PDF -->
         <h3 class="title"><?= $fecth_pdfs['title']; ?></h3>
         <!-- Formulaire pour supprimer le PDF -->
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="pdf_id" value="<?= $pdf_id; ?>">
            <!-- Bouton pour mettre à jour le PDF -->
            <a href="update_content.php?get_id=<?= $pdf_id; ?>" class="option-btn">update</a>
            <!-- Bouton pour supprimer le PDF -->
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this pdf?');" name="delete_pdf">
         </form>
         <!-- Bouton pour voir le PDF -->
         <a href="view_content.php?get_id=<?= $pdf_id; ?>" class="btn">view course</a>
      </div>
   <?php
         }
      }else{
         // Message affiché s'il n'y a aucun PDF ajouté
         echo '<p class="empty">no contents added yet!</p>';
      }
   ?>

   </div>

</section>



<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
