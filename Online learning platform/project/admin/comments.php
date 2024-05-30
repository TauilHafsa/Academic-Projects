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

// Traitement de la suppression de commentaire
if(isset($_POST['delete_comment'])){
   // Récupération et filtrage de l'identifiant du commentaire à supprimer
   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Vérification de l'existence du commentaire dans la base de données
   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   // Si le commentaire existe, le supprimer
   if($verify_comment->rowCount() > 0){
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      $message[] = 'comment deleted successfully!';
   }else{
      // Sinon, afficher un message indiquant que le commentaire est déjà supprimé
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
   <title>Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   

<section class="comments">

   <h1 class="heading">user comments</h1>

   
   <div class="show-comments">
      <?php
         // Récupération des commentaires associés à ce tuteur depuis la base de données
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
         $select_comments->execute([$tutor_id]);
         // Vérification s'il y a des commentaires
         if($select_comments->rowCount() > 0){
            // Parcours des commentaires
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){
               // Récupération des détails du contenu associé à ce commentaire
               $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ?");
               $select_content->execute([$fetch_comment['content_id']]);
               $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box" style="<?php if($fetch_comment['tutor_id'] == $tutor_id){echo 'order:-1;';} ?>">
         <div class="content"><span><?= $fetch_comment['date']; ?></span><p> - <?= $fetch_content['title']; ?> - </p><a href="view_content.php?get_id=<?= $fetch_content['id']; ?>">view content</a></div>
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         <!-- Formulaire pour supprimer le commentaire -->
         <form action="" method="post">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('delete this comment?');">delete comment</button>
         </form>
      </div>
      <?php
       }
      }else{
         // Message si aucun commentaire n'est disponible pour ce tuteur
         echo '<p class="empty">no comments added yet!</p>';
      }
      ?>
      </div>
   
</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
