<?php
// Inclusion du fichier de connexion à la base de données
include '../components/connect.php';

// Vérification de la soumission du formulaire d'inscription
if(isset($_POST['submit'])){
   
   // Génération d'un identifiant unique
   $id = unique_id();

   // Récupération et filtrage du nom
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   // Récupération et filtrage de l'email
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   // Hachage du mot de passe
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   // Hachage du mot de passe de confirmation
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   // Récupération du nom de l'image
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);

   // Récupération de l'extension de l'image
   $ext = pathinfo($image, PATHINFO_EXTENSION);

   // Renommage de l'image avec un identifiant unique
   $rename = unique_id().'.'.$ext;

   // Récupération de la taille de l'image
   $image_size = $_FILES['image']['size'];

   // Récupération du nom temporaire de l'image
   $image_tmp_name = $_FILES['image']['tmp_name'];

   // Chemin du dossier pour enregistrer l'image
   $image_folder = '../uploaded_files/'.$rename;

   // Vérification de l'existence de l'email dans la base de données
   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email = ?");
   $select_tutor->execute([$email]);
   
   // Si l'email existe déjà, afficher un message d'erreur
   if($select_tutor->rowCount() > 0){
      $message[] = 'email already taken!';
   }else{
      // Si les mots de passe ne correspondent pas, afficher un message d'erreur
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         // Insertion du nouvel utilisateur dans la base de données et enregistrement de l'image
         $insert_tutor = $conn->prepare("INSERT INTO `tutors`(id, name, email, password, image) VALUES(?,?,?,?,?)");
         $insert_tutor->execute([$id, $name, $email, $cpass, $rename]);
         move_uploaded_file($image_tmp_name, $image_folder);
         $message[] = 'new tutor registered! please login now';
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
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body style="padding-left: 0;">

<?php
// Affichage des messages d'erreur s'il y en a
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message form">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!-- register section starts  -->

<section class="form-container">

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>register new</h3>
      <div class="flex">
         <div class="col">
            <p>your name <span>*</span></p>
            <input type="text" name="name" placeholder="eneter your name" maxlength="50" required class="box">
            
            <p>your email <span>*</span></p>
            <input type="email" name="email" placeholder="enter your email" maxlength="20" required class="box">
         </div>
         <div class="col">
            <p>your password <span>*</span></p>
            <input type="password" name="pass" placeholder="enter your password" maxlength="20" required class="box">
            <p>confirm password <span>*</span></p>
            <input type="password" name="cpass" placeholder="confirm your password" maxlength="20" required class="box">
            <p>select pic <span>*</span></p>
            <input type="file" name="image" accept="image/*" required class="box">
         </div>
      </div>
      <p class="link">already have an account? <a href="login.php">login now</a></p>
      <input type="submit" name="submit" value="register now" class="btn">
   </form>

</section>

<!-- registe section ends -->

<!-- Script pour activer le mode sombre -->

<script>

let darkMode = localStorage.getItem('dark-mode');
let body = document.body;

const enabelDarkMode = () =>{
   body.classList.add('dark');
   localStorage.setItem('dark-mode', 'enabled');
}

const disableDarkMode = () =>{
   body.classList.remove('dark');
   localStorage.setItem('dark-mode', 'disabled');
}

if(darkMode === 'enabled'){
   enabelDarkMode();
}else{
   disableDarkMode();
}

</script>
   
</body>
</html>
