<?php

// Inclure le fichier de connexion à la base de données
include 'components/connect.php';

// Initialisation de la variable user_id
$user_id = '';

// Vérifier si l'ID utilisateur est défini dans les cookies
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

// Initialisation de la variable get_id
$get_id = '';

// Vérifier si l'ID de l'élément à récupérer est défini dans la requête GET
if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   // Rediriger vers la page d'accueil si l'ID de l'élément à récupérer n'est pas défini
   header('location:home.php');
}

// Traitement de la soumission du formulaire de "like" pour le contenu
if(isset($_POST['like_content'])){

   // Vérifier si l'utilisateur est connecté
   if($user_id != ''){

      // Récupérer l'ID du contenu "liké" du formulaire et le filtrer
      $content_id = $_POST['content_id'];
      $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

      // Sélectionner le contenu en fonction de son ID
      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $select_content->execute([$content_id]);
      $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

      // Récupérer l'ID du tuteur du contenu "liké"
      $tutor_id = $fetch_content['tutor_id'];

      // Vérifier si l'utilisateur a déjà "liké" ce contenu
      $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
      $select_likes->execute([$user_id, $content_id]);

      // Si l'utilisateur a déjà "liké" ce contenu, le supprimer de la table des "likes", sinon l'ajouter
      if($select_likes->rowCount() > 0){
         $remove_likes = $conn->prepare("DELETE FROM `likes` WHERE user_id = ? AND content_id = ?");
         $remove_likes->execute([$user_id, $content_id]);
         $message[] = 'removed from likes!';
      }else{
         $insert_likes = $conn->prepare("INSERT INTO `likes`(user_id, tutor_id, content_id) VALUES(?,?,?)");
         $insert_likes->execute([$user_id, $tutor_id, $content_id]);
         $message[] = 'added to likes!';
      }

   }else{
      // Message d'erreur si l'utilisateur n'est pas connecté
      $message[] = 'please login first!';
   }

}

// Traitement de la soumission du formulaire pour ajouter un commentaire
if(isset($_POST['add_comment'])){

   // Vérifier si l'utilisateur est connecté
   if($user_id != ''){

      // Générer un identifiant unique pour le commentaire
      $id = unique_id();
      // Récupérer le contenu du commentaire du formulaire et le filtrer
      $comment_box = $_POST['comment_box'];
      $comment_box = filter_var($comment_box, FILTER_SANITIZE_STRING);
      // Récupérer l'ID du contenu auquel le commentaire est associé et le filtrer
      $content_id = $_POST['content_id'];
      $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

      // Sélectionner le contenu en fonction de son ID pour vérifier son existence
      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $select_content->execute([$content_id]);
      $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

      // Récupérer l'ID du tuteur du contenu associé au commentaire
      $tutor_id = $fetch_content['tutor_id'];

      // Vérifier si le contenu existe
      if($select_content->rowCount() > 0){

         // Vérifier si le commentaire existe déjà pour ce contenu, cet utilisateur et ce tuteur
         $select_comment = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ? AND user_id = ? AND tutor_id = ? AND comment = ?");
         $select_comment->execute([$content_id, $user_id, $tutor_id, $comment_box]);

         // Si le commentaire n'existe pas encore, l'ajouter à la table des commentaires
         if($select_comment->rowCount() > 0){
            $message[] = 'comment already added!';
         }else{
            $insert_comment = $conn->prepare("INSERT INTO `comments`(id, content_id, user_id, tutor_id, comment) VALUES(?,?,?,?,?)");
            $insert_comment->execute([$id, $content_id, $user_id, $tutor_id, $comment_box]);
            $message[] = 'new comment added!';
         }

      }else{
         // Message d'erreur si quelque chose ne va pas lors de la récupération du contenu
         $message[] = 'something went wrong!';
      }

   }else{
      // Message d'erreur si l'utilisateur n'est pas connecté
      $message[] = 'please login first!';
   }

}

// Traitement de la soumission du formulaire pour supprimer un commentaire
if(isset($_POST['delete_comment'])){

   // Récupérer l'ID du commentaire à supprimer du formulaire et le filtrer
   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Vérifier si le commentaire existe en fonction de son ID
   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   // Si le commentaire existe, le supprimer de la base de données
   if($verify_comment->rowCount() > 0){
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      $message[] = 'comment deleted successfully!';
   }else{
      // Sinon, afficher un message d'erreur indiquant que le commentaire a déjà été supprimé
      $message[] = 'comment already deleted!';
   }

}

// Traitement de la soumission du formulaire pour mettre à jour un commentaire
if(isset($_POST['update_now'])){

   // Récupérer l'ID du commentaire à mettre à jour du formulaire et le filtrer
   $update_id = $_POST['update_id'];
   $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
   // Récupérer le contenu mis à jour du commentaire du formulaire et le filtrer
   $update_box = $_POST['update_box'];
   $update_box = filter_var($update_box, FILTER_SANITIZE_STRING);

   // Vérifier si le commentaire à mettre à jour existe déjà avec le contenu mis à jour
   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? AND comment = ?");
   $verify_comment->execute([$update_id, $update_box]);

   // Si le commentaire existe déjà avec le contenu mis à jour, afficher un message d'erreur
   if($verify_comment->rowCount() > 0){
      $message[] = 'comment already added!';
   }else{
      // Sinon, mettre à jour le commentaire dans la base de données avec le nouveau contenu
      $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
      $update_comment->execute([$update_box, $update_id]);
      $message[] = 'comment edited successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>see course</title>

   <!-- Lien CDN pour Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien du fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<?php
   // Vérifier si le formulaire d'édition d'un commentaire a été soumis
   if(isset($_POST['edit_comment'])){
      // Récupérer l'ID du commentaire à éditer du formulaire et le filtrer
      $edit_id = $_POST['comment_id'];
      $edit_id = filter_var($edit_id, FILTER_SANITIZE_STRING);
      // Vérifier si le commentaire à éditer existe dans la base de données
      $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? LIMIT 1");
      $verify_comment->execute([$edit_id]);
      if($verify_comment->rowCount() > 0){
         // Si le commentaire existe, récupérer ses détails
         $fetch_edit_comment = $verify_comment->fetch(PDO::FETCH_ASSOC);
?>
<section class="edit-comment">
   <h1 class="heading">edit comment</h1>
   <!-- Formulaire pour éditer le commentaire -->
   <form action="" method="post">
      <input type="hidden" name="update_id" value="<?= $fetch_edit_comment['id']; ?>">
      <textarea name="update_box" class="box" maxlength="1000" required placeholder="please enter your comment" cols="30" rows="10"><?= $fetch_edit_comment['comment']; ?></textarea>
      <div class="flex">
         <!-- Lien pour annuler l'édition du commentaire -->
         <a href="voirpdf.php?get_id=<?= $get_id; ?>" class="inline-option-btn">cancel edit</a>
         <!-- Bouton pour mettre à jour le commentaire -->
         <input type="submit" value="update now" name="update_now" class="inline-btn">
      </div>
   </form>
</section>
<?php
   }else{
      // Si le commentaire n'existe pas, afficher un message d'erreur
      $message[] = 'comment was not found!';
   }
}
?>
<section class="voirpdf">

<?php
   // Sélectionner le contenu en fonction de son ID pour l'afficher
   $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND status = ?");
   $select_content->execute([$get_id, 'active']);
   if($select_content->rowCount() > 0){
      while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
         $content_id = $fetch_content['id'];

         // Sélectionner les "likes" pour ce contenu
         $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE content_id = ?");
         $select_likes->execute([$content_id]);
         $total_likes = $select_likes->rowCount();  

         // Vérifier si l'utilisateur a déjà "liké" ce contenu
         $verify_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
         $verify_likes->execute([$user_id, $content_id]);

         // Sélectionner les détails du tuteur associé à ce contenu
         $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
         $select_tutor->execute([$fetch_content['tutor_id']]);
         $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
?>
<div class="pdf-details">
   <!-- Afficher le PDF -->
   <embed src="uploaded_files/<?= $fetch_content['pdf']; ?>" type="application/pdf" class="pdf-viewer" width="100%" height="600px" />
   <h3 class="title"><?= $fetch_content['title']; ?></h3>
   <div class="info">
      <p><i class="fas fa-calendar"></i><span><?= $fetch_content['date']; ?></span></p>
      <p><i class="fas fa-heart"></i><span><?= $total_likes; ?> likes</span></p>
   </div>
   <div class="tutor">
      <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
      <div>
         <h3><?= $fetch_tutor['name']; ?></h3>
      </div>
   </div>
   <!-- Formulaire pour "liker" le contenu -->
   <form action="" method="post" class="flex">
      <input type="hidden" name="content_id" value="<?= $content_id; ?>">
      <a href="playlist.php?get_id=<?= $fetch_content['playlist_id']; ?>" class="inline-btn">view playlist</a>
      <?php
         // Afficher le bouton "like" ou "liked" en fonction de si l'utilisateur a déjà "liké" le contenu
         if($verify_likes->rowCount() > 0){
      ?>
      <button type="submit" name="like_content"><i class="fas fa-heart"></i><span>liked</span></button>
      <?php
      }else{
      ?>
      <button type="submit" name="like_content"><i class="far fa-heart"></i><span>like</span></button>
      <?php
         }
      ?>
   </form>
   <div class="description"><p><?= $fetch_content['description']; ?></p></div>
</div>
<?php
      }
   }else{
      // Afficher un message si aucun contenu n'est disponible
      echo '<p class="empty">no course added yet!</p>';
   }
?>

</section>

<!-- Section des commentaires -->

<section class="comments">

   <h1 class="heading">add a comment</h1>

   <!-- Formulaire pour ajouter un commentaire -->
   <form action="" method="post" class="add-comment">
      <input type="hidden" name="content_id" value="<?= $get_id; ?>">
      <textarea name="comment_box" required placeholder="write your comment..." maxlength="1000" cols="30" rows="10"></textarea>
      <input type="submit" value="add comment" name="add_comment" class="inline-btn">
   </form>

   <h1 class="heading">user comments</h1>

   
   <div class="show-comments">
      <?php
         // Sélectionner tous les commentaires associés à ce contenu
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ?");
         $select_comments->execute([$get_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){   
               // Sélectionner les détails de l'utilisateur qui a commenté
               $select_commentor = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_commentor->execute([$fetch_comment['user_id']]);
               $fetch_commentor = $select_commentor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box" style="<?php if($fetch_comment['user_id'] == $user_id){echo 'order:-1;';} ?>">
         <div class="user">
            <img src="uploaded_files/<?= $fetch_commentor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_commentor['name']; ?></h3>
               <span><?= $fetch_comment['date']; ?></span>
            </div>
         </div>
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         <?php
            // Afficher les boutons d'édition et de suppression pour les commentaires de l'utilisateur actuel
            if($fetch_comment['user_id'] == $user_id){ 
         ?>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="edit_comment" class="inline-option-btn">edit comment</button>
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('delete this comment?');">delete comment</button>
         </form>
         <?php
         }
         ?>
      </div>
      <?php
       }
      }else{
         // Afficher un message si aucun commentaire n'a été ajouté pour ce contenu
         echo '<p class="empty">no comments added yet!</p>';
      }
      ?>
      </div>
   
</section>

<?php include 'components/footer.php'; ?>

<!-- Lien du fichier JavaScript personnalisé -->
<script src="js/script.js"></script>
   
</body>
</html>
