<?php

// Inclure le fichier de connexion à la base de données
include 'components/connect.php';

// Vérifier si l'ID utilisateur est défini dans les cookies
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

// Vérifier si le formulaire de contact est soumis
if(isset($_POST['submit'])){

   // Récupérer et filtrer les données du formulaire
   $name = $_POST['name']; 
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email']; 
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number']; 
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $msg = $_POST['msg']; 
   $msg = filter_var($msg, FILTER_SANITIZE_STRING);

   // Vérifier si le message existe déjà dans la base de données
   $select_contact = $conn->prepare("SELECT * FROM `contact` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $select_contact->execute([$name, $email, $number, $msg]);

   if($select_contact->rowCount() > 0){
      $message[] = 'message sent already!';
   }else{
      // Insérer le nouveau message dans la base de données
      $insert_message = $conn->prepare("INSERT INTO `contact`(name, email, number, message) VALUES(?,?,?,?)");
      $insert_message->execute([$name, $email, $number, $msg]);
      $message[] = 'message sent successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>contact</title>

   <!-- Lien CDN pour Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien du fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- Section de contact -->

<section class="contact">

   <div class="row">

      <!-- Formulaire de contact -->
      <form action="" method="post">
         <h3>get in touch</h3>
         <input type="text" placeholder="enter your name" required maxlength="100" name="name" class="box">
         <input type="email" placeholder="enter your email" required maxlength="100" name="email" class="box">
         <input type="number" min="0" max="9999999999" placeholder="enter your number" required maxlength="10" name="number" class="box">
         <textarea name="msg" class="box" placeholder="enter your message" required cols="30" rows="10" maxlength="1000"></textarea>
         <input type="submit" value="send message" class="inline-btn" name="submit">
      </form>

   </div>

   <!-- Coordonnées de contact -->
   <div class="box-container">

      <div class="box">
         <i class="fas fa-phone"></i>
         <h3>phone number</h3>
         <a href="tel:05396-88025">05396-88025</a>
      </div>

      <div class="box">
         <i class="fas fa-envelope"></i>
         <h3>email address</h3>
         <a href="mailto:Ensate@uae.ac.ma">Ensate@uae.ac.ma</a>
      </div>

      <div class="box">
         <i class="fas fa-map-marker-alt"></i>
         <h3>University address</h3>
         <a href="#">Avenue de la Palestine Mhanech I, TÉTOUAN</a>
      </div>

   </div>

</section>

<!-- Section de contact ends -->

<?php include 'components/footer.php'; ?>  

<!-- Lien du fichier JavaScript personnalisé -->
<script src="js/script.js"></script>
   
</body>
</html>
