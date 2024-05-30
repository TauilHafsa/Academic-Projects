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

// Sélection du nombre total de playlists associées au tuteur
$select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
$select_playlists->execute([$tutor_id]);
$total_playlists = $select_playlists->rowCount();

// Sélection du nombre total de contenus associés au tuteur
$select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
$select_contents->execute([$tutor_id]);
$total_contents = $select_contents->rowCount();

// Sélection du nombre total de likes associés au tuteur
$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
$select_likes->execute([$tutor_id]);
$total_likes = $select_likes->rowCount();

// Sélection du nombre total de commentaires associés au tuteur
$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
$select_comments->execute([$tutor_id]);
$total_comments = $select_comments->rowCount();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
<?php include '../components/admin_header.php'; ?>

<!-- Section affichant les détails du profil du tuteur -->
<section class="tutor-profile" style="min-height: calc(100vh - 19rem);"> 

   <h1 class="heading">profile details</h1>

   <div class="details">
      <div class="tutor">
         <!-- Affichage de l'image, nom du tuteur et lien pour mettre à jour le profil -->
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= $fetch_profile['name']; ?></h3>
         <a href="update.php" class="inline-btn">update profile</a>
      </div>
      <!-- Affichage des statistiques des playlists, des contenus PDF, des likes et des commentaires -->
      <div class="flex">
         <div class="box">
            <span><?= $total_playlists; ?></span>
            <p>total playlists</p>
            <a href="playlists.php" class="btn">view playlists</a>
         </div>
         <div class="box">
            <span><?= $total_contents; ?></span>
            <p>total pdf</p>
            <a href="contents.php" class="btn">view contents</a>
         </div>
         <div class="box">
            <span><?= $total_likes; ?></span>
            <p>total likes</p>
            <a href="contents.php" class="btn">view contents</a>
         </div>
         <div class="box">
            <span><?= $total_comments; ?></span>
            <p>total comments</p>
            <a href="comments.php" class="btn">view comments</a>
         </div>
      </div>
   </div>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
