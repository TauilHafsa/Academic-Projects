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

// Requête pour sélectionner tous les contenus associés à ce tuteur
$select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
$select_contents->execute([$tutor_id]);
// Comptage du nombre total de contenus
$total_contents = $select_contents->rowCount();

// Requête pour sélectionner toutes les playlists associées à ce tuteur
$select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
$select_playlists->execute([$tutor_id]);
// Comptage du nombre total de playlists
$total_playlists = $select_playlists->rowCount();

// Requête pour sélectionner tous les likes associés à ce tuteur
$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
$select_likes->execute([$tutor_id]);
// Comptage du nombre total de likes
$total_likes = $select_likes->rowCount();

// Requête pour sélectionner tous les commentaires associés à ce tuteur
$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
$select_comments->execute([$tutor_id]);
// Comptage du nombre total de commentaires
$total_comments = $select_comments->rowCount();

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
   
<section class="dashboard">

   <h1 class="heading">dashboard</h1>

   <div class="box-container">

      <div class="box">
         <h3>welcome!</h3>
         <!-- Affichage du nom du profil -->
         <p><?= $fetch_profile['name']; ?></p>
         <a href="profile.php" class="btn">view profile</a>
      </div>

      <div class="box">
         <!-- Affichage du nombre total de contenus -->
         <h3><?= $total_contents; ?></h3>
         <p>total courses</p>
         <a href="add_content.php" class="btn">add new course</a>
      </div>

      <div class="box">
         <!-- Affichage du nombre total de playlists -->
         <h3><?= $total_playlists; ?></h3>
         <p>total playlists</p>
         <a href="add_playlist.php" class="btn">add new playlist</a>
      </div>

      <div class="box">
         <!-- Affichage du nombre total de likes -->
         <h3><?= $total_likes; ?></h3>
         <p>total likes</p>
         <a href="contents.php" class="btn">view courses</a>
      </div>

      <div class="box">
         <!-- Affichage du nombre total de commentaires -->
         <h3><?= $total_comments; ?></h3>
         <p>total comments</p>
         <a href="comments.php" class="btn">view comments</a>
      </div>

      <div class="box">
         <h3>quick select</h3>
         <p>login or register</p>
         <!-- Boutons de connexion et d'inscription -->
         <div class="flex-btn">
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div>
      </div>

   </div>

</section>


<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
