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

// Vérification de l'existence de l'identifiant du contenu dans la requête GET
if(isset($_GET['get_id'])){
   // Si l'identifiant du contenu est défini dans la requête GET, le récupérer
   $get_id = $_GET['get_id'];
}else{
   // Si l'identifiant du contenu n'est pas défini dans la requête GET, initialiser à une chaîne vide et rediriger vers la page de visualisation des contenus
   $get_id = '';
   header('location:contents.php');
}

// Traitement de la suppression d'un PDF
if(isset($_POST['delete_pdf'])){
   // Récupération de l'identifiant du PDF à supprimer
   $delete_id = $_POST['pdf_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Sélection du chemin du thumbnail associé au PDF à supprimer
   $delete_pdf_thumb = $conn->prepare("SELECT thumb FROM `content` WHERE id = ? LIMIT 1");
   $delete_pdf_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_pdf_thumb->fetch(PDO::FETCH_ASSOC);
   // Suppression du thumbnail du serveur
   unlink('../uploaded_files/'.$fetch_thumb['thumb']);

   // Sélection du chemin du PDF à supprimer
   $delete_pdf = $conn->prepare("SELECT pdf FROM `content` WHERE id = ? LIMIT 1");
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

   // Redirection vers la page de visualisation des contenus
   header('location:contents.php');
}

// Traitement de la suppression d'un commentaire
if(isset($_POST['delete_comment'])){
   // Récupération de l'identifiant du commentaire à supprimer
   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Vérification de l'existence du commentaire dans la base de données
   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   // Si le commentaire existe
   if($verify_comment->rowCount() > 0){
      // Suppression du commentaire de la base de données
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      $message[] = 'comment deleted successfully!';
   }else{
      // Si le commentaire n'existe pas, afficher un message d'erreur
      $message[] = 'comment already deleted!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>view content</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
<?php include '../components/admin_header.php'; ?>

<!-- Section affichant le contenu PDF et les commentaires associés -->
<section class="view-content">

   <?php
      // Sélection du contenu PDF associé à l'identifiant spécifié et au tuteur actuel
      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND tutor_id = ?");
      $select_content->execute([$get_id, $tutor_id]);
      if($select_content->rowCount() > 0){
         while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
            $pdf_id = $fetch_content['id'];

            // Calcul du nombre de likes associés au PDF
            $count_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ? AND content_id = ?");
            $count_likes->execute([$tutor_id, $pdf_id]);
            $total_likes = $count_likes->rowCount();

            // Calcul du nombre de commentaires associés au PDF
            $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ? AND content_id = ?");
            $count_comments->execute([$tutor_id, $pdf_id]);
            $total_comments = $count_comments->rowCount();
   ?>
   <div class="container">
      <!-- Affichage du contenu PDF -->
      <embed src="../uploaded_files/<?= $fetch_content['pdf']; ?>" type="application/pdf" width="100%" height="600px" />
      <div class="date"><i class="fas fa-calendar"></i><span><?= $fetch_content['date']; ?></span></div>
      <h3 class="title"><?= $fetch_content['title']; ?></h3>
      <div class="flex">
         <div><i class="fas fa-heart"></i><span><?= $total_likes; ?></span></div>
         <div><i class="fas fa-comment"></i><span><?= $total_comments; ?></span></div>
      </div>
      <div class="description"><?= $fetch_content['description']; ?></div>
      <!-- Formulaire pour la suppression du PDF -->
      <form action="" method="post">
         <div class="flex-btn">
            <input type="hidden" name="pdf_id" value="<?= $pdf_id; ?>">
            <a href="update_content.php?get_id=<?= $pdf_id; ?>" class="option-btn">update</a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this pdf?');" name="delete_pdf">
         </div>
      </form>
   </div>
   <?php
         }
      } else {
         // Si aucun contenu n'est trouvé, afficher un message indiquant que aucun contenu n'a été ajouté
         echo '<p class="empty">no contents added yet! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">add pdfs</a></p>';
      }
   ?>

</section>

<!-- Section affichant les commentaires associés au PDF -->
<section class="comments">

   <h1 class="heading">user comments</h1>

   <!-- Affichage des commentaires -->
   <div class="show-comments">
      <?php
         // Sélection de tous les commentaires associés au PDF
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ?");
         $select_comments->execute([$get_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){   
               // Sélection des informations de l'utilisateur ayant fait le commentaire
               $select_commentor = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_commentor->execute([$fetch_comment['user_id']]);
               $fetch_commentor = $select_commentor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="user">
            <!-- Affichage de l'image et du nom de l'utilisateur qui a fait le commentaire -->
            <img src="../uploaded_files/<?= $fetch_commentor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_commentor['name']; ?></h3>
               <span><?= $fetch_comment['date']; ?></span>
            </div>
         </div>
         <!-- Affichage du texte du commentaire -->
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         <!-- Formulaire pour la suppression du commentaire -->
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('delete this comment?');">delete comment</button>
         </form>
      </div>
      <?php
       }
      }else{
         // Si aucun commentaire n'a été ajouté, afficher un message
         echo '<p class="empty">no comments added yet!</p>';
      }
      ?>
      </div>
   
</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
